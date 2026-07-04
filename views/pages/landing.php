
<?=include_page("redirector")?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
  <title><?=variable("appname")?> | Performance Parts Supply</title>
  <!-- Bootstrap 5 CSS + Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?=assets('landing.css')?>">
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-autoparts fixed-top py-3">
  <div class="container">
    <a class="navbar-brand fw-bold fs-3 text-white" href="#">
      <i class="bi bi-gear-wide-connected me-2 text-warning"></i><span class="brand-glow"><?=variable('appname')?></span>
    </a>
    <button class="navbar-toggler border-0 bg-white bg-opacity-10" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-label="Toggle navigation">
      <i class="bi bi-list text-white fs-2"></i>
    </button>
    <div class="collapse navbar-collapse" id="navbarMain">
      <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-2">
        <li class="nav-item"><a class="nav-link text-white-50 hover-text" href="#home">Home</a></li>
        <li class="nav-item"><a class="nav-link text-white-50" href="#features">Features</a></li>
        <li class="nav-item"><a class="nav-link text-white-50" href="#categories">Categories</a></li>
        <li class="nav-item"><a class="nav-link text-white-50" href="#testimonials">Testimonials</a></li>
      </ul>
      <div class="d-flex gap-2">
        <a href="/login" class="btn btn-outline-autoparts">Login</a>
        <a href="/order" class="btn btn-autoparts-primary"><i class="bi bi-cart-fill"></i> Direct order</a>
      </div>
    </div>
  </div>
</nav>

<!-- HERO SECTION (Landing) -->
<section id="home" class="hero-section pt-5 mt-5">
  <div class="container pt-5 pb-5">
    <div class="row align-items-center g-5">
      <div class="col-lg-6 text-white">
        <div class="mb-3">
          <span class="badge bg-warning bg-opacity-25 text-warning px-3 py-2 rounded-pill"><i class="bi bi-lightning-charge-fill me-1"></i> Powering Performance Since 2012</span>
        </div>
        <h1 class="hero-title fw-bold mb-4">Heavy‑Duty <span class="highlight-orange">Auto Parts</span> <br>For Extreme Performance</h1>
        <p class="lead text-white-70 mb-4" style="color: #cddfe5;">Access 50,000+ genuine OEM & aftermarket parts. Real-time inventory, technical specs, and wholesale pricing for workshops and enthusiasts.</p>
        <div class="d-flex flex-wrap gap-3 mb-4">
          <a href="#" class="btn btn-autoparts-primary px-4 py-2" data-bs-toggle="modal" data-bs-target="#signupModal">Get Started <i class="bi bi-arrow-right-circle"></i></a>
          <a href="#features" class="btn btn-outline-light-custom px-4 py-2">Explore Catalog <i class="bi bi-compass"></i></a>
        </div>
        <div class="row g-3 mt-2">
          <div class="col-6 col-sm-4">
            <div class="hero-stats-card p-2 text-center">
              <h3 class="fw-bold text-warning mb-0">50k+</h3>
              <small class="text-white-50">Products</small>
            </div>
          </div>
          <div class="col-6 col-sm-4">
            <div class="hero-stats-card p-2 text-center">
              <h3 class="fw-bold text-warning mb-0">24/7</h3>
              <small class="text-white-50">Tech Support</small>
            </div>
          </div>
          <div class="col-6 col-sm-4">
            <div class="hero-stats-card p-2 text-center">
              <h3 class="fw-bold text-warning mb-0">2.3k+</h3>
              <small class="text-white-50">Garages Trust Us</small>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-6 text-center">
        <div class="p-3">
          <img src="https://cdn-icons-png.flaticon.com/512/3176/3176363.png" alt="Auto Parts Illustration" class="img-fluid" style="max-width: 85%; filter: drop-shadow(0 10px 25px rgba(0,0,0,0.2));">
          <div class="mt-3 d-flex justify-content-center gap-2 flex-wrap">
            <span class="badge bg-dark bg-opacity-50 px-3 py-2 rounded-pill"><i class="bi bi-trophy"></i> Racing Spec</span>
            <span class="badge bg-dark bg-opacity-50 px-3 py-2 rounded-pill"><i class="bi bi-shield-check"></i> 2-Year Warranty</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- FEATURES SECTION -->
