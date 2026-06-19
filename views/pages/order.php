<?php

use Classes\Ctrql;

Ctrql::activate("CRUDQ");
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
  <title><?= variable('appname') ?> | Place Order</title>
  <!-- Bootstrap 5 CSS + Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    /* custom styles - pure bootstrap + minimal enhancements */
    body {
      background: #f0f2f5;
      font-family: 'Segoe UI', system-ui, 'Roboto', sans-serif;
    }

    /* navbar styling */
    .navbar-autoparts {
      background: linear-gradient(135deg, #0f2128 0%, #0a181f 100%);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .brand-text {
      font-weight: 800;
      letter-spacing: -0.5px;
    }

    .btn-autoparts-primary {
      background: #e25822;
      border: none;
      color: white;
      transition: all 0.2s;
    }

    .btn-autoparts-primary:hover {
      background: #c9471a;
      transform: translateY(-1px);
      box-shadow: 0 4px 10px rgba(226, 88, 34, 0.3);
    }

    .btn-outline-autoparts {
      border: 2px solid #e25822;
      color: #e25822;
      border-radius: 50px;
    }

    .btn-outline-autoparts:hover {
      background: #e25822;
      color: white;
    }

    .product-card {
      border: none;
      border-radius: 1.2rem;
      transition: transform 0.2s, box-shadow 0.2s;
      cursor: pointer;
    }

    .product-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
    }

    .product-img {
      height: 140px;
      object-fit: contain;
      padding: 1rem;
    }

    .cart-item-img {
      width: 55px;
      height: 55px;
      object-fit: contain;
      background: #f8f9fa;
      border-radius: 10px;
      padding: 5px;
    }

    .quantity-input {
      width: 70px;
      text-align: center;
    }

    .badge-stock {
      font-size: 0.7rem;
    }

    footer {
      background: #0f2128;
      color: #9bb7c2;
    }

    .checkout-summary {
      background: #fef9f5;
      border-radius: 1rem;
    }

    /* Cart floating button */
    .cart-float-btn {
      position: fixed;
      bottom: 20px;
      right: 20px;
      z-index: 1000;
      width: 60px;
      height: 60px;
      border-radius: 50%;
      background: #e25822;
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.8rem;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
      cursor: pointer;
      transition: all 0.2s;
    }

    .cart-float-btn:hover {
      transform: scale(1.05);
      background: #c9471a;
    }

    .cart-float-badge {
      position: absolute;
      top: -5px;
      right: -5px;
      background: #dc3545;
      color: white;
      border-radius: 50%;
      width: 24px;
      height: 24px;
      font-size: 0.7rem;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
    }

    /* ========== FILTER SECTION STYLES ========== */
    .filter-section {
      background: white;
      border-radius: 1rem;
      padding: 1rem 1.25rem;
      margin-bottom: 1.5rem;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
      border: 1px solid rgba(0, 0, 0, 0.05);
    }
    .filter-label {
      font-size: 0.8rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: #6c757d;
      margin-bottom: 0.4rem;
    }
    .filter-label i {
      margin-right: 4px;
    }
    .search-btn-custom {
      background: #e25822;
      border-color: #e25822;
      color: white;
      padding: 0.5rem 1.2rem;
      font-weight: 500;
    }
    .search-btn-custom:hover {
      background: #c9471a;
      border-color: #c9471a;
    }
    .clear-filter-link {
      font-size: 0.8rem;
      cursor: pointer;
      text-decoration: none;
    }
    .filter-badge-active {
      background: #e2582210;
      color: #e25822;
      border-radius: 50px;
      padding: 0.25rem 0.75rem;
      font-size: 0.75rem;
    }
    /* end filter styles */
  </style>
</head>

