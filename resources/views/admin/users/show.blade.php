@extends('layouts.marketplace')

@section('title', 'User Details - ' . $user->name)

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}" class="text-decoration-none">Home</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.users') }}" class="text-decoration-none">Manage Users</a>
            </li>
            <li class="breadcrumb-item active">{{ $user->name }}</li>
        </ol>
    </nav>

    <!-- User Header -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center me-4" style="width: 80px; height: 80px;">
                            <i class="fas fa-user fa-2x text-primary"></i>
                        </div>
                        <div>
                            <h2 class="h3 mb-2">{{ $user->name }}</h2>
                            <p class="text-muted mb-1">{{ $user->email }}</p>
                            <div class="d-flex gap-2">
                                <span class="badge bg-{{ $user->role === 'farmer' ? 'success' : ($user->role === 'buyer' ? 'info' : 'danger') }}">
                                    <i class="fas fa-{{ $user->role === 'farmer' ? 'seedling' : ($user->role === 'buyer' ? 'shopping-cart' : 'shield-alt') }} me-1"></i>
                                    {{ $user->role }}
                                </span>
                                <span class="badge bg-success">
                                    <i class="fas fa-check me-1"></i>Active
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="d-grid gap-2">
                        <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-pause me-2"></i>Toggle Status
                            </button>
                        </form>
                        <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Users
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- User Information -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-user me-2"></i>User Information
                    </h5>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Full Name:</span>
                            <strong>{{ $user->name }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Email:</span>
                            <strong>{{ $user->email }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Role:</span>
                            <strong class="text-{{ $user->role === 'farmer' ? 'success' : ($user->role === 'buyer' ? 'info' : 'danger') }}">
                                {{ $user->role }}
                            </strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Status:</span>
                            <strong class="text-success">Active</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Joined:</span>
                            <strong>{{ $user->created_at->format('M d, Y') }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-address-card me-2"></i>Contact Information
                    </h5>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Phone:</span>
                            <strong>{{ $user->phone ?: 'Not provided' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Address:</span>
                            <strong>{{ $user->address ?: 'Not provided' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Region:</span>
                            <strong>{{ $user->region ?: 'Not provided' }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Activity -->
    @if($user->role === 'farmer')
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-seedling me-2"></i>Farm Activity
                        </h5>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Total Crops:</span>
                                <strong>{{ $user->crops->count() }}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Available Crops:</span>
                                <strong>{{ $user->crops->where('is_available', true)->count() }}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Total Orders:</span>
                                <strong>{{ $user->role === 'farmer' ? $user->farmerOrderItems->count() : ($user->buyerOrders ? $user->buyerOrders->sum(function($order) { return $order->orderItems->count(); }) : 0) }}</strong>
                            </div>
                        </div>
                        
                        <a href="{{ route('admin.crops') }}?farmer={{ $user->id }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-eye me-2"></i>View Crops
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-chart-line me-2"></i>Performance
                        </h5>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Total Revenue:</span>
                                <strong class="text-success">TZS{{ number_format($user->farmerOrderItems->sum('total_price'), 2) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Quantity Sold:</span>
                                <strong>{{ $user->farmerOrderItems->sum('quantity') }} kg</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Average Order:</span>
                                <strong>{{ $user->farmerOrderItems->count() > 0 ? number_format($user->farmerOrderItems->avg('total_price'), 2) : 0 }}</strong>
                            </div>
                        </div>
                        
                        <a href="{{ route('admin.orders') }}?farmer={{ $user->id }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-shopping-bag me-2"></i>View Orders
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @elseif($user->role === 'buyer')
        <div class="row g-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-shopping-cart me-2"></i>Buyer Activity
                        </h5>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Total Orders:</span>
                                <strong>{{ $user->buyerOrders()->count() }}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Total Spent:</span>
                                <strong class="text-success">TZS{{ number_format($user->buyerOrders()->sum('total_amount'), 2) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Average Order:</span>
                                <strong>{{ $user->buyerOrders()->count() > 0 ? number_format($user->buyerOrders()->avg('total_amount'), 2) : 0 }}</strong>
                            </div>
                        </div>
                        
                        <a href="{{ route('admin.orders') }}?buyer={{ $user->id }}" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-receipt me-2"></i>View Orders
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
