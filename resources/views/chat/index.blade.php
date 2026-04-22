@extends('layouts.marketplace')

@section('title', 'Messages')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-comments me-2 text-success"></i>Messages
            </h1>
            <p class="text-muted mb-0">Chat with farmers and buyers</p>
        </div>
        <div>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>
        </div>
    </div>

    <!-- Chats List -->
    <div class="card">
        <div class="card-body">
            @if($chats->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($chats as $chat)
                        <a href="{{ Auth::user()->isBuyer() ? route('buyer.chats.show', $chat) : route('farmer.chats.show', $chat) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex align-items-start">
                                <!-- Avatar -->
                                <div class="me-3">
                                    <img src="{{ $chat->getAvatar() }}" alt="{{ $chat->getDisplayName() }}" 
                                         class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                                    @if($chat->unread_count > 0)
                                        <span class="badge bg-danger rounded-pill position-absolute top-0 start-50 translate-middle">
                                            {{ $chat->unread_count }}
                                        </span>
                                    @endif
                                </div>
                                
                                <!-- Chat Info -->
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">{{ $chat->getDisplayName() }}</h6>
                                            <small class="text-muted">
                                                {{ $chat->subject }}
                                                @if($chat->crop)
                                                    <span class="ms-2">
                                                        <i class="fas fa-carrot text-success me-1"></i>{{ $chat->crop->name }}
                                                    </span>
                                                @endif
                                                @if($chat->order)
                                                    <span class="ms-2">
                                                        <i class="fas fa-shopping-bag text-info me-1"></i>Order #{{ $chat->order->order_number }}
                                                    </span>
                                                @endif
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <small class="text-muted">{{ $chat->last_message_at ? $chat->last_message_at->format('M d') : '' }}</small>
                                        </div>
                                    </div>
                                    
                                    @if($chat->last_message)
                                        <div class="mt-2">
                                            <small class="text-muted">{{ Str::limit($chat->last_message, 80) }}</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $chats->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-comments fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted mb-3">No messages yet</h4>
                    <p class="text-muted mb-4">Start chatting with farmers or buyers to see your conversations here.</p>
                    @if(auth()->user()->isBuyer())
                        <a href="{{ route('buyer.crops.browse') }}" class="btn btn-success btn-lg">
                            <i class="fas fa-carrot me-2"></i>Browse Crops to Start Chatting
                        </a>
                    @else
                        <a href="{{ route('farmer.crops.index') }}" class="btn btn-success btn-lg">
                            <i class="fas fa-seedling me-2"></i>Manage Your Crops
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
