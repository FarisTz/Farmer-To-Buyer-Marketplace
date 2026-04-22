@extends('layouts.marketplace')

@section('title', $crop->name)

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-carrot me-2 text-success"></i>{{ $crop->name }}
            </h1>
            <p class="text-muted mb-0">Crop details and performance</p>
        </div>
        <div>
            <a href="{{ route('farmer.crops.edit', $crop) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-2"></i>Edit Crop
            </a>
            <a href="{{ route('farmer.crops.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Crops
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <!-- Crop Header -->
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h2 class="h4 mb-2">{{ $crop->name }}</h2>
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

                    <!-- Location Information -->
                    <div class="mb-4">
                        <h5><i class="fas fa-map-marker-alt me-2"></i>Farm Information</h5>
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1">
                                            <strong>Region:</strong> {{ $crop->region }}
                                        </p>
                                        @if($crop->farm_location)
                                            <p class="mb-1">
                                                <strong>Farm Location:</strong> {{ $crop->farm_location }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1">
                                            <strong>Unit:</strong> {{ $crop->unit }}
                                        </p>
                                        <p class="mb-0">
                                            <strong>Listed:</strong> {{ $crop->created_at->format('M d, Y') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Orders -->
                    <div class="mb-4">
                        <h5><i class="fas fa-shopping-bag me-2"></i>Recent Orders</h5>
                        @if($crop->orderItems->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Order #</th>
                                            <th>Buyer</th>
                                            <th>Quantity</th>
                                            <th>Total</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($crop->orderItems->take(5) as $orderItem)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('farmer.orders.show', $orderItem->order) }}" class="text-decoration-none">
                                                        {{ $orderItem->order->order_number }}
                                                    </a>
                                                </td>
                                                <td>{{ $orderItem->order->buyer_name }}</td>
                                                <td>{{ $orderItem->quantity }} kg</td>
                                                <td>{{ $orderItem->formatted_total_price }}</td>
                                                <td>{{ $orderItem->created_at->format('M d, Y') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if($crop->orderItems->count() > 5)
                                <div class="text-center mt-2">
                                    <small class="text-muted">Showing 5 of {{ $crop->orderItems->count() }} orders</small>
                                </div>
                            @endif
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                No orders for this crop yet. Make sure it's marked as available and competitively priced!
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Performance Stats -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-chart-line me-2"></i>Performance Stats
                    </h5>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Total Orders:</span>
                            <strong>{{ $crop->orderItems->count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Total Sold:</span>
                            <strong>{{ $crop->orderItems->sum('quantity') }} kg</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Revenue:</span>
                            <strong class="text-success">₦{{ number_format($crop->orderItems->sum('total_price'), 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Avg Order Size:</span>
                            <strong>{{ $crop->orderItems->count() > 0 ? number_format($crop->orderItems->avg('quantity'), 1) : 0 }} kg</strong>
                        </div>
                    </div>
                    
                    <div class="progress mb-2" style="height: 8px;">
                        @if($crop->available_quantity > 0)
                            <?php $soldPercentage = ($crop->orderItems->sum('quantity') / ($crop->orderItems->sum('quantity') + $crop->available_quantity)) * 100; ?>
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: {{ min($soldPercentage, 100) }}%">
                            </div>
                        @endif
                    </div>
                    <small class="text-muted">
                        {{ $crop->orderItems->sum('quantity') }} kg sold of {{ $crop->orderItems->sum('quantity') + $crop->available_quantity }} kg total
                    </small>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h5>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('farmer.crops.edit', $crop) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Edit Crop
                        </a>
                        
                        @if($crop->is_available)
                            <form action="{{ route('farmer.crops.toggle-availability', $crop) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-warning w-100" 
                                        onclick="return confirm('Are you sure you want to mark this crop as unavailable?')">
                                    <i class="fas fa-pause me-2"></i>Mark Unavailable
                                </button>
                            </form>
                        @else
                            <form action="{{ route('farmer.crops.toggle-availability', $crop) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-success w-100" 
                                        onclick="return confirm('Are you sure you want to mark this crop as available?')">
                                    <i class="fas fa-play me-2"></i>Mark Available
                                </button>
                            </form>
                        @endif
                        
                        <form action="{{ route('farmer.crops.delete', $crop) }}" method="POST" class="d-inline" 
                              onsubmit="return confirm('Are you sure you want to delete this crop? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="fas fa-trash me-2"></i>Delete Crop
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tips -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-lightbulb me-2"></i>Selling Tips
                    </h5>
                    
                    <div class="alert alert-success">
                        <h6 class="alert-heading">
                            <i class="fas fa-star me-2"></i>Optimize Sales
                        </h6>
                        <ul class="mb-0 mt-2 small">
                            <li>Update quantity regularly</li>
                            <li>Competitive pricing helps</li>
                            <li>Good photos attract buyers</li>
                            <li>Quick responses build trust</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
