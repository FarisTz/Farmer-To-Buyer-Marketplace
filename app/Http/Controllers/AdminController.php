<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Crop;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Activity;

class AdminController extends Controller
{
    /**
     * Display admin dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_farmers' => User::where('role', 'farmer')->count(),
            'total_buyers' => User::where('role', 'buyer')->count(),
            'total_crops' => Crop::count(),
            'available_crops' => Crop::where('is_available', true)->count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'delivered_orders' => Order::where('status', 'delivered')->count(),
            'total_revenue' => Order::where('status', 'delivered')->sum('total_amount'),
        ];

        $recentUsers = User::orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentOrders = Order::with('buyer', 'orderItems.crop')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentCrops = Crop::with('farmer')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentOrders', 'recentCrops'));
    }

    /**
     * Display all users
     */
    public function users()
    {
        $query = User::orderBy('created_at', 'desc');

        // Apply role filter
        if (request('role')) {
            $query->where('role', request('role'));
        }

        // Apply search filter
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if (request('status')) {
            if (request('status') === 'active') {
                $query->where('last_login_at', '>=', now()->subDays(30));
            } elseif (request('status') === 'inactive') {
                $query->where('last_login_at', '<', now()->subDays(30));
            }
        }

        $users = $query->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show user details
     */
    public function showUser(User $user)
    {
        if ($user->role === 'farmer') {
            $user->load('crops', 'farmerOrderItems.order', 'farmerOrderItems.crop');
        } elseif ($user->role === 'buyer') {
            $user->load('buyerOrders.orderItems.crop');
        }

        return view('admin.users.show', compact('user'));
    }

    /**
     * Toggle user status (active/inactive)
     */
    public function toggleUserStatus(User $user)
    {
        // This would require adding an 'is_active' field to users table
        // For now, we'll just redirect back with a message
        return back()->with('info', 'User status toggle feature requires additional database field.');
    }

    /**
     * Display all crops
     */
    public function crops()
    {
        $crops = Crop::with('farmer')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $farmers = User::where('role', 'farmer')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.crops.index', compact('crops', 'farmers'));
    }

    /**
     * Show crop details
     */
    public function showCrop(Crop $crop)
    {
        $crop->load('farmer', 'orderItems.order');

        return view('admin.crops.show', compact('crop'));
    }

    /**
     * Toggle crop availability
     */
    public function toggleCropAvailability(Crop $crop)
    {
        $crop->is_available = !$crop->is_available;
        $crop->save();

        $status = $crop->is_available ? 'available' : 'unavailable';
        return back()->with('success', "Crop is now {$status}.");
    }

    /**
     * Display all orders
     */
    public function orders()
    {
        $query = Order::with('buyer', 'orderItems.crop', 'orderItems.farmer');

        // Apply status filter
        if (request('status')) {
            $query->where('status', request('status'));
        }

        // Apply search filter
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('buyer', function($buyerQuery) use ($search) {
                      $buyerQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Apply buyer filter
        if (request('buyer')) {
            $query->where('buyer_id', request('buyer'));
        }

        // Apply farmer filter
        if (request('farmer')) {
            $query->whereHas('orderItems', function($itemQuery) {
                $itemQuery->where('farmer_id', request('farmer'));
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get status counts
        $statusCounts = [
            'total' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'confirmed' => Order::where('status', 'confirmed')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
        ];

        // Get unique buyers and farmers for filters
        $buyers = \App\Models\User::where('role', 'buyer')
            ->orderBy('name')
            ->get();
            
        $farmers = \App\Models\User::where('role', 'farmer')
            ->orderBy('name')
            ->get();

        return view('admin.orders.index', compact('orders', 'buyers', 'farmers', 'statusCounts'));
    }

    /**
     * Show order details
     */
    public function showOrder(Order $order)
    {
        $order->load('buyer', 'orderItems.crop', 'orderItems.farmer');

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update order status
     */
    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,delivered,cancelled',
        ]);

        $order->status = $request->status;
        $order->save();

        return back()->with('success', 'Order status updated successfully!');
    }

    /**
     * Display platform statistics
     */
    public function statistics()
    {
        // User statistics
        $userStats = [
            'total' => User::count(),
            'farmers' => User::where('role', 'farmer')->count(),
            'buyers' => User::where('role', 'buyer')->count(),
            'admins' => User::where('role', 'admin')->count(),
        ];

        // Crop statistics
        $cropStats = [
            'total' => Crop::count(),
            'available' => Crop::where('is_available', true)->count(),
            'by_category' => Crop::selectRaw('category as name, COUNT(*) as count')
                ->groupBy('category')
                ->orderBy('count', 'desc')
                ->get(),
            'by_region' => Crop::selectRaw('region, COUNT(*) as count')
                ->groupBy('region')
                ->orderBy('count', 'desc')
                ->get(),
        ];

        // Order statistics
        $orderStats = [
            'total' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'confirmed' => Order::where('status', 'confirmed')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
            'total_revenue' => Order::where('status', 'delivered')->sum('total_amount'),
        ];

        // Monthly orders (last 6 months)
        $monthlyOrders = Order::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count, SUM(total_amount) as revenue')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Top farmers by revenue
        $topFarmers = User::where('role', 'farmer')
            ->withCount(['farmerOrderItems' => function($query) {
                $query->whereHas('order', function($orderQuery) {
                    $orderQuery->where('status', 'delivered');
                });
            }])
            ->withSum(['farmerOrderItems' => function($query) {
                $query->whereHas('order', function($orderQuery) {
                    $orderQuery->where('status', 'delivered');
                });
            }], 'total_price')
            ->orderBy('farmer_order_items_sum_total_price', 'desc')
            ->limit(10)
            ->get();

        return view('admin.statistics', [
            'stats' => [
                'total_users' => $userStats['total'],
                'total_farmers' => $userStats['farmers'],
                'total_buyers' => $userStats['buyers'],
                'total_admins' => $userStats['admins'],
                'new_users_this_month' => User::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
                'new_farmers_this_month' => User::where('role', 'farmer')->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
                'total_crops' => $cropStats['total'],
                'available_crops' => $cropStats['available'],
                'new_crops_this_month' => Crop::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
                'total_orders' => $orderStats['total'],
                'pending_orders' => $orderStats['pending'],
                'confirmed_orders' => $orderStats['confirmed'],
                'delivered_orders' => $orderStats['delivered'],
                'cancelled_orders' => $orderStats['cancelled'],
                'new_orders_this_month' => Order::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
                'total_revenue' => $orderStats['total_revenue'],
                'revenue_this_month' => Order::where('status', 'delivered')->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total_amount'),
                'new_farmers_this_month' => User::where('role', 'farmer')->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
                'new_buyers_this_month' => User::where('role', 'buyer')->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
                'active_users' => User::where('last_login_at', '>=', now()->subDays(30))->count(),
                'average_order_value' => Order::where('status', 'delivered')->avg('total_amount'),
                'conversion_rate' => Order::where('status', 'delivered')->count() > 0 ? round((Order::where('status', 'delivered')->count() / Order::count()) * 100, 2) : 0,
                'monthly_revenue' => [
                    'jan' => Order::where('status', 'delivered')->whereMonth('created_at', 1)->whereYear('created_at', now()->year)->sum('total_amount'),
                    'feb' => Order::where('status', 'delivered')->whereMonth('created_at', 2)->whereYear('created_at', now()->year)->sum('total_amount'),
                    'mar' => Order::where('status', 'delivered')->whereMonth('created_at', 3)->whereYear('created_at', now()->year)->sum('total_amount'),
                    'apr' => Order::where('status', 'delivered')->whereMonth('created_at', 4)->whereYear('created_at', now()->year)->sum('total_amount'),
                    'may' => Order::where('status', 'delivered')->whereMonth('created_at', 5)->whereYear('created_at', now()->year)->sum('total_amount'),
                    'jun' => Order::where('status', 'delivered')->whereMonth('created_at', 6)->whereYear('created_at', now()->year)->sum('total_amount'),
                ],
                'platform_growth' => [
                    'users_growth' => $userStats['total'] > 0 ? round((User::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count() / $userStats['total']) * 100, 2) : 0,
                    'orders_growth' => $orderStats['total'] > 0 ? round((Order::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count() / $orderStats['total']) * 100, 2) : 0,
                    'revenue_growth' => $orderStats['total_revenue'] > 0 ? round((Order::where('status', 'delivered')->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total_amount') / $orderStats['total_revenue']) * 100, 2) : 0,
                ],
                'top_farmers' => $topFarmers,
                'popular_categories' => $cropStats['by_category']->take(5),
            ],
            'userStats' => $userStats,
            'cropStats' => $cropStats,
            'orderStats' => $orderStats,
            'monthlyOrders' => $monthlyOrders,
        ]);
    }

    /**
     * Display system logs or activities
     */
    public function activities()
    {
        $query = Activity::with(['causer', 'subject'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if (request('type')) {
            if (request('type') === 'user') {
                $query->where('type', 'like', 'user_%');
            } elseif (request('type') === 'crop') {
                $query->where('type', 'like', 'crop_%');
            } elseif (request('type') === 'order') {
                $query->where('type', 'like', 'order_%');
            } elseif (request('type') === 'system') {
                $query->whereIn('type', ['login', 'logout', 'admin_action']);
            }
        }

        // Apply date filters
        if (request('date_from')) {
            $query->whereDate('created_at', '>=', request('date_from'));
        }

        if (request('date_to')) {
            $query->whereDate('created_at', '<=', request('date_to'));
        }

        $activities = $query->paginate(20);

        // Get activity statistics
        $activityStats = [
            'total_activities' => Activity::count(),
            'today_activities' => Activity::whereDate('created_at', now()->toDateString())->count(),
            'login_activities' => Activity::where('type', 'login')->count(),
            'crop_activities' => Activity::where('type', 'like', 'crop_%')->count(),
            'order_activities' => Activity::where('type', 'like', 'order_%')->count(),
            'user_activities' => Activity::where('type', 'like', 'user_%')->count(),
            'admin_activities' => Activity::where('type', 'admin_action')->count(),
        ];

        return view('admin.activities', compact('activities', 'activityStats'));
    }
}
