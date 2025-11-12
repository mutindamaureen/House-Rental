<!DOCTYPE html>
<html>
<head>
    @include('home.css')
    <style>
        .chat-list-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
        }
        .chat-item {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: all 0.3s;
            cursor: pointer;
        }
        .chat-item:hover {
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }
        .chat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 20px;
        }
        .user-details h5 {
            margin: 0;
            color: #333;
        }
        .user-details p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 14px;
        }
        .unread-badge {
            background: #e74c3c;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .last-message {
            color: #666;
            font-size: 14px;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }
        .message-time {
            color: #999;
            font-size: 12px;
        }
        .no-chats {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .no-chats i {
            font-size: 60px;
            color: #ddd;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="hero_area">
        @include('home.header')
    </div>

    <div class="chat-list-container">
        <h2 class="mb-4">My Conversations</h2>

        @if($conversations->isEmpty())
            <div class="no-chats">
                <i class="fa fa-comments"></i>
                <h4>No conversations yet</h4>
                <p>Start chatting with landlords about properties you're interested in.</p>
                <a href="{{ url('/see_house') }}" class="btn btn-primary mt-3">Browse Properties</a>
            </div>
        @else
            @foreach($conversations as $conversation)
                <div class="chat-item" onclick="window.location='{{ url('/chat/' . $conversation['house']->id . '/' . $conversation['other_user']->id) }}'">
                    <div class="chat-header">
                        <div class="user-info">
                            <div class="user-avatar">
                                {{ strtoupper(substr($conversation['other_user']->name, 0, 1)) }}
                            </div>
                            <div class="user-details">
                                <h5>{{ $conversation['other_user']->name }}</h5>
                                <p>
                                    <i class="fa fa-home"></i> {{ $conversation['house']->title }} - {{ $conversation['house']->location }}
                                </p>
                            </div>
                        </div>
                        @if($conversation['unread_count'] > 0)
                            <span class="unread-badge">{{ $conversation['unread_count'] }} new</span>
                        @endif
                    </div>
                    <div class="last-message">
                        <strong>{{ $conversation['last_message']->sender_id == Auth::id() ? 'You' : $conversation['other_user']->name }}:</strong>
                        {{ Str::limit($conversation['last_message']->message, 80) }}
                        <span class="message-time float-end">
                            {{ $conversation['last_message']->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    @include('home.footer')
</body>
</html>
