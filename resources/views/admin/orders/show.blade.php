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
                <a href="{{ route('admin.orders') }}" class="text-decoration-none">Manage Orders</a>
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
                        <p class="mb-1">
                            <i class="fas fa-user me-2"></i>Buyer: {{ $order->buyer_name }}
                        </p>
                        <p class="mb-0">
                            <i class="fas fa-shopping-bag me-2"></i>{{ $order->orderItems->count() }} items • 
                            <span class="text-success fw-bold">{{ $order->formatted_total }}</span>
                        </p>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="mb-2">
                        @if($order->status === 'pending')
                            <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="confirmed">
                                <button type="submit" class="btn btn-success btn-lg" 
                                        onclick="return confirm('Are you sure you want to confirm this order?')">
                                    <i class="fas fa-check me-2"></i>Confirm Order
                                </button>
                            </form>
                        @elseif($order->status === 'confirmed')
                            <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="delivered">
                                <button type="submit" class="btn btn-info btn-lg" 
                                        onclick="return confirm('Are you sure you want to mark this order as delivered?')">
                                    <i class="fas fa-truck me-2"></i>Mark as Delivered
                                </button>
                            </form>
                        @elseif($order->status === 'delivered')
                            <button class="btn btn-success btn-lg" disabled>
                                <i class="fas fa-check-circle me-2"></i>Order Completed
                            </button>
                        @endif
                    </div>
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.orders') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Orders
                        </a>
                        <a href="{{ route('admin.users.show', $order->buyer) }}" class="btn btn-outline-primary">
                            <i class="fas fa-user me-2"></i>View Buyer
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Order Items -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-shopping-bag me-2"></i>Order Items
                    </h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Farmer</th>
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
                                                <small class="text-muted">{{ $item->farmer->region }}</small>
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
                            <div class="alert alert-info">
                                {{ $order->notes }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Order Summary & Customer Info -->
        <div class="col-md-4">
            <!-- Order Summary -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">Order Summary</h5>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span>₦{{ number_format($order->total_amount, 2) }}</span>
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
                            <h5>Total Amount</h5>
                            <h5 class="text-success">{{ $order->formatted_total }}</h5>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-user me-2"></i>Customer Information
                    </h5>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Name:</span>
                            <strong>{{ $order->buyer_name }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Email:</span>
                            <strong>{{ $order->buyer_email }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Phone:</span>
                            <strong>{{ $order->phone }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Delivery Address:</span>
                            <strong>{{ $order->delivery_address }}</strong>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.users.show', $order->buyer) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-user me-2"></i>View Buyer Profile
                        </a>
                        <a href="{{ route('admin.orders') }}?buyer={{ $order->buyer_id }}" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-receipt me-2"></i>View Buyer Orders
                        </a>
                    </div>
                </div>
            </div>

            <!-- Farmers Information -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-users me-2"></i>Involved Farmers
                    </h5>
                    
                    @foreach($order->orderItems->groupBy('farmer_id') as $farmerId => $items)
                        <div class="mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <h6 class="mb-2">{{ $items->first()->farmer->name }}</h6>
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-map-marker-alt me-1"></i>{{ $items->first()->farmer->region }}
                                    @if($items->first()->farmer->phone)
                                        <span class="ms-2">
                                            <i class="fas fa-phone me-1"></i>{{ $items->first()->farmer->phone }}
                                        </span>
                                    @endif
                                </small>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Items:</span>
                                <strong>{{ $items->count() }} items</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Quantity:</span>
                                <strong>{{ $items->sum('quantity') }} kg</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Revenue:</span>
                                <strong class="text-success">₦{{ number_format($items->sum('total_price'), 2) }}</strong>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
