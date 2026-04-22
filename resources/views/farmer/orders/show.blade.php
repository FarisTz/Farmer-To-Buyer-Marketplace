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
                <a href="{{ route('farmer.orders') }}" class="text-decoration-none">Manage Orders</a>
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
                    @if($order->status === 'pending')
                        <div class="mb-2">
                            <button type="button" class="btn btn-success btn-lg w-100" 
                                    onclick="confirmOrder({{ $order->id }})">
                                <i class="fas fa-check me-2"></i>Confirm Order
                            </button>
                        </div>
                        <button class="btn btn-outline-danger w-100" disabled>
                            <i class="fas fa-times me-2"></i>Reject Order
                        </button>
                    @elseif($order->status === 'confirmed')
                        <div class="mb-2">
                            <button type="button" class="btn btn-info btn-lg w-100" 
                                    onclick="deliverOrder({{ $order->id }})">
                                <i class="fas fa-truck me-2"></i>Mark as Delivered
                            </button>
                        </div>
                        <button class="btn btn-outline-warning w-100" disabled>
                            <i class="fas fa-clock me-2"></i>Awaiting Delivery
                        </button>
                    @elseif($order->status === 'delivered')
                        <button class="btn btn-success btn-lg w-100" disabled>
                            <i class="fas fa-check-circle me-2"></i>Order Completed
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
                        <h6 class="text-muted">Name</h6>
                        <p>{{ $order->buyer_name }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="text-muted">Delivery Address</h6>
                        <p>{{ $order->delivery_address }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="text-muted">Contact Number</h6>
                        <p>{{ $order->phone }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="text-muted">Email</h6>
                        <p>{{ $order->buyer_email }}</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h5>
                    
                    <div class="d-grid gap-2">
                        @if($order->status === 'pending')
                            <button type="button" class="btn btn-success w-100" 
                                    onclick="confirmOrder({{ $order->id }})">
                                <i class="fas fa-check me-2"></i>Confirm Order
                            </button>
                        @elseif($order->status === 'confirmed')
                            <button type="button" class="btn btn-info w-100" 
                                    onclick="deliverOrder({{ $order->id }})">
                                <i class="fas fa-truck me-2"></i>Mark as Delivered
                            </button>
                        @endif
                        
                        <button class="btn btn-outline-primary w-100" disabled>
                            <i class="fas fa-phone me-2"></i>Contact Customer
                        </button>
                        
                        <a href="{{ route('farmer.orders') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-arrow-left me-2"></i>Back to Orders
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmOrder(orderId) {
    Swal.fire({
        title: 'Confirm Order',
        text: 'Are you sure you want to confirm this order? This will notify the buyer that their order is being processed.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-check me-2"></i>Yes, Confirm Order',
        cancelButtonText: '<i class="fas fa-times me-2"></i>Cancel',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return fetch(`/farmer/orders/${orderId}/confirm`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            }).then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            }).catch(error => {
                Swal.showValidationMessage(`Request failed: ${error.message}`);
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Order Confirmed!',
                text: 'The order has been confirmed successfully. The buyer will be notified.',
                icon: 'success',
                confirmButtonColor: '#28a745',
                confirmButtonText: '<i class="fas fa-check me-2"></i>Great!'
            }).then(() => {
                window.location.reload();
            });
        }
    });
}

function deliverOrder(orderId) {
    Swal.fire({
        title: 'Mark Order as Delivered',
        text: 'Are you sure you want to mark this order as delivered? This will notify the buyer that their order has been delivered.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#17a2b8',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-truck me-2"></i>Yes, Mark as Delivered',
        cancelButtonText: '<i class="fas fa-times me-2"></i>Cancel',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return fetch(`/farmer/orders/${orderId}/deliver`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            }).then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            }).catch(error => {
                Swal.showValidationMessage(`Request failed: ${error.message}`);
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Order Delivered!',
                text: 'The order has been marked as delivered successfully. The buyer will be notified.',
                icon: 'success',
                confirmButtonColor: '#17a2b8',
                confirmButtonText: '<i class="fas fa-check me-2"></i>Excellent!'
            }).then(() => {
                window.location.reload();
            });
        }
    });
}
</script>
@endsection
