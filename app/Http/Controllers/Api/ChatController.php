<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    /**
     * Get user's chats
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = $user->chats()->with(['buyer', 'farmer', 'crop', 'order']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $chats = $query->orderBy('last_message_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => [
                'chats' => $chats->items(),
                'pagination' => [
                    'current_page' => $chats->currentPage(),
                    'per_page' => $chats->perPage(),
                    'total' => $chats->total(),
                    'last_page' => $chats->lastPage(),
                ]
            ]
        ]);
    }

    /**
     * Create new chat
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'buyer_id' => 'required|exists:users,id',
            'farmer_id' => 'required|exists:users,id',
            'crop_id' => 'nullable|exists:crops,id',
            'order_id' => 'nullable|exists:orders,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Ensure user is participant
        if ($user->id !== $request->buyer_id && $user->id !== $request->farmer_id) {
            return response()->json([
                'success' => false,
                'message' => 'You can only create chats you are participating in'
            ], 403);
        }

        $chat = Chat::create([
            'buyer_id' => $request->buyer_id,
            'farmer_id' => $request->farmer_id,
            'crop_id' => $request->crop_id,
            'order_id' => $request->order_id,
            'subject' => $request->subject,
            'last_message' => $request->message,
            'last_message_at' => now(),
            'status' => 'active',
        ]);

        // Create initial message
        $chat->messages()->create([
            'sender_id' => $user->id,
            'content' => $request->message,
            'is_read' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Chat created successfully',
            'data' => [
                'chat' => $chat->load(['buyer', 'farmer', 'crop', 'order', 'messages.sender'])
            ]
        ], 201);
    }

    /**
     * Get specific chat with messages
     */
    public function show($id)
    {
        $user = $request->user();
        $chat = $user->chats()
            ->with(['buyer', 'farmer', 'crop', 'order', 'messages.sender'])
            ->findOrFail($id);

        // Check if user is participant
        if ($chat->buyer_id !== $user->id && $chat->farmer_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to access this chat'
            ], 403);
        }

        // Mark messages as read
        $chat->messages()
            ->where('sender_id', '!=', $user->id)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'data' => [
                'chat' => $chat
            ]
        ]);
    }

    /**
     * Send message in chat
     */
    public function sendMessage(Request $request, $id)
    {
        $user = $request->user();
        $chat = $user->chats()->findOrFail($id);

        // Check if user is participant
        if ($chat->buyer_id !== $user->id && $chat->farmer_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to send messages in this chat'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $message = $chat->messages()->create([
            'sender_id' => $user->id,
            'content' => $request->content,
            'is_read' => true,
        ]);

        // Update chat
        $chat->update([
            'last_message' => $request->content,
            'last_message_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
            'data' => [
                'message' => $message->load('sender')
            ]
        ]);
    }
}
