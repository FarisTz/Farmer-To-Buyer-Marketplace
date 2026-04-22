@extends('layouts.marketplace')

@section('title', 'My Orders')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-receipt me-2 text-success"></i>My Orders
            </h1>
            <p class="text-muted mb-0">Track and manage your orders</p>
        </div>
        <div>
            <a href="{{ route('buyer.crops.browse') }}" class="btn btn-success">
                <i class="fas fa-carrot me-2"></i>Browse More Crops
            </a>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="card mb-4">
        <div class="card-body">
            <ul class="nav nav-pills" role="tablist">
                <li class="nav-item">
                    <a class="nav-link {{ request('status') == null ? 'active' : '' }}" 
                       href="{{ route('buyer.orders') }}">
                        All Orders ({{ $statusCounts['total'] }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('status') == 'pending' ? 'active' : '' }}" 
                       href="{{ route('buyer.orders', ['status' => 'pending']) }}">
                        <i class="fas fa-clock me-1"></i>Pending ({{ $statusCounts['pending'] }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('status') == 'confirmed' ? 'active' : '' }}" 
                       href="{{ route('buyer.orders', ['status' => 'confirmed']) }}">
                        <i class="fas fa-check me-1"></i>Confirmed ({{ $statusCounts['confirmed'] }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('status') == 'delivered' ? 'active' : '' }}" 
                       href="{{ route('buyer.orders', ['status' => 'delivered']) }}">
                        <i class="fas fa-truck me-1"></i>Delivered ({{ $statusCounts['delivered'] }})
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Orders List -->
    @if($orders->count() > 0)
        <div class="row g-4">
            @foreach($orders as $order)
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h5 class="mb-1">Order #{{ $order->order_number }}</h5>
                                            <p class="text-muted mb-0">
                                                <i class="fas fa-calendar me-1"></i>{{ $order->created_at->format('M d, Y h:i A') }}
                                                <span class="ms-3">
                                                    <i class="fas fa-shopping-bag me-1"></i>{{ $order->orderItems->count() }} items
                                                </span>
                                            </p>
                                        </div>
                                        <div>{!! $order->status_badge !!}</div>
                                    </div>
                                    
                                    <!-- Order Items Preview -->
                                    <div class="mb-3">
                                        @foreach($order->orderItems->take(3) as $item)
                                            <div class="d-flex align-items-center mb-2">
                                                <img src="{{ $item->crop->image_url }}" alt="{{ $item->crop->name }}" 
                                                     class="rounded me-2" style="width: 30px; height: 30px; object-fit: cover;">
                                                <div class="flex-grow-1">
                                                    <small class="fw-bold">{{ $item->crop->name }}</small>
                                                    <br>
                                                    <small class="text-muted">{{ $item->quantity }} kg × {{ $item->crop->formatted_price }}</small>
                                                </div>
                                                <small class="fw-bold">{{ $item->formatted_total_price }}</small>
                                            </div>
                                        @endforeach
                                        @if($order->orderItems->count() > 3)
                                            <small class="text-muted">+{{ $order->orderItems->count() - 3 }} more items</small>
                                        @endif
                                    </div>
                                    
                                    <!-- Delivery Info -->
                                    <div class="text-muted small">
                                        <i class="fas fa-map-marker-alt me-1"></i>{{ $order->delivery_address }}
                                        <span class="ms-3">
                                            <i class="fas fa-phone me-1"></i>{{ $order->phone }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="col-md-4 text-end">
                                    <div class="mb-3">
                                        <h4 class="text-success mb-0">{{ $order->formatted_total }}</h4>
                                        <small class="text-muted">Total Amount</small>
                                    </div>
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('buyer.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i>View Details
                                        </a>
                                        <a href="{{ route('buyer.chats.start.order', $order) }}" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-comments me-1"></i>Chat
                                        </a>
                                        @if($order->status === 'delivered')
                                            <button class="btn btn-sm btn-outline-success" disabled>
                                                <i class="fas fa-check me-1"></i>Completed
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $orders->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-receipt fa-4x text-muted mb-3"></i>
            <h4 class="text-muted mb-3">No orders found</h4>
            <p class="text-muted mb-4">Start browsing crops to place your first order!</p>
            <a href="{{ route('buyer.crops.browse') }}" class="btn btn-success btn-lg">
                <i class="fas fa-carrot me-2"></i>Browse Crops
            </a>
        </div>
    @endif
</div>
@endsection
