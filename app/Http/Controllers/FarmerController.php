<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Crop;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentReceipt;
use App\Models\BankDetail;
use App\Services\ActivityLogger;
use App\Notifications\OrderConfirmedNotification;
use App\Notifications\OrderDeliveredNotification;

class FarmerController extends Controller
{
    /**
     * Display farmer dashboard
     */
    public function dashboard()
    {
        $farmer = Auth::user();
        
        // Get farmer's crops and orders with relationships
        $crops = Crop::where('farmer_id', $farmer->id)->get();
        $orderItems = OrderItem::where('farmer_id', $farmer->id)
            ->with('order.buyer', 'crop')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Calculate comprehensive analytics
        $stats = [
            // Crop Analytics
            'total_crops' => $crops->count(),
            'available_crops' => $crops->where('is_available', true)->count(),
            'sold_crops' => $crops->where('is_available', false)->count(),
            'crops_by_category' => $crops->groupBy('category')->map->count(),
            'crops_by_region' => $crops->groupBy('region')->map->count(),
            
            // Order Analytics
            'total_orders' => $orderItems->count(),
            'pending_orders' => $orderItems->where('order.status', 'pending')->count(),
            'confirmed_orders' => $orderItems->where('order.status', 'confirmed')->count(),
            'delivered_orders' => $orderItems->where('order.status', 'delivered')->count(),
            'cancelled_orders' => $orderItems->where('order.status', 'cancelled')->count(),
            
            // Revenue Analytics
            'total_revenue' => $orderItems->where('order.status', 'delivered')->sum('total_price'),
            'pending_revenue' => $orderItems->where('order.status', 'pending')->sum('total_price'),
            'confirmed_revenue' => $orderItems->where('order.status', 'confirmed')->sum('total_price'),
            'average_order_value' => $orderItems->where('order.status', 'delivered')->avg('total_price'),
            
            // Performance Analytics
            'total_quantity_sold' => $orderItems->where('order.status', 'delivered')->sum('quantity'),
            'average_price_per_kg' => $orderItems->where('order.status', 'delivered')->avg('price_per_kg'),
            'most_popular_crop' => $orderItems->groupBy('crop.name')->map->count()->sortDesc()->first(),
            'top_buyer' => $orderItems->where('order.status', 'delivered')->groupBy('order.buyer.name')->map->sum('total_price')->sortDesc()->first(),
            
            // Growth Analytics
            'new_crops_this_month' => $crops->where('created_at', '>=', now()->startOfMonth())->count(),
            'new_orders_this_month' => $orderItems->where('created_at', '>=', now()->startOfMonth())->count(),
            'revenue_this_month' => $orderItems->where('order.status', 'delivered')->where('created_at', '>=', now()->startOfMonth())->sum('total_price'),
            
            // Monthly Performance (Last 6 months)
            'monthly_performance' => $orderItems->where('order.status', 'delivered')
                ->groupBy(function($item) {
                    return $item->created_at->format('Y-m');
                })
                ->map(function($items) {
                    return [
                        'orders' => $items->count(),
                        'revenue' => $items->sum('total_price'),
                        'quantity' => $items->sum('quantity'),
                    ];
                })
                ->sortKeys()
                ->take(-6),
        ];

        // Get recent orders for display
        $recentOrders = $orderItems->take(5);

        return view('farmer.dashboard', compact('farmer', 'crops', 'orderItems', 'stats', 'recentOrders'));
    }

