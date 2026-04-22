@extends('layouts.marketplace')

@section('title', 'Checkout')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-credit-card me-2 text-success"></i>Checkout
            </h1>
            <p class="text-muted mb-0">Complete your order details</p>
        </div>
        <div>
            <a href="{{ route('buyer.cart') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Back to Cart
            </a>
        </div>
    </div>

    <!-- Success and Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- JavaScript functions must be defined before HTML elements that call them -->
    <script>
    // Define functions first to ensure they're available when HTML loads
    function togglePaymentMethod() {
        console.log('=== togglePaymentMethod called ===');
        const bankTransferSection = document.getElementById('bank-transfer-section');
        const receiptImage = document.getElementById('receipt_image');
        const termsCheckbox = document.getElementById('terms');
        const cashRadio = document.getElementById('payment_cash');
        const transferRadio = document.getElementById('payment_transfer');
        
        console.log('Cash checked:', cashRadio?.checked);
        console.log('Transfer checked:', transferRadio?.checked);
        console.log('Bank transfer section:', bankTransferSection);
        console.log('Bank details container:', document.getElementById('bank-details-container'));
        
        if (transferRadio?.checked) {
            console.log('Showing bank transfer section');
            bankTransferSection.style.display = 'block';
            receiptImage.required = true;
            loadBankDetails();
        } else {
            console.log('Hiding bank transfer section');
            bankTransferSection.style.display = 'none';
            receiptImage.required = false;
        }
    }

    function loadBankDetails() {
        console.log('=== loadBankDetails called ===');
        const container = document.getElementById('bank-details-container');
        console.log('Container found:', container);
        
        let bankDetailsHtml = '<div class="row g-3">';
        
        @foreach($cartItems as $item)
            bankDetailsHtml += `
                <div class="col-12 mb-3">
                    <h6 class="text-primary"><i class="fas fa-university me-2"></i>{{ $item['crop']->farmer->name }}'s Bank Details</h6>
                    <div class="card bg-light">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Bank:</strong> {{ $item['crop']->farmer->bankDetails->bank_name ?? 'Contact farmer for bank details' }}</p>
                                    <p class="mb-2"><strong>Account Name:</strong> {{ $item['crop']->farmer->bankDetails->account_name ?? 'Contact farmer for account name' }}</p>
                                    <p class="mb-2"><strong>Account Number:</strong> {{ $item['crop']->farmer->bankDetails->formatted_account_number ?? 'Contact farmer for account number' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Account Type:</strong> {{ ucfirst($item['crop']->farmer->bankDetails->account_type ?? 'savings') }}</p>
                                    @if($item['crop']->farmer->bankDetails->branch_address ?? null)
                                        <p class="mb-2"><strong>Branch Address:</strong> {{ $item['crop']->farmer->bankDetails->branch_address }}</p>
                                    @endif
                                    @if($item['crop']->farmer->bankDetails->instructions ?? null)
                                        <p class="mb-0"><strong>Instructions:</strong> {{ $item['crop']->farmer->bankDetails->instructions }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        @endforeach
        
        bankDetailsHtml += '</div>';
        console.log('Bank details HTML generated:', bankDetailsHtml);
        console.log('Setting container innerHTML...');
        container.innerHTML = bankDetailsHtml;
        console.log('Bank details loaded successfully');
    }

    function previewReceiptImage(input) {
        const preview = document.getElementById('receipt-preview');
        const previewImg = document.getElementById('receipt-preview-img');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        togglePaymentMethod();
    });
    </script>

    <form action="{{ route('buyer.orders.place') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-4">
            <!-- Order Items -->
            <div class="col-md-8">
                <div class="card mb-4">
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
                                        <th>Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cartItems as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $item['crop']->image_url }}" alt="{{ $item['crop']->name }}" 
                                                         class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                    <div>
                                                        <h6 class="mb-0">{{ $item['crop']->name }}</h6>
                                                        <small class="text-muted">{{ $item['crop']->category }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <small>
                                                    <i class="fas fa-user me-1"></i>{{ $item['crop']->farmer->name }}
                                                    <br>
                                                    <i class="fas fa-map-marker-alt me-1"></i>{{ $item['crop']->region }}
                                                </small>
                                            </td>
                                            <td>{{ $item['quantity'] }} kg</td>
                                            <td>{{ $item['crop']->formatted_price }}/kg</td>
                                            <td class="fw-bold">TZS{{ number_format($item['total_price'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Delivery Information -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-truck me-2"></i>Delivery Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="delivery_address" class="form-label">Delivery Address *</label>
                                <textarea class="form-control @error('delivery_address') is-invalid @enderror" 
                                          id="delivery_address" name="delivery_address" rows="3" required>{{ old('delivery_address', $user->address) }}</textarea>
                                @error('delivery_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number *</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label for="notes" class="form-label">Order Notes (Optional)</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" name="notes" rows="2" placeholder="Any special instructions for delivery...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
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
                                <h5 class="text-success">TZS{{ number_format($totalAmount, 2) }}</h5>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="mb-4">
                            <h6 class="mb-3">Payment Method</h6>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="payment_cash" value="cash" checked onchange="togglePaymentMethod()">
                                <label class="form-check-label" for="payment_cash">
                                    <i class="fas fa-money-bill-wave me-2"></i>Cash on Delivery
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="payment_transfer" value="bank_transfer" onchange="togglePaymentMethod()">
                                <label class="form-check-label" for="payment_transfer">
                                    <i class="fas fa-university me-2"></i>Bank Transfer
                                </label>
                            </div>
                        </div>

                        <!-- Bank Transfer Details (Hidden by default) -->
                        <div id="bank-transfer-section" class="mb-4" style="display: none;">
                            <div class="alert alert-info">
                                <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Bank Transfer Instructions</h6>
                                <p class="mb-2">Please transfer the total amount to the farmer's bank account and upload your payment receipt below.</p>
                                <div id="bank-details-container">
                                    <!-- Bank details will be loaded here via JavaScript -->
                                </div>
                            </div>
                            
                            <!-- Receipt Upload -->
                            <div class="mb-3">
                                <label for="receipt_image" class="form-label">Upload Payment Receipt *</label>
                                <input type="file" class="form-control @error('receipt_image') is-invalid @enderror" 
                                       id="receipt_image" name="receipt_image" accept="image/*" 
                                       onchange="previewReceiptImage(this)">
                                @error('receipt_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Upload a clear image of your payment receipt (JPG, PNG, max 5MB)</div>
                            </div>
                            
                            <!-- Receipt Preview -->
                            <div id="receipt-preview" class="mb-3" style="display: none;">
                                <label class="form-label">Receipt Preview</label>
                                <img id="receipt-preview-img" class="img-fluid rounded border" style="max-height: 200px;">
                            </div>
                            
                            <!-- Transaction Reference -->
                            <div class="mb-3">
                                <label for="transaction_reference" class="form-label">Transaction Reference (Optional)</label>
                                <input type="text" class="form-control" id="transaction_reference" name="transaction_reference" 
                                       placeholder="Enter transaction reference or confirmation number">
                                <div class="form-text">Helps the farmer verify your payment faster</div>
                            </div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox" id="terms" name="terms" value="1" required>
                                <label class="form-check-label" for="terms">
                                    I agree to the <a href="#" class="text-decoration-none">Terms and Conditions</a> 
                                    and understand that delivery arrangements will be made directly with farmers.
                                </label>
                                @error('terms')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Place Order Button -->
                        <button type="submit" class="btn btn-success w-100 btn-lg">
                            <i class="fas fa-check-circle me-2"></i>Place Order
                        </button>

                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Important:</strong> After placing your order, farmers will contact you directly to arrange delivery.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<!-- JavaScript functions are now defined inline above the form -->
@endpush
