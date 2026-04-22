@extends('layouts.marketplace')

@section('title', 'Payment Receipts')

@section('content')
<!-- JavaScript functions must be defined before HTML elements that call them -->
<script>
// Debug: Check if SweetAlert2 is loaded
console.log('SweetAlert2 loaded:', typeof Swal !== 'undefined');

function verifyReceipt(receiptId) {
    console.log('verifyReceipt called with ID:', receiptId);
    
    if (typeof Swal === 'undefined') {
        console.error('SweetAlert2 not loaded');
        alert('SweetAlert2 library not loaded. Please refresh the page.');
        return;
    }
    
    Swal.fire({
        title: 'Verify Payment Receipt',
        text: 'Are you sure you want to verify this payment receipt?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-check me-2"></i>Yes, Verify',
        cancelButtonText: '<i class="fas fa-times me-2"></i>Cancel',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            console.log('Sending verification request for receipt:', receiptId);
            return fetch(`/farmer/payment-receipts/${receiptId}/verify`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            }).then(response => {
                console.log('Verification response status:', response.status);
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            }).then(data => {
                console.log('Verification response data:', data);
                return data;
            }).catch(error => {
                console.error('Verification error:', error);
                Swal.showValidationMessage(`Request failed: ${error.message}`);
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Receipt Verified!',
                text: 'Payment receipt has been verified successfully.',
                icon: 'success',
                confirmButtonColor: '#28a745',
                confirmButtonText: '<i class="fas fa-check me-2"></i>Great!'
            }).then(() => {
                window.location.reload();
            });
        }
    });
}

function rejectReceipt(receiptId) {
    console.log('rejectReceipt called with ID:', receiptId);
    
    if (typeof Swal === 'undefined') {
        console.error('SweetAlert2 not loaded');
        alert('SweetAlert2 library not loaded. Please refresh the page.');
        return;
    }
    
    Swal.fire({
        title: 'Reject Payment Receipt',
        input: 'textarea',
        inputLabel: 'Rejection Reason',
        inputPlaceholder: 'Please provide a reason for rejecting this receipt...',
        inputValidator: (value) => {
            if (!value) {
                return 'Rejection reason is required!';
            }
        },
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-times me-2"></i>Yes, Reject',
        cancelButtonText: '<i class="fas fa-ban me-2"></i>Cancel',
        showLoaderOnConfirm: true,
        preConfirm: (reason) => {
            console.log('Sending rejection request for receipt:', receiptId, 'with reason:', reason);
            return fetch(`/farmer/payment-receipts/${receiptId}/reject`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ rejection_reason: reason })
            }).then(response => {
                console.log('Rejection response status:', response.status);
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            }).then(data => {
                console.log('Rejection response data:', data);
                return data;
            }).catch(error => {
                console.error('Rejection error:', error);
                Swal.showValidationMessage(`Request failed: ${error.message}`);
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Receipt Rejected!',
                text: 'Payment receipt has been rejected.',
                icon: 'success',
                confirmButtonColor: '#dc3545',
                confirmButtonText: '<i class="fas fa-check me-2"></i>OK'
            }).then(() => {
                window.location.reload();
            });
        }
    });
}
</script>

