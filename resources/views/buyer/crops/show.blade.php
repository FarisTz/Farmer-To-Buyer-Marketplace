@extends('layouts.marketplace')

@section('title', $crop->name)

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}" class="text-decoration-none">Home</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('crops.public') }}" class="text-decoration-none">Browse Crops</a>
            </li>
            <li class="breadcrumb-item active">{{ $crop->name }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <!-- Crop Header -->
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h1 class="h2 mb-2">{{ $crop->name }}</h1>
                            <div class="d-flex gap-2 mb-3">
                                <span class="badge bg-primary">
                                    <i class="fas fa-tag me-1"></i>{{ $crop->category }}
                                </span>
                                <span class="badge bg-info">
                                    <i class="fas fa-map-marker-alt me-1"></i>{{ $crop->region }}
                                </span>
                                @if($crop->is_available)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check me-1"></i>Available
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times me-1"></i>Not Available
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="text-end">
                            <h3 class="text-success mb-0">{{ $crop->formatted_price }}/kg</h3>
                            <p class="text-muted">{{ $crop->available_quantity }} kg available</p>
                        </div>
                    </div>

                    <!-- Crop Image -->
                    <div class="mb-4">
                        <img src="{{ $crop->image_url }}" class="img-fluid rounded" alt="{{ $crop->name }}" 
                             style="max-height: 400px; width: 100%; object-fit: cover;">
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <h5><i class="fas fa-info-circle me-2"></i>Description</h5>
                        <p>{{ $crop->description }}</p>
                    </div>

                    <!-- Farmer Information -->
                    <div class="mb-4">
                        <h5><i class="fas fa-user me-2"></i>Farm Information</h5>
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="fas fa-user-tie fa-lg text-success"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $crop->farmer->name }}</h6>
                                        <p class="text-muted mb-0">
                                            <i class="fas fa-map-marker-alt me-1"></i>{{ $crop->farmer->region }}
                                            @if($crop->farmer->phone)
                                                <span class="ms-3">
                                                    <i class="fas fa-phone me-1"></i>{{ $crop->farmer->phone }}
                                                </span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Add to Cart Form -->
                    @if($crop->is_available && $crop->available_quantity > 0)
                        <div class="card border-success">
                            <div class="card-body">
                                <h5 class="card-title text-success">
                                    <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                                </h5>
                                <form action="{{ route('buyer.crops.add-to-cart', $crop) }}" method="POST">
                                    @csrf
                                    <div class="row align-items-end">
                                        <div class="col-md-6">
                                            <label for="quantity" class="form-label">Quantity (kg)</label>
                                            <input type="number" class="form-control" id="quantity" name="quantity" 
                                                   value="1" min="0.1" max="{{ $crop->available_quantity }}" step="0.1" required>
                                            <small class="text-muted">Maximum: {{ $crop->available_quantity }} kg</small>
                                        </div>
                                        <div class="col-md-6">
                                            <button type="submit" class="btn btn-success w-100">
                                                <i class="fas fa-cart-plus me-2"></i>Add to Cart
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            This crop is currently not available or out of stock.
                        </div>
                    @@endif
                    
                    <!-- Chat with Farmer -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="fas fa-comments me-2"></i>Chat with Farmer
                            </h5>
                            <p class="text-muted mb-3">Have questions about this crop? Start a conversation with the farmer.</p>
                            <a href="{{ route('chats.start.crop', $crop) }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-comment-medical me-2"></i>Start Chat
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Quick Info -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Quick Info</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <strong>Price:</strong> <span class="text-success">{{ $crop->formatted_price }}/kg</span>
                        </li>
                        <li class="mb-2">
                            <strong>Available:</strong> {{ $crop->available_quantity }} kg
                        </li>
                        <li class="mb-2">
                            <strong>Category:</strong> {{ $crop->category }}
                        </li>
                        <li class="mb-2">
                            <strong>Region:</strong> {{ $crop->region }}
                        </li>
                        <li class="mb-2">
                            <strong>Unit:</strong> {{ $crop->unit }}
                        </li>
                        <li class="mb-0">
                            <strong>Listed:</strong> {{ $crop->created_at->format('M d, Y') }}
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Related Crops -->
            @if($relatedCrops->count() > 0)
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Related Crops</h5>
                        <div class="row g-2">
                            @foreach($relatedCrops as $relatedCrop)
                                <div class="col-12">
                                    <div class="card card-sm">
                                        <div class="card-body p-2">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $relatedCrop->image_url }}" alt="{{ $relatedCrop->name }}" 
                                                     class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-0 small">{{ $relatedCrop->name }}</h6>
                                                    <small class="text-muted">{{ $relatedCrop->formatted_price }}/kg</small>
                                                </div>
                                                <a href="{{ route('crops.public.show', $relatedCrop) }}" class="btn btn-sm btn-outline-primary">
                                                    View
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        @endif
        </div>
    </div>
</div>
@endsection
