@extends('layouts.marketplace')

@section('title', 'Account Verification')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-shield-alt me-2 text-success"></i>Account Verification
            </h1>
            <p class="text-muted mb-0">Complete verification to increase trust and security</p>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Verification Status Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="card-title mb-2">Verification Status</h5>
                            <div class="d-flex align-items-center mb-3">
                                <div>{!! $verification->getStatusBadge() !!}</div>
                                <div class="ms-3">
                                    <small class="text-muted">Completion: {{ $verification->getCompletionPercentage() }}%</small>
                                </div>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: {{ $verification->getCompletionPercentage() }}%"></div>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="mb-2">
                                <small class="text-muted d-block">ID Verification</small>
                                <span class="fw-bold {{ $verification->isIdVerified() ? 'text-success' : 'text-warning' }}">
                                    {{ $verification->isIdVerified() ? 'Verified' : 'Not Verified' }}
                                </span>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted d-block">Phone Verification</small>
                                <span class="fw-bold {{ $verification->isPhoneVerified() ? 'text-success' : 'text-warning' }}">
                                    {{ $verification->isPhoneVerified() ? 'Verified' : 'Not Verified' }}
                                </span>
                            </div>
                            <div>
                                <small class="text-muted d-block">Address Verification</small>
                                <span class="fw-bold {{ $verification->isAddressVerified() ? 'text-success' : 'text-warning' }}">
                                    {{ $verification->isAddressVerified() ? 'Verified' : 'Not Verified' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Verification Forms -->
    <div class="row">
        <!-- ID Verification -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-id-card me-2"></i>ID Verification
                    </h5>
                    
                    @if($verification->isIdVerified())
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            Your ID has been verified successfully!
                        </div>
                    @else
                        <form action="{{ route('verification.id.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="mb-3">
                                <label class="form-label">ID Type</label>
                                <select name="id_type" class="form-select @error('id_type') is-invalid @enderror" required>
                                    <option value="">Select ID Type</option>
                                    <option value="national_id" {{ old('id_type', $verification->id_type ?? '') === 'national_id' ? 'selected' : '' }}>National ID</option>
                                    <option value="passport" {{ old('id_type', $verification->id_type ?? '') === 'passport' ? 'selected' : '' }}>Passport</option>
                                    <option value="driving_license" {{ old('id_type', $verification->id_type ?? '') === 'driving_license' ? 'selected' : '' }}>Driver's License</option>
                                </select>
                                @error('id_type')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">ID Number</label>
                                <input type="text" name="id_number" class="form-control @error('id_number') is-invalid @enderror" 
                                       value="{{ old('id_number', $verification->id_number ?? '') }}" required>
                                @error('id_number')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">ID Front Image</label>
                                <input type="file" name="id_front_image" class="form-control @error('id_front_image') is-invalid @enderror" 
                                       accept="image/*" required>
                                <small class="text-muted">Upload front side of your ID</small>
                                @error('id_front_image')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">ID Back Image</label>
                                <input type="file" name="id_back_image" class="form-control @error('id_back_image') is-invalid @enderror" 
                                       accept="image/*">
                                <small class="text-muted">Upload back side of your ID (if applicable)</small>
                                @error('id_back_image')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Selfie Image</label>
                                <input type="file" name="selfie_image" class="form-control @error('selfie_image') is-invalid @enderror" 
                                       accept="image/*" required>
                                <small class="text-muted">Upload a selfie holding your ID</small>
                                @error('selfie_image')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-upload me-2"></i>Submit ID Verification
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Phone Verification -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-phone me-2"></i>Phone Verification
                    </h5>
                    
                    @if($verification->isPhoneVerified())
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            Your phone number has been verified!
                        </div>
                    @else
                        <!-- Send Code Form -->
                        <form action="{{ route('verification.phone.send') }}" method="POST" class="mb-4">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror" 
                                       value="{{ old('phone_number', $verification->phone_number ?? Auth::user()->phone ?? '') }}" 
                                       placeholder="+255123456789" required>
                                @error('phone_number')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-outline-primary w-100">
                                <i class="fas fa-sms me-2"></i>Send Verification Code
                            </button>
                        </form>

                        <!-- Verify Code Form -->
                        <form action="{{ route('verification.phone.verify') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Verification Code</label>
                                <input type="text" name="phone_verification_code" class="form-control @error('phone_verification_code') is-invalid @enderror" 
                                       placeholder="Enter 6-digit code" maxlength="6" required>
                                <small class="text-muted">Enter the code sent to your phone</small>
                                @error('phone_verification_code')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-check me-2"></i>Verify Phone Number
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Address Verification -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-home me-2"></i>Address Verification
                    </h5>
                    
                    @if($verification->isAddressVerified())
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            Your address has been verified successfully!
                        </div>
                    @else
                        <form action="{{ route('verification.address.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="mb-3">
                                <label class="form-label">Document Type</label>
                                <select name="verification_document" class="form-select @error('verification_document') is-invalid @enderror" required>
                                    <option value="">Select Document Type</option>
                                    <option value="utility_bill" {{ old('verification_document', $verification->verification_document ?? '') === 'utility_bill' ? 'selected' : '' }}>Utility Bill</option>
                                    <option value="lease_agreement" {{ old('verification_document', $verification->verification_document ?? '') === 'lease_agreement' ? 'selected' : '' }}>Lease Agreement</option>
                                    <option value="bank_statement" {{ old('verification_document', $verification->verification_document ?? '') === 'bank_statement' ? 'selected' : '' }}>Bank Statement</option>
                                </select>
                                @error('verification_document')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Address Proof Image</label>
                                <input type="file" name="address_proof_image" class="form-control @error('address_proof_image') is-invalid @enderror" 
                                       accept="image/*" required>
                                <small class="text-muted">Upload a recent document showing your address</small>
                                @error('address_proof_image')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-upload me-2"></i>Submit Address Verification
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Submit for Review -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="card-title mb-4">
                            <i class="fas fa-paper-plane me-2"></i>Submit for Review
                        </h5>
                        <p class="text-muted">
                            Once you've completed the necessary verifications, submit them for admin review.
                        </p>
                    </div>
                    
                    @if($verification->getCompletionPercentage() >= 50)
                        <form action="{{ route('verification.submit') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-paper-plane me-2"></i>Submit for Review
                            </button>
                        </form>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Complete at least 50% of verification requirements before submitting.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Verification Benefits -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-star me-2"></i>Verification Benefits
                    </h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <i class="fas fa-shield-alt fa-2x text-success mb-2"></i>
                                <h6>Increased Trust</h6>
                                <small class="text-muted">Buyers and sellers trust verified users more</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <i class="fas fa-lock fa-2x text-primary mb-2"></i>
                                <h6>Enhanced Security</h6>
                                <small class="text-muted">Protected account with verified identity</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <i class="fas fa-trophy fa-2x text-warning mb-2"></i>
                                <h6>Priority Access</h6>
                                <small class="text-muted">Get priority in dispute resolution</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
