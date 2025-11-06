<section class="footer_section bg-dark text-light py-4">
  <div class="container text-center">
    <p class="mb-0 small">
      &copy; <span id="displayYear"></span> All Rights Reserved By
      <a href="" class="text-decoration-none text-info">Maureenn Mutinda</a>
    </p>
  </div>
</section>

<!-- Scripts -->
<script src="{{ asset('js/jquery-3.4.1.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script src="{{ asset('js/custom.js') }}"></script>

<script>
  // Set current year dynamically
  document.getElementById('displayYear').textContent = new Date().getFullYear();
</script>
