@extends('layouts.marketplace')

@section('title', 'Manage Crops')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-carrot me-2 text-success"></i>Manage Crops
            </h1>
            <p class="text-muted mb-0">Review and manage all crop listings</p>
        </div>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.crops') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Search Crops</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Search by name or category...">
                    </div>
                    <div class="col-md-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">All Categories</option>
                            <option value="Vegetables" {{ request('category') == 'Vegetables' ? 'selected' : '' }}>Vegetables</option>
                            <option value="Fruits" {{ request('category') == 'Fruits' ? 'selected' : '' }}>Fruits</option>
                            <option value="Grains" {{ request('category') == 'Grains' ? 'selected' : '' }}>Grains</option>
                            <option value="Legumes" {{ request('category') == 'Legumes' ? 'selected' : '' }}>Legumes</option>
                            <option value="Tubers" {{ request('category') == 'Tubers' ? 'selected' : '' }}>Tubers</option>
                            <option value="Spices" {{ request('category') == 'Spices' ? 'selected' : '' }}>Spices</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="availability" class="form-label">Availability</label>
                        <select class="form-select" id="availability" name="availability">
                            <option value="">All</option>
                            <option value="available" {{ request('availability') == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="unavailable" {{ request('availability') == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="farmer" class="form-label">Farmer</label>
                        <select class="form-select" id="farmer" name="farmer">
                            <option value="">All Farmers</option>
                            @foreach($farmers as $farmer)
                                <option value="{{ $farmer->id }}" {{ request('farmer') == $farmer->id ? 'selected' : '' }}>
                                    {{ $farmer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Search
                            </button>
                            <a href="{{ route('admin.crops') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Clear
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Crops Table -->
    @if($crops->count() > 0)
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Crop</th>
                                <th>Farmer</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Orders</th>
                                <th>Revenue</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($crops as $crop)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $crop->image_url }}" alt="{{ $crop->name }}" 
                                                 class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                            <div>
                                                <h6 class="mb-0">{{ $crop->name }}</h6>
                                                <small class="text-muted">{{ Str::limit($crop->description, 50) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $crop->farmer->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $crop->farmer->region }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $crop->category }}</span>
                                    </td>
                                    <td>
                                        <strong class="text-success">{{ $crop->formatted_price }}/kg</strong>
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ $crop->available_quantity }} kg</span>
                                        <br>
                                        <small class="text-muted">{{ $crop->unit }}</small>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $crop->orderItems->count() }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $crop->orderItems->sum('quantity') }} kg sold</small>
                                        </div>
                                    </td>
                                    <td>
                                        <strong class="text-success">TZS{{ number_format($crop->orderItems->sum('total_price'), 2) }}</strong>
                                    </td>
                                    <td>
                                        @if($crop->is_available)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>Available
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times me-1"></i>Unavailable
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.crops.show', $crop) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="{{ route('admin.crops.toggle-availability', $crop) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-warning" 
                                                        title="Toggle availability">
                                                    <i class="fas fa-toggle-on"></i>
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
            {{ $crops->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-carrot fa-4x text-muted mb-3"></i>
            <h4 class="text-muted mb-3">No crops found</h4>
            <p class="text-muted mb-4">Try adjusting your search criteria or check back later.</p>
            <a href="{{ route('admin.crops') }}" class="btn btn-success btn-lg">
                <i class="fas fa-redo me-2"></i>Clear Filters
            </a>
        </div>
    @endif
</div>
@endsection
