@extends('layouts.marketplace')

@section('title', 'Browse Crops')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-carrot me-2 text-success"></i>Browse Crops
            </h1>
            <p class="text-muted mb-0">Discover fresh produce from local farmers</p>
        </div>
        <div>
            <a href="{{ route('buyer.cart') }}" class="btn btn-success position-relative">
                <i class="fas fa-shopping-cart me-2"></i>Cart
                @if(session('cart'))
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        {{ count(session('cart')) }}
                    </span>
                @endif
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('buyer.crops.browse') }}">
                <div class="row g-3">
                    <!-- Search -->
                    <div class="col-md-4">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Search crops...">
                    </div>
                    
                    <!-- Category Filter -->
                    <div class="col-md-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Region Filter -->
                    <div class="col-md-3">
                        <label for="region" class="form-label">Region</label>
                        <select class="form-select" id="region" name="region">
                            <option value="">All Regions</option>
                            @foreach($regions as $region)
                                <option value="{{ $region }}" {{ request('region') == $region ? 'selected' : '' }}>
                                    {{ $region }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Price Range -->
                    <div class="col-md-2">
                        <label class="form-label">Price Range</label>
                        <div class="input-group">
                            <input type="number" class="form-control" name="min_price" 
                                   value="{{ request('min_price') }}" placeholder="Min" min="0" step="0.01">
                            <input type="number" class="form-control" name="max_price" 
                                   value="{{ request('max_price') }}" placeholder="Max" min="0" step="0.01">
                        </div>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>Search
                        </button>
                        <a href="{{ route('buyer.crops.browse') }}" class="btn btn-outline-secondary ms-2">
                            <i class="fas fa-times me-2"></i>Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Count -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="mb-0">
            <strong>{{ $crops->total() }}</strong> crops found
        </p>
        <div class="d-flex gap-2">
            <select class="form-select form-select-sm" style="width: auto;">
                <option>Latest First</option>
                <option>Price: Low to High</option>
                <option>Price: High to Low</option>
                <option>Name: A-Z</option>
            </select>
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
                            @if($crop->available_quantity > 50)
                                <span class="badge bg-success position-absolute top-0 end-0 m-2">
                                    <i class="fas fa-check me-1"></i>Available
                                </span>
                            @else
                                <span class="badge bg-warning position-absolute top-0 end-0 m-2">
                                    <i class="fas fa-exclamation-triangle me-1"></i>Low Stock
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
                            
                            <div class="mt-auto">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('buyer.crops.show', $crop) }}" class="btn btn-outline-primary btn-sm flex-fill">
                                        <i class="fas fa-eye me-1"></i>View Details
                                    </a>
                                    <form action="{{ route('buyer.crops.add-to-cart', $crop) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="quantity" value="1" step="0.1" min="0.1">
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fas fa-cart-plus me-1"></i>Add to Cart
                                        </button>
                                    </form>
                                    <a href="{{ route('buyer.chats.start.crop', $crop) }}" class="btn btn-outline-info btn-sm">
                                        <i class="fas fa-comments me-1"></i>Chat
                                    </a>
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
            <h4 class="text-muted">No crops found</h4>
            <p class="text-muted">Try adjusting your search criteria or browse all available crops.</p>
            <a href="{{ route('buyer.crops.browse') }}" class="btn btn-success">
                <i class="fas fa-redo me-2"></i>Clear Filters
            </a>
        </div>
    @endif
</div>
@endsection
