<!DOCTYPE html>
<html>
<head>
    @include('home.css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .chat-container {
            max-width: 1000px;
            margin: 30px auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .chat-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .chat-header-info h4 {
            margin: 0;
            font-size: 18px;
        }
        .chat-header-info p {
            margin: 5px 0 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        .whatsapp-btn {
            background: #25D366;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s;
        }
        .whatsapp-btn:hover {
            background: #20bd5a;
            color: white;
            transform: scale(1.05);
        }
        .chat-messages {
            height: 500px;
            overflow-y: auto;
            padding: 20px;
            background: #f8f9fa;
        }
        .message {
            margin-bottom: 15px;
            display: flex;
            align-items: flex-end;
        }
        .message.sent {
            justify-content: flex-end;
        }
        .message-bubble {
            max-width: 70%;
            padding: 12px 18px;
            border-radius: 18px;
            word-wrap: break-word;
        }
        .message.received .message-bubble {
            background: white;
            color: #333;
            border-bottom-left-radius: 4px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .message.sent .message-bubble {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom-right-radius: 4px;
        }
        .message-time {
            font-size: 11px;
            margin-top: 5px;
            opacity: 0.7;
        }
        .chat-input-container {
            padding: 20px;
            background: white;
            border-top: 1px solid #eee;
        }
        .chat-input-form {
            display: flex;
            gap: 10px;
        }
        .chat-input-form input {
            flex: 1;
            padding: 12px 20px;
            border: 1px solid #ddd;
            border-radius: 25px;
            font-size: 14px;
        }
        .chat-input-form button {
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
        }
        .chat-input-form button:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="hero_area">
        @include('home.header')
    </div>

    <div class="chat-container">
        <div class="chat-header">
            <div class="chat-header-info">
                <h4>{{ $landlord->name }}</h4>
                <p><i class="fa fa-home"></i> {{ $house->title }} - {{ $house->location }}</p>
                <p><i class="fa fa-tag"></i> KES {{ number_format($house->price) }}</p>
            </div>
            @if($whatsappUrl)
                <a href="{{ $whatsappUrl }}" target="_blank" class="whatsapp-btn">
                    <i class="fa fa-whatsapp"></i> WhatsApp
                </a>
            @endif
        </div>

        <div class="chat-messages" id="chatMessages">
            @forelse($messages as $message)
                <div class="message {{ $message->sender_id == Auth::id() ? 'sent' : 'received' }}">
                    <div>
                        <div class="message-bubble">
                            {{ $message->message }}
                        </div>
                        <div class="message-time {{ $message->sender_id == Auth::id() ? 'text-end' : '' }}">
                            {{ $message->created_at->format('h:i A') }}
                            @if($message->sender_id == Auth::id() && $message->is_read)
                                <i class="fa fa-check-double text-primary"></i>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-5">
                    <i class="fa fa-comments fa-3x mb-3"></i>
                    <p>No messages yet. Start the conversation!</p>
                </div>
            @endforelse
        </div>

        <div class="chat-input-container">
            <form class="chat-input-form" id="chatForm">
                @csrf
                <input type="hidden" name="house_id" value="{{ $house->id }}">
                <input type="hidden" name="receiver_id" value="{{ $landlord->id }}">
                <input type="text" name="message" id="messageInput" placeholder="Type your message..." required autocomplete="off">
                <button type="submit">
                    <i class="fa fa-paper-plane"></i> Send
                </button>
            </form>
        </div>
    </div>

    @include('home.footer')

    <script>
        // Auto-scroll to bottom
        const chatMessages = document.getElementById('chatMessages');
        chatMessages.scrollTop = chatMessages.scrollHeight;

        // Handle form submission with AJAX
        document.getElementById('chatForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const messageInput = document.getElementById('messageInput');
            const message = messageInput.value.trim();

            if (!message) return;

            // Disable input while sending
            messageInput.disabled = true;

            fetch('{{ url("/chat/send") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Add message to chat
                    const messageHTML = `
                        <div class="message sent">
                            <div>
                                <div class="message-bubble">
                                    ${message}
                                </div>
                                <div class="message-time text-end">
                                    Just now
                                </div>
                            </div>
                        </div>
                    `;
                    chatMessages.insertAdjacentHTML('beforeend', messageHTML);
                    chatMessages.scrollTop = chatMessages.scrollHeight;

                    // Clear input
                    messageInput.value = '';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to send message. Please try again.');
            })
            .finally(() => {
                messageInput.disabled = false;
                messageInput.focus();
            });
        });

        // Poll for new messages every 5 seconds
        setInterval(function() {
            fetch('{{ url("/chat/messages/{$house->id}/{$landlord->id}") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update messages (you can implement a more sophisticated diff check)
                        // For now, we'll just let users refresh manually
                    }
                });
        }, 5000);
    </script>
</body>
</html>
