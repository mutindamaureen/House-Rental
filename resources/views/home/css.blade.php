  <!-- Basic -->
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <!-- Mobile Metas -->
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <!-- Site Metas -->
  <meta name="keywords" content="" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <link rel="shortcut icon" href="images/favicon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <title>
    Giftos
  </title>

  <!-- slider stylesheet -->
  {{-- <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" /> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- bootstrap core css -->
  <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.css') }}" />

  <!-- Custom styles for this template -->
  {{-- <link href="{{ asset('css/style.css') }}" rel="stylesheet" /> --}}
  <!-- responsive style -->
  <link href="{{ asset('css/responsive.css') }}" rel="stylesheet" />

  <style>
    .main-header {
    background-color: #ffffff !important;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    position: sticky;
    top: 0;
    z-index: 1000;
    }
    .slider_section {
    position: relative;
    overflow: hidden;
    }

    .slider_section .detail-box h1 {
    font-size: 2.8rem;
    line-height: 1.3;
    }

    .slider_section .btn {
    transition: all 0.3s ease-in-out;
    }

    .slider_section .btn:hover {
    transform: translateY(-2px);
    }
    .products_section .card {
    height: 100%; /* Ensures all cards have equal height */
    }

    .products_section .product-img {
    height: 250px;   /* Set a fixed image height */
    object-fit: cover; /* Crops image to fill the space without distortion */
    }
    .user_option {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-align: center;
        -ms-flex-align: center;
            align-items: center;
    margin-left: 25px;
    }

    .user_option a {
    margin-right: 25px;
    color: #000000;
    }

    .user_option a span {
    margin-left: 5px;
    }
    .chat-notification {
        position: relative;
        display: inline-block;
        margin: 0 15px;
    }
    .chat-notification .badge {
        position: absolute;
        top: -8px;
        right: -10px;
        padding: 3px 6px;
        border-radius: 10px;
        background: #dc3545;
        color: white;
        font-size: 11px;
        font-weight: bold;
    }
    .chat-icon {
        font-size: 20px;
        color: #333;
        cursor: pointer;
        transition: color 0.3s;
    }
    .chat-icon:hover {
        color: #007bff;
    }

  </style>
