@extends('layouts.marketplace')

@section('title', 'Bank Details')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-university me-2 text-primary"></i>Bank Details
            </h1>
            <p class="text-muted mb-0">Manage your bank information for buyer payments</p>
        </div>
        <div>
            <a href="{{ route('farmer.dashboard') }}" class="btn btn-outline-primary">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>
        </div>
    </div>

    @if($bankDetails)
        <!-- Bank Details Display -->
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-university me-2"></i>Current Bank Details
                        </h5>
                        <div>
                            <span class="badge bg-{{ $bankDetails->is_active ? 'success' : 'secondary' }}">
                                {{ $bankDetails->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Bank Name</label>
                                    <p class="fw-bold mb-0">{{ $bankDetails->bank_name }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">Account Name</label>
                                    <p class="fw-bold mb-0">{{ $bankDetails->account_name }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">Account Number</label>
                                    <p class="fw-bold mb-0">{{ $bankDetails->formatted_account_number }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">Account Type</label>
                                    <p class="fw-bold mb-0">{{ ucfirst($bankDetails->account_type) }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                @if($bankDetails->routing_number)
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Routing Number</label>
                                        <p class="fw-bold mb-0">{{ $bankDetails->routing_number }}</p>
                                    </div>
                                @endif
                                @if($bankDetails->swift_code)
                                    <div class="mb-3">
                                        <label class="form-label text-muted">SWIFT Code</label>
                                        <p class="fw-bold mb-0">{{ $bankDetails->swift_code }}</p>
                                    </div>
                                @endif
                                @if($bankDetails->branch_address)
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Branch Address</label>
                                        <p class="fw-bold mb-0">{{ $bankDetails->branch_address }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        @if($bankDetails->instructions)
                            <div class="mt-3">
                                <label class="form-label text-muted">Payment Instructions</label>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    {{ $bankDetails->instructions }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title mb-3">Quick Actions</h6>
                        <div class="d-grid gap-2">
                            <a href="{{ route('farmer.bank-details.edit') }}" class="btn btn-primary">
                                <i class="fas fa-edit me-2"></i>Edit Bank Details
                            </a>
                            <button type="button" class="btn btn-outline-secondary" disabled>
                                <i class="fas fa-eye me-2"></i>View as Buyer
                            </button>
                        </div>
                        
                        <hr>
                        
                        <h6 class="card-title mb-3">Security Info</h6>
                        <div class="small text-muted">
                            <p class="mb-2">
                                <i class="fas fa-shield-alt me-2"></i>
                                Account number is masked for buyers
                            </p>
                            <p class="mb-0">
                                <i class="fas fa-lock me-2"></i>
                                Only you can see full details
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- No Bank Details -->
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-university fa-4x text-muted"></i>
                        </div>
                        <h4 class="text-muted mb-3">No Bank Details Added</h4>
                        <p class="text-muted mb-4">
                            Add your bank details to receive payments from buyers who choose bank transfer option.
                        </p>
                        <a href="{{ route('farmer.bank-details.edit') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-plus me-2"></i>Add Bank Details
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title mb-3">Why Add Bank Details?</h6>
                        <ul class="list-unstyled small">
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Enable bank transfer payments
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Faster payment processing
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Professional appearance
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Secure payment tracking
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
