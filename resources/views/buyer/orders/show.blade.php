@extends('layouts.marketplace')

@section('title', 'Order Details - ' . $order->order_number)

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}" class="text-decoration-none">Home</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('buyer.orders') }}" class="text-decoration-none">My Orders</a>
            </li>
            <li class="breadcrumb-item active">{{ $order->order_number }}</li>
        </ol>
    </nav>

    <!-- Order Header -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center mb-3">
                        <h1 class="h3 mb-0 me-3">Order #{{ $order->order_number }}</h1>
                        {!! $order->status_badge !!}
                    </div>
                    <div class="text-muted">
                        <p class="mb-1">
                            <i class="fas fa-calendar me-2"></i>Placed on {{ $order->created_at->format('M d, Y h:i A') }}
                        </p>
                        <p class="mb-0">
                            <i class="fas fa-shopping-bag me-2"></i>{{ $order->orderItems->count() }} items • 
                            <span class="text-success fw-bold">{{ $order->formatted_total }}</span>
                        </p>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    @if($order->status === 'pending')
                        <button class="btn btn-warning" disabled>
                            <i class="fas fa-clock me-2"></i>Waiting for Confirmation
                        </button>
                    @elseif($order->status === 'confirmed')
                        <button class="btn btn-info" disabled>
                            <i class="fas fa-truck me-2"></i>Being Prepared
                        </button>
                    @elseif($order->status === 'delivered')
                        <button class="btn btn-success" disabled>
                            <i class="fas fa-check me-2"></i>Delivered
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Order Items -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-shopping-bag me-2"></i>Order Items
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Farm</th>
                                    <th>Quantity</th>
                                    <th>Price/kg</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderItems as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $item->crop->image_url }}" alt="{{ $item->crop->name }}" 
                                                     class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                                <div>
                                                    <h6 class="mb-0">{{ $item->crop->name }}</h6>
                                                    <small class="text-muted">{{ $item->crop->category }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $item->farmer->name }}</strong>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fas fa-map-marker-alt me-1"></i>{{ $item->crop->region }}
                                                </small>
                                            </div>
                                        </td>
                                        <td>{{ $item->quantity }} kg</td>
                                        <td>{{ $item->formatted_price_per_kg }}</td>
                                        <td class="fw-bold">{{ $item->formatted_total_price }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Order Notes -->
                    @if($order->notes)
                        <div class="mt-4">
                            <h6><i class="fas fa-sticky-note me-2"></i>Order Notes</h6>
                            <p class="text-muted">{{ $order->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Order Summary & Delivery Info -->
        <div class="col-md-4">
            <!-- Order Summary -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">Order Summary</h5>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span>TZS{{ number_format($order->total_amount, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Delivery Fee</span>
                            <span class="text-success">Free</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Service Fee</span>
                            <span class="text-success">Free</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax</span>
                            <span class="text-success">Included</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <h5>Total Paid</h5>
                            <h5 class="text-success">{{ $order->formatted_total }}</h5>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delivery Information -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-truck me-2"></i>Delivery Information
                    </h5>
                    
                    <div class="mb-3">
                        <h6 class="text-muted">Delivery Address</h6>
                        <p>{{ $order->delivery_address }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="text-muted">Contact Number</h6>
                        <p>{{ $order->phone }}</p>
                    </div>
                    
                    <!-- Delivery Status -->
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="fas fa-info-circle me-2"></i>Delivery Status
                        </h6>
                        @switch($order->status)
                            @case('pending')
                                <p class="mb-0">Your order is pending confirmation. Farmers will contact you soon to confirm availability and arrange delivery.</p>
                                @break
                            @case('confirmed')
                                <p class="mb-0">Your order has been confirmed! Farmers are preparing your items and will contact you to schedule delivery.</p>
                                @break
                            @case('delivered')
                                <p class="mb-0">Your order has been successfully delivered. Thank you for your purchase!</p>
                                @break
                        @endswitch
                    </div>
                </div>
            </div>

            <!-- Farmers Contact Info -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-users me-2"></i>Farmers Information
                    </h5>
                    
                    @foreach($order->orderItems->groupBy('farmer_id') as $farmerId => $items)
                        <div class="mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <h6 class="mb-1">{{ $items->first()->farmer->name }}</h6>
                            <p class="text-muted small mb-1">
                                <i class="fas fa-map-marker-alt me-1"></i>{{ $items->first()->farmer->region }}
                            </p>
                            @if($items->first()->farmer->phone)
                                <p class="text-muted small mb-0">
                                    <i class="fas fa-phone me-1"></i>{{ $items->first()->farmer->phone }}
                                </p>
                            @endif
                            <small class="text-muted">
                                {{ $items->count() }} items • 
                                ₦{{ number_format($items->sum('total_price'), 2) }}
                            </small>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-4">
        <div class="d-flex gap-2">
            <a href="{{ route('buyer.orders') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Back to Orders
            </a>
            <a href="{{ route('buyer.crops.browse') }}" class="btn btn-success">
                <i class="fas fa-carrot me-2"></i>Order More Crops
            </a>
            @if($order->status === 'delivered')
                <button class="btn btn-outline-success ms-auto" disabled>
                    <i class="fas fa-check me-2"></i>Order Completed
                </button>
            @endif
        </div>
    </div>
</div>
@endsection