    /**
     * Display farmer analytics
     */
    public function analytics()
    {
        $farmer = Auth::user();
        
        // Get farmer's crops and orders with relationships
        $crops = Crop::where('farmer_id', $farmer->id)->get();
        $orderItems = OrderItem::where('farmer_id', $farmer->id)
            ->with('order.buyer', 'crop')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Calculate comprehensive analytics
        $stats = [
            // Crop Analytics
            'total_crops' => $crops->count(),
            'available_crops' => $crops->where('is_available', true)->count(),
            'sold_crops' => $crops->where('is_available', false)->count(),
            'crops_by_category' => $crops->groupBy('category')->map->count(),
            'crops_by_region' => $crops->groupBy('region')->map->count(),
            
            // Order Analytics
            'total_orders' => $orderItems->count(),
            'pending_orders' => $orderItems->where('order.status', 'pending')->count(),
            'confirmed_orders' => $orderItems->where('order.status', 'confirmed')->count(),
            'delivered_orders' => $orderItems->where('order.status', 'delivered')->count(),
            'cancelled_orders' => $orderItems->where('order.status', 'cancelled')->count(),
            
            // Revenue Analytics
            'total_revenue' => $orderItems->where('order.status', 'delivered')->sum('total_price'),
            'pending_revenue' => $orderItems->where('order.status', 'pending')->sum('total_price'),
            'confirmed_revenue' => $orderItems->where('order.status', 'confirmed')->sum('total_price'),
            'average_order_value' => $orderItems->where('order.status', 'delivered')->avg('total_price'),
            
            // Performance Analytics
            'total_quantity_sold' => $orderItems->where('order.status', 'delivered')->sum('quantity'),
            'average_price_per_kg' => $orderItems->where('order.status', 'delivered')->avg('price_per_kg'),
            'most_popular_crop' => $orderItems->groupBy('crop.name')->map->count()->sortDesc()->first(),
            'top_buyer' => $orderItems->where('order.status', 'delivered')->groupBy('order.buyer.name')->map->sum('total_price')->sortDesc()->first(),
            
            // Growth Analytics
            'new_crops_this_month' => $crops->where('created_at', '>=', now()->startOfMonth())->count(),
            'new_orders_this_month' => $orderItems->where('created_at', '>=', now()->startOfMonth())->count(),
            'revenue_this_month' => $orderItems->where('order.status', 'delivered')->where('created_at', '>=', now()->startOfMonth())->sum('total_price'),
            
            // Monthly Performance (Last 6 months)
            'monthly_performance' => $orderItems->where('order.status', 'delivered')
                ->groupBy(function($item) {
                    return $item->created_at->format('Y-m');
                })
                ->map(function($items) {
                    return [
                        'orders' => $items->count(),
                        'revenue' => $items->sum('total_price'),
                        'quantity' => $items->sum('quantity'),
                    ];
                })
                ->sortKeys()
                ->take(-6),
        ];

        return view('farmer.analytics', compact('farmer', 'stats'));
    }

    /**
     * Display all crops for the farmer
     */
    public function crops()
    {
        // Check if farmer is verified
        $farmer = Auth::user();
        if (!$farmer->verification || $farmer->verification->status !== 'verified') {
            return redirect()->route('verification.index')
                ->with('error', 'You must verify your account before listing crops. Please complete your verification process.');
        }

        $crops = Crop::where('farmer_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('farmer.crops.index', compact('crops'));
    }

    /**
     * Show form to create new crop
     */
    public function createCrop()
    {
        // Check if farmer is verified
        $farmer = Auth::user();
        if (!$farmer->verification || $farmer->verification->status !== 'verified') {
            return redirect()->route('verification.index')
                ->with('error', 'You must verify your account before creating crops. Please complete your verification process.');
        }

        return view('farmer.crops.create');
    }

    /**
     * Store new crop
     */
    public function storeCrop(Request $request)
    {
        // Check if farmer is verified
        $farmer = Auth::user();
        if (!$farmer->verification || $farmer->verification->status !== 'verified') {
            return redirect()->route('verification.index')
                ->with('error', 'You must verify your account before creating crops. Please complete your verification process.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price_per_kg' => 'required|numeric|min:0',
            'available_quantity' => 'required|numeric|min:0',
            'category' => 'required|string|max:100',
            'region' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $cropData = $validated + [
            'farmer_id' => Auth::id(),
            'unit' => 'kg',
            'is_available' => true,
        ];

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('storage/crops'), $imageName);
            $cropData['image'] = $imageName;
        }

        $crop = Crop::create($cropData);

        // Log crop creation activity
        ActivityLogger::cropCreated($crop);

        return redirect()->route('farmer.crops.index')->with('success', 'Crop added successfully!');
    }

    /**
     * Show form to edit crop
     */
    public function editCrop(Crop $crop)
    {
        if ($crop->farmer_id !== Auth::id()) {
            abort(403);
        }

        return view('farmer.crops.edit', compact('crop'));
    }

    /**
     * Update crop
     */
    public function updateCrop(Request $request, Crop $crop)
    {
        if ($crop->farmer_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price_per_kg' => 'required|numeric|min:0',
            'available_quantity' => 'required|numeric|min:0',
            'category' => 'required|string|max:100',
            'region' => 'required|string|max:100',
            'is_available' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $cropData = $validated;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('storage/crops'), $imageName);
            $cropData['image'] = $imageName;
        }

        $crop->update($cropData);

        return redirect()->route('farmer.crops.index')->with('success', 'Crop updated successfully!');
    }

    /**
     * Delete crop
     */
    public function deleteCrop(Crop $crop)
    {
        if ($crop->farmer_id !== Auth::id()) {
            abort(403);
        }

        // Check if crop has active orders
        $hasActiveOrders = $crop->orderItems()
            ->whereHas('order', function($query) {
                $query->whereIn('status', ['pending', 'confirmed']);
            })
            ->exists();

        if ($hasActiveOrders) {
            return redirect()->route('farmer.crops.show', $crop)
                ->with('error', 'Cannot delete crop with active orders. Please complete or cancel all orders first.');
        }

        $crop->delete();

        return redirect()->route('farmer.crops.index')->with('success', 'Crop deleted successfully!');
    }

