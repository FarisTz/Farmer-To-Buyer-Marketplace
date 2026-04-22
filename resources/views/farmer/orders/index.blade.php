@extends('layouts.marketplace')

@section('title', 'Manage Orders')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-shopping-bag me-2 text-success"></i>Manage Orders
            </h1>
            <p class="text-muted mb-0">Track and manage customer orders</p>
        </div>
        <div>
            <a href="{{ route('farmer.dashboard') }}" class="btn btn-outline-primary">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="card mb-4">
        <div class="card-body">
            <ul class="nav nav-pills" role="tablist">
                <li class="nav-item">
                    <a class="nav-link {{ request('status') == null ? 'active' : '' }}" 
                       href="{{ route('farmer.orders') }}">
                        All Orders ({{ $statusCounts['total'] }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('status') == 'pending' ? 'active' : '' }}" 
                       href="{{ route('farmer.orders', ['status' => 'pending']) }}">
                        <i class="fas fa-clock me-1"></i>Pending ({{ $statusCounts['pending'] }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('status') == 'confirmed' ? 'active' : '' }}" 
                       href="{{ route('farmer.orders', ['status' => 'confirmed']) }}">
                        <i class="fas fa-check me-1"></i>Confirmed ({{ $statusCounts['confirmed'] }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('status') == 'delivered' ? 'active' : '' }}" 
                       href="{{ route('farmer.orders', ['status' => 'delivered']) }}">
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
                                                    <i class="fas fa-shopping-bag me-1"></i>1 item
                                                </span>
                                                <span class="ms-3">
                                                    <i class="fas fa-user me-1"></i>{{ $order->buyer_name }}
                                                </span>
                                            </p>
                                        </div>
                                        <div>{!! $order->status_badge !!}</div>
                                    </div>
                                    
                                    <!-- Order Items Preview -->
                                    <div class="mb-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <img src="{{ $order->crop->image_url }}" alt="{{ $order->crop->name }}" 
                                                 class="rounded me-2" style="width: 30px; height: 30px; object-fit: cover;">
                                            <div class="flex-grow-1">
                                                <small class="fw-bold">{{ $order->crop->name }}</small>
                                                <br>
                                                <small class="text-muted">{{ $order->quantity }} kg × {{ $order->formatted_price_per_kg }}</small>
                                            </div>
                                            <small class="fw-bold">{{ $order->formatted_total_price }}</small>
                                        </div>
                                    </div>
                                    
                                    <!-- Delivery Info -->
                                    <div class="text-muted small">
                                        <i class="fas fa-map-marker-alt me-1"></i>{{ $order->delivery_address }}
                                        <span class="ms-3">
                                            <i class="fas fa-phone me-1"></i>{{ $order->phone }}
                                        </span>
                                        @if($order->notes)
                                            <span class="ms-3">
                                                <i class="fas fa-sticky-note me-1"></i>Has notes
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="col-md-4 text-end">
                                    <div class="mb-3">
                                        <h4 class="text-success mb-0">{{ $order->formatted_total }}</h4>
                                        <small class="text-muted">Total Amount</small>
                                    </div>
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('farmer.orders.show', $order->order) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i>View Details
                                        </a>
                                        <a href="{{ route('farmer.chats.start.order', $order->order) }}" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-comments me-1"></i>Chat
                                        </a>
                                        @if($order->order->status === 'pending')
                                            <button type="button" class="btn btn-sm btn-success" 
                                                    onclick="confirmOrderFromList({{ $order->order->id }}, '{ $order->order->order_number }')">
                                                <i class="fas fa-check me-1"></i>Confirm
                                            </button>
                                        @elseif($order->order->status === 'confirmed')
                                            <button type="button" class="btn btn-sm btn-info" 
                                                    onclick="deliverOrderFromList({{ $order->order->id }}, '{ $order->order->order_number }')">
                                                <i class="fas fa-truck me-1"></i>Deliver
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
            <i class="fas fa-shopping-bag fa-4x text-muted mb-3"></i>
            <h4 class="text-muted mb-3">No orders found</h4>
            <p class="text-muted mb-4">
                @if(request('status'))
                    No {{ request('status') }} orders at the moment.
                @else
                    You haven't received any orders yet. Make sure your crops are available and competitively priced!
                @endif
            </p>
            <a href="{{ route('farmer.crops.index') }}" class="btn btn-success btn-lg">
                <i class="fas fa-carrot me-2"></i>Manage Crops
            </a>
        </div>
    @endif
</div>
@endsection

<script>
function confirmOrderFromList(orderId, orderNumber) {
    Swal.fire({
        title: 'Confirm Order',
        text: `Are you sure you want to confirm order #${orderNumber}? This will notify the buyer that their order is being processed.`,
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
                text: `Order #${orderNumber} has been confirmed successfully. The buyer will be notified.`,
                icon: 'success',
                confirmButtonColor: '#28a745',
                confirmButtonText: '<i class="fas fa-check me-2"></i>Great!'
            }).then(() => {
                window.location.reload();
            });
        }
    });
}

function deliverOrderFromList(orderId, orderNumber) {
    Swal.fire({
        title: 'Mark Order as Delivered',
        text: `Are you sure you want to mark order #${orderNumber} as delivered? This will notify the buyer that their order has been delivered.`,
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
                text: `Order #${orderNumber} has been marked as delivered successfully. The buyer will be notified.`,
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
