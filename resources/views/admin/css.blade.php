<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>Dark Bootstrap Admin</title>
<meta name="description" content="">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="all,follow">

<!-- Bootstrap CSS -->
<link rel="stylesheet" href="{{ asset('admincss/vendor/bootstrap/css/bootstrap.min.css') }}">
<!-- Font Awesome CSS -->
<link rel="stylesheet" href="{{ asset('admincss/vendor/font-awesome/css/font-awesome.min.css') }}">
<!-- Custom Font Icons CSS -->
<link rel="stylesheet" href="{{ asset('admincss/css/font.css') }}">
<!-- Google fonts - Muli -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Muli:300,400,700">
<!-- Theme stylesheet -->
<link rel="stylesheet" href="{{ asset('admincss/css/style.default.css') }}" id="theme-stylesheet">
<!-- Custom stylesheet - for your changes -->
<link rel="stylesheet" href="{{ asset('admincss/css/custom.css') }}">
<!-- Favicon -->
<link rel="shortcut icon" href="{{ asset('admincss/img/favicon.ico') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


<style>
    /* Make the sidebar sticky and scrollable only when hovered */
    #sidebar {
    position: sticky;
    top: 0;
    height: 100vh; /* full viewport height */
    overflow-y: hidden; /* hide scroll by default */
    overflow-x: hidden;
    transition: all 0.2s ease-in-out;
    background: #343a40; /* adjust to your theme */
    color: #fff;
    z-index: 1000;
    }

    /* Show scrollbar only when hovered */
    #sidebar:hover {
    overflow-y: auto;
    }

    /* Optional: make scrollbar look cleaner */
    #sidebar::-webkit-scrollbar {
    width: 6px;
    }

    #sidebar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 10px;
    }

    #sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
    }

    /* Ensure main content scrolls independently */
    .page-content {
    flex: 1;
    overflow-y: auto;
    height: 100vh;
    /* background: #f8f9fa; */
    padding: 20px;
    }

</style>
<!-- Tweaks for older IEs -->
<!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
