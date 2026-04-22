<!-- Chatbot Widget -->
<div id="chatbot-widget" class="chatbot-widget">
    <!-- Chat Toggle Button -->
    <button id="chatbot-toggle" class="chatbot-toggle">
        <i class="fas fa-comments"></i>
    </button>
    
    <!-- Chat Window -->
    <div id="chatbot-window" class="chatbot-window">
        <div class="chatbot-header">
            <div class="d-flex align-items-center">
                <i class="fas fa-robot me-2"></i>
                <h6 class="mb-0">FarmMarket Assistant</h6>
            </div>
            <button id="chatbot-close" class="chatbot-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="chatbot-messages" id="chatbot-messages">
            <div class="chat-message bot-message">
                <div class="message-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="message-content">
                    <strong>FarmMarket Assistant</strong>
                    <p>Hello! I'm here to help you with questions about the FarmMarket platform. How can I assist you today?</p>
                </div>
            </div>
        </div>
        
        <div class="chatbot-input">
            <div class="input-group">
                <input type="text" id="chatbot-input" class="form-control" 
                       placeholder="Type your message..." 
                       maxlength="500">
                <button id="chatbot-send" class="btn btn-primary" type="button">
                    <i class="fas fa-paper-plane me-1"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.chatbot-widget {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1000;
}

.chatbot-toggle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #28a745, #0f4c81);
    border: none;
    color: white;
    font-size: 24px;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.chatbot-toggle:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
}

.chatbot-window {
    position: absolute;
    bottom: 80px;
    right: 0;
    width: 350px;
    height: 500px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    display: none;
    flex-direction: column;
    overflow: hidden;
}

.chatbot-window.open {
    display: flex;
}

