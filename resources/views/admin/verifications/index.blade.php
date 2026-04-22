@extends('layouts.marketplace')

@section('title', 'User Verifications')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-user-check me-2 text-success"></i>User Verifications
            </h1>
            <p class="text-muted mb-0">Review and manage user verification requests</p>
        </div>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="text-warning mb-2">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                    <h4 class="fw-bold">{{ \App\Models\UserVerification::where('status', 'pending')->count() }}</h4>
                    <p class="text-muted mb-0">Pending</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="text-info mb-2">
                        <i class="fas fa-search fa-2x"></i>
                    </div>
                    <h4 class="fw-bold">{{ \App\Models\UserVerification::where('status', 'under_review')->count() }}</h4>
                    <p class="text-muted mb-0">Under Review</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="text-success mb-2">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                    <h4 class="fw-bold">{{ \App\Models\UserVerification::where('status', 'verified')->count() }}</h4>
                    <p class="text-muted mb-0">Verified</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="text-danger mb-2">
                        <i class="fas fa-times-circle fa-2x"></i>
                    </div>
                    <h4 class="fw-bold">{{ \App\Models\UserVerification::where('status', 'rejected')->count() }}</h4>
                    <p class="text-muted mb-0">Rejected</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Verifications Table -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>Pending Verifications
                </h5>
                <div class="d-flex gap-2">
                    <select class="form-select form-select-sm" style="width: 150px;" onchange="this.form.submit()">
                        <option value="all">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>Under Review</option>
                        <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Status</th>
                            <th>Completion</th>
                            <th>Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($verifications as $verification)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $verification->user->avatar_url }}" alt="Avatar" 
                                             class="rounded-circle me-2" width="32" height="32">
                                        <div>
                                            <div class="fw-bold">{{ $verification->user->name }}</div>
                                            <small class="text-muted">{{ $verification->user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{!! $verification->getStatusBadge() !!}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress me-2" style="width: 60px; height: 8px;">
                                            <div class="progress-bar bg-success" 
                                                 style="width: {{ $verification->getCompletionPercentage() }}%"></div>
                                        </div>
                                        <small>{{ $verification->getCompletionPercentage() }}%</small>
                                    </div>
                                </td>
                                <td>
                                    @if($verification->submitted_at)
                                        {{ $verification->submitted_at->format('M j, Y') }}
                                        <small class="text-muted d-block">
                                            {{ $verification->submitted_at->format('g:i A') }}
                                        </small>
                                    @else
                                        <span class="text-muted">Not submitted</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.verifications.show', $verification) }}" 
                                           class="btn btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($verification->status === 'pending' || $verification->status === 'under_review')
                                            <button type="button" class="btn btn-outline-success" 
                                                    onclick="showApproveModal({{ $verification->id }})">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    onclick="showRejectModal({{ $verification->id }})">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $verifications->links() }}
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
            <form action="{{ route('admin.verifications.approve', ':id') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Are you sure you want to approve this verification?</p>
                    <div class="mb-3">
                        <label class="form-label">Admin Notes (Optional)</label>
                        <textarea name="admin_notes" class="form-control" rows="3" 
                                  placeholder="Add any notes about this approval..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-2"></i>Approve
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
            <form action="{{ route('admin.verifications.reject', ':id') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Are you sure you want to reject this verification?</p>
                    <div class="mb-3">
                        <label class="form-label">Rejection Reason *</label>
                        <textarea name="rejection_reason" class="form-control" rows="3" 
                                  placeholder="Please provide a reason for rejection..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Admin Notes (Optional)</label>
                        <textarea name="admin_notes" class="form-control" rows="3" 
                                  placeholder="Add any additional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-2"></i>Reject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showApproveModal(id) {
    const modal = document.getElementById('approveModal');
    const form = modal.querySelector('form');
    form.action = form.action.replace(':id', id);
    new bootstrap.Modal(modal).show();
}

function showRejectModal(id) {
    const modal = document.getElementById('rejectModal');
    const form = modal.querySelector('form');
    form.action = form.action.replace(':id', id);
    new bootstrap.Modal(modal).show();
}
</script>
@endsection
