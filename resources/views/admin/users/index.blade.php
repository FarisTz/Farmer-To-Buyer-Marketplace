@extends('layouts.marketplace')

@section('title', 'Manage Users')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-users me-2 text-success"></i>Manage Users
            </h1>
            <p class="text-muted mb-0">View and manage platform users</p>
        </div>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="card mb-4">
        <div class="card-body">
            <ul class="nav nav-pills" role="tablist">
                <li class="nav-item">
                    <a class="nav-link {{ request('role') == null ? 'active' : '' }}" 
                       href="{{ route('admin.users') }}">
                        All Users ({{ $users->total() }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('role') == 'farmer' ? 'active' : '' }}" 
                       href="{{ route('admin.users', ['role' => 'farmer']) }}">
                        <i class="fas fa-seedling me-1"></i>Farmers
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('role') == 'buyer' ? 'active' : '' }}" 
                       href="{{ route('admin.users', ['role' => 'buyer']) }}">
                        <i class="fas fa-shopping-cart me-1"></i>Buyers
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('role') == 'admin' ? 'active' : '' }}" 
                       href="{{ route('admin.users', ['role' => 'admin']) }}">
                        <i class="fas fa-shield-alt me-1"></i>Admins
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.users') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Search Users</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Search by name or email...">
                    </div>
                    <div class="col-md-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role">
                            <option value="">All Roles</option>
                            <option value="farmer" {{ request('role') == 'farmer' ? 'selected' : '' }}>Farmer</option>
                            <option value="buyer" {{ request('role') == 'buyer' ? 'selected' : '' }}>Buyer</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    @if($users->count() > 0)
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Role</th>
                                <th>Contact</th>
                                <th>Location</th>
                                <th>Joined</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                <i class="fas fa-user text-primary"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $user->name }}</h6>
                                                <small class="text-muted">{{ $user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $user->role === 'farmer' ? 'success' : ($user->role === 'buyer' ? 'info' : 'danger') }}">
                                            <i class="fas fa-{{ $user->role === 'farmer' ? 'seedling' : ($user->role === 'buyer' ? 'shopping-cart' : 'shield-alt') }} me-1"></i>
                                            {{ $user->role }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($user->phone)
                                            <small class="text-muted">
                                                <i class="fas fa-phone me-1"></i>{{ $user->phone }}
                                            </small>
                                        @else
                                            <small class="text-muted">No phone</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->region)
                                            <small class="text-muted">
                                                <i class="fas fa-map-marker-alt me-1"></i>{{ $user->region }}
                                            </small>
                                        @else
                                            <small class="text-muted">No location</small>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $user->created_at->format('M d, Y') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>Active
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-warning" 
                                                        title="Toggle user status">
                                                    <i class="fas fa-pause"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $users->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-users fa-4x text-muted mb-3"></i>
            <h4 class="text-muted mb-3">No users found</h4>
            <p class="text-muted mb-4">Try adjusting your search criteria or check back later.</p>
            <a href="{{ route('admin.users') }}" class="btn btn-success btn-lg">
                <i class="fas fa-redo me-2"></i>Clear Filters
            </a>
        </div>
    @endif
</div>
@endsection
