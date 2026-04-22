@extends('layouts.marketplace')

@section('title', 'Chat - ' . $chat->subject)

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ Auth::user()->isBuyer() ? route('buyer.chats.index') : route('farmer.chats.index') }}" class="btn btn-outline-secondary me-3">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="d-inline-block">
                <h1 class="h3 mb-0 d-inline-block">{{ $chat->getDisplayName() }}</h1>
                <div class="d-inline-block ms-3">
                    {!! $chat->buyer->role_badge !!}
                </div>
            </div>
        </div>
        <div>
            <button class="btn btn-outline-danger" 
                    onclick="event.preventDefault(); 
                           Swal.fire({
                               title: 'Are you sure?',
                               text: 'Do you want to archive this chat?',
                               icon: 'warning',
                               showCancelButton: true,
                               confirmButtonColor: '#dc3545',
                               cancelButtonColor: '#6c757d',
                               confirmButtonText: 'Yes, archive it!'
                           }).then((result) => {
                               if (result.isConfirmed) {
                                   window.location.href = '{{ Auth::user()->isBuyer() ? route('buyer.chats.delete', $chat) : route('farmer.chats.delete', $chat) }}';
                               }
                           })">
                <i class="fas fa-archive me-2"></i>Archive Chat
            </button>
        </div>
    </div>

    <!-- Chat Subject -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="mb-2">{{ $chat->subject }}</h5>
            <div class="text-muted">
                @if($chat->crop)
                    <span class="me-3">
                        <i class="fas fa-carrot text-success me-1"></i>{{ $chat->crop->name }}
                    </span>
                @endif
                @if($chat->order)
                    <span class="me-3">
                        <i class="fas fa-shopping-bag text-info me-1"></i>Order #{{ $chat->order->order_number }}
                    </span>
                @endif
                <span>
                    <i class="fas fa-calendar me-1"></i>Started {{ $chat->created_at->format('M d, Y') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Messages Container -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="messages-container" style="max-height: 500px; overflow-y: auto;">
                @if($chat->messages->count() > 0)
                    @foreach($chat->messages as $message)
                        <div class="d-flex {{ $message->getAlignmentClass() }} mb-3">
                            @if(!$message->isFromCurrentUser())
                                <img src="{{ $message->sender->avatar ? asset('storage/avatars/' . $message->sender->avatar) : asset('images/default-avatar.png') }}" 
                                     alt="{{ $message->sender_name }}" 
                                     class="rounded-circle me-2" 
                                     style="width: 40px; height: 40px; object-fit: cover;">
                            @endif
                            
                            <div class="{{ $message->isFromCurrentUser() ? 'text-end' : '' }}">
                                <div class="d-inline-block">
                                    @if(!$message->isFromCurrentUser())
                                        <small class="text-muted d-block mb-1">
                                            {{ $message->sender_name }} {!! $message->sender_role_badge !!}
                                        </small>
                                    @endif
                                    <div class="message-bubble {{ $message->getBackgroundClass() }}" 
                                         style="padding: 10px 15px; border-radius: 15px; max-width: 300px;">
                                        <p class="mb-0 {{ $message->getTextClass() }}">{{ $message->content }}</p>
                                    </div>
                                    <small class="text-muted d-block mt-1">
                                        {{ $message->formatted_time }}
                                        @if($message->isFromCurrentUser())
                                            @if($message->is_read)
                                                <i class="fas fa-check-double text-info ms-1"></i>
                                            @else
                                                <i class="fas fa-check text-muted ms-1"></i>
                                            @endif
                                        @endif
                                    </small>
                                </div>
                            </div>
                            
                            @if($message->isFromCurrentUser())
                                <img src="{{$message->sender->avatar ? asset('storage/avatars/' . $message->sender->avatar) : asset('images/default-avatar.png') }}" 
                                     alt="{{ $message->sender_name }}" 
                                     class="rounded-circle ms-2" 
                                     style="width: 40px; height: 40px; object-fit: cover;">
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-comments fa-3x mb-3"></i>
                        <p>No messages yet. Start the conversation!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Message Form -->
    <div class="card">
        <div class="card-body">
            <form action="{{ Auth::user()->isBuyer() ? route('buyer.chats.send-message', $chat) : route('farmer.chats.send-message', $chat) }}" method="POST" id="messageForm">
                @csrf
                <div class="input-group">
                    <input type="text" class="form-control" name="content" id="messageInput" 
                           placeholder="Type your message..." required maxlength="1000">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-paper-plane me-2"></i>Send
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.message-bubble {
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    word-wrap: break-word;
}

.messages-container {
    padding: 20px;
    background-color: #f8f9fa;
    border-radius: 10px;
}

.messages-container::-webkit-scrollbar {
    width: 6px;
}

.messages-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.messages-container::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

.messages-container::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>

<script>
// Auto-scroll to bottom on load
document.addEventListener('DOMContentLoaded', function() {
    const messagesContainer = document.querySelector('.messages-container');
    if (messagesContainer) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
});

// Auto-scroll to bottom after form submission
document.getElementById('messageForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    
    // Show loading state
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
    submitButton.disabled = true;
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        // Check if response is JSON or HTML redirect
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else {
            // It's a redirect response, reload the page
            window.location.reload();
            throw new Error('Redirecting...');
        }
    })
    .then(data => {
        if (data && data.success) {
            // Clear input
            document.getElementById('messageInput').value = '';
            
            // Show success message
            toastr.success('Message sent successfully!');
            
            // Reload page to show new message
            window.location.reload();
        } else if (data) {
            // Show error
            toastr.error(data.message || 'Failed to send message');
        }
    })
    .catch(error => {
        // Don't show error if it's a redirect
        if (error.message !== 'Redirecting...') {
            toastr.error('Failed to send message');
        }
    })
    .finally(() => {
        // Restore button
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
    });
});
</script>
@endsection
