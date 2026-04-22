<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use App\Models\Crop;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Display chat list for the current user
     */
    public function index()
    {
        $user = Auth::user();
        
        $chats = Chat::forUser($user->id)
            ->with(['buyer', 'farmer', 'crop', 'messages' => function ($query) {
                $query->latest()->first();
            }])
            ->active()
            ->orderByLastMessage()
            ->paginate(15);

        // Get unread count for each chat
        $chats->getCollection()->each(function ($chat) use ($user) {
            $chat->unread_count = $chat->unreadMessagesCount($user->id);
        });

        return view('chat.index', compact('chats'));
    }

    /**
     * Show specific chat with messages
     */
    public function show(Chat $chat)
    {
        $user = Auth::user();
        
        // Check if user is participant in this chat
        if ($chat->buyer_id !== $user->id && $chat->farmer_id !== $user->id) {
            abort(403);
        }

        // Load messages with sender info
        $chat->load(['messages.sender', 'buyer', 'farmer', 'crop', 'order']);

        // Mark messages as read
        $chat->markAsRead($user->id);

        return view('chat.show', compact('chat'));
    }

    /**
     * Start a new chat
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'farmer_id' => 'required_without:buyer_id|exists:users,id',
            'buyer_id' => 'required_without:farmer_id|exists:users,id',
            'crop_id' => 'nullable|exists:crops,id',
            'order_id' => 'nullable|exists:orders,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
        ]);

        // Determine buyer and farmer IDs based on who is starting the chat
        if ($user->isBuyer()) {
            $buyerId = $user->id;
            $farmerId = $validated['farmer_id'];
        } else {
            $buyerId = $validated['buyer_id'];
            $farmerId = $user->id;
        }

        // Check if chat already exists
        $existingChat = Chat::where('buyer_id', $buyerId)
            ->where('farmer_id', $farmerId)
            ->where('crop_id', $validated['crop_id'] ?? null)
            ->where('order_id', $validated['order_id'] ?? null)
            ->first();

        if ($existingChat) {
            // Add message to existing chat
            $message = $existingChat->messages()->create([
                'sender_id' => $user->id,
                'content' => $validated['message'],
                'is_read' => false,
            ]);

            // Update chat
            $existingChat->update([
                'last_message' => $validated['message'],
                'last_message_at' => now(),
            ]);

            return redirect()->route(Auth::user()->isBuyer() ? 'buyer.chats.show' : 'farmer.chats.show', $existingChat)
                ->with('success', 'Message sent successfully!');
        }

        // Create new chat
        $chat = Chat::create([
            'buyer_id' => $buyerId,
            'farmer_id' => $farmerId,
            'crop_id' => $validated['crop_id'] ?? null,
            'order_id' => $validated['order_id'] ?? null,
            'subject' => $validated['subject'],
            'last_message' => $validated['message'],
            'last_message_at' => now(),
            'is_active' => true,
        ]);

        // Add first message
        $chat->messages()->create([
            'sender_id' => $user->id,
            'content' => $validated['message'],
            'is_read' => false,
        ]);

        return redirect()->route(Auth::user()->isBuyer() ? 'buyer.chats.show' : 'farmer.chats.show', $chat)
            ->with('success', 'Chat started successfully!');
    }

    /**
     * Send a new message
     */
    public function sendMessage(Request $request, Chat $chat)
    {
        $user = Auth::user();
        
        // Check if user is participant in this chat
        if ($chat->buyer_id !== $user->id && $chat->farmer_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $message = $chat->messages()->create([
            'sender_id' => $user->id,
            'content' => $validated['content'],
            'is_read' => false,
        ]);

        // Update chat
        $chat->update([
            'last_message' => $validated['content'],
            'last_message_at' => now(),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully!',
                'data' => [
                    'id' => $message->id,
                    'content' => $message->content,
                    'formatted_time' => $message->formatted_time,
                    'sender_name' => $message->sender_name,
                    'is_from_current_user' => $message->isFromCurrentUser(),
                ]
            ]);
        }

        return redirect()->route(Auth::user()->isBuyer() ? 'buyer.chats.show' : 'farmer.chats.show', $chat)
            ->with('success', 'Message sent successfully!');
    }

    /**
     * Delete chat
     */
    public function delete(Chat $chat)
    {
        $user = Auth::user();
        
        // Check if user is participant in this chat
        if ($chat->buyer_id !== $user->id && $chat->farmer_id !== $user->id) {
            abort(403);
        }

        $chat->update(['is_active' => false]);

        return redirect()->route(Auth::user()->isBuyer() ? 'buyer.chats.index' : 'farmer.chats.index')
            ->with('success', 'Chat archived successfully!');
    }

    /**
     * Start chat from crop page
     */
    public function startFromCrop(Request $request, Crop $crop)
    {
        $user = Auth::user();
        
        if (!$user->isBuyer() && !$user->isFarmer()) {
            abort(403);
        }
        
        // If user is farmer, they can only chat about their own crops
        if ($user->isFarmer() && $crop->farmer_id !== $user->id) {
            abort(403);
        }

        return view('chat.create', [
            'farmer' => $user->isBuyer() ? $crop->farmer : null,
            'buyer' => $user->isFarmer() ? null : null,
            'crop' => $crop,
            'subject' => $user->isBuyer() ? "Inquiry about: {$crop->name}" : "Discussion about: {$crop->name}",
        ]);
    }

    /**
     * Start chat from order page
     */
    public function startFromOrder(Request $request, Order $order)
    {
        $user = Auth::user();
        
        // Allow buyers who placed the order
        $isBuyer = $order->buyer_id === $user->id;
        
        // Allow farmers who own crops in this order
        $isFarmer = false;
        $farmer = null;
        
        if ($user->isFarmer()) {
            foreach ($order->orderItems as $item) {
                if ($item->crop->farmer_id === $user->id) {
                    $isFarmer = true;
                    $farmer = $user;
                    break;
                }
            }
        }
        
        if (!$isBuyer && !$isFarmer) {
            abort(403);
        }

        // Get the other party to chat with
        if ($isBuyer) {
            // Buyer is starting chat, get farmer from first order item
            $firstItem = $order->orderItems->first();
            $chatFarmer = $firstItem->farmer;
            $chatBuyer = null;
        } else {
            // Farmer is starting chat, get buyer
            $chatFarmer = null;
            $chatBuyer = $order->buyer;
        }

        return view('chat.create', [
            'farmer' => $chatFarmer,
            'buyer' => $chatBuyer,
            'order' => $order,
            'subject' => $isBuyer ? "Regarding Order #{$order->order_number}" : "Discussion about Order #{$order->order_number}",
        ]);
    }
}
