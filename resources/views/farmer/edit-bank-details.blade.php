@extends('layouts.marketplace')

@section('title', 'Edit Bank Details')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-university me-2 text-primary"></i>{{ $bankDetails ? 'Edit' : 'Add' }} Bank Details
            </h1>
            <p class="text-muted mb-0">{{ $bankDetails ? 'Update your bank information' : 'Add your bank information to receive payments' }}</p>
        </div>
        <div>
            <a href="{{ route('farmer.bank-details') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Back to Bank Details
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Bank Information
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ $bankDetails ? route('farmer.bank-details.update') : route('farmer.bank-details.store') }}" 
                          method="{{ $bankDetails ? 'PUT' : 'POST' }}">
                        @csrf
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="bank_name" class="form-label">Bank Name *</label>
                                <input type="text" class="form-control @error('bank_name') is-invalid @enderror" 
                                       id="bank_name" name="bank_name" 
                                       value="{{ old('bank_name', $bankDetails->bank_name ?? '') }}" 
                                       placeholder="e.g., National Bank, CRDB Bank" required>
                                @error('bank_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="account_name" class="form-label">Account Name *</label>
                                <input type="text" class="form-control @error('account_name') is-invalid @enderror" 
                                       id="account_name" name="account_name" 
                                       value="{{ old('account_name', $bankDetails->account_name ?? '') }}" 
                                       placeholder="Full account holder name" required>
                                @error('account_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="account_number" class="form-label">Account Number *</label>
                                <input type="text" class="form-control @error('account_number') is-invalid @enderror" 
                                       id="account_number" name="account_number" 
                                       value="{{ old('account_number', $bankDetails->account_number ?? '') }}" 
                                       placeholder="Your bank account number" required>
                                @error('account_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">This will be partially hidden from buyers for security</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="account_type" class="form-label">Account Type *</label>
                                <select class="form-select @error('account_type') is-invalid @enderror" 
                                        id="account_type" name="account_type" required>
                                    <option value="">Select account type</option>
                                    <option value="savings" {{ old('account_type', $bankDetails->account_type ?? '') == 'savings' ? 'selected' : '' }}>
                                        Savings Account
                                    </option>
                                    <option value="current" {{ old('account_type', $bankDetails->account_type ?? '') == 'current' ? 'selected' : '' }}>
                                        Current Account
                                    </option>
                                </select>
                                @error('account_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="routing_number" class="form-label">Routing Number</label>
                                <input type="text" class="form-control @error('routing_number') is-invalid @enderror" 
                                       id="routing_number" name="routing_number" 
                                       value="{{ old('routing_number', $bankDetails->routing_number ?? '') }}" 
                                       placeholder="Bank routing number (optional)">
                                @error('routing_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="swift_code" class="form-label">SWIFT Code</label>
                                <input type="text" class="form-control @error('swift_code') is-invalid @enderror" 
                                       id="swift_code" name="swift_code" 
                                       value="{{ old('swift_code', $bankDetails->swift_code ?? '') }}" 
                                       placeholder="For international transfers (optional)">
                                @error('swift_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <label for="branch_address" class="form-label">Branch Address</label>
                                <textarea class="form-control @error('branch_address') is-invalid @enderror" 
                                          id="branch_address" name="branch_address" rows="2" 
                                          placeholder="Bank branch address (optional)">{{ old('branch_address', $bankDetails->branch_address ?? '') }}</textarea>
                                @error('branch_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <label for="instructions" class="form-label">Payment Instructions</label>
                                <textarea class="form-control @error('instructions') is-invalid @enderror" 
                                          id="instructions" name="instructions" rows="3" 
                                          placeholder="Any special instructions for buyers (e.g., include reference number, specific transfer instructions)">{{ old('instructions', $bankDetails->instructions ?? '') }}</textarea>
                                @error('instructions')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">These instructions will be shown to buyers when they select bank transfer</div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('farmer.bank-details') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>{{ $bankDetails ? 'Update' : 'Save' }} Bank Details
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title mb-3">
                        <i class="fas fa-info-circle me-2"></i>Important Information
                    </h6>
                    
                    <div class="alert alert-info">
                        <h6 class="alert-heading mb-2">
                            <i class="fas fa-shield-alt me-2"></i>Security Notice
                        </h6>
                        <p class="mb-0 small">
                            Your account number will be partially hidden from buyers (****1234) for security purposes.
                        </p>
                    </div>
                    
                    <h6 class="mb-3">Required Fields</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Bank Name
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Account Name
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Account Number
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-check text-success me-2"></i>
                            Account Type
                        </li>
                    </ul>
                    
                    <h6 class="mb-3">Optional Fields</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2">
                            <i class="fas fa-circle text-muted me-2"></i>
                            Routing Number
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-circle text-muted me-2"></i>
                            SWIFT Code
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-circle text-muted me-2"></i>
                            Branch Address
                        </li>
                        <li class="mb-0">
                            <i class="fas fa-circle text-muted me-2"></i>
                            Payment Instructions
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
