@extends('layouts.marketplace')

@section('title', 'Shopping Cart')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-shopping-cart me-2 text-success"></i>Shopping Cart
            </h1>
            <p class="text-muted mb-0">Review your selected crops</p>
        </div>
        <div>
            <a href="{{ route('buyer.crops.browse') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Continue Shopping
            </a>
        </div>
    </div>

    @if(session('cart') && count(session('cart')) > 0)
        <div class="row g-4">
            <!-- Cart Items -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('buyer.cart.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Price/kg</th>
                                            <th>Quantity</th>
                                            <th>Total</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($cartItems as $item)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ $item['crop']->image_url }}" alt="{{ $item['crop']->name }}" 
                                                             class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                                        <div>
                                                            <h6 class="mb-1">{{ $item['crop']->name }}</h6>
                                                            <small class="text-muted">
                                                                <i class="fas fa-user me-1"></i>{{ $item['crop']->farmer->name }}
                                                                <span class="ms-2">
                                                                    <i class="fas fa-map-marker-alt me-1"></i>{{ $item['crop']->region }}
                                                                </span>
                                                            </small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $item['crop']->formatted_price }}</td>
                                                <td>
                                                    <input type="number" name="quantity[{{ $item['crop']->id }}]" 
                                                           value="{{ $item['quantity'] }}" 
                                                           min="0.1" max="{{ $item['crop']->available_quantity }}" 
                                                           step="0.1" class="form-control form-control-sm" style="width: 80px;">
                                                    <small class="text-muted">Max: {{ $item['crop']->available_quantity }}kg</small>
                                                </td>
                                                <td class="fw-bold">TZS{{ number_format($item['total_price'], 2) }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                                            onclick="removeFromCart({{ $item['crop']->id }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-sync me-2"></i>Update Cart
                                </button>
                                <a href="{{ route('buyer.checkout') }}" class="btn btn-success btn-lg">
                                    <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Order Summary</h5>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal ({{ count($cartItems) }} items)</span>
                                <strong>TZS{{ number_format($totalAmount, 2) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Delivery Fee</span>
                                <span class="text-success">Free</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tax</span>
                                <span class="text-success">Included</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <h5>Total</h5>
                                <h5 class="text-success">TZS{{ number_format($totalAmount, 2) }}</h5>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Note:</strong> Delivery arrangements will be made with individual farmers after order confirmation.
                        </div>

                        <a href="{{ route('buyer.checkout') }}" class="btn btn-success w-100 btn-lg">
                            <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                        </a>
                        
                        <a href="{{ route('buyer.crops.browse') }}" class="btn btn-outline-secondary w-100 mt-2">
                            <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-shopping-cart fa-4x text-muted"></i>
            </div>
            <h3 class="text-muted mb-3">Your cart is empty</h3>
            <p class="text-muted mb-4">Start adding some fresh crops from local farmers!</p>
            <a href="{{ route('buyer.crops.browse') }}" class="btn btn-success btn-lg">
                <i class="fas fa-carrot me-2"></i>Browse Crops
            </a>
        </div>
    @endif
</div>

<script>
function removeFromCart(cropId) {
    if (confirm('Are you sure you want to remove this item from your cart?')) {
        // Create form to remove item
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("buyer.cart.remove") }}';
        
        // Add CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        
        // Add method spoofing for DELETE
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        // Add crop ID
        const cropIdInput = document.createElement('input');
        cropIdInput.type = 'hidden';
        cropIdInput.name = 'crop_id';
        cropIdInput.value = cropId;
        form.appendChild(cropIdInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