.chatbot-header {
    background: linear-gradient(135deg, #28a745, #0f4c81);
    color: white;
    padding: 15px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.chatbot-close {
    background: none;
    border: none;
    color: white;
    font-size: 18px;
    cursor: pointer;
    padding: 5px;
    border-radius: 3px;
    transition: background 0.2s;
}

.chatbot-close:hover {
    background: rgba(255, 255, 255, 0.2);
}

.chatbot-messages {
    flex: 1;
    padding: 15px;
    overflow-y: auto;
    background: #f8f9fa;
}

.chat-message {
    display: flex;
    margin-bottom: 15px;
    align-items: flex-start;
}

.bot-message {
    justify-content: flex-start;
}

.user-message {
    justify-content: flex-end;
}

.message-avatar {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 10px;
    font-size: 14px;
}

.bot-message .message-avatar {
    background: #28a745;
    color: white;
}

.user-message .message-avatar {
    background: #007bff;
    color: white;
    margin-right: 0;
    margin-left: 10px;
}

.message-content {
    max-width: 70%;
    background: white;
    padding: 12px 15px;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.user-message .message-content {
    background: #007bff;
    color: white;
}

.message-content p {
    margin: 0;
    font-size: 14px;
    line-height: 1.4;
}

.message-content strong {
    font-size: 12px;
    color: #6c757d;
    display: block;
    margin-bottom: 5px;
}

.user-message .message-content strong {
    color: rgba(255, 255, 255, 0.8);
}

.chatbot-input {
    padding: 15px;
    border-top: 1px solid #dee2e6;
    background: white;
}

.typing-indicator {
    display: none;
    color: #6c757d;
    font-style: italic;
    font-size: 12px;
    margin: 5px 0;
}

.typing-indicator.show {
    display: block;
}

@media (max-width: 768px) {
    .chatbot-window {
        width: 300px;
        height: 450px;
        bottom: 70px;
    }
    
    .chatbot-toggle {
        width: 50px;
        height: 50px;
        font-size: 20px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('chatbot-toggle');
    const closeBtn = document.getElementById('chatbot-close');
    const chatWindow = document.getElementById('chatbot-window');
    const sendBtn = document.getElementById('chatbot-send');
    const input = document.getElementById('chatbot-input');
    const messages = document.getElementById('chatbot-messages');
    
    let isOpen = false;
    
    // Toggle chat window
    toggleBtn.addEventListener('click', function() {
        isOpen = !isOpen;
        chatWindow.classList.toggle('open', isOpen);
        if (isOpen) {
            input.focus();
        }
    });
    
    // Close chat window
    closeBtn.addEventListener('click', function() {
        isOpen = false;
        chatWindow.classList.remove('open');
    });
    
    // Send message
    function sendMessage() {
        const message = input.value.trim();
        if (!message) return;
        
        // Add user message
        addMessage(message, 'user');
        input.value = '';
        
        // Show typing indicator
        showTypingIndicator();
        
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        // Send to bot
        fetch('/chatbot/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                message: message
            })
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            hideTypingIndicator();
            if (data.success) {
                addMessage(data.response, 'bot', data.conversation_id);
            } else {
                addMessage('Sorry, I encountered an error. Please try again.', 'bot');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            hideTypingIndicator();
            addMessage('Connection error. Please try again.', 'bot');
        });
    }
    
    sendBtn.addEventListener('click', sendMessage);
    
    input.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });
    
    function addMessage(text, sender, conversationId = null) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `chat-message ${sender}-message`;
        
        const avatarDiv = document.createElement('div');
        avatarDiv.className = 'message-avatar';
        avatarDiv.innerHTML = sender === 'user' ? 
            '<i class="fas fa-user"></i>' : 
            '<i class="fas fa-robot"></i>';
        
        const contentDiv = document.createElement('div');
        contentDiv.className = 'message-content';
        
        if (sender === 'user') {
            contentDiv.innerHTML = `<p>${text}</p>`;
        } else {
            // Add feedback buttons for bot messages
            const feedbackDiv = conversationId ? `
                <div class="message-feedback">
                    <small class="text-muted">Was this helpful?</small>
                    <div class="btn-group btn-group-sm mt-1">
                        <button class="btn btn-outline-success btn-sm feedback-btn" data-conversation="${conversationId}" data-helpful="true" data-rating="5">
                            <i class="fas fa-thumbs-up"></i>
                        </button>
                        <button class="btn btn-outline-danger btn-sm feedback-btn" data-conversation="${conversationId}" data-helpful="false" data-rating="1">
                            <i class="fas fa-thumbs-down"></i>
                        </button>
                    </div>
                </div>
            ` : '';
            
            contentDiv.innerHTML = `
                <strong>Assistant</strong>
                <p>${text}</p>
                ${feedbackDiv}
            `;
        }
        
        messageDiv.appendChild(avatarDiv);
        messageDiv.appendChild(contentDiv);
        
        messages.appendChild(messageDiv);
        messages.scrollTop = messages.scrollHeight;
    }
    
    // Submit feedback function
    function submitFeedback(conversationId, wasHelpful, rating) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        fetch(`/chatbot/feedback/${conversationId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                was_helpful: wasHelpful,
                rating: rating
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove feedback buttons after submission
                const feedbackDivs = document.querySelectorAll('.message-feedback');
                feedbackDivs.forEach(div => div.remove());
                
                // Show thank you message
                addMessage('Thank you for your feedback! This helps me improve.', 'bot');
            }
        })
        .catch(error => {
            console.error('Feedback error:', error);
        });
    }
    
    // Add event listeners for feedback buttons
    document.addEventListener('click', function(e) {
        if (e.target.closest('.feedback-btn')) {
            const btn = e.target.closest('.feedback-btn');
            const conversationId = btn.dataset.conversation;
            const wasHelpful = btn.dataset.helpful === 'true';
            const rating = parseInt(btn.dataset.rating);
            
            submitFeedback(conversationId, wasHelpful, rating);
        }
    });
    
    function showTypingIndicator() {
        const indicator = document.createElement('div');
        indicator.className = 'typing-indicator show';
        indicator.innerHTML = '<i class="fas fa-robot me-2"></i> Assistant is typing...';
        messages.appendChild(indicator);
        messages.scrollTop = messages.scrollHeight;
    }
    
    function hideTypingIndicator() {
        const indicator = document.querySelector('.typing-indicator');
        if (indicator) {
            indicator.remove();
        }
    }
});
</script>
