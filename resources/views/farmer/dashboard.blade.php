@extends('layouts.marketplace')

@section('title', 'Farmer Dashboard')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-seedling me-2 text-success"></i>Farmer Dashboard
            </h1>
            <p class="text-muted mb-0">Welcome back, {{ $farmer->name }}!</p>
        </div>
        <div>
            <a href="{{ route('verification.index') }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-shield-alt me-2"></i>Verify Account
            </a>
            <a href="{{ route('farmer.crops.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>Add New Crop
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stats-card text-center">
                <div class="text-primary mb-2">
                    <i class="fas fa-carrot fa-2x"></i>
                </div>
                <h4 class="fw-bold">{{ $stats['total_crops'] }}</h4>
                <p class="text-muted mb-0">Total Crops</p>
                <small class="text-success">
                    <i class="fas fa-arrow-up me-1"></i>+{{ $stats['new_crops_this_month'] }} this month
                </small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card text-center">
                <div class="text-success mb-2">
                    <i class="fas fa-check-circle fa-2x"></i>
                </div>
                <h4 class="fw-bold">{{ $stats['available_crops'] }}</h4>
                <p class="text-muted mb-0">Available Crops</p>
                <small class="text-muted">{{ $stats['sold_crops'] }} sold</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card text-center">
                <div class="text-warning mb-2">
                    <i class="fas fa-shopping-bag fa-2x"></i>
                </div>
                <h4 class="fw-bold">{{ $stats['total_orders'] }}</h4>
                <p class="text-muted mb-0">Total Orders</p>
                <small class="text-success">
                    <i class="fas fa-arrow-up me-1"></i>+{{ $stats['new_orders_this_month'] }} this month
                </small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card text-center">
                <div class="text-info mb-2">
                    <i class="fas fa-wallet fa-2x"></i>
                </div>
                <h4 class="fw-bold">TZS{{ number_format($stats['total_revenue'], 2) }}</h4>
                <p class="text-muted mb-0">Total Revenue</p>
                <small class="text-success">
                    <i class="fas fa-arrow-up me-1"></i>+TZS{{ number_format($stats['revenue_this_month'], 2) }} this month
                </small>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-carrot me-2 text-success"></i>Manage Crops
                    </h5>
                    <p class="card-text">Add, edit, or manage your crop listings.</p>
                    <a href="{{ route('farmer.crops.index') }}" class="btn btn-outline-success">
                        <i class="fas fa-list me-2"></i>Manage Crops
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-shopping-bag me-2 text-warning"></i>View Orders
                    </h5>
                    <p class="card-text">Track and manage customer orders.</p>
                    <a href="{{ route('farmer.orders') }}" class="btn btn-outline-warning">
                        <i class="fas fa-receipt me-2"></i>View Orders
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-university me-2 text-primary"></i>Bank Details
                    </h5>
                    <p class="card-text">Manage your bank information for buyer payments.</p>
                    <a href="{{ route('farmer.bank-details') }}" class="btn btn-outline-primary">
                        <i class="fas fa-university me-2"></i>Manage Bank Details
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-receipt me-2 text-info"></i>Payment Receipts
                    </h5>
                    <p class="card-text">Review and verify buyer payment receipts.</p>
                    <a href="{{ route('farmer.payment-receipts') }}" class="btn btn-outline-info">
                        <i class="fas fa-receipt me-2"></i>View Receipts
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-shopping-bag me-2"></i>Recent Orders
                    </h5>
                    <a href="{{ route('farmer.orders') }}" class="btn btn-sm btn-outline-primary">
                        View All
                    </a>
                </div>
                <div class="card-body">
                    @if($recentOrders->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentOrders as $orderItem)
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $orderItem->crop->name }}</h6>
                                            <small class="text-muted">
                                                {{ $orderItem->created_at->format('M d, Y') }} by {{ $orderItem->order->buyer->name }} 
                                                <span class="badge bg-secondary ms-1">{{ $orderItem->quantity }} kg</span>
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <div class="badge bg-{{ $orderItem->order->status === 'delivered' ? 'success' : ($orderItem->order->status === 'confirmed' ? 'info' : ($orderItem->order->status === 'pending' ? 'warning' : 'danger')) }}">
                                                {{ ucfirst($orderItem->order->status) }}
                                            </div>
                                            <small class="text-success fw-bold d-block mt-1">{{ $orderItem->formatted_total_price }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-shopping-bag fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">No orders yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Crops -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-carrot me-2"></i>My Crops
                    </h5>
                    <a href="{{ route('farmer.crops.index') }}" class="btn btn-sm btn-outline-primary">
                        Manage All
                    </a>
                </div>
                <div class="card-body">
                    @if($crops->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($crops->take(5) as $crop)
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $crop->image_url }}" alt="{{ $crop->name }}" 
                                                 class="rounded me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                            <div>
                                                <h6 class="mb-1">{{ $crop->name }}</h6>
                                                <small class="text-muted">
                                                    {{ $crop->category }} • {{ $crop->available_quantity }} kg available
                                                </small>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div class="text-success fw-bold">{{ $crop->formatted_price }}/kg</div>
                                            @if($crop->is_available)
                                                <span class="badge bg-success">Available</span>
                                            @else
                                                <span class="badge bg-danger">Not Available</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-carrot fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-2">No crops listed yet</p>
                            <a href="{{ route('farmer.crops.create') }}" class="btn btn-sm btn-success">
                                <i class="fas fa-plus me-1"></i>Add Your First Crop
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
