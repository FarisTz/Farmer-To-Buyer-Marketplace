<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Crop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Get user's orders
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = $user->buyerOrders()->with(['orderItems.crop', 'buyer', 'orderItems.crop.farmer']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $orders = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => [
                'orders' => $orders->items(),
                'pagination' => [
                    'current_page' => $orders->currentPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total(),
                    'last_page' => $orders->lastPage(),
                ]
            ]
        ]);
    }

    /**
     * Create new order
     */
    public function store(Request $request)
    {
        $user = $request->user();
        
        if (!$user->isBuyer()) {
            return response()->json([
                'success' => false,
                'message' => 'Only buyers can create orders'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.crop_id' => 'required|exists:crops,id',
            'items.*.quantity' => 'required|integer|min:1',
            'delivery_address' => 'required|string|max:500',
            'payment_method' => 'required|in:bank_transfer,mobile_money,cash_on_delivery',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Test database save operation
            $order = new Order();
            $order->buyer_id = $user->id;
            $order->order_number = 'ORD-TEST-' . time();
            $order->total_amount = 100.00;
            $order->status = 'pending';
            
            // Try to save
            $result = $order->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => [
                    'order_id' => $order->id,
                    'save_result' => $result,
                    'order_number' => $order->order_number
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order creation failed: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Get specific order details
     */
    public function show($id)
    {
        $user = $request->user();
        $order = $user->buyerOrders()
            ->with(['orderItems.crop.farmer', 'buyer', 'orderItems.crop.farmer'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'order' => $order
            ]
        ]);
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, $id)
    {
        $user = $request->user();
        $order = $user->buyerOrders()->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,confirmed,shipped,delivered,cancelled',
            'payment_receipt' => 'nullable|string|max:255',
            'tracking_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Only farmers can update order status
        if ($user->isBuyer() && $order->buyer_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this order'
            ], 403);
        }

        $order->update($request->only([
            'status', 'payment_receipt', 'tracking_number', 'notes'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully',
            'data' => [
                'order' => $order->fresh()
            ]
        ]);
    }
}