<body>
<?=translation_icon()?>
  <!-- Navbar -->
  <nav class="navbar navbar-autoparts navbar-dark py-3 sticky-top">
    <div class="container">
      <a class="navbar-brand brand-text fs-4" class="exitbtn" href="/logout">
        <i class="bi bi-gear-wide-connected me-2 text-warning"></i><span class="text-warning"><?= variable('appname') ?></span>
      </a>
      <div class="d-flex gap-2 align-items-center">
        <!-- Cart Button in Top-Right -->
        <button class="btn btn-outline-light rounded-pill position-relative" type="button" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas" aria-controls="cartOffcanvas">
          <i class="bi bi-cart-fill"></i>
          <span id="cartBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">0</span>
        </button>
        <button class="btn btn-primary rounded-pill" type="button" id="startover">
          <i class="bi bi-arrow-repeat"></i>
        </button>
        <button class="btn bg-white text-dark rounded-pill" type="button" id="">
        <i class="bi bi-clock-history"></i>
        </button>
        <button class="btn btn-danger rounded-pill" type="button" id="exitButtonMain">
          <i class="bi bi-power"></i>
        </button>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <div class="container my-4">
    <div class="row g-4">
      <!-- Products Section (Full width on all screens) -->
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
          <h4 class="fw-bold"><i class="bi bi-grid-3x3-gap-fill text-warning"></i> <span id="user_name">Unknown user</span></h4>
        </div>

        <!-- ========== FILTER SECTION: Input + Combobox + Search Button ========== -->
        <div class="filter-section">
          <div class="row g-3 align-items-end">
            <!-- Search Input Field -->
            <div class="col-md-5 col-12">
              <div class="filter-label"><i class="bi bi-search"></i> <?=t('Search product')?></div>
              <input type="text" class="form-control" id="searchInput" placeholder="<?=t('Type product name, SKU, or category...')?>" autocomplete="off">
            </div>
            <!-- Category Combobox -->
            <div class="col-md-4 col-12">
              <div class="filter-label"><i class="bi bi-tags"></i> <?=t('Category')?></div>
              <select class="form-select" id="categorySelect">
                <option value="all">All Categories</option>
                <option value="Brakes">Brakes</option>
                <option value="Engine">Engine</option>
                <option value="Lighting">Lighting</option>
                <option value="Exterior">Exterior</option>
                <option value="Ignition">Ignition</option>
                <option value="Tools">Tools</option>
              </select>
            </div>
            <!-- Search Button + Clear action -->
            <div class="col-md-3 col-12">
              <div class="d-flex gap-2">
                <button class="btn search-btn-custom px-4 flex-grow-1" id="searchButton">
                  <i class="bi bi-funnel-fill"></i> Filter
                </button>
                <button class="btn btn-outline-secondary" id="clearFilterBtn" title="Clear filters">
                  <i class="bi bi-arrow-counterclockwise"></i>
                </button>
              </div>
            </div>
          </div>
          <!-- Active filter hint area (can be populated via JS) -->
          <div class="mt-3" id="activeFilterHint"></div>
        </div>
        <!-- ========== END FILTER SECTION ========== -->

        <div class="row g-3" id="productsContainer">
          <!-- Products will be injected dynamically -->
        </div>
      </div>
    </div>
  </div>

  <!-- Floating Cart Button (Mobile/General) -->
  <div class="cart-float-btn d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas" aria-controls="cartOffcanvas">
    <i class="bi bi-cart-fill"></i>
    <span class="cart-float-badge" id="cartFloatBadge">0</span>
  </div>

  <!-- Desktop Cart Button (Visible on large screens) -->
  <div class="d-none d-lg-block position-fixed" style="bottom: 20px; right: 20px; z-index: 1000;">
    <button class="btn btn-autoparts-primary rounded-pill px-4 py-3 shadow-lg" type="button" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas">
      <i class="bi bi-cart-fill"></i> <?=t('View Cart')?> (<span id="cartCountDesktopFixed">0</span>)
    </button>
  </div>

  <!-- Cart Offcanvas (General Cart - Works for all devices) -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="cartOffcanvas" aria-labelledby="cartOffcanvasLabel" style="width: 400px; max-width: 85%;">
    <div class="offcanvas-header bg-dark text-white">
      <h5 class="offcanvas-title" id="cartOffcanvasLabel"><i class="bi bi-cart-fill"></i> Shopping Cart</h5>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body" id="cartItemsList" style="max-height: 60vh; overflow-y: auto;">
      <div class="text-center text-muted py-4">Your cart is empty.</div>
    </div>
    <div class="offcanvas-footer border-top p-3">
      <!-- Shipping Address Selection -->
      <div class="mb-3">
        <label class="form-label fw-semibold"><i class="bi bi-geo-alt-fill text-warning"></i> Shipping Address</label>
        <select class="form-select" id="shippingaddress" required>
          <option value="">Select address...</option>
        </select>
      </div>
      <div class="d-flex justify-content-between fw-bold mb-3">
        <span>Subtotal:</span>
        <span id="cartSubtotal">₱0.00</span>
      </div>
      <div class="d-flex justify-content-between text-muted small mb-2">
        <span>Shipping Fee:</span>
        <span id="cartShippingFee">₱0.00</span>
      </div>
      <div class="d-flex justify-content-between fw-bold mb-3 border-top pt-2">
        <span>Total:</span>
        <span id="cartTotalAmount" class="text-warning">₱0.00</span>
      </div>
      <button class="btn btn-autoparts-primary w-100 py-2 rounded-pill" id="checkoutBtnCart">
        Proceed to Checkout <i class="bi bi-arrow-right"></i>
      </button>
    </div>
  </div>

  <!-- Checkout Modal -->
  <div class="modal fade" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 border-0 shadow">
        <div class="modal-header bg-dark text-white rounded-top-4">
          <h5 class="modal-title fw-bold"><i class="bi bi-credit-card"></i> Complete Order</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4">
          <div class="mb-3 p-3 bg-light rounded-3">
            <div class="d-flex justify-content-between mb-2">
              <span>Total Items:</span>
              <strong id="checkoutTotalItems">0</strong>
            </div>
            <div class="d-flex justify-content-between mb-2">
              <span>Subtotal:</span>
              <strong id="checkoutSubtotal">₱0.00</strong>
            </div>
            <div class="d-flex justify-content-between mb-2">
              <span>Shipping Fee:</span>
              <strong id="checkoutShippingFee">₱0.00</strong>
            </div>
            <hr>
            <div class="d-flex justify-content-between fs-5 fw-bold">
              <span>Total:</span>
              <span class="text-warning" id="checkoutTotal">₱0.00</span>
            </div>
          </div>
          <form id="checkoutForm">
            <div class="mb-2">
              <label class="form-label small">Full Name</label>
              <input type="text" class="form-control" id="checkoutName" placeholder="Enter name..." >
            </div>
            <div class="mb-2">
              <label class="form-label small">Contact number</label>
              <input type="text" class="form-control" id="contactnum">
            </div>
            <div class="mb-2">
              <label class="form-label small">Email Address</label>
              <input type="email" class="form-control" id="checkoutEmail" placeholder="customer@autoparts.com">
            </div>
            <div class="mb-2">
              <label class="form-label small">City/Municipality</label>
              <select class="form-control" name="" id="city"></select>
            </div>
            <div class="mb-2">
              <label class="form-label small">Barangay</label>
              <select class="form-control" name="" id="brgy"></select>
            </div>
            <div class="mb-3">
              <label class="form-label small">Town/Zone/Purok</label>
              <textarea class="form-control" id="fulladdress" rows="2" placeholder="Zone, Purok, Street"></textarea>
            </div>
            <button type="submit" class="btn btn-autoparts-primary w-100 py-2 rounded-pill">Place Order <i class="bi bi-check-lg"></i></button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Order Confirmation Toast -->
  <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
    <div id="orderToast" class="toast align-items-center text-white bg-success border-0" role="alert">
      <div class="d-flex">
        <div class="toast-body">✅ Order placed successfully! Thank you.</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <?= js() ?>
</body>

</html>