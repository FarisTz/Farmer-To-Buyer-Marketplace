@extends('layouts.marketplace')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-cog me-2 text-success"></i>Admin Dashboard
            </h1>
            <p class="text-muted mb-0">Platform management and analytics</p>
        </div>
        <div>
            <span class="badge bg-danger fs-6">
                <i class="fas fa-shield-alt me-1"></i>Administrator
            </span>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stats-card text-center">
                <div class="text-primary mb-2">
                    <i class="fas fa-users fa-2x"></i>
                </div>
                <h4 class="fw-bold">{{ $stats['total_users'] }}</h4>
                <p class="text-muted mb-0">Total Users</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card text-center">
                <div class="text-success mb-2">
                    <i class="fas fa-seedling fa-2x"></i>
                </div>
                <h4 class="fw-bold">{{ $stats['total_crops'] }}</h4>
                <p class="text-muted mb-0">Total Crops</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card text-center">
                <div class="text-warning mb-2">
                    <i class="fas fa-shopping-bag fa-2x"></i>
                </div>
                <h4 class="fw-bold">{{ $stats['total_orders'] }}</h4>
                <p class="text-muted mb-0">Total Orders</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card text-center">
                <div class="text-info mb-2">
                    <i class="fas fa-wallet fa-2x"></i>
                </div>
                <h4 class="fw-bold">TZS{{ number_format($stats['total_revenue'], 2) }}</h4>
                <p class="text-muted mb-0">Total Revenue</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-users me-2 text-primary"></i>Manage Users
                    </h5>
                    <p class="card-text">View and manage platform users.</p>
                    <a href="{{ route('admin.users') }}" class="btn btn-outline-primary">
                        <i class="fas fa-users me-2"></i>Manage Users
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-carrot me-2 text-success"></i>Manage Crops
                    </h5>
                    <p class="card-text">Review and manage crop listings.</p>
                    <a href="{{ route('admin.crops') }}" class="btn btn-outline-success">
                        <i class="fas fa-carrot me-2"></i>Manage Crops
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-shopping-bag me-2 text-warning"></i>Manage Orders
                    </h5>
                    <p class="card-text">Track and manage all orders.</p>
                    <a href="{{ route('admin.orders') }}" class="btn btn-outline-warning">
                        <i class="fas fa-shopping-bag me-2"></i>Manage Orders
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-shield-alt me-2 text-warning"></i>Manage Verifications
                    </h5>
                    <p class="card-text">Review user verification requests and manage account security.</p>
                    <a href="{{ route('admin.verifications.index') }}" class="btn btn-outline-warning">
                        <i class="fas fa-shield-alt me-2"></i>Manage Verifications
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
