<?=include_page("redirector")?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
  <title>AutoParts Elite | Login</title>
  <!-- Bootstrap 5 CSS + Icons (Bootstrap Icons are part of BS ecosystem, but only BS5 CSS used for styling) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons (optional but keeps visual consistency, no external CSS frameworks aside from BS) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    /* custom overrides using only Bootstrap variables / minimal extra to enhance "auto parts" theme
       no extra css frameworks, but custom background & subtle effects are added to match the amazing autoparts vibe.
       All structural layout remains Bootstrap 5 compliant. */
    body {
      background: linear-gradient(135deg, #0a1a1f 0%, #0f2128 100%);
      min-height: 100vh;
      font-family: system-ui, 'Segoe UI', 'Roboto', 'Helvetica Neue', sans-serif;
    }

    /* Carbon fiber texture overlay inspired by auto parts - just background enrichment */
    .carbon-pattern {
      position: relative;
    }
    .carbon-pattern::before {
      content: "";
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-image: radial-gradient(rgba(30, 40, 45, 0.3) 1.5px, transparent 1.5px);
      background-size: 28px 28px;
      pointer-events: none;
      z-index: 0;
    }

    /* main card styling: modern, sleek, metallic touch */
    .autoparts-card {
      background: rgba(10, 20, 22, 0.85);
      backdrop-filter: blur(8px);
      border: 1px solid rgba(255, 140, 0, 0.35);
      border-radius: 2rem;
      transition: transform 0.25s ease, box-shadow 0.3s;
      box-shadow: 0 25px 45px -12px rgba(0,0,0,0.5), 0 0 0 1px rgba(255, 90, 0, 0.1) inset;
    }
    .autoparts-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 30px 55px -12px black;
      border-color: rgba(255, 100, 0, 0.6);
    }

    /* Custom accent for input groups, buttons */
    .btn-autoparts {
      background: linear-gradient(95deg, #e25822 0%, #ff7b2c 100%);
      border: none;
      font-weight: 700;
      letter-spacing: 0.5px;
      transition: all 0.2s;
      color: white;
      box-shadow: 0 4px 12px rgba(226, 88, 34, 0.3);
    }
    .btn-autoparts:hover {
      background: linear-gradient(95deg, #c9471a 0%, #e8622a 100%);
      transform: scale(1.01);
      box-shadow: 0 8px 18px rgba(226, 88, 34, 0.5);
    }

    .form-control-autoparts {
      background-color: rgba(20, 30, 33, 0.85);
      border: 1px solid #3a525b;
      color: #f0f3f4;
      border-radius: 1rem;
      padding: 0.75rem 1rem;
      transition: all 0.2s;
    }
    .form-control-autoparts:focus {
      background-color: #1e2f35;
      border-color: #ff7b2c;
      outline: none;
      box-shadow: 0 0 0 3px rgba(255, 100, 0, 0.35);
      color: white;
    }
    .form-control-autoparts::placeholder {
      color: #8ea3aa;
      font-weight: 400;
    }

    .input-group-text-custom {
      background-color: #1b2c32;
      border: 1px solid #3a525b;
      border-radius: 1rem 0 0 1rem;
      color: #ff9142;
    }

    /* gear & mechanical decorative elements */
    .gear-icon {
      background: rgba(226, 88, 34, 0.15);
      width: 55px;
      height: 55px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      color: #ff7b2c;
      transition: 0.3s;
    }

    /* mechanical thin line above footer */
    .mech-line {
      height: 2px;
      background: linear-gradient(90deg, transparent, #ff7b2c, #ffb347, transparent);
      width: 80%;
      margin: 0 auto;
    }

    /* logo area with bolt icon */
    .brand-icon {
      font-size: 3.4rem;
      filter: drop-shadow(0 2px 6px rgba(0,0,0,0.5));
    }

    /* custom link styling */
    .link-autoparts {
      color: #ffaa66;
      text-decoration: none;
      font-weight: 500;
      transition: 0.2s;
    }
    .link-autoparts:hover {
      color: #ff7b2c;
      text-decoration: underline;
    }

    /* responsive adjustments */
    @media (max-width: 576px) {
      .autoparts-card {
        border-radius: 1.5rem;
        margin: 0 1rem;
      }
      .brand-icon {
        font-size: 2.6rem;
      }
    }
  </style>
</head>
<body class="carbon-pattern d-flex align-items-center">
  <?=translation_icon("#wowid")?>
  <div class="container py-5 my-auto">
    <div class="row justify-content-center align-items-center min-vh-100">
      <div class="col-lg-5 col-md-7 col-sm-10">
        <!-- main card with premium auto parts theme -->
        <div class="autoparts-card p-4 p-xl-5">
          <!-- top decorative emblem & title -->
          <div class="text-center mb-4">
            <div class="d-flex justify-content-center align-items-center gap-3 mb-2">
              <div class="gear-icon">
                <i class="bi bi-gear-wide-connected"></i>
              </div>
              <i class="bi bi-lightning-charge-fill brand-icon text-warning"></i>
              <div class="gear-icon">
                <i class="bi bi-tools"></i>
              </div>
            </div>
            <div id="wowid"></div>
            <h2 class="display-6 fw-bold text-white mt-2">
              <span style="background: linear-gradient(120deg, #fff, #ffb347); background-clip: text; -webkit-background-clip: text; color: transparent;">KYG AUTOPARTS</span>
            </h2>
            <p class="text-light-emphasis text-white-50 mb-0"><?=t('Heavy‑Duty Performance Portal')?></p>
            <div class="mech-line my-3"></div>
            <p class="text-white-70 mt-2" style="color: #cfdfe3;"><?=t('Sign in to access inventory, orders & technical specs')?></p>
          </div>

          <!-- login form -->
          <form id="loginForm">
            <!-- Email / Username field with icon group -->
            <div class="mb-4">
              <label style="margin-right: 5px; color:white;display:none;" for="cust"><input type="radio" name="type" id="cust" disabled> Customer</label>
              <label style="margin-right: 5px; color:white; display:none;" for="ad"><input type="radio" name="type" checked id="ad"> Admin/Rider</label>
            </div>
            <div class="text-center mt-2">
              <!--
              <p class="text-white-50 small">New to AutoParts Elite? 
                <a href="#" class="link-autoparts fw-semibold" data-bs-toggle="modal" data-bs-target="#signupModal">Create an account</a>
              </p>
                -->
              <div class="d-flex justify-content-center gap-2 mt-3 mb-4">
                <span class="badge bg-dark text-warning border border-warning rounded-pill px-3 py-2">
                  <i class="bi bi-shield-check"></i> Login as admin
                </span>
                <span class="badge bg-dark text-warning border border-warning rounded-pill px-3 py-2">
                  <i class="bi bi-truck"></i> Login as Rider
                </span>
              </div>
            </div>
            <div class="mb-4">
              <label for="loginEmail" class="form-label text-light fw-semibold small text-uppercase tracking-wide">
                <i class="bi bi-envelope-paper-fill me-1"></i> <?=t('Email address')?>
              </label>
              <div class="input-group">
                <span class="input-group-text input-group-text-custom border-end-0">
                  <i class="bi bi-person-badge"></i>
                </span>
                <input type="text" class="form-control form-control-autoparts" id="loginEmail" 
                       placeholder="Enter email..." name="email" value="" >
              </div>
              <div class="text-danger errmsg" id="_email">
                
              </div>
            </div>

            <!-- Password field with toggle visibility (extra UX) -->
            <div class="mb-4">
              <label for="loginPassword" class="form-label text-light fw-semibold small text-uppercase">
                <i class="bi bi-shield-lock-fill me-1"></i> <?=t('Password')?>
              </label>
              <div class="input-group">
                <span class="input-group-text input-group-text-custom border-end-0">
                  <i class="bi bi-key-fill"></i>
                </span>
                <input type="password" class="form-control form-control-autoparts" id="loginPassword" 
                       placeholder="••••••••" value="" name="password">
                <button class="btn btn-outline-secondary border-start-0" type="button" id="togglePasswordBtn" 
                        style="background-color:#1b2c32; border-color:#3a525b; color:#ff9142; border-radius: 0 1rem 1rem 0;">
                  <i class="bi bi-eye-slash-fill" id="toggleIcon"></i>
                </button>
              </div>
              <div class="text-danger errmsg" id="_password">
              </div>
            </div>

            <!-- extra row: remember me & forgot password -->
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="rememberCheck" style="background-color:#1f3a42; border-color:#ff7b2c;">
                <label class="form-check-label text-white-50" for="rememberCheck">
                  Keep me signed in
                </label>
              </div>
              <a href="#" class="link-autoparts small" data-bs-toggle="modal" data-bs-target="#forgotModal">
                <i class="bi bi-question-circle"></i> Forgot password?
              </a>
            </div>

            <!-- Login Button with loading simulation -->
            <div class="d-grid gap-2 mb-4">
              <button type="submit" class="btn btn-autoparts btn-lg py-2" id="loginBtn">
                <i class="bi bi-box-arrow-in-right me-2"></i> LOGIN TO DASHBOARD
              </button>
            </div>

            <div class="d-grid gap-2 mb-4" align='center'>
              <a href="#" id="signupclick">Sign up here</a>
            </div>

            <!-- signup prompt & special auto parts note -->
            <div class="text-center mt-2">
              <!--
              <p class="text-white-50 small">New to AutoParts Elite? 
                <a href="#" class="link-autoparts fw-semibold" data-bs-toggle="modal" data-bs-target="#signupModal">Create an account</a>
              </p>
  -->
              <div class="d-flex justify-content-center gap-2 mt-3">
                <span class="badge bg-dark text-warning border border-warning rounded-pill px-3 py-2">
                  <i class="bi bi-shield-check"></i> OEM Parts
                </span>
                <span class="badge bg-dark text-warning border border-warning rounded-pill px-3 py-2">
                  <i class="bi bi-truck"></i> Express Delivery
                </span>
              </div>
            </div>
          </form>
        </div>
        
        <!-- mechanical footer note -->
        <div class="text-center mt-4 text-white-50 small">
          <div>
          <i class="bi bi-wrench-adjustable-circle-fill"></i> Powering professional workshops & performance garages
          </div>
          <span class="mx-2">⚙️</span> <span id="year"></span> MADE IN CODETAZER 
        </div>
      </div>
    </div>
  </div>

  <!-- Forgot Password Modal (Bootstrap 5 modal) No extra CSS, fully BS component -->
  <div class="modal fade" id="forgotModal" tabindex="-1" aria-labelledby="forgotModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content bg-dark text-white border-secondary">
        <div class="modal-header border-bottom border-secondary">
          <h5 class="modal-title fw-bold" id="forgotModalLabel"><i class="bi bi-envelope-exclamation-fill text-warning me-2"></i>Reset password</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="text-white-70">Enter your email address and we’ll send you a link to reset your password for <strong class="text-warning">AutoParts Portal</strong>.</p>
          <input type="email" class="form-control form-control-autoparts mt-2" id="resetEmail" placeholder="your@email.com">
          <div class="mt-3 small text-info"><i class="bi bi-info-circle"></i> Reset link will be sent within minutes.</div>
        </div>
        <div class="modal-footer border-secondary">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-autoparts" id="sendResetBtn">Send reset link</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Signup demo Modal (just for showcase, no actual backend) -->
  <div class="modal fade" id="signupModal" tabindex="-1" aria-labelledby="signupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content bg-dark text-white border-secondary">
        <div class="modal-header border-bottom border-secondary">
          <h5 class="modal-title fw-bold" id="signupModalLabel"><i class="bi bi-person-plus-fill text-warning me-2"></i>Join AutoParts Elite</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="text-white-70">Get exclusive trade prices, real-time inventory & diagnostic tools.</p>
          <div class="alert alert-dark border-warning text-warning-emphasis bg-dark">
            <i class="bi bi-star-fill"></i> Demo credentials: <strong>demo@autoparts.com</strong> / <strong>autoparts2025</strong>
          </div>
          <form>
            <input type="text" class="form-control form-control-autoparts mb-3" placeholder="Full name">
            <input type="email" class="form-control form-control-autoparts mb-3" placeholder="Email address">
            <input type="password" class="form-control form-control-autoparts mb-3" placeholder="Password">
            <div class="form-check mb-2">
              <input class="form-check-input" type="checkbox" id="termsCheck">
              <label class="form-check-label small text-white-50" for="termsCheck">I agree to the <a href="#" class="link-autoparts">terms & data policy</a></label>
            </div>
          </form>
        </div>
        <div class="modal-footer border-secondary">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-autoparts" id="simulateSignupBtn">Register (demo)</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <?=js()?>
</body>
</html>