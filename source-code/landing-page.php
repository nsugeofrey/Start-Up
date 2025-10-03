<?php include "header.php"; ?>
<body>
<header class="bg-dark text-white py-3">
    <!--Navbar-->
    <nav class="navbar navbar-expand-lg sticky-top shadow-sm">
      <div class="container">
        <a class="navbar-brand" href="#">
          <img src="dist/logo-image/logo.png" alt="Business Logo">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
          <ul class="navbar-nav align-items-lg-center">
            <li class="nav-item"><a class="nav-link" href="#listings">Our Properties</a></li>
            <li class="nav-item"><a class="nav-link" href="#agents">Locations</a></li>
            <li class="nav-item"><a class="nav-link" href="#testimonials">Testimonials</a></li>
            <li class="nav-item"><a class="nav-link" href="#blogs">Blogs</a></li>
            <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
            <li class="nav-item"><a class="nav-link text-primary" href="tel:+971500000000"><i class="fas fa-phone"></i> +971 50 000 0000</a></li>
          </ul>
        </div>
      </div>
    </nav>
  </header>
   <!-- Theme Toggle -->
  <button class="btn btn-sm btn-outline-secondary btn-theme-toggle" onclick="toggleTheme()">Toggle Theme</button>


    <!-- Sidebar -->
    <nav id="sidebar" class="d-lg-block">
        <div class="sidebar-header">
            <h2 class="text-indigo-400">Admin Panel</h2>
            <button id="sidebarToggleClose" class="btn btn-link text-white d-lg-none"><i class="fas fa-times"></i></button>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item mb-2">
                <a class="nav-link active" href="#"><i class="fas fa-home me-3"></i> Dashboard</a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link" href="#"><i class="fas fa-list me-3"></i> Listings</a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link" href="#"><i class="fas fa-users me-3"></i> Users</a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link" href="#"><i class="fas fa-briefcase me-3"></i> Leads</a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link" href="#"><i class="fas fa-chart-bar me-3"></i> Analytics</a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link" href="#"><i class="fas fa-cog me-3"></i> Settings</a>
            </li>
        </ul>
    </nav>


  <!-- Features -->
  <section class="py-5 text-center" id="features">
    <div class="container">
      <h2 class="mb-5">Why Choose Us?</h2>
      <div class="row">
        <div class="col-md-3" data-aos="fade-up">
          <div class="feature-icon"><i class="fas fa-user-shield"></i></div>
          <h5>Trusted Agents</h5>
          <p>All listings are vetted and verified by local professionals.</p>
        </div>
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
          <div class="feature-icon"><i class="fas fa-photo-video"></i></div>
          <h5>High-Res Media</h5>
          <p>Every property includes full HD photos and walk-throughs.</p>
        </div>
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
          <div class="feature-icon"><i class="fas fa-globe"></i></div>
          <h5>Global Access</h5>
          <p>Buy or rent from anywhere in the world with confidence.</p>
        </div>
        <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
          <div class="feature-icon"><i class="fas fa-headset"></i></div>
          <h5>24/7 Support</h5>
          <p>Dedicated multilingual team ready to assist anytime.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Listings -->
  <section class="py-5 bg-light" id="listings">
    <div class="container">
      <h2 class="text-center mb-5">Featured Listings</h2>
      <div class="row">
        <div class="col-md-4" data-aos="fade-right">
          <div class="card">
            <img src="" class="card-img-top" alt="Villa">
            <div class="card-body">
              <h5 class="card-title">Palm Jumeirah Villa</h5>
              <p class="card-text">AED 18M • 5 Beds • Private Beach</p>
              <a href="#" class="btn btn-primary">View</a>
            </div>
          </div>
        </div>
        <div class="col-md-4" data-aos="fade-up">
          <div class="card">
            <img src="assets/images/penthouse.jpg" class="card-img-top" alt="Penthouse">
            <div class="card-body">
              <h5 class="card-title">Downtown Penthouse</h5>
              <p class="card-text">AED 25M • Burj Khalifa View</p>
              <a href="#" class="btn btn-primary">View</a>
            </div>
          </div>
        </div>
        <div class="col-md-4" data-aos="fade-left">
          <div class="card">
            <img src="assets/images/apartment.jpg" class="card-img-top" alt="Apartment">
            <div class="card-body">
              <h5 class="card-title">Dubai Marina Apartment</h5>
              <p class="card-text">AED 3.5M • 2 Beds • Sea View</p>
              <a href="#" class="btn btn-primary">View</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <?php include "footer.php"; ?>  

  