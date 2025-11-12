<!DOCTYPE html>
<html>
<head>
    @include('admin.css')
    <style>
        .chat-item {
            border-bottom: 1px solid #eee;
            padding: 15px 0;
            transition: all 0.3s ease;
        }
        .chat-item:hover {
            background-color: #f8f9fa;
            padding-left: 10px;
        }
        .chat-item:last-child {
            border-bottom: none;
        }
        .user-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            margin-left: 5px;
        }
        .badge-sender {
            background: #e3f2fd;
            color: #1976d2;
        }
        .badge-receiver {
            background: #f3e5f5;
            color: #7b1fa2;
        }
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    </style>
</head>
<body>
    @include('admin.header')

    <div class="d-flex align-items-stretch">
        @include('admin.sidebar')

        <div class="page-content py-5">
            <div class="container-fluid">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white text-center">
                        <h3 class="mb-0">All Chat Conversations</h3>
                    </div>

                    <div class="card-body">
                        @if($conversations->isEmpty())
                            <div class="text-center py-5">
                                <i class="fa fa-comments fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No conversations found</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle">
                                    <thead class="table-primary text-center">
                                        <tr>
                                            <th scope="col">Sender</th>
                                            <th scope="col">Receiver</th>
                                            <th scope="col">House</th>
                                            <th scope="col">Last Message</th>
                                            <th scope="col">Messages Count</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center">
                                        @foreach($conversations as $conversation)
                                            <tr>
                                                <td>
                                                    <i class="fa fa-user text-primary"></i>
                                                    {{ $conversation['sender']->name }}
                                                    <span class="user-badge badge-sender">Sender</span>
                                                </td>
                                                <td>
                                                    <i class="fa fa-user text-success"></i>
                                                    {{ $conversation['receiver']->name }}
                                                    <span class="user-badge badge-receiver">Receiver</span>
                                                </td>
                                                <td>
                                                    <i class="fa fa-home"></i>
                                                    <strong>{{ $conversation['house']->title }}</strong><br>
                                                    <small class="text-muted">{{ $conversation['house']->location }}</small>
                                                </td>
                                                <td class="text-start">
                                                    <em>"{{ Str::limit($conversation['last_message']->message, 80) }}"</em><br>
                                                    <small class="text-muted">
                                                        {{ $conversation['last_message']->created_at->diffForHumans() }}
                                                    </small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ $conversation['message_count'] }}</span>
                                                </td>
                                                <td>
                                                    <a href="{{ url('/admin/chat/' . $conversation['sender']->id . '/' . $conversation['receiver']->id . '/' . $conversation['house']->id) }}"
                                                       class="btn btn-sm btn-primary">
                                                        <i class="fa fa-eye"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.js')
</body>
</html>