<section id="features" class="py-5 bg-light">
  <div class="container py-4">
    <div class="text-center mb-5">
      <span class="badge bg-warning bg-opacity-15 text-dark mb-2 rounded-pill px-3 py-2">Why Choose Us</span>
      <h2 class="display-6 fw-bold">Engineered for <span class="text-warning">Professionals</span></h2>
      <p class="text-muted col-lg-7 mx-auto">Everything you need to keep your workshop ahead of the curve</p>
    </div>
    <div class="row g-4">
      <div class="col-md-6 col-lg-3">
        <div class="feature-card card h-100 p-4 text-center">
          <div class="feature-icon mx-auto mb-3"><i class="bi bi-box-seam"></i></div>
          <h5 class="fw-bold">50K+ Parts</h5>
          <p class="small text-muted">Brakes, suspension, engines, electrical, and more. Fast filtering & cross-reference.</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="feature-card card h-100 p-4 text-center">
          <div class="feature-icon mx-auto mb-3"><i class="bi bi-truck"></i></div>
          <h5 class="fw-bold">Express Delivery</h5>
          <p class="small text-muted">Same-day shipping for orders before 2PM. Real-time tracking for mechanics.</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="feature-card card h-100 p-4 text-center">
          <div class="feature-icon mx-auto mb-3"><i class="bi bi-graph-up"></i></div>
          <h5 class="fw-bold">Bulk Pricing</h5>
          <p class="small text-muted">Wholesale discounts for garages & fleets. Loyalty rewards program.</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="feature-card card h-100 p-4 text-center">
          <div class="feature-icon mx-auto mb-3"><i class="bi bi-headset"></i></div>
          <h5 class="fw-bold">Expert Support</h5>
          <p class="small text-muted">Certified auto specialists ready to assist with technical fitment.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CATEGORIES SECTION -->
<!--
<section id="categories" class="py-5">
  <div class="container py-3">
    <div class="text-center mb-4">
      <h2 class="fw-bold">Shop by <span class="text-warning">Category</span></h2>
      <p class="text-muted">Explore trending auto parts categories trusted by pros</p>
    </div>
    <div class="d-flex flex-wrap justify-content-center gap-2 mb-5">
      <div class="category-badge">Brakes & Rotors</div>
      <div class="category-badge">Engine Components</div>
      <div class="category-badge">Suspension</div>
      <div class="category-badge">Exhaust Systems</div>
      <div class="category-badge">Electrical & Lighting</div>
      <div class="category-badge">Filters & Fluids</div>
    </div>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="card border-0 rounded-4 overflow-hidden shadow-sm">
          <img src="https://cdn-icons-png.flaticon.com/512/2942/2942958.png" class="card-img-top p-3 bg-white" alt="brakes">
          <div class="card-body text-center">
            <h5 class="fw-bold">Performance Brakes</h5>
            <p class="small text-muted">Ceramic & semi-metallic pads, drilled rotors</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card border-0 rounded-4 overflow-hidden shadow-sm">
          <img src="https://cdn-icons-png.flaticon.com/512/2630/2630537.png" class="card-img-top p-3 bg-white" alt="engine">
          <div class="card-body text-center">
            <h5 class="fw-bold">Engine Parts</h5>
            <p class="small text-muted">Timing kits, gaskets, pistons & turbochargers</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card border-0 rounded-4 overflow-hidden shadow-sm">
          <img src="https://cdn-icons-png.flaticon.com/512/1699/1699273.png" class="card-img-top p-3 bg-white" alt="suspension">
          <div class="card-body text-center">
            <h5 class="fw-bold">Suspension Kits</h5>
            <p class="small text-muted">Coilovers, shocks, control arms & lift kits</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
-->

<!-- TESTIMONIALS SECTION -->
<!-- 
<section id="testimonials" class="py-5 bg-light">
  <div class="container">
    <div class="text-center mb-5">
      <i class="bi bi-chat-quote-fill fs-1 text-warning"></i>
      <h2 class="fw-bold mt-2">Trusted by 2,300+ Workshops</h2>
      <p class="text-muted">What automotive professionals say about <?=variable('appname')?></p>
    </div>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="testimonial-card p-4 h-100">
          <div class="d-flex gap-3 align-items-center mb-3">
            <div class="avatar-icon">JD</div>
            <div><h6 class="mb-0 fw-bold">James Donovan</h6><small class="text-muted">Master Technician</small></div>
          </div>
          <p class="mb-0">“The fastest delivery and accurate parts catalog. Saved our shop countless hours on sourcing rare engine components. 10/10.”</p>
          <i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i>
        </div>
      </div>
      <div class="col-md-4">
        <div class="testimonial-card p-4 h-100">
          <div class="d-flex gap-3 align-items-center mb-3">
            <div class="avatar-icon">MR</div>
            <div><h6 class="mb-0 fw-bold">Maria Rodriguez</h6><small class="text-muted">Garage Owner</small></div>
          </div>
          <p class="mb-0">“Incredible bulk pricing & real-time stock levels. The admin portal makes inventory management effortless.”</p>
          <i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-half text-warning"></i>
        </div>
      </div>
      <div class="col-md-4">
        <div class="testimonial-card p-4 h-100">
          <div class="d-flex gap-3 align-items-center mb-3">
            <div class="avatar-icon">TW</div>
            <div><h6 class="mb-0 fw-bold">Tony Wu</h6><small class="text-muted">Performance Tuner</small></div>
          </div>
          <p class="mb-0">“Massive selection of performance parts. Tech specs and fitment guides saved us from wrong orders. Highly recommended.”</p>
          <i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i>
        </div>
      </div>
    </div>
  </div>
</section>
-->

