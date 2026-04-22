@extends('layouts.marketplace')

@section('title', 'Profile - ' . $user->name)

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-user me-2 text-success"></i>My Profile
            </h1>
            <p class="text-muted mb-0">Manage your account information</p>
        </div>
        <div>
            <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>Edit Profile
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Profile Info -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <!-- Avatar -->
                    <img src="{{ $user->avatar? asset('storage/avatars/' . $user->avatar) : asset('images/default-avatar.png') }}" 
                         alt="{{ $user->name }}" 
                         class="rounded-circle mb-3" 
                         style="width: 120px; height: 120px; object-fit: cover;">
                    
                    <h5 class="mb-2">{{ $user->name }}</h5>
                    <div class="mb-2">
                        {!! $user->role_badge !!}
                    </div>
                    
                    <!-- Avatar Upload -->
                    <form action="{{ route('profile.update-avatar') }}" method="POST" enctype="multipart/form-data" class="mt-3">
                        @csrf
                        <div class="input-group">
                            <input type="file" name="avatar" class="form-control" accept="image/*" id="avatarInput">
                            <button type="submit" class="btn btn-outline-secondary">
                                <i class="fas fa-camera"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="mb-3">Quick Actions</h6>
                    <div class="d-grid gap-2">
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary">
                            <i class="fas fa-edit me-2"></i>Edit Information
                        </a>
                        <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                            <i class="fas fa-key me-2"></i>Change Password
                        </button>
                        @if(Auth::user()->isBuyer())
                            <a href="{{ route('buyer.orders') }}" class="btn btn-outline-info">
                                <i class="fas fa-shopping-bag me-2"></i>My Orders
                            </a>
                        @elseif(Auth::user()->isFarmer())
                            <a href="{{ route('farmer.orders') }}" class="btn btn-outline-info">
                                <i class="fas fa-shopping-bag me-2"></i>Manage Orders
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Details -->
        <div class="col-md-8">
            <!-- Personal Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-user me-2"></i>Personal Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name</label>
                            <p class="form-control-plaintext">{{ $user->name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email Address</label>
                            <p class="form-control-plaintext">{{ $user->email }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone Number</label>
                            <p class="form-control-plaintext">{{ $user->phone ?? 'Not provided' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Region</label>
                            <p class="form-control-plaintext">{{ $user->region ?? 'Not provided' }}</p>
                        </div>
                    </div>
                    @if($user->address)
                    <div class="row">
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <p class="form-control-plaintext">{{ $user->address }}</p>
                        </div>
                    </div>
                    @endif

                    @if(Auth::user()->isFarmer() && ($user->bank_name || $user->account_number || $user->account_full_name))
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-university me-2"></i>Bank Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if($user->bank_name)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Bank Name</label>
                                    <p class="form-control-plaintext">{{ $user->bank_name }}</p>
                                </div>
                                @endif
                                @if($user->account_number)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Account Number</label>
                                    <p class="form-control-plaintext">{{ $user->account_number }}</p>
                                </div>
                                @endif
                                @if($user->account_full_name)
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Account Full Name</label>
                                    <p class="form-control-plaintext">{{ $user->account_full_name }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Account Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-cog me-2"></i>Account Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Account Type</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'farmer' ? 'success' : 'primary') }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Member Since</label>
                            <p class="form-control-plaintext">{{ $user->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email Verified</label>
                            <p class="form-control-plaintext">
                                @if($user->email_verified_at)
                                    <span class="text-success">
                                        <i class="fas fa-check-circle me-1"></i>
                                        {{ $user->email_verified_at->format('M d, Y') }}
                                    </span>
                                @else
                                    <span class="text-warning">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        Not Verified
                                    </span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Updated</label>
                            <p class="form-control-plaintext">{{ $user->updated_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            @if(Auth::user()->isFarmer())
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Statistics
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 mb-3">
                            <div class="p-3 bg-light rounded">
                                <h4 class="text-primary mb-1">{{ $user->crops()->count() }}</h4>
                                <small class="text-muted">Total Crops</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="p-3 bg-light rounded">
                                <h4 class="text-success mb-1">{{ $user->crops()->where('is_available', true)->count() }}</h4>
                                <small class="text-muted">Available Crops</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="p-3 bg-light rounded">
                                <h4 class="text-info mb-1">{{ $user->farmerOrderItems()->count() }}</h4>
                                <small class="text-muted">Total Orders</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="p-3 bg-light rounded">
                                <h4 class="text-warning mb-1">{{ $user->chats()->count() }}</h4>
                                <small class="text-muted">Total Chats</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('profile.update-password') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" name="current_password" required>
                        @error('current_password')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" class="form-control" name="password" required minlength="8">
                        @error('password')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" name="password_confirmation" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
