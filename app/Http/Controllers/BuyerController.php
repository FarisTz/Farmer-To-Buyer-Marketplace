<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Crop;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentReceipt;
use App\Services\ActivityLogger;
use App\Notifications\OrderCreatedNotification;

class BuyerController extends Controller
{
    /**
     * Display buyer dashboard
     */
    public function dashboard()
    {
        $buyer = Auth::user();
        $orders = Order::where('buyer_id', $buyer->id)
            ->with('orderItems.crop')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $stats = [
            'total_orders' => $orders->count(),
            'pending_orders' => $orders->where('status', 'pending')->count(),
            'delivered_orders' => $orders->where('status', 'delivered')->count(),
            'total_spent' => $orders->where('status', 'delivered')->sum('total_amount'),
        ];

        return view('buyer.dashboard', compact('buyer', 'orders', 'stats'));
    }

    /**
     * Browse available crops
     */
    public function browseCrops(Request $request)
    {
        $query = Crop::available()->with('farmer');

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->byCategory($request->category);
        }

        // Filter by region
        if ($request->has('region') && $request->region) {
            $query->byRegion($request->region);
        }

        // Filter by price range
        if ($request->has('min_price') && $request->min_price) {
            $query->where('price_per_kg', '>=', $request->min_price);
        }
        if ($request->has('max_price') && $request->max_price) {
            $query->where('price_per_kg', '<=', $request->max_price);
        }

        $crops = $query->orderBy('created_at', 'desc')->paginate(12);

        // Get unique categories and regions for filters
        $categories = Crop::distinct()->pluck('category')->filter();
        $regions = Crop::distinct()->pluck('region')->filter();