<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-receipt me-2 text-info"></i>Payment Receipts
            </h1>
            <p class="text-muted mb-0">Review and verify buyer payment receipts</p>
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
                    <a class="nav-link {{ request('status') == 'pending' ? 'active' : '' }}" 
                       href="{{ route('farmer.payment-receipts', ['status' => 'pending']) }}">
                        <i class="fas fa-clock me-1"></i>Pending Verification ({{ $statusCounts['pending'] }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('status') == 'verified' ? 'active' : '' }}" 
                       href="{{ route('farmer.payment-receipts', ['status' => 'verified']) }}">
                        <i class="fas fa-check me-1"></i>Verified ({{ $statusCounts['verified'] }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('status') == 'rejected' ? 'active' : '' }}" 
                       href="{{ route('farmer.payment-receipts', ['status' => 'rejected']) }}">
                        <i class="fas fa-times me-1"></i>Rejected ({{ $statusCounts['rejected'] }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('status') == null ? 'active' : '' }}" 
                       href="{{ route('farmer.payment-receipts') }}">
                        <i class="fas fa-list me-1"></i>All Receipts ({{ $statusCounts['total'] }})
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Receipts List -->
    @if($receipts->count() > 0)
        <div class="row g-4">
            @foreach($receipts as $receipt)
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h5 class="mb-1">Order #{{ $receipt->order->order_number }}</h5>
                                            <p class="text-muted mb-0">
                                                <i class="fas fa-calendar me-1"></i>{{ $receipt->payment_date->format('M d, Y h:i A') }}
                                                <span class="ms-3">
                                                    <i class="fas fa-user me-1"></i>{{ $receipt->buyer->name }}
                                                </span>
                                            </p>
                                        </div>
                                        <div>{!! $receipt->status_badge !!}</div>
                                    </div>
                                    
                                    <!-- Receipt Image -->
                                    <div class="mb-3">
                                        <h6 class="text-primary mb-2">
                                            <i class="fas fa-image me-2"></i>Payment Receipt
                                        </h6>
                                        <div class="text-center">
                                            <a href="{{ $receipt->receipt_image_url }}" target="_blank" 
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-external-link-alt me-1"></i>View Full Size
                                            </a>
                                        </div>
                                        <div class="text-center mt-2">
                                            <img src="{{ $receipt->receipt_image_url }}" 
                                                 alt="Payment Receipt" 
                                                 class="img-fluid rounded border"
                                                 style="max-height: 150px; max-width: 200px;">
                                        </div>
                                    </div>
                                    
                                    <!-- Order Details -->
                                    <div class="row text-muted small">
                                        <div class="col-md-6">
                                            <p class="mb-1">
                                                <strong>Amount Paid:</strong> TZS{{ number_format($receipt->amount_paid, 2) }}
                                            </p>
                                            <p class="mb-1">
                                                <strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $receipt->payment_method)) }}
                                            </p>
                                            @if($receipt->transaction_reference)
                                                <p class="mb-1">
                                                    <strong>Transaction Ref:</strong> {{ $receipt->transaction_reference }}
                                                </p>
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1">
                                                <strong>Order Total:</strong> TZS{{ number_format($receipt->order->total_amount, 2) }}
                                            </p>
                                            <p class="mb-1">
                                                <strong>Order Status:</strong> {{ ucfirst($receipt->order->status) }}
                                            </p>
                                            @if($receipt->verified_at)
                                                <p class="mb-1">
                                                    <strong>Verified At:</strong> {{ $receipt->verified_at->format('M d, Y h:i A') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    @if($receipt->notes)
                                        <div class="mt-3">
                                            <h6 class="text-muted mb-2">Order Notes</h6>
                                            <p class="text-muted">{{ $receipt->notes }}</p>
                                        </div>
                                    @endif
                                    
                                    @if($receipt->rejection_reason)
                                        <div class="mt-3">
                                            <h6 class="text-danger mb-2">Rejection Reason</h6>
                                            <p class="text-danger">{{ $receipt->rejection_reason }}</p>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="col-md-4 text-end">
                                    <div class="mb-3">
                                        <h4 class="text-info mb-0">TZS{{ number_format($receipt->amount_paid, 2) }}</h4>
                                        <small class="text-muted">Amount Paid</small>
                                    </div>
                                    
                                    @if($receipt->status === 'pending')
                                        <div class="d-grid gap-2">
                                            <button type="button" class="btn btn-success w-100" 
                                                    onclick="verifyReceipt({{ $receipt->id }})">
                                                <i class="fas fa-check me-2"></i>Verify Receipt
                                            </button>
                                            <button type="button" class="btn btn-danger w-100" 
                                                    onclick="rejectReceipt({{ $receipt->id }})">
                                                <i class="fas fa-times me-2"></i>Reject Receipt
                                            </button>
                                        </div>
                                    @elseif($receipt->status === 'verified')
                                        <div class="alert alert-success">
                                            <i class="fas fa-check-circle me-2"></i>
                                            <strong>Verified</strong> - Payment confirmed by {{ $receipt->verifiedBy->name }}
                                        </div>
                                    @elseif($receipt->status === 'rejected')
                                        <div class="alert alert-danger">
                                            <i class="fas fa-times-circle me-2"></i>
                                            <strong>Rejected</strong> - See rejection reason above
                                        </div>
                                    @endif
                                    
                                    <div class="mt-3">
                                        <a href="{{ route('farmer.orders.show', $receipt->order) }}" 
                                           class="btn btn-outline-primary w-100">
                                            <i class="fas fa-eye me-2"></i>View Order Details
                                        </a>
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
            {{ $receipts->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-receipt fa-4x text-muted mb-3"></i>
            <h4 class="text-muted mb-3">No payment receipts found</h4>
            <p class="text-muted mb-4">
                @if(request('status'))
                    No {{ request('status') }} payment receipts at the moment.
                @else
                    You haven't received any payment receipts yet.
                @endif
            </p>
            <a href="{{ route('farmer.dashboard') }}" class="btn btn-primary">
                <i class="fas fa-tachometer-alt me-2"></i>Back to Dashboard
            </a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<!-- JavaScript functions are now defined inline above the content -->
@endpush
