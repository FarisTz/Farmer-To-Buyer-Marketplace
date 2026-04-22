@extends('layouts.marketplace')

@section('title', 'Platform Statistics')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-chart-line me-2 text-success"></i>Platform Statistics
            </h1>
            <p class="text-muted mb-0">Comprehensive analytics and insights</p>
        </div>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>
        </div>
    </div>

    <!-- Overview Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stats-card text-center">
                <div class="text-primary mb-2">
                    <i class="fas fa-users fa-2x"></i>
                </div>
                <h4 class="fw-bold">{{ $stats['total_users'] }}</h4>
                <p class="text-muted mb-0">Total Users</p>
                <small class="text-success">
                    <i class="fas fa-arrow-up me-1"></i>+{{ $stats['new_users_this_month'] }} this month
                </small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card text-center">
                <div class="text-success mb-2">
                    <i class="fas fa-seedling fa-2x"></i>
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

    <!-- Charts Section -->
    <div class="row g-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-chart-bar me-2"></i>Revenue Overview
                    </h5>
                    
                    <!-- Revenue Chart -->
                    <div class="mb-4">
                        <h6>Monthly Revenue</h6>
                        <div class="bg-light p-3 rounded">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>January:</span>
                                <strong class="text-success">TZS{{ number_format($stats['monthly_revenue']['jan'] ?? 0, 2) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>February:</span>
                                <strong class="text-success">TZS{{ number_format($stats['monthly_revenue']['feb'] ?? 0, 2) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>March:</span>
                                <strong class="text-success">TZS{{ number_format($stats['monthly_revenue']['mar'] ?? 0, 2) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>April:</span>
                                <strong class="text-success">TZS{{ number_format($stats['monthly_revenue']['apr'] ?? 0, 2) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>May:</span>
                                <strong class="text-success">TZS{{ number_format($stats['monthly_revenue']['may'] ?? 0, 2) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>June:</span>
                                <strong class="text-success">TZS{{ number_format($stats['monthly_revenue']['jun'] ?? 0, 2) }}</strong>
                            </div>
                        </div>
                        <small class="text-muted">Showing last 6 months of revenue data</small>
                    </div>
                    
                    <!-- Order Trends -->
                    <div>
                        <h6>Order Trends</h6>
                        <div class="bg-light p-3 rounded">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Pending Orders:</span>
                                <strong class="text-warning">{{ $stats['pending_orders'] }}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Confirmed Orders:</span>
                                <strong class="text-info">{{ $stats['confirmed_orders'] }}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Delivered Orders:</span>
                                <strong class="text-success">{{ $stats['delivered_orders'] }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Top Performers -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-trophy me-2"></i>Top Performers
                    </h5>
                    
                    <div class="mb-3">
                        <h6>Top Farmers</h6>
                        <div class="list-group list-group-flush">
                            @foreach($stats['top_farmers'] as $farmer)
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $farmer->name }}</h6>
                                            <small class="text-muted">{{ $farmer->crops_count }} crops</small>
                                        </div>
                                        <div class="text-end">
                                            <strong class="text-success">TZS{{ number_format($farmer->revenue, 2) }}</strong>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Popular Categories -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-tags me-2"></i>Popular Categories
                    </h5>
                    
                    <div>
                        <div class="list-group list-group-flush">
                            @foreach($stats['popular_categories'] as $category)
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>{{ $category['name'] }}</span>
                                        <strong>{{ $category['count'] }} items</strong>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Summary -->
    <div class="row g-4 mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-chart-pie me-2"></i>Activity Summary
                    </h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6>User Activity</h6>
                            <div class="bg-light p-3 rounded">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>New Farmers:</span>
                                    <strong class="text-success">{{ $stats['new_farmers_this_month'] }}</strong>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>New Buyers:</span>
                                    <strong class="text-success">{{ $stats['new_buyers_this_month'] }}</strong>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Active Users:</span>
                                    <strong class="text-info">{{ $stats['active_users'] }}</strong>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h6>Platform Health</h6>
                            <div class="bg-light p-3 rounded">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>Average Order Value:</span>
                                    <strong class="text-success">TZS{{ number_format($stats['average_order_value'], 2) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>Conversion Rate:</span>
                                    <strong class="text-info">{{ $stats['conversion_rate'] }}%</strong>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Platform Growth:</span>
                                    <strong class="text-success">{{ $stats['platform_growth']['revenue_growth'] }}%</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
