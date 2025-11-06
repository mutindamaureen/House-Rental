<section class="contact_us_section py-5 bg-light">
  <div class="container">
    <!-- Section Heading -->
    <div class="text-center mb-5">
      <h2 class="fw-bold">Contact Us</h2>
      <p class="text-muted">Have questions or want to rent a home? Get in touch with us!</p>
    </div>

    <div class="row g-4">
      <!-- Contact Form -->
      <div class="col-lg-6">
        <div class="card shadow-sm border-0 p-4">
          <form action="" method="POST">
            @csrf
            <div class="mb-3">
              <label for="name" class="form-label">Full Name</label>
              <input type="text" class="form-control" id="name" name="name" placeholder="Your Name" required>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email Address</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="Your Email" required>
            </div>
            <div class="mb-3">
              <label for="subject" class="form-label">Subject</label>
              <input type="text" class="form-control" id="subject" name="subject" placeholder="Subject" required>
            </div>
            <div class="mb-3">
              <label for="message" class="form-label">Message</label>
              <textarea class="form-control" id="message" name="message" rows="5" placeholder="Your Message" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-lg shadow-sm">Send Message</button>
          </form>
        </div>
      </div>

      <!-- Contact Info -->
      <div class="col-lg-6">
        <div class="card shadow-sm border-0 p-4 h-100">
          <h5 class="fw-bold mb-3">Our Office</h5>
          <p class="mb-2"><i class="fa fa-map-marker me-2"></i>123 Main Street, Nairobi, Kenya</p>
          <p class="mb-2"><i class="fa fa-phone me-2"></i>+254 700 123 456</p>
          <p class="mb-2"><i class="fa fa-envelope me-2"></i>info@homerental.com</p>

          <h5 class="fw-bold mt-4 mb-3">Find Us Here</h5>
          <div class="ratio ratio-16x9">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18..." style="border:0;" allowfullscreen="" loading="lazy"></iframe>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
