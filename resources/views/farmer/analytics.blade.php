@extends('layouts.marketplace')

@section('title', 'Farmer Analytics')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-chart-line me-2 text-success"></i>Farmer Analytics
            </h1>
            <p class="text-muted mb-0">Comprehensive performance and sales analytics</p>
        </div>
        <div>
            <a href="{{ route('farmer.dashboard') }}" class="btn btn-outline-primary">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>
        </div>
    </div>

    <!-- Statistics Overview -->
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

    <!-- Performance Overview -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-chart-line me-2 text-primary"></i>Performance Overview
                    </h5>
                    <div class="row g-3">
                        <div class="col-6">
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
                        <div class="col-6">
                            <div class="bg-light p-3 rounded">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>Total Quantity:</span>
                                    <strong class="text-primary">{{ $stats['total_quantity_sold'] }} kg</strong>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>Avg Order Value:</span>
                                    <strong class="text-success">TZS{{ number_format($stats['average_order_value'], 2) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Avg Price/kg:</span>
                                    <strong class="text-info">TZS{{ number_format($stats['average_price_per_kg'], 2) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-trophy me-2 text-warning"></i>Top Performers
                    </h5>
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="bg-light p-3 rounded">
                                <div class="text-muted mb-2">Most Popular Crop:</div>
                                <strong class="text-primary">{{ $stats['most_popular_crop'] ?? 'N/A' }}</strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light p-3 rounded">
                                <div class="text-muted mb-2">Top Buyer:</div>
                                <strong class="text-success">{{ $stats['top_buyer'] ?? 'N/A' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Analytics -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-wallet me-2 text-success"></i>Revenue Breakdown
                    </h5>
                    <div class="bg-light p-3 rounded">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Pending Revenue:</span>
                            <strong class="text-warning">TZS{{ number_format($stats['pending_revenue'], 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Confirmed Revenue:</span>
                            <strong class="text-info">TZS{{ number_format($stats['confirmed_revenue'], 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Total Revenue:</span>
                            <strong class="text-success">TZS{{ number_format($stats['total_revenue'], 2) }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-chart-pie me-2 text-info"></i>Crop Analytics
                    </h5>
                    <div class="bg-light p-3 rounded">
                        @if($stats['crops_by_category']->count() > 0)
                            @foreach($stats['crops_by_category'] as $category => $count)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>{{ ucfirst($category) }}:</span>
                                    <strong class="text-primary">{{ $count }}</strong>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted text-center">No crop data available</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-map-marker-alt me-2 text-warning"></i>Regional Distribution
                    </h5>
                    <div class="bg-light p-3 rounded">
                        @if($stats['crops_by_region']->count() > 0)
                            @foreach($stats['crops_by_region'] as $region => $count)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>{{ $region }}:</span>
                                    <strong class="text-success">{{ $count }}</strong>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted text-center">No regional data available</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Performance Chart -->
    <div class="row g-4 mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-chart-bar me-2 text-info"></i>Monthly Performance (Last 6 Months)
                    </h5>
                    <div class="row g-3">
                        @forelse($stats['monthly_performance'] as $month => $performance)
                            <div class="col-md-2">
                                <div class="bg-light p-3 rounded text-center">
                                    <div class="text-muted mb-2">{{ \Carbon\Carbon::parse($month . '-01')->format('M') }}</div>
                                    <div class="mb-2">
                                        <strong class="text-primary">{{ $performance['orders'] }}</strong>
                                        <small class="d-block text-muted">orders</small>
                                    </div>
                                    <div>
                                        <strong class="text-success">TZS{{ number_format($performance['revenue'], 0) }}</strong>
                                        <small class="d-block text-muted">revenue</small>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center text-muted">
                                <p>No performance data available yet</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
