<!DOCTYPE html>
<html>
<head>
    @include('admin.css')
    <style>
        .chat-container {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        .chat-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            text-align: center;
        }
        .chat-header h5 {
            font-weight: 600;
            margin-bottom: 5px;
        }
        .chat-header p {
            font-size: 0.9rem;
        }
        .chat-messages {
            padding: 25px;
            max-height: 600px;
            overflow-y: auto;
            background: #f8f9fa;
        }
        .message {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            transition: transform 0.2s ease;
        }
        .message.sent {
            align-items: flex-end;
        }
        .message.received {
            align-items: flex-start;
        }
        .message-content {
            max-width: 75%;
        }
        .message-header {
            font-size: 13px;
            margin-bottom: 5px;
            color: #666;
        }
        .message-bubble {
            padding: 12px 18px;
            border-radius: 18px;
            word-wrap: break-word;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .message.received .message-bubble {
            background: #ffffff;
            color: #333;
            border-bottom-left-radius: 4px;
        }
        .message.sent .message-bubble {
            background: #667eea;
            color: white;
            border-bottom-right-radius: 4px;
        }
        .message-footer {
            font-size: 11px;
            margin-top: 5px;
            color: #999;
        }
        .delete-btn {
            font-size: 11px;
            padding: 3px 8px;
            margin-left: 8px;
        }
        .badge {
            font-size: 10px;
            padding: 4px 8px;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    @include('admin.header')

    <div class="d-flex align-items-stretch">
        @include('admin.sidebar')

        <div class="page-content py-5">
            <div class="container-fluid">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Conversation Details</h5>
                            <a href="{{ url('/admin/chat') }}" class="btn btn-light btn-sm">
                                <i class="fa fa-arrow-left"></i> Back to All Chats
                            </a>
                        </div>
                    </div>

                    <div class="chat-container mt-3">
                        <div class="chat-header">
                            <h5>Conversation Between</h5>
                            <p class="mb-1">
                                <i class="fa fa-user"></i> {{ $sender->name }}
                                <i class="fa fa-arrow-right mx-2"></i>
                                <i class="fa fa-user"></i> {{ $receiver->name }}
                            </p>
                            <p class="mb-0">
                                <i class="fa fa-home"></i> {{ $house->title }} - {{ $house->location }}
                            </p>
                        </div>

                        <div class="chat-messages">
                            @forelse($messages as $message)
                                <div class="message {{ $message->sender_id == $sender->id ? 'sent' : 'received' }}">
                                    <div class="message-content">
                                        <div class="message-header">
                                            <strong>{{ $message->sender->name }}</strong>
                                            @if($message->sender_id == $sender->id)
                                                <span class="badge bg-primary">Sender</span>
                                            @else
                                                <span class="badge bg-success">Receiver</span>
                                            @endif
                                        </div>
                                        <div class="message-bubble">
                                            {{ $message->message }}
                                        </div>
                                        <div class="message-footer">
                                            {{ $message->created_at->format('M d, Y h:i A') }}
                                            @if($message->is_read)
                                                <i class="fa fa-check-double text-primary ms-1"></i> Read
                                            @else
                                                <i class="fa fa-check text-muted ms-1"></i> Sent
                                            @endif

                                            <form action="{{ url('/admin/chat/delete/' . $message->id) }}"
                                                  method="POST"
                                                  style="display: inline;"
                                                  onsubmit="return confirm('Are you sure you want to delete this message?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm delete-btn">
                                                    <i class="fa fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5">
                                    <i class="fa fa-comments fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No messages in this conversation</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.js')

    <script>
        // Auto-scroll to bottom
        const chatMessages = document.querySelector('.chat-messages');
        chatMessages.scrollTop = chatMessages.scrollHeight;
    </script>
</body>
</html>
