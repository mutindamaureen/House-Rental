<!DOCTYPE html>
<html>

<head>
    @include('home.css')
</head>

<body>
  <div class="hero_area">
    <!-- header section strats -->
    @include('home.header')
    <!-- end header section -->
    <!-- slider section -->
    <section class="products_section py-5 bg-light">
    <div class="container">
        <!-- Section Heading -->
        <div class="text-center mb-5">
        <h2 class="fw-bold">Available Houses</h2>
        </div>

        <div class="row g-4">
        <!-- Product Card Template -->

        @foreach ($house as $houses)

        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm border-0">
            <div class="position-relative">
                <img src="houses/{{ $houses->image }}" class="card-img-top img-fluid product-img" alt="House Image">
            </div>
            <div class="card-body text-center d-flex flex-column justify-content-between">
                <h6 class="card-title mb-2">{{ $houses->title }}</h6>
                <p class="card-text text-primary fw-bold mb-3">Ksh{{ $houses->price }}</p>
                <p class="card-text text-primary fw-bold mb-3">{{ $houses->location }}</p>
                {{-- <p class="card-text text-primary fw-bold mb-3">{{ $houses->status }}</p> --}}

                {{-- <a href="{{ url('house_details') }}" class="btn btn-sm btn-outline-primary mt-auto">View</a> --}}
                <a href="{{ route('house.details', $houses->id) }}" class="btn btn-sm btn-outline-primary mt-auto">View</a>

            </div>
            </div>
        </div>

        @endforeach

        </div>

    </div>
    </section>

    {{-- @include('home.slider') --}}
    <!-- end slider section -->
  </div>
  <!-- end hero area -->


  @include('home.footer')
</body>

</html>