    /**
     * Show crop details
     */
    public function showCrop(Crop $crop)
    {
        if ($crop->farmer_id !== Auth::id()) {
            abort(403);
        }

        $crop->load('orderItems.order');

        return view('farmer.crops.show', compact('crop'));
    }

    /**
     * Toggle crop availability
     */
    public function toggleCropAvailability(Crop $crop)
    {
        if ($crop->farmer_id !== Auth::id()) {
            abort(403);
        }

        $crop->update(['is_available' => !$crop->is_available]);

        $status = $crop->is_available ? 'available' : 'unavailable';

        return redirect()->route('farmer.crops.show', $crop)
            ->with('success', "Crop marked as {$status}!");
    }

    /**
     * Display farmer orders
     */
    public function orders(Request $request)
    {
        $query = OrderItem::where('farmer_id', Auth::id())
            ->with('order', 'crop', 'order.buyer')
            ->orderBy('created_at', 'desc');

        // Filter by status if provided
        if ($request->has('status') && in_array($request->status, ['pending', 'confirmed', 'delivered'])) {
            $query->whereHas('order', function($orderQuery) use ($request) {
                $orderQuery->where('status', $request->status);
            });
        }

        $orders = $query->paginate(10);

        // Get counts for each status
        $statusCounts = [
            'total' => OrderItem::where('farmer_id', Auth::id())->count(),
            'pending' => OrderItem::where('farmer_id', Auth::id())->whereHas('order', function($query) {
                $query->where('status', 'pending');
            })->count(),
            'confirmed' => OrderItem::where('farmer_id', Auth::id())->whereHas('order', function($query) {
                $query->where('status', 'confirmed');
            })->count(),
            'delivered' => OrderItem::where('farmer_id', Auth::id())->whereHas('order', function($query) {
                $query->where('status', 'delivered');
            })->count(),
        ];

        return view('farmer.orders.index', compact('orders', 'statusCounts'));
    }

    /**
     * Show order details
     */
    public function showOrder(Order $order)
    {
        // Check if this order contains items from this farmer
        $hasFarmerItems = $order->orderItems()->where('farmer_id', Auth::id())->exists();
        
        if (!$hasFarmerItems) {
            abort(403);
        }

        return view('farmer.orders.show', compact('order'));
    }

    /**
     * Confirm order
     */
    public function confirmOrder(Order $order)
    {
        // Check if this order contains items from this farmer
        $hasFarmerItems = $order->orderItems()->where('farmer_id', Auth::id())->exists();
        
        if (!$hasFarmerItems) {
            if (request()->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            abort(403);
        }

        $order->update(['status' => 'confirmed']);

        // Notify buyer about order confirmation
        $order->buyer->notify(new OrderConfirmedNotification($order));

        // Handle AJAX requests
        if (request()->ajax() || request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Order confirmed successfully!',
                'order' => $order
            ]);
        }

