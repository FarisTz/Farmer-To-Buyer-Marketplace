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
            <p class="text-muted mb-0">Manage your crop listings and inventory</p>
        </div>
        <div>
            <a href="{{ route('farmer.crops.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>Add New Crop
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('farmer.crops.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
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
                    
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="{{ route('farmer.crops.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Crops Grid -->
    @if($crops->count() > 0)
        <div class="row g-4">
            @foreach($crops as $crop)
                <div class="col-md-6 col-lg-4">
                    <div class="card crop-card h-100">
                        <div class="position-relative">
                            <img src="{{ $crop->image_url }}" class="card-img-top" alt="{{ $crop->name }}" 
                                 style="height: 200px; object-fit: cover;">
                            @if($crop->is_available)
                                <span class="badge bg-success position-absolute top-0 end-0 m-2">
                                    <i class="fas fa-check me-1"></i>Available
                                </span>
                            @else
                                <span class="badge bg-danger position-absolute top-0 end-0 m-2">
                                    <i class="fas fa-times me-1"></i>Not Available
                                </span>
                            @endif
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $crop->name }}</h5>
                            <p class="card-text text-muted small">{{ Str::limit($crop->description, 80) }}</p>
                            
                            <div class="mb-2">
                                <span class="badge bg-light text-dark me-2">
                                    <i class="fas fa-tag me-1"></i>{{ $crop->category }}
                                </span>
                                <span class="badge bg-light text-dark">
                                    <i class="fas fa-map-marker-alt me-1"></i>{{ $crop->region }}
                                </span>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-success fs-5">{{ $crop->formatted_price }}/kg</span>
                                    <span class="text-muted">{{ $crop->available_quantity }} kg available</span>
                                </div>
                            </div>
                            
                            <!-- Order Statistics -->
                            @if($crop->orderItems->count() > 0)
                                <div class="mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-shopping-bag me-1"></i>
                                        {{ $crop->orderItems->count() }} orders • 
                                        {{ $crop->orderItems->sum('quantity') }} kg sold
                                    </small>
                                </div>
                            @endif
                            
                            <div class="mt-auto">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('farmer.crops.show', $crop) }}" class="btn btn-outline-primary btn-sm flex-fill">
                                        <i class="fas fa-eye me-1"></i>View
                                    </a>
                                    <a href="{{ route('farmer.crops.edit', $crop) }}" class="btn btn-outline-warning btn-sm">
                                        <i class="fas fa-edit me-1"></i>Edit
                                    </a>
                                    <a href="{{ route('farmer.chats.index') }}?crop={{ $crop->id }}" class="btn btn-outline-info btn-sm">
                                        <i class="fas fa-comments me-1"></i>Messages
                                    </a>
                                    @if(!$crop->hasActiveOrders())
                                    <form action="{{ route('farmer.crops.delete', $crop) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm"
                                                onclick="event.preventDefault(); 
                                                       Swal.fire({
                                                           title: 'Are you sure?',
                                                           text: 'Are you sure you want to delete this crop?',
                                                           icon: 'warning',
                                                           showCancelButton: true,
                                                           confirmButtonColor: '#dc3545',
                                                           cancelButtonColor: '#6c757d',
                                                           confirmButtonText: 'Yes, delete it!'
                                                       }).then((result) => {
                                                           if (result.isConfirmed) {
                                                               this.closest('form').submit();
                                                           }
                                                       })">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @else
                                    <div class="text-muted small">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Cannot delete - crop has active orders
                                    </div>
                                @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $crops->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-carrot fa-4x text-muted mb-3"></i>
            <h4 class="text-muted mb-3">No crops found</h4>
            <p class="text-muted mb-4">Start by adding your first crop listing!</p>
            <a href="{{ route('farmer.crops.create') }}" class="btn btn-success btn-lg">
                <i class="fas fa-plus me-2"></i>Add Your First Crop
            </a>
        </div>
    @endif
</div>
@endsection
