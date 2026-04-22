@extends('layouts.marketplace')

@section('title', 'Start New Chat')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-comment-medical me-2 text-success"></i>Start New Chat
            </h1>
            <p class="text-muted mb-0">
            @if(Auth::user()->isBuyer())
                Send a message to {{ $farmer->name }}
            @else
                Send a message to {{ $buyer->name }}
            @endif
        </p>
        </div>
        <div>
            <a href="{{ Auth::user()->isBuyer() ? route('buyer.chats.index') : route('farmer.chats.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-comments me-2"></i>Back to Messages
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <!-- User Info -->
            <div class="card mb-4">
                <div class="card-body text-center">
                    @if(Auth::user()->isBuyer())
                        <img src="{{ $farmer->avatar ?? '/images/default-avatar.png' }}" 
                             alt="{{ $farmer->name }}" 
                             class="rounded-circle mb-3" 
                             style="width: 100px; height: 100px; object-fit: cover;">
                        <h5 class="mb-2">{{ $farmer->name }}</h5>
                        <div class="mb-2">
                            {!! $farmer->role_badge !!}
                        </div>
                        <div class="text-muted">
                            @if($farmer->region)
                                <p class="mb-1">
                                    <i class="fas fa-map-marker-alt me-1"></i>{{ $farmer->region }}
                                </p>
                            @endif
                            @if($farmer->phone)
                                <p class="mb-1">
                                    <i class="fas fa-phone me-1"></i>{{ $farmer->phone }}
                                </p>
                            @endif
                        </div>
                    @else
                        <img src="{{ $buyer->avatar ?? '/images/default-avatar.png' }}" 
                             alt="{{ $buyer->name }}" 
                             class="rounded-circle mb-3" 
                             style="width: 100px; height: 100px; object-fit: cover;">
                        <h5 class="mb-2">{{ $buyer->name }}</h5>
                        <div class="mb-2">
                            {!! $buyer->role_badge !!}
                        </div>
                        <div class="text-muted">
                            @if($buyer->region)
                                <p class="mb-1">
                                    <i class="fas fa-map-marker-alt me-1"></i>{{ $buyer->region }}
                                </p>
                            @endif
                            @if($buyer->phone)
                                <p class="mb-1">
                                    <i class="fas fa-phone me-1"></i>{{ $buyer->phone }}
                                </p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            @if(isset($crop))
                <!-- Crop Info -->
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-3">About this Crop</h6>
                        <div class="d-flex align-items-center mb-3">
                            <img src="{{ $crop->image_url }}" alt="{{ $crop->name }}" 
                                 class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">
                            <div>
                                <h6 class="mb-1">{{ $crop->name }}</h6>
                                <small class="text-muted">{{ $crop->category }}</small>
                            </div>
                        </div>
                        <div class="text-success fw-bold">
                            {{ $crop->formatted_price }}/kg
                        </div>
                        <div class="text-muted">
                            {{ $crop->available_quantity }} kg available
                        </div>
                    </div>
                </div>
            @endif

            @if(isset($order))
                <!-- Order Info -->
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-3">About this Order</h6>
                        <div class="mb-2">
                            <strong>Order #{{ $order->order_number }}</strong>
                        </div>
                        <div class="text-muted">
                            <small>Placed on {{ $order->created_at->format('M d, Y') }}</small>
                        </div>
                        <div class="text-success fw-bold">
                            {{ $order->formatted_total }}
                        </div>
                        <div class="text-muted">
                            {{ $order->orderItems->count() }} items
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-8">
            <!-- Chat Form -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ Auth::user()->isBuyer() ? route('buyer.chats.create') : route('farmer.chats.create') }}" method="POST">
                        @csrf
                        @if(Auth::user()->isBuyer())
                            <input type="hidden" name="farmer_id" value="{{ $farmer->id }}">
                        @else
                            <input type="hidden" name="buyer_id" value="{{ $buyer->id }}">
                        @endif
                        @if(isset($crop))
                            <input type="hidden" name="crop_id" value="{{ $crop->id }}">
                        @endif
                        @if(isset($order))
                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                        @endif
                        
                        <div class="mb-4">
                            <label for="subject" class="form-label">Subject *</label>
                            <input type="text" class="form-control @error('subject') is-invalid @enderror" 
                                   id="subject" name="subject" value="{{ old('subject', $subject ?? '') }}" 
                                   required maxlength="255">
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="message" class="form-label">Message *</label>
                            <textarea class="form-control @error('message') is-invalid @enderror" 
                                      id="message" name="message" rows="6" required maxlength="1000"
                                      placeholder="Type your message here...">{{ old('message') }}</textarea>
                            <div class="form-text">
                                Maximum 1000 characters
                            </div>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-paper-plane me-2"></i>Send Message
                            </button>
                            <a href="{{ Auth::user()->isBuyer() ? route('buyer.chats.index') : route('farmer.chats.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Message Tips -->
            <div class="card mt-4">
                <div class="card-body">
                    <h6 class="mb-3">
                        <i class="fas fa-lightbulb me-2"></i>Tips for Good Communication
                    </h6>
                    <ul class="mb-0">
                        <li>Be clear and specific about your questions</li>
                        <li>Ask about product quality, availability, and delivery options</li>
                        <li>Be respectful and professional in your communication</li>
                        <li>Response time may vary based on farmer's schedule</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
