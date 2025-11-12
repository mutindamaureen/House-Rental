<!DOCTYPE html>
<html>
<head>
    @include('home.css')
    <style>
        .contact-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .contact-buttons .btn {
            flex: 1;
            padding: 15px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 10px;
            transition: all 0.3s;
        }
        .whatsapp-btn {
            background: #25D366;
            color: white;
            border: none;
        }
        .whatsapp-btn:hover {
            background: #20bd5a;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(37, 211, 102, 0.3);
        }
        .chat-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }
        .chat-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        .landlord-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .landlord-info h5 {
            margin-bottom: 15px;
            color: #333;
        }
        .landlord-detail {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            color: #666;
        }
        .landlord-detail i {
            width: 25px;
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="hero_area">
        @include('home.header')
    </div>

    <section class="house-detail py-5">
        <div class="container">
            <div class="row">
                <!-- House Image -->
                <div class="col-md-6 mb-4">
                    <img src="{{ asset('houses/' . $house->image) }}" class="img-fluid rounded shadow" alt="{{ $house->title }}">
                </div>

                <!-- House Details -->
                <div class="col-md-6">
                    <h2 class="fw-bold mb-3">{{ $house->title }}</h2>

                    <div class="mb-3">
                        <span class="badge bg-primary">{{ $house->status }}</span>
                    </div>

                    <h3 class="text-primary fw-bold mb-4">Ksh {{ number_format($house->price) }}</h3>

                    <div class="mb-4">
                        <h5 class="fw-semibold">Location</h5>
                        <p class="text-muted">{{ $house->location }}</p>
                    </div>

                    <div class="mb-4">
                        <h5 class="fw-semibold">Category</h5>
                        <p class="text-muted">{{ $house->category }}</p>
                    </div>

                    @if($house->description)
                    <div class="mb-4">
                        <h5 class="fw-semibold">Description</h5>
                        <p class="text-muted">{{ $house->description }}</p>
                    </div>
                    @endif

                    @if($landlord)
                    <div class="landlord-info">
                        <h5 class="fw-semibold"><i class="fa fa-user"></i> Landlord Information</h5>
                        <div class="landlord-detail">
                            <i class="fa fa-user-circle"></i>
                            <span>{{ $landlord->name }}</span>
                        </div>
                        @if($landlord->email)
                        <div class="landlord-detail">
                            <i class="fa fa-envelope"></i>
                            <span>{{ $landlord->email }}</span>
                        </div>
                        @endif
                        @if($landlord->phone)
                        <div class="landlord-detail">
                            <i class="fa fa-phone"></i>
                            <span>{{ $landlord->phone }}</span>
                        </div>
                        @endif
                    </div>

                    <!-- Contact Buttons -->
                    @auth
                    <div class="contact-buttons">
                        @if($whatsappUrl)
                        <a href="{{ $whatsappUrl }}" target="_blank" class="btn whatsapp-btn">
                            <i class="fa fa-whatsapp"></i> Contact via WhatsApp
                        </a>
                        @endif
                        <a href="{{ url('/chat/' . $house->id . '/' . $landlord->id) }}" class="btn chat-btn">
                            <i class="fa fa-comments"></i> Chat with Landlord
                        </a>
                    </div>
                    @else
                    <div class="alert alert-info mt-3">
                        <i class="fa fa-info-circle"></i> Please <a href="{{ route('login') }}">login</a> to contact the landlord
                    </div>
                    @endauth
                    @endif

                    <div class="d-grid gap-2 mt-3">
                        <a href="{{ url('/see_house') }}" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left"></i> Back to Houses
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('home.footer')
</body>
</html>
