@extends('layouts.marketplace')

@section('title', 'Chat Assistant')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-robot me-2"></i>Chat Assistant
                    </h5>
                </div>
                <div class="card-body">
                    <div id="chat-messages" class="chat-messages mb-3" style="height: 400px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 8px; padding: 15px; background: #f8f9fa;">
                        <!-- Welcome message -->
                        <div class="chat-message bot-message mb-2">
                            <div class="d-flex align-items-start">
                                <div class="bot-avatar me-2">
                                    <i class="fas fa-robot"></i>
                                </div>
                                <div class="message-content">
                                    <strong>FarmMarket Assistant</strong>
                                    <p>Hello! I'm here to help you with questions about the FarmMarket platform. How can I assist you today?</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Message input -->
                    <div class="input-group">
                        <input type="text" id="chat-input" class="form-control" 
                               placeholder="Type your message here..." 
                               maxlength="500">
                        <button class="btn btn-primary" id="send-btn" type="button">
                            <i class="fas fa-paper-plane me-1"></i>Send
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.chat-widget {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 350px;
    height: 500px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    z-index: 1000;
    display: none;
}

.chat-widget.open {
    display: block;
}

.chat-header {
    background: linear-gradient(135deg, #28a745, #0f4c81);
    color: white;
    padding: 15px;
    border-radius: 12px 12px 0 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.chat-messages {
    background: #f8f9fa;
    border-radius: 8px;
}

.chat-message {
    margin-bottom: 15px;
    display: flex;
    align-items: flex-start;
}

.user-message {
    background: #007bff;
    color: white;
    margin-left: auto;
    border-radius: 18px;
}

.bot-message {
    background: #6c757d;
    color: white;
    margin-right: auto;
    border-radius: 18px;
}

.message-content {
    padding: 12px 15px;
    border-radius: 12px;
    max-width: 280px;
}

.bot-avatar {
    width: 30px;
    height: 30px;
    background: #28a745;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin-right: 10px;
}

.typing-indicator {
    display: none;
    color: #6c757d;
    font-style: italic;
    font-size: 0.9em;
    margin-top: 5px;
}

.chat-toggle {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #28a745;
    color: white;
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

@media (max-width: 768px) {
    .chat-widget {
        width: 300px;
        height: 450px;
        bottom: 10px;
        right: 10px;
    }
}
</style>
@endpush

@push('scripts')
<script>
let chatWidget = document.createElement('div');
chatWidget.className = 'chat-widget';
chatWidget.innerHTML = `
    <div class="chat-header">
        <div class="d-flex align-items-center">
            <h6 class="mb-0">Chat Assistant</h6>
            <button class="chat-toggle" onclick="toggleChat()">
                <i class="fas fa-comments"></i>
            </button>
        </div>
    </div>
    <div class="chat-messages" id="widget-messages" style="height: 300px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 8px; padding: 15px; background: #f8f9fa;">
        <div class="text-center text-muted py-3">
            <i class="fas fa-robot"></i>
            <p>How can I help you today?</p>
        </div>
    </div>
    <div class="p-2">
        <div class="input-group">
            <input type="text" id="widget-chat-input" class="form-control" 
                   placeholder="Type your message..." 
                   maxlength="500">
            <button class="btn btn-primary btn-sm" onclick="sendWidgetMessage()" type="button">
                <i class="fas fa-paper-plane me-1"></i>
            </button>
        </div>
    </div>
`;

document.body.appendChild(chatWidget);

let isChatOpen = false;

function toggleChat() {
    isChatOpen = !isChatOpen;
    chatWidget.style.display = isChatOpen ? 'block' : 'none';
}

function sendWidgetMessage() {
    const input = document.getElementById('widget-chat-input');
    const message = input.value.trim();
    
    if (!message) return;
    
    // Show typing indicator
    showTypingIndicator();
    
    fetch('/chatbot/send', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            message: message
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            addWidgetMessage(data.response, 'bot');
            input.value = '';
        } else {
            addWidgetMessage('Sorry, I encountered an error. Please try again.', 'bot');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        addWidgetMessage('Connection error. Please try again.', 'bot');
    })
    .finally(() => {
        hideTypingIndicator();
    });
}

function addWidgetMessage(message, sender = 'user') {
    const messagesDiv = document.getElementById('widget-messages');
    const messageDiv = document.createElement('div');
    messageDiv.className = `chat-message ${sender}-message mb-2`;
    
    const avatarDiv = document.createElement('div');
    avatarDiv.className = sender === 'user' ? 'user-avatar me-2' : 'bot-avatar me-2';
    avatarDiv.innerHTML = sender === 'user' ? 
        '<i class="fas fa-user"></i>' : 
        '<i class="fas fa-robot"></i>';
    
    const contentDiv = document.createElement('div');
    contentDiv.className = 'message-content';
    contentDiv.innerHTML = `
        <div class="d-flex align-items-start">
            ${avatarDiv.outerHTML}
            <div class="message-content">
                ${sender === 'user' ? message : `<strong>Assistant:</strong><p>${message}</p>`}
            </div>
        </div>
    `;
    
    messageDiv.appendChild(avatarDiv);
    messageDiv.appendChild(contentDiv);
    
    messagesDiv.appendChild(messageDiv);
    messagesDiv.scrollTop = messagesDiv.scrollHeight;
}

function showTypingIndicator() {
    const messagesDiv = document.getElementById('widget-messages');
    const typingDiv = document.createElement('div');
    typingDiv.className = 'typing-indicator';
    typingDiv.innerHTML = '<i class="fas fa-robot me-2"></i> Assistant is typing...';
    
    messagesDiv.appendChild(typingDiv);
    messagesDiv.scrollTop = messagesDiv.scrollHeight;
}

function hideTypingIndicator() {
    const typingIndicator = document.querySelector('.typing-indicator');
    if (typingIndicator) {
        typingIndicator.remove();
    }
}

// Handle Enter key in widget input
document.getElementById('widget-chat-input').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        sendWidgetMessage();
    }
});
</script>
@endpush
