@extends('layouts.marketplace')

@section('title', 'Edit Profile - ' . $user->name)

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-user-edit me-2 text-success"></i>Edit Profile
            </h1>
            <p class="text-muted mb-0">Update your personal information</p>
        </div>
        <div>
            <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Profile
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Edit Form -->
        <div class="col-md-8">
            <form action="{{ route('profile.update') }}" method="POST" class="card">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <!-- Personal Information -->
                    <h6 class="mb-3">Personal Information</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Full Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email Address *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                   name="phone" value="{{ old('phone', $user->phone) }}" 
                                   placeholder="+1234567890">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="region" class="form-label">Region</label>
                            <select class="form-select @error('region') is-invalid @enderror" name="region">
                                <option value="">Select Region</option>
                                <optgroup label="Mainland Tanzania">
                                    <option value="Arusha" {{ old('region', $user->region) === 'Arusha' ? 'selected' : '' }}>Arusha</option>
                                    <option value="Dar es Salaam" {{ old('region', $user->region) === 'Dar es Salaam' ? 'selected' : '' }}>Dar es Salaam</option>
                                    <option value="Dodoma" {{ old('region', $user->region) === 'Dodoma' ? 'selected' : '' }}>Dodoma</option>
                                    <option value="Geita" {{ old('region', $user->region) === 'Geita' ? 'selected' : '' }}>Geita</option>
                                    <option value="Iringa" {{ old('region', $user->region) === 'Iringa' ? 'selected' : '' }}>Iringa</option>
                                    <option value="Kagera" {{ old('region', $user->region) === 'Kagera' ? 'selected' : '' }}>Kagera</option>
                                    <option value="Katavi" {{ old('region', $user->region) === 'Katavi' ? 'selected' : '' }}>Katavi</option>
                                    <option value="Kigoma" {{ old('region', $user->region) === 'Kigoma' ? 'selected' : '' }}>Kigoma</option>
                                    <option value="Kilimanjaro" {{ old('region', $user->region) === 'Kilimanjaro' ? 'selected' : '' }}>Kilimanjaro</option>
                                    <option value="Lindi" {{ old('region', $user->region) === 'Lindi' ? 'selected' : '' }}>Lindi</option>
                                    <option value="Manyara" {{ old('region', $user->region) === 'Manyara' ? 'selected' : '' }}>Manyara</option>
                                    <option value="Mara" {{ old('region', $user->region) === 'Mara' ? 'selected' : '' }}>Mara</option>
                                    <option value="Mbeya" {{ old('region', $user->region) === 'Mbeya' ? 'selected' : '' }}>Mbeya</option>
                                    <option value="Morogoro" {{ old('region', $user->region) === 'Morogoro' ? 'selected' : '' }}>Morogoro</option>
                                    <option value="Mtwara" {{ old('region', $user->region) === 'Mtwara' ? 'selected' : '' }}>Mtwara</option>
                                    <option value="Mwanza" {{ old('region', $user->region) === 'Mwanza' ? 'selected' : '' }}>Mwanza</option>
                                    <option value="Njombe" {{ old('region', $user->region) === 'Njombe' ? 'selected' : '' }}>Njombe</option>
                                    <option value="Pwani" {{ old('region', $user->region) === 'Pwani' ? 'selected' : '' }}>Pwani (Coast Region)</option>
                                    <option value="Rukwa" {{ old('region', $user->region) === 'Rukwa' ? 'selected' : '' }}>Rukwa</option>
                                    <option value="Ruvuma" {{ old('region', $user->region) === 'Ruvuma' ? 'selected' : '' }}>Ruvuma</option>
                                    <option value="Shinyanga" {{ old('region', $user->region) === 'Shinyanga' ? 'selected' : '' }}>Shinyanga</option>
                                    <option value="Simiyu" {{ old('region', $user->region) === 'Simiyu' ? 'selected' : '' }}>Simiyu</option>
                                    <option value="Singida" {{ old('region', $user->region) === 'Singida' ? 'selected' : '' }}>Singida</option>
                                    <option value="Songwe" {{ old('region', $user->region) === 'Songwe' ? 'selected' : '' }}>Songwe</option>
                                    <option value="Tabora" {{ old('region', $user->region) === 'Tabora' ? 'selected' : '' }}>Tabora</option>
                                    <option value="Tanga" {{ old('region', $user->region) === 'Tanga' ? 'selected' : '' }}>Tanga</option>
                                </optgroup>
                                <optgroup label="Zanzibar (Semi-autonomous part of Tanzania)">
                                    <option value="Kaskazini Unguja" {{ old('region', $user->region) === 'Kaskazini Unguja' ? 'selected' : '' }}>Kaskazini Unguja (North Unguja)</option>
                                    <option value="Kusini Unguja" {{ old('region', $user->region) === 'Kusini Unguja' ? 'selected' : '' }}>Kusini Unguja (South Unguja)</option>
                                    <option value="Mjini Magharibi" {{ old('region', $user->region) === 'Mjini Magharibi' ? 'selected' : '' }}>Mjini Magharibi (Urban West)</option>
                                    <option value="Kaskazini Pemba" {{ old('region', $user->region) === 'Kaskazini Pemba' ? 'selected' : '' }}>Kaskazini Pemba (North Pemba)</option>
                                    <option value="Kusini Pemba" {{ old('region', $user->region) === 'Kusini Pemba' ? 'selected' : '' }}>Kusini Pemba (South Pemba)</option>
                                </optgroup>
                            </select>
                            @error('region')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      name="address" rows="3" placeholder="Enter your full address">{{ old('address', $user->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    

                    <!-- Form Actions -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Changes
                        </button>
                        <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Side Panel -->
        <div class="col-md-4">
            <!-- Current Avatar -->
            <div class="card mb-4">
                <div class="card-body text-center">
                    <h6 class="mb-3">Current Avatar</h6>
                    <img src="{{ $user->avatar_url ?? asset('images/default-avatar.png') }}" 
                         alt="{{ $user->name }}" 
                         class="rounded-circle mb-3" 
                         style="width: 100px; height: 100px; object-fit: cover;">
                    
                    <form action="{{ route('profile.update-avatar') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="avatar" class="form-label">Upload New Avatar</label>
                            <input type="file" class="form-control" name="avatar" accept="image/*" id="avatarInput">
                            <small class="text-muted">Allowed: JPG, PNG, GIF (Max 2MB)</small>
                            @error('avatar')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                            <i class="fas fa-camera me-2"></i>Update Avatar
                        </button>
                    </form>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="mb-3">Quick Actions</h6>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                            <i class="fas fa-key me-2"></i>Change Password
                        </button>
                        @if(Auth::user()->isBuyer())
                            <a href="{{ route('buyer.orders') }}" class="btn btn-outline-info btn-sm">
                                <i class="fas fa-shopping-bag me-2"></i>My Orders
                            </a>
                        @elseif(Auth::user()->isFarmer())
                            <a href="{{ route('farmer.orders') }}" class="btn btn-outline-info btn-sm">
                                <i class="fas fa-shopping-bag me-2"></i>Manage Orders
                            </a>
                        @endif
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                    </div>
                </div>
            </div>

            <!-- Account Info -->
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3">Account Information</h6>
                    <div class="small text-muted">
                        <div class="mb-2">
                            <strong>Account Type:</strong> 
                            <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'farmer' ? 'success' : 'primary') }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </div>
                        <div class="mb-2">
                            <strong>Member Since:</strong> {{ $user->created_at->format('M d, Y') }}
                        </div>
                        <div>
                            <strong>Email Verified:</strong> 
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
                        </div>
                    </div>
                </div>
            </div>
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
                <div class="modal-body">
                    @csrf
                     @method('PUT')
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
                        <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" required>
                        @error('password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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

<script>
// Preview avatar before upload
document.getElementById('avatarInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Update preview if needed
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endsection
