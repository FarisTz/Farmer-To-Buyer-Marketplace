@extends('layouts.marketplace')

@section('title', 'Verification Details')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-user-check me-2 text-success"></i>Verification Details
            </h1>
            <p class="text-muted mb-0">Review user verification documents and information</p>
        </div>
        <div>
            <a href="{{ route('admin.verifications.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Back to Verifications
            </a>
        </div>
    </div>

    <!-- User Information -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-user me-2"></i>User Information
                    </h5>
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ $verification->user->avatar_url }}" alt="Avatar" 
                             class="rounded-circle me-3" width="60" height="60">
                        <div>
                            <h6 class="mb-1">{{ $verification->user->name }}</h6>
                            <p class="text-muted mb-0">{{ $verification->user->email }}</p>
                            <small class="text-muted">
                                Role: {{ ucfirst($verification->user->role) }} | 
                                Joined: {{ $verification->user->created_at->format('M j, Y') }}
                            </small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted d-block">Phone</small>
                            <span>{{ $verification->user->phone ?: 'Not provided' }}</span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Address</small>
                            <span>{{ $verification->user->address ?: 'Not provided' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-chart-line me-2"></i>Verification Status
                    </h5>
                    <div class="text-center mb-3">
                        <div class="mb-2">{!! $verification->getStatusBadge() !!}</div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success" 
                                 style="width: {{ $verification->getCompletionPercentage() }}%"></div>
                        </div>
                        <small class="text-muted">{{ $verification->getCompletionPercentage() }}% Complete</small>
                    </div>
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="mb-2">
                                <i class="fas fa-id-card fa-2x {{ $verification->isIdVerified() ? 'text-success' : 'text-muted' }}"></i>
                            </div>
                            <small class="d-block">ID</small>
                            <span class="fw-bold {{ $verification->isIdVerified() ? 'text-success' : 'text-muted' }}">
                                {{ $verification->isIdVerified() ? 'Verified' : 'Not Verified' }}
                            </span>
                        </div>
                        <div class="col-4">
                            <div class="mb-2">
                                <i class="fas fa-phone fa-2x {{ $verification->isPhoneVerified() ? 'text-success' : 'text-muted' }}"></i>
                            </div>
                            <small class="d-block">Phone</small>
                            <span class="fw-bold {{ $verification->isPhoneVerified() ? 'text-success' : 'text-muted' }}">
                                {{ $verification->isPhoneVerified() ? 'Verified' : 'Not Verified' }}
                            </span>
                        </div>
                        <div class="col-4">
                            <div class="mb-2">
                                <i class="fas fa-home fa-2x {{ $verification->isAddressVerified() ? 'text-success' : 'text-muted' }}"></i>
                            </div>
                            <small class="d-block">Address</small>
                            <span class="fw-bold {{ $verification->isAddressVerified() ? 'text-success' : 'text-muted' }}">
                                {{ $verification->isAddressVerified() ? 'Verified' : 'Not Verified' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Verification Documents -->
    <div class="row mb-4">
        <!-- ID Verification -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-id-card me-2"></i>ID Verification
                    </h5>
                    @if($verification->id_type)
                        <div class="mb-2">
                            <small class="text-muted">Type:</small>
                            <span class="ms-2">{{ ucfirst(str_replace('_', ' ', $verification->id_type)) }}</span>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Number:</small>
                            <span class="ms-2">{{ $verification->id_number }}</span>
                        </div>
                        
                        @if($verification->id_front_image)
                            <div class="mb-2">
                                <small class="text-muted d-block">Front Side:</small>
                                <a href="{{ asset('storage/' . $verification->id_front_image) }}" 
                                   target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye me-1"></i>View
                                </a>
                            </div>
                        @endif
                        
                        @if($verification->id_back_image)
                            <div class="mb-2">
                                <small class="text-muted d-block">Back Side:</small>
                                <a href="{{ asset('storage/' . $verification->id_back_image) }}" 
                                   target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye me-1"></i>View
                                </a>
                            </div>
                        @endif
                        
                        @if($verification->selfie_image)
                            <div class="mb-2">
                                <small class="text-muted d-block">Selfie with ID:</small>
                                <a href="{{ asset('storage/' . $verification->selfie_image) }}" 
                                   target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye me-1"></i>View
                                </a>
                            </div>
                        @endif
                    @else
                        <p class="text-muted">No ID verification submitted</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Phone Verification -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-phone me-2"></i>Phone Verification
                    </h5>
                    @if($verification->phone_number)
                        <div class="mb-2">
                            <small class="text-muted">Phone Number:</small>
                            <span class="ms-2">{{ $verification->phone_number }}</span>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Status:</small>
                            <span class="ms-2 {{ $verification->isPhoneVerified() ? 'text-success' : 'text-warning' }}">
                                {{ $verification->isPhoneVerified() ? 'Verified' : 'Not Verified' }}
                            </span>
                        </div>
                        @if($verification->phone_verified_at)
                            <div class="mb-2">
                                <small class="text-muted">Verified At:</small>
                                <span class="ms-2">{{ $verification->phone_verified_at->format('M j, Y g:i A') }}</span>
                            </div>
                        @endif
                    @else
                        <p class="text-muted">No phone verification submitted</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Address Verification -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-home me-2"></i>Address Verification
                    </h5>
                    @if($verification->verification_document)
                        <div class="mb-2">
                            <small class="text-muted">Document Type:</small>
                            <span class="ms-2">{{ ucfirst(str_replace('_', ' ', $verification->verification_document)) }}</span>
                        </div>
                        
                        @if($verification->address_proof_image)
                            <div class="mb-2">
                                <small class="text-muted d-block">Address Proof:</small>
                                <a href="{{ asset('storage/' . $verification->address_proof_image) }}" 
                                   target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye me-1"></i>View
                                </a>
                            </div>
                        @endif
                    @else
                        <p class="text-muted">No address verification submitted</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Review Information -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-history me-2"></i>Review History
                    </h5>
                    <div class="row">
                        <div class="col-md-4">
                            <small class="text-muted d-block">Submitted At:</small>
                            @if($verification->submitted_at)
                                {{ $verification->submitted_at->format('M j, Y g:i A') }}
                            @else
                                <span class="text-muted">Not submitted</span>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Reviewed At:</small>
                            @if($verification->reviewed_at)
                                {{ $verification->reviewed_at->format('M j, Y g:i A') }}
                            @else
                                <span class="text-muted">Not reviewed</span>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Reviewed By:</small>
                            @if($verification->reviewer)
                                {{ $verification->reviewer->name }}
                            @else
                                <span class="text-muted">Not reviewed</span>
                            @endif
                        </div>
                    </div>
                    
                    @if($verification->rejection_reason)
                        <div class="mt-3">
                            <small class="text-muted d-block">Rejection Reason:</small>
                            <div class="alert alert-danger">
                                {{ $verification->rejection_reason }}
                            </div>
                        </div>
                    @endif
                    
                    @if($verification->admin_notes)
                        <div class="mt-3">
                            <small class="text-muted d-block">Admin Notes:</small>
                            <div class="alert alert-info">
                                {{ $verification->admin_notes }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Review Actions</h5>
                            <p class="text-muted mb-0">Approve or reject this verification request</p>
                        </div>
                        <div>
                            @if($verification->status === 'pending' || $verification->status === 'under_review')
                                <button type="button" class="btn btn-success me-2" 
                                        onclick="showApproveModal({{ $verification->id }})">
                                    <i class="fas fa-check me-2"></i>Approve
                                </button>
                                <button type="button" class="btn btn-danger" 
                                        onclick="showRejectModal({{ $verification->id }})">
                                    <i class="fas fa-times me-2"></i>Reject
                                </button>
                            @else
                                <div class="alert alert-info mb-0">
                                    This verification has already been {{ $verification->status }}.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approve Verification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.verifications.approve', $verification) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Are you sure you want to approve this verification for <strong>{{ $verification->user->name }}</strong>?</p>
                    <div class="mb-3">
                        <label class="form-label">Admin Notes (Optional)</label>
                        <textarea name="admin_notes" class="form-control" rows="3" 
                                  placeholder="Add any notes about this approval...">{{ $verification->admin_notes ?? '' }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-2"></i>Approve Verification
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Verification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.verifications.reject', $verification) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Are you sure you want to reject this verification for <strong>{{ $verification->user->name }}</strong>?</p>
                    <div class="mb-3">
                        <label class="form-label">Rejection Reason *</label>
                        <textarea name="rejection_reason" class="form-control" rows="3" 
                                  placeholder="Please provide a reason for rejection..." required>{{ $verification->rejection_reason ?? '' }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Admin Notes (Optional)</label>
                        <textarea name="admin_notes" class="form-control" rows="3" 
                                  placeholder="Add any additional notes...">{{ $verification->admin_notes ?? '' }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-2"></i>Reject Verification
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showApproveModal(id) {
    new bootstrap.Modal(document.getElementById('approveModal')).show();
}

function showRejectModal(id) {
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
</script>
@endsection
