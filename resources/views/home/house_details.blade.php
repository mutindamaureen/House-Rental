<!DOCTYPE html>
<html>

<head>
    @include('home.css')
</head>

<body>
  <div class="hero_area">

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

            <div class="d-grid gap-2">
            <a href="{{ url('houses') }}" class="btn btn-primary btn-lg">Contact Agent</a>
            <a href="{{ url('/see_house') }}" class="btn btn-outline-secondary">Back to Houses</a>
            </div>
        </div>
        </div>
    </div>
    </section>

  @include('home.footer')
</body>

</html>