<!-- CALL TO ACTION (Landing CTA) -->
<!-- 
<section class="py-5">
  <div class="container">
    <div class="cta-section p-5 text-center text-white">
      <h2 class="fw-bold mb-3">Ready to upgrade your inventory?</h2>
      <p class="mb-4 opacity-75">Join thousands of professionals who trust <?=variable('appname')?> for premium quality & reliability.</p>
      <div class="d-flex justify-content-center gap-3 flex-wrap">
        <a href="#" class="btn btn-autoparts-primary px-4 py-2" data-bs-toggle="modal" data-bs-target="#signupModal">Start Free Trial <i class="bi bi-arrow-right"></i></a>
        <a href="#" class="btn btn-outline-light border-white" data-bs-toggle="modal" data-bs-target="#loginModal">Login to Portal</a>
        <a href="#" class="btn btn-outline-light border-white" data-bs-toggle="modal" data-bs-target="#loginModal">Order</a>
      </div>
    </div>
  </div>
</section>
-->

<!-- FOOTER (Landing) -->
<footer class="pt-5 pb-4">
  <div class="container">
    <div class="row">
      <div class="col-md-5 mb-4">
        <i class="bi bi-gear-wide-connected fs-2 text-warning"></i>
        <h5 class="fw-bold mt-2 text-white"><?=variable('appname')?></h5>
        <p class="small">Delivering excellence in automotive parts distribution worldwide. Innovation meets durability.</p>
      </div>
      <div class="col-md-2 mb-4">
        <h6 class="fw-bold text-white">Quick Links</h6>
        <ul class="list-unstyled small">
          <li><a href="#" class="text-decoration-none text-white-50">About Us</a></li>
          <li><a href="#" class="text-decoration-none text-white-50">Catalog</a></li>
          <li><a href="#" class="text-decoration-none text-white-50">Support</a></li>
        </ul>
      </div>
      <div class="col-md-2 mb-4">
        <h6 class="fw-bold text-white">Resources</h6>
        <ul class="list-unstyled small">
          <li><a href="#" class="text-decoration-none text-white-50">Blog</a></li>
          <li><a href="#" class="text-decoration-none text-white-50">Technical Guides</a></li>
          <li><a href="#" class="text-decoration-none text-white-50">Warranty</a></li>
        </ul>
      </div>
      <div class="col-md-3 mb-4">
        <h6 class="fw-bold text-white">Contact</h6>
        <p class="small text-white-50 mb-1"><i class="bi bi-envelope"></i> <?=config('email')?></p>
        <p class="small text-white-50"><i class="bi bi-telephone"></i> <?=config('contact')?></p>
      </div>
    </div>
    <hr class="bg-secondary">
    <div class="text-center small text-white-50">© 2026 <?=variable('appname')?> — Heavy Duty Performance. All rights reserved.</div>
  </div>
</footer>

<!-- LOGIN MODAL (Bootstrap 5) -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow-lg">
      <div class="modal-header border-0 bg-dark text-white rounded-top-4">
        <h5 class="modal-title fw-bold"><i class="bi bi-box-arrow-in-right"></i> Welcome Back</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <form id="landingLoginForm">
          <div class="mb-3">
            <label class="form-label">Email address</label>
            <input type="email" name="email" class="form-control" id="loginEmailLanding" placeholder="mechanic@autoparts.com" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" id="loginPasswordLanding" placeholder="••••••" required>
          </div>
          <div class="d-grid">
            <button type="submit" class="btn btn-autoparts-primary py-2">Login to Dashboard</button>
          </div>
          <!--
          <div class="text-center mt-3 small">
            Don't have an account? <a href="#" data-bs-toggle="modal" data-bs-dismiss="modal" data-bs-target="#signupModal">Sign up</a>
          </div>
-->
          <div class="text-center mt-3 small">
            I am a web?<a href="/login"> Admin</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- SIGNUP MODAL -->
<div class="modal fade" id="signupModal" tabindex="-1" aria-labelledby="signupModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow-lg">
      <div class="modal-header border-0 bg-dark text-white rounded-top-4">
        <h5 class="modal-title fw-bold"><i class="bi bi-person-plus-fill"></i> Create Account</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <form id="landingSignupForm">
          <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" class="form-control" id="signupName" placeholder="Alex Turner">
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" id="signupEmail" placeholder="hello@autoparts.com">
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" class="form-control" id="signupPassword" placeholder="Create a password">
          </div>
          <div class="d-grid">
            <button type="submit" class="btn btn-autoparts-primary py-2">Start Trial <i class="bi bi-arrow-right"></i></button>
          </div>
          <div class="text-center mt-3 small">Already registered? <a href="#" data-bs-toggle="modal" data-bs-dismiss="modal" data-bs-target="#loginModal">Login</a></div>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<?=js()?>
<script>
  const signupForm = document.getElementById('landingSignupForm');
  if(signupForm) {
    signupForm.addEventListener('submit', (e) => {
      e.preventDefault();
      alert('Account creation demo: Thank you! Please check email for verification (simulated).');
      const modal = bootstrap.Modal.getInstance(document.getElementById('signupModal'));
      if(modal) modal.hide();
    });
  }

  // Smooth hover for navbar links
  const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
  navLinks.forEach(link => {
    link.addEventListener('mouseenter', () => link.classList.add('text-warning'));
    link.addEventListener('mouseleave', () => link.classList.remove('text-warning'));
  });
</script>
</body>
</html>