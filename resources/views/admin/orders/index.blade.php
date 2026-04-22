@extends('layouts.marketplace')

@section('title', 'Manage Orders')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-shopping-bag me-2 text-success"></i>Manage Orders
            </h1>
            <p class="text-muted mb-0">Track and manage all platform orders</p>
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
                    <a class="nav-link {{ request('status') == null ? 'active' : '' }}" 
                       href="{{ route('admin.orders') }}">
                        All Orders ({{ $statusCounts['total'] }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('status') == 'pending' ? 'active' : '' }}" 
                       href="{{ route('admin.orders', ['status' => 'pending']) }}">
                        <i class="fas fa-clock me-1"></i>Pending ({{ $statusCounts['pending'] }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('status') == 'confirmed' ? 'active' : '' }}" 
                       href="{{ route('admin.orders', ['status' => 'confirmed']) }}">
                        <i class="fas fa-check me-1"></i>Confirmed ({{ $statusCounts['confirmed'] }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('status') == 'delivered' ? 'active' : '' }}" 
                       href="{{ route('admin.orders', ['status' => 'delivered']) }}">
                        <i class="fas fa-truck me-1"></i>Delivered ({{ $statusCounts['delivered'] }})
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.orders') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Search Orders</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Search by order number or buyer...">
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="buyer" class="form-label">Buyer</label>
                        <select class="form-select" id="buyer" name="buyer">
                            <option value="">All Buyers</option>
                            @foreach($buyers as $buyer)
                                <option value="{{ $buyer->id }}" {{ request('buyer') == $buyer->id ? 'selected' : '' }}>
                                    {{ $buyer->name }}
                                </option>
                            @endforeach
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
                            <a href="{{ route('admin.orders') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Clear
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    @if($orders->count() > 0)
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Buyer</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td>
                                        <strong>{{ $order->order_number }}</strong>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $order->buyer_name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $order->buyer_email }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $order->orderItems->count() }} items</strong>
                                            <br>
                                            <small class="text-muted">
                                                @foreach($order->orderItems->take(2) as $item)
                                                    {{ $item->crop->name }}{{ !$loop->last ? ', ' : '' }}
                                                @endforeach
                                                @if($order->orderItems->count() > 2)
                                                    +{{ $order->orderItems->count() - 2 }} more
                                                @endif
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <strong class="text-success">{{ $order->formatted_total }}</strong>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $order->created_at->format('M d, Y') }}</small>
                                    </td>
                                    <td>
                                        {!! $order->status_badge !!}
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($order->status === 'pending')
                                                <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="confirmed">
                                                    <button type="submit" class="btn btn-sm btn-outline-success" 
                                                            title="Mark as Confirmed">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @elseif($order->status === 'confirmed')
                                                <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="delivered">
                                                    <button type="submit" class="btn btn-sm btn-outline-info" 
                                                            title="Mark as Delivered">
                                                        <i class="fas fa-truck"></i>
                                                    </button>
                                                </form>
                                            @endif
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
            {{ $orders->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-shopping-bag fa-4x text-muted mb-3"></i>
            <h4 class="text-muted mb-3">No orders found</h4>
            <p class="text-muted mb-4">Try adjusting your search criteria or check back later.</p>
            <a href="{{ route('admin.orders') }}" class="btn btn-success btn-lg">
                <i class="fas fa-redo me-2"></i>Clear Filters
            </a>
        </div>
    @endif
</div>
@endsection
