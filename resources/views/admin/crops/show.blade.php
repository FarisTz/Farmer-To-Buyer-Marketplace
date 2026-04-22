@extends('layouts.marketplace')

@section('title', 'Crop Details - ' . $crop->name)

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}" class="text-decoration-none">Home</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.crops') }}" class="text-decoration-none">Manage Crops</a>
            </li>
            <li class="breadcrumb-item active">{{ $crop->name }}</li>
        </ol>
    </nav>

    <!-- Crop Header -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center">
                        <img src="{{ $crop->image_url }}" alt="{{ $crop->name }}" 
                             class="rounded me-4" style="width: 100px; height: 100px; object-fit: cover;">
                        <div>
                            <h2 class="h3 mb-2">{{ $crop->name }}</h2>
                            <p class="text-muted mb-2">{{ $crop->description }}</p>
                            <div class="d-flex gap-2">
                                <span class="badge bg-primary">
                                    <i class="fas fa-tag me-1"></i>{{ $crop->category }}
                                </span>
                                <span class="badge bg-info">
                                    <i class="fas fa-map-marker-alt me-1"></i>{{ $crop->region }}
                                </span>
                                @if($crop->is_available)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check me-1"></i>Available
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times me-1"></i>Unavailable
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="mb-3">
                        <h3 class="text-success mb-0">{{ $crop->formatted_price }}/kg</h3>
                        <p class="text-muted mb-0">{{ $crop->available_quantity }} kg available</p>
                    </div>
                    <div class="d-grid gap-2">
                        <form action="{{ route('admin.crops.toggle-availability', $crop) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-toggle-on me-2"></i>Toggle Availability
                            </button>
                        </form>
                        <a href="{{ route('admin.crops') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Crops
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Crop Information -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-info-circle me-2"></i>Crop Information
                    </h5>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Name:</span>
                            <strong>{{ $crop->name }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Category:</span>
                            <strong>{{ $crop->category }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Price:</span>
                            <strong class="text-success">{{ $crop->formatted_price }}/kg</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Available Quantity:</span>
                            <strong>{{ $crop->available_quantity }} {{ $crop->unit }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Unit:</span>
                            <strong>{{ $crop->unit }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Listed:</span>
                            <strong>{{ $crop->created_at->format('M d, Y') }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Farmer Information -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-user me-2"></i>Farmer Information
                    </h5>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Name:</span>
                            <strong>{{ $crop->farmer->name }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Email:</span>
                            <strong>{{ $crop->farmer->email }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Phone:</span>
                            <strong>{{ $crop->farmer->phone ?: 'Not provided' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Region:</span>
                            <strong>{{ $crop->farmer->region }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Farm Location:</span>
                            <strong>{{ $crop->farm_location ?: 'Not provided' }}</strong>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.users.show', $crop->farmer) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-user me-2"></i>View Farmer Profile
                        </a>
                        <a href="{{ route('admin.crops') }}?farmer={{ $crop->farmer->id }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-carrot me-2"></i>View All Crops
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance & Orders -->
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-chart-line me-2"></i>Performance
                    </h5>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Total Orders:</span>
                            <strong>{{ $crop->orderItems->count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Quantity Sold:</span>
                            <strong>{{ $crop->orderItems->sum('quantity') }} kg</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Total Revenue:</span>
                            <strong class="text-success">₦{{ number_format($crop->orderItems->sum('total_price'), 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Average Order:</span>
                            <strong>{{ $crop->orderItems->count() > 0 ? number_format($crop->orderItems->avg('quantity'), 1) : 0 }} kg</strong>
                        </div>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div class="mb-2">
                        <small class="text-muted">Sales Progress</small>
                        <div class="progress" style="height: 8px;">
                            @if($crop->available_quantity > 0)
                                <?php $soldPercentage = ($crop->orderItems->sum('quantity') / ($crop->orderItems->sum('quantity') + $crop->available_quantity)) * 100; ?>
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: {{ min($soldPercentage, 100) }}%">
                                </div>
                            @endif
                        </div>
                        <small class="text-muted">
                            {{ $crop->orderItems->sum('quantity') }} kg sold of {{ $crop->orderItems->sum('quantity') + $crop->available_quantity }} kg total
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-shopping-bag me-2"></i>Recent Orders
                    </h5>
                    
                    @if($crop->orderItems->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($crop->orderItems->take(5) as $orderItem)
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">Order #{{ $orderItem->order->order_number }}</h6>
                                            <small class="text-muted">
                                                {{ $orderItem->order->buyer_name }} • 
                                                {{ $orderItem->created_at->format('M d, Y') }}
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <div>{!! $orderItem->order->status_badge !!}</div>
                                            <small class="text-success fw-bold">{{ $orderItem->formatted_total_price }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($crop->orderItems->count() > 5)
                            <div class="text-center mt-2">
                                <small class="text-muted">Showing 5 of {{ $crop->orderItems->count() }} orders</small>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-shopping-bag fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">No orders for this crop yet</p>
                        </div>
                    @endif
                    
                    <div class="mt-3">
                        <a href="{{ route('admin.orders') }}?crop={{ $crop->id }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-receipt me-2"></i>View All Orders
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