        return view('buyer.crops.browse', compact('crops', 'categories', 'regions'));
    }

    /**
     * Show crop details
     */
    public function showCrop(Crop $crop)
    {
        if (!$crop->is_available) {
            abort(404);
        }

        $crop->load('farmer');
        
        // Get related crops from same region or category
        $relatedCrops = Crop::available()
            ->where('id', '!=', $crop->id)
            ->where(function ($query) use ($crop) {
                $query->where('region', $crop->region)
                      ->orWhere('category', $crop->category);
            })
            ->with('farmer')
            ->take(4)
            ->get();

        return view('buyer.crops.show', compact('crop', 'relatedCrops'));
    }

    /**
     * Show cart
     */
    public function cart()
    {
        $cart = session()->get('cart', []);
        $cartItems = [];

        foreach ($cart as $cropId => $quantity) {
            $crop = Crop::with('farmer')->find($cropId);
            if ($crop && $crop->is_available && $crop->available_quantity >= $quantity) {
                $cartItems[] = [
                    'crop' => $crop,
                    'quantity' => $quantity,
                    'total_price' => $crop->price_per_kg * $quantity,
                ];
            }
        }

        $totalAmount = collect($cartItems)->sum('total_price');

        return view('buyer.cart', compact('cartItems', 'totalAmount'));
    }

    /**
     * Add item to cart
     */
    public function addToCart(Request $request, Crop $crop)
    {
        if (!$crop->is_available) {
            return back()->with('error', 'This crop is not available.');
        }

        $request->validate([
            'quantity' => 'required|numeric|min:0.1|max:' . $crop->available_quantity,
        ]);

        $cart = session()->get('cart', []);
        $cart[$crop->id] = ($cart[$crop->id] ?? 0) + $request->quantity;

        // Ensure we don't exceed available quantity
        if ($cart[$crop->id] > $crop->available_quantity) {
            $cart[$crop->id] = $crop->available_quantity;
        }

        session()->put('cart', $cart);

        return back()->with('success', 'Item added to cart!');
    }

    /**
     * Update cart
     */
    public function updateCart(Request $request)
    {
        $cart = session()->get('cart', []);

        foreach ($request->quantity as $cropId => $quantity) {
            $crop = Crop::find($cropId);
            if ($crop && $crop->is_available) {
                if ($quantity > 0 && $quantity <= $crop->available_quantity) {
                    $cart[$cropId] = $quantity;
                } else {
                    unset($cart[$cropId]);
                }
            } else {
                unset($cart[$cropId]);
            }
        }

        session()->put('cart', $cart);

        return redirect()->route('buyer.cart')->with('success', 'Cart updated!');
    }

    /**
     * Remove item from cart
     */
    public function removeFromCart(Request $request)
    {
        $request->validate([
            'crop_id' => 'required|exists:crops,id'
        ]);

        $cart = session()->get('cart', []);
        $cropId = $request->crop_id;

        if (isset($cart[$cropId])) {
            unset($cart[$cropId]);
            session()->put('cart', $cart);
            
            return redirect()->route('buyer.cart')->with('success', 'Item removed from cart!');
        }

        return redirect()->route('buyer.cart')->with('error', 'Item not found in cart!');
    }

    /**
     * Show checkout form
     */
    public function checkout()
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('buyer.cart')->with('error', 'Your cart is empty.');
        }

        $cartItems = [];
        foreach ($cart as $cropId => $quantity) {
            $crop = Crop::with('farmer')->find($cropId);
            if ($crop && $crop->is_available && $crop->available_quantity >= $quantity) {
                $cartItems[] = [
                    'crop' => $crop,
                    'quantity' => $quantity,
                    'total_price' => $crop->price_per_kg * $quantity,
                ];
            }
        }

        if (empty($cartItems)) {
            return redirect()->route('buyer.cart')->with('error', 'No valid items in cart.');
        }

        $totalAmount = collect($cartItems)->sum('total_price');
        $user = Auth::user();

        return view('buyer.checkout', compact('cartItems', 'totalAmount', 'user'));
    }

    /**
     * Place order
     */
    public function placeOrder(Request $request)
    {
        // Debug logging
        \Log::info('=== placeOrder method called ===');
        \Log::info('Request method: ' . $request->method());
        \Log::info('Request data: ', $request->all());
        \Log::info('User ID: ' . Auth::id());
        
        $cart = session()->get('cart', []);
        \Log::info('Cart contents: ', $cart);
        
        if (empty($cart)) {
            \Log::warning('Cart is empty, redirecting to cart');
            return redirect()->route('buyer.cart')->with('error', 'Your cart is empty.');
        }

        try {
            $validated = $request->validate([
                'delivery_address' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'notes' => 'nullable|string|max:500',
                'payment_method' => 'required|in:cash,bank_transfer',
                'terms' => 'required|accepted',
            ]);
            \Log::info('Basic validation passed: ', $validated);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Basic validation failed: ', $e->errors());
            throw $e;
        }

        // Only validate receipt if bank transfer is selected
        if ($request->payment_method === 'bank_transfer') {
            \Log::info('Bank transfer selected, checking receipt upload');
            \Log::info('Has file: ' . ($request->hasFile('receipt_image') ? 'Yes' : 'No'));
            
            if ($request->hasFile('receipt_image')) {
                \Log::info('File info: ', [
                    'name' => $request->file('receipt_image')->getClientOriginalName(),
                    'size' => $request->file('receipt_image')->getSize()
                ]);
            } else {
                \Log::info('No file uploaded');
            }
            
            try {
                $request->validate([
                    'receipt_image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
                ]);
                \Log::info('Bank transfer validation passed');
            } catch (\Illuminate\Validation\ValidationException $e) {
                \Log::error('Bank transfer validation failed: ', $e->errors());
                throw $e;
            }
        }

        $cartItems = [];
        $totalAmount = 0;

        foreach ($cart as $cropId => $quantity) {
            $crop = Crop::find($cropId);
            if ($crop && $crop->is_available && $crop->available_quantity >= $quantity) {
                $cartItems[] = [
                    'crop' => $crop,
                    'quantity' => $quantity,
                    'total_price' => $crop->price_per_kg * $quantity,
                ];
                $totalAmount += $crop->price_per_kg * $quantity;
            }
        }

        if (empty($cartItems)) {
            \Log::warning('No valid cart items found');
            return redirect()->route('buyer.cart')->with('error', 'No valid items in cart.');
        }

        $totalAmount = collect($cartItems)->sum('total_price');
        \Log::info('Total amount calculated: ' . $totalAmount);
        \Log::info('Cart items count: ' . count($cartItems));

        // Create order
        try {
            $orderData = [
                'buyer_id' => Auth::id(),
                'order_number' => Order::generateOrderNumber(),
                'total_amount' => $totalAmount,
                'status' => $request->payment_method === 'bank_transfer' ? 'pending_payment' : 'pending',
                'delivery_address' => $request->delivery_address,
                'phone' => $request->phone,
                'notes' => $request->notes,
            ];
            \Log::info('Creating order with data: ', $orderData);
            
            $order = Order::create($orderData);
            \Log::info('Order created successfully: #' . $order->order_number);
        } catch (\Exception $e) {
            \Log::error('Order creation failed: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Failed to create order: ' . $e->getMessage())->withInput();
        }

        // Create order items and update crop quantities
        try {
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'crop_id' => $item['crop']->id,
                    'farmer_id' => $item['crop']->farmer_id,
                    'quantity' => $item['quantity'],
                    'price_per_kg' => $item['crop']->price_per_kg,
                    'total_price' => $item['total_price'],
                ]);
                
                // Update crop quantity
                $item['crop']->decrement('available_quantity', $item['quantity']);
            }
            \Log::info('Order items created and crop quantities updated');
        } catch (\Exception $e) {
            \Log::error('Order items creation failed: ' . $e->getMessage());
            // Clean up the order if items creation fails
            $order->delete();
            return back()->with('error', 'Failed to create order items: ' . $e->getMessage())->withInput();
        }

        // Handle payment receipt if bank transfer
        if ($request->payment_method === 'bank_transfer') {
            try {
                $receiptImage = $request->file('receipt_image');
                $receiptPath = $receiptImage->store('receipts', 'public');
                
                PaymentReceipt::create([
                    'order_id' => $order->id,
                    'buyer_id' => Auth::id(),
                    'farmer_id' => $cartItems[0]['crop']->farmer_id,
                    'receipt_image' => basename($receiptPath),
                    'amount_paid' => $totalAmount,
                    'payment_method' => 'bank_transfer',
                    'transaction_reference' => $request->transaction_reference,
                    'notes' => $request->notes,
                    'status' => 'pending',
                    'payment_date' => now(),
                ]);
                \Log::info('Payment receipt created');
            } catch (\Exception $e) {
                \Log::error('Payment receipt creation failed: ' . $e->getMessage());
                // Continue without receipt - order is still valid
            }
        }

        // Clear cart
        session()->forget('cart');
        \Log::info('Cart cleared');

        // Log activity
        ActivityLogger::orderCreated($order);
        \Log::info('Activity logged');

        // Redirect with success message
        \Log::info('Redirecting to orders page with success');
        return redirect()->route('buyer.orders')
            ->with('success', 'Order placed successfully! Order #' . $order->order_number);
    }

    /**
     * Display buyer orders
     */
    public function orders(Request $request)
    {
        $query = Order::where('buyer_id', Auth::id())
            ->with('orderItems.crop', 'orderItems.farmer')
            ->orderBy('created_at', 'desc');

        // Filter by status if provided
        if ($request->has('status') && in_array($request->status, ['pending', 'confirmed', 'delivered'])) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(10);

        // Get counts for each status
        $statusCounts = [
            'total' => Order::where('buyer_id', Auth::id())->count(),
            'pending' => Order::where('buyer_id', Auth::id())->where('status', 'pending')->count(),
            'confirmed' => Order::where('buyer_id', Auth::id())->where('status', 'confirmed')->count(),
            'delivered' => Order::where('buyer_id', Auth::id())->where('status', 'delivered')->count(),
        ];

        return view('buyer.orders.index', compact('orders', 'statusCounts'));
    }

    /**
     * Show order details
     */
    public function showOrder(Order $order)
    {
        if ($order->buyer_id !== Auth::id()) {
            abort(403);
        }

        $order->load('orderItems.crop', 'orderItems.farmer');

        return view('buyer.orders.show', compact('order'));
    }
}