        return redirect()->route('farmer.orders.show', $order)
            ->with('success', 'Order confirmed successfully!');
    }

    /**
     * Deliver order
     */
    public function deliverOrder(Order $order)
    {
        // Check if this order contains items from this farmer
        $hasFarmerItems = $order->orderItems()->where('farmer_id', Auth::id())->exists();
        
        if (!$hasFarmerItems) {
            if (request()->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            abort(403);
        }

        $order->update(['status' => 'delivered']);

        // Notify buyer about order delivery
        $order->buyer->notify(new OrderDeliveredNotification($order));

        // Handle AJAX requests
        if (request()->ajax() || request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Order marked as delivered!',
                'order' => $order
            ]);
        }

        return redirect()->route('farmer.orders.show', $order)
            ->with('success', 'Order marked as delivered!');
    }

    /**
     * Display payment receipts
     */
    public function paymentReceipts(Request $request)
    {
        $query = PaymentReceipt::with(['order', 'buyer', 'verifiedBy'])
            ->where('farmer_id', Auth::id())
            ->orderBy('created_at', 'desc');

        // Apply status filter
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $receipts = $query->paginate(10);

        // Get status counts
        $statusCounts = [
            'total' => PaymentReceipt::where('farmer_id', Auth::id())->count(),
            'pending' => PaymentReceipt::where('farmer_id', Auth::id())->where('status', 'pending')->count(),
            'verified' => PaymentReceipt::where('farmer_id', Auth::id())->where('status', 'verified')->count(),
            'rejected' => PaymentReceipt::where('farmer_id', Auth::id())->where('status', 'rejected')->count(),
        ];

        return view('farmer.payment-receipts', compact('receipts', 'statusCounts'));
    }

    /**
     * Verify payment receipt
     */
    public function verifyPaymentReceipt(Request $request, PaymentReceipt $receipt)
    {
        // Check if receipt belongs to this farmer
        if ($receipt->farmer_id !== Auth::id()) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            abort(403);
        }

        // Check if receipt is already processed
        if ($receipt->status !== 'pending') {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['error' => 'Receipt already processed'], 422);
            }
            return back()->with('error', 'Receipt already processed.');
        }

        // Update receipt status
        $receipt->update([
            'status' => 'verified',
            'verified_at' => now(),
            'verified_by' => Auth::id(),
        ]);

        // Update order status if all receipts are verified
        $order = $receipt->order;
        $allReceiptsVerified = $order->paymentReceipts()
            ->where('status', '!=', 'pending')
            ->where('status', '!=', 'rejected')
            ->count() === $order->orderItems()->count();

        if ($allReceiptsVerified) {
            $order->update(['status' => 'confirmed']);
        }

        // Log activity
        ActivityLogger::log(
            'payment_receipt_verified',
            "Verified payment receipt for order #{$order->order_number}",
            Auth::user()
        );

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment receipt verified successfully!',
                'receipt' => $receipt
            ]);
        }

        return back()->with('success', 'Payment receipt verified successfully!');
    }

    /**
     * Reject payment receipt
     */
    public function rejectPaymentReceipt(Request $request, PaymentReceipt $receipt)
    {
        // Check if receipt belongs to this farmer
        if ($receipt->farmer_id !== Auth::id()) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            abort(403);
        }

        // Check if receipt is already processed
        if ($receipt->status !== 'pending') {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['error' => 'Receipt already processed'], 422);
            }
            return back()->with('error', 'Receipt already processed.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        // Update receipt status
        $receipt->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        // Update order status
        $receipt->order->update(['status' => 'payment_rejected']);

        // Log activity
        ActivityLogger::log(
            'payment_receipt_rejected',
            "Rejected payment receipt for order #{$receipt->order->order_number}",
            Auth::user()
        );

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment receipt rejected successfully!',
                'receipt' => $receipt
            ]);
        }

        return back()->with('success', 'Payment receipt rejected successfully!');
    }

    /**
     * Display bank details
     */
    public function bankDetails()
    {
        $bankDetails = Auth::user()->bankDetails;
        return view('farmer.bank-details', compact('bankDetails'));
    }

    /**
     * Store bank details
     */
    public function storeBankDetails(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'account_type' => 'required|in:savings,current',
            'routing_number' => 'nullable|string|max:255',
            'swift_code' => 'nullable|string|max:255',
            'branch_address' => 'nullable|string|max:500',
            'instructions' => 'nullable|string|max:1000',
        ]);

        $bankDetails = BankDetail::create([
            'farmer_id' => Auth::id(),
            'bank_name' => $request->bank_name,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
            'account_type' => $request->account_type,
            'routing_number' => $request->routing_number,
            'swift_code' => $request->swift_code,
            'branch_address' => $request->branch_address,
            'instructions' => $request->instructions,
            'is_active' => true,
        ]);

        // Log activity
        ActivityLogger::log(
            'bank_details_added',
            "Added bank details for {$bankDetails->bank_name}",
            $bankDetails,
            ['bank_name' => $bankDetails->bank_name]
        );

        return redirect()->route('farmer.bank-details')
            ->with('success', 'Bank details added successfully!');
    }

    /**
     * Edit bank details
     */
    public function editBankDetails()
    {
        $bankDetails = Auth::user()->bankDetails;
        return view('farmer.edit-bank-details', compact('bankDetails'));
    }

    /**
     * Update bank details
     */
    public function updateBankDetails(Request $request)
    {
        $bankDetails = Auth::user()->bankDetails;

        if (!$bankDetails) {
            return redirect()->route('farmer.bank-details')
                ->with('error', 'No bank details found to update.');
        }

        $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'account_type' => 'required|in:savings,current',
            'routing_number' => 'nullable|string|max:255',
            'swift_code' => 'nullable|string|max:255',
            'branch_address' => 'nullable|string|max:500',
            'instructions' => 'nullable|string|max:1000',
        ]);

        $bankDetails->update([
            'bank_name' => $request->bank_name,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
            'account_type' => $request->account_type,
            'routing_number' => $request->routing_number,
            'swift_code' => $request->swift_code,
            'branch_address' => $request->branch_address,
            'instructions' => $request->instructions,
        ]);

        // Log activity
        ActivityLogger::log(
            'bank_details_updated',
            "Updated bank details for {$bankDetails->bank_name}",
            $bankDetails,
            ['bank_name' => $bankDetails->bank_name]
        );

        return redirect()->route('farmer.bank-details')
            ->with('success', 'Bank details updated successfully!');
    }
}
