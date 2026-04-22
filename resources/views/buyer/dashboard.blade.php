@extends('layouts.marketplace')

@section('title', 'Buyer Dashboard')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-shopping-cart me-2 text-success"></i>Buyer Dashboard
            </h1>
            <p class="text-muted mb-0">Welcome back, {{ $buyer->name }}!</p>
        </div>
        <div>
            <a href="{{ route('verification.index') }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-shield-alt me-2"></i>Verify Account
            </a>
            <a href="{{ route('buyer.crops.browse') }}" class="btn btn-success">
                <i class="fas fa-carrot me-2"></i>Browse Crops
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stats-card text-center">
                <div class="text-success mb-2">
                    <i class="fas fa-shopping-bag fa-2x"></i>
                </div>
                <h4 class="fw-bold">{{ $stats['total_orders'] }}</h4>
                <p class="text-muted mb-0">Total Orders</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card text-center">
                <div class="text-warning mb-2">
                    <i class="fas fa-clock fa-2x"></i>
                </div>
                <h4 class="fw-bold">{{ $stats['pending_orders'] }}</h4>
                <p class="text-muted mb-0">Pending Orders</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card text-center">
                <div class="text-info mb-2">
                    <i class="fas fa-check-circle fa-2x"></i>
                </div>
                <h4 class="fw-bold">{{ $stats['delivered_orders'] }}</h4>
                <p class="text-muted mb-0">Delivered Orders</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card text-center">
                <div class="text-primary mb-2">
                    <i class="fas fa-wallet fa-2x"></i>
                </div>
                <h4 class="fw-bold">TZS{{ number_format($stats['total_spent'], 2) }}</h4>
                <p class="text-muted mb-0">Total Spent</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-shopping-cart me-2 text-success"></i>Shopping Cart
                    </h5>
                    <p class="card-text">View and manage your shopping cart items.</p>
                    <a href="{{ route('buyer.cart') }}" class="btn btn-outline-success">
                        <i class="fas fa-eye me-2"></i>View Cart
                        @if(session('cart'))
                            <span class="badge bg-success ms-1">{{ count(session('cart')) }}</span>
                        @endif
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-carrot me-2 text-warning"></i>Browse Crops
                    </h5>
                    <p class="card-text">Discover fresh produce from local farmers.</p>
                    <a href="{{ route('buyer.crops.browse') }}" class="btn btn-outline-warning">
                        <i class="fas fa-search me-2"></i>Browse Now
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-receipt me-2"></i>Recent Orders
            </h5>
            <a href="{{ route('buyer.orders') }}" class="btn btn-sm btn-outline-primary">
                View All Orders
            </a>
        </div>
        <div class="card-body">
            @if($orders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Date</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders->take(5) as $order)
                                <tr>
                                    <td>
                                        <strong>{{ $order->order_number }}</strong>
                                    </td>
                                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                                    <td>{{ $order->orderItems->count() }} items</td>
                                    <td>{{ $order->formatted_total }}</td>
                                    <td>{!! $order->status_badge !!}</td>
                                    <td>
                                        <a href="{{ route('buyer.orders.show', $order) }}" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No orders yet</h5>
                    <p class="text-muted">Start browsing crops to place your first order!</p>
                    <a href="{{ route('buyer.crops.browse') }}" class="btn btn-success">
                        <i class="fas fa-carrot me-2"></i>Browse Crops
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
