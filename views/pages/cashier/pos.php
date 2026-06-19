<?=include_page("cashier/filter")?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
  <title>AutoParts Admin | Point of Sale</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?=assets('cashier.css')?>">
  <style>
    .product-card {
      transition: all 0.2s ease;
      cursor: pointer;
      background: white;
    }
    .product-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      border-color: var(--bs-primary) !important;
    }
    .product-card.out-of-stock {
      opacity: 0.6;
      background: #f8f9fa;
    }
    .product-card.out-of-stock .add-to-cart-btn {
      opacity: 0.5;
      pointer-events: none;
    }
    .cart-items-container {
      max-height: 350px;
      overflow-y: auto;
    }
    .cart-item {
      transition: background 0.2s ease;
    }
    .cart-item:hover {
      background: #f8f9fa;
    }
    .payment-section {
      background: #f8f9fa;
      padding: 15px;
      border-radius: 10px;
    }
    @media (max-width: 768px) {
      .product-card .product-img {
        width: 60px !important;
        height: 60px !important;
      }
    }
    .cart-items-container::-webkit-scrollbar {
      width: 5px;
    }
    .cart-items-container::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 10px;
    }
    .cart-items-container::-webkit-scrollbar-thumb {
      background: #888;
      border-radius: 10px;
    }
    .cart-items-container::-webkit-scrollbar-thumb:hover {
      background: #555;
    }
    #receiptContent {
      font-size: 14px;
    }
    #receiptContent hr {
      margin: 8px 0;
    }
    .pos-quantity-badge {
      position: absolute;
      top: -5px;
      right: -5px;
      background: #dc3545;
      color: white;
      border-radius: 50%;
      padding: 2px 6px;
      font-size: 10px;
      font-weight: bold;
    }
    .product-card {
      position: relative;
    }
  </style>
</head>
<body>

<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<?=include_page("cashier/sidebar")?>

<div class="main-content-wrapper" id="mainContentWrapper">
  <?=include_page('cashier/navbar', ["pagename"=>"Point of Sale"])?>

  <div class="content-inner">
    <!-- POS SYSTEM -->
    <div class="row g-4">
      <!-- LEFT COLUMN: PRODUCT CATALOG -->
      <div class="col-lg-8">
        <div class="card admin-card border-0 shadow-sm">
          <div class="card-header bg-white border-0 pt-4 pb-0">
            <div class="header-actions">
              <!-- SEARCH BAR -->
              <div class="search-section">
                <div class="search-container">
                  <div class="input-group search-input-group">
                    <input type="text" class="form-control search-input" id="posSearchInput" 
                           placeholder="🔍 <?=t('Search products by name...')?>" 
                           aria-label="Search products">
                    <button class="btn search-btn" id="posSearchButton" type="button">
                      <i class="bi bi-search"></i>
                    </button>
                    <button class="btn clear-search-btn" id="posClearSearch" type="button" title="Clear search">
                      <i class="bi bi-x-lg"></i>
                    </button>
                  </div>
                </div>
              </div>
              
              <!-- CATEGORY FILTER -->
              <div class="add-button-section">
                <select class="form-select rounded-pill" id="categoryFilter" style="width: auto;">
                  <option value="">All Categories</option>
                </select>
              </div>
            </div>
            <div class="mt-2">
              <p class="text-muted small mb-0"><i class="bi bi-grid-3x3-gap-fill"></i> <?=t('Select products to add to cart')?></p>
            </div>
          </div>
          <div class="card-body p-3 p-md-4">
            <!-- PRODUCT GRID -->
            <div class="row g-3" id="productGrid">
              <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
              </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3">
              <small class="text-muted">Showing <span id="posRowCount">0</span> products</small>
              <small class="text-muted" id="posSearchStatus"></small>
            </div>
          </div>
        </div>
      </div>

      <!-- RIGHT COLUMN: CART & PAYMENT -->
      <div class="col-lg-4">
        <div class="card admin-card border-0 shadow-sm">
          <div class="card-header bg-white border-0 pt-4 pb-0">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="fw-bold mb-0"><i class="bi bi-cart-fill text-warning"></i> Current Order <span id="cartBadge" class="badge bg-danger rounded-pill" style="display:none;">0</span></h5>
              <button class="btn btn-sm btn-outline-danger rounded-circle" id="clearCartBtn" title="Clear Cart">
                <i class="bi bi-trash3"></i>
              </button>
            </div>
          </div>
          <div class="card-body p-3">
            <!-- CART ITEMS -->
            <div class="cart-items-container" id="cartItems">
              <div class="text-center text-muted py-4" id="emptyCartMessage">
                <i class="bi bi-cart-plus display-6 d-block mb-2"></i>
                <small>No items in cart</small>
              </div>
            </div>

            <!-- CART SUMMARY -->
            <div class="border-top pt-3 mt-2">
              <div class="d-flex justify-content-between mb-1">
                <span class="text-muted">Subtotal:</span>
                <span class="fw-bold" id="cartSubtotal">₱0.00</span>
              </div>
              <div class="d-flex justify-content-between mb-1">
                <span class="text-muted">Tax (0%):</span>
                <span class="fw-bold" id="cartTax">₱0.00</span>
              </div>
              <div class="d-flex justify-content-between mb-3">
                <span class="h5 mb-0">Total:</span>
                <span class="h5 mb-0 text-success fw-bold" id="cartTotal">₱0.00</span>
              </div>

              <!-- PAYMENT SECTION -->
              <div class="payment-section">
                <div class="input-group mb-2">
                  <span class="input-group-text">₱</span>
                  <input type="number" class="form-control" id="paymentAmount" placeholder="Enter payment amount" step="0.01" min="0">
                  <button class="btn btn-autoparts-primary" id="payNowBtn" type="button">
                    <i class="bi bi-credit-card"></i> Pay
                  </button>
                </div>
                <div id="changeDisplay" class="text-center mt-2" style="display: none;">
                  <span class="badge bg-success p-2 fs-6">Change: <span id="changeAmount">₱0.00</span></span>
                </div>
                <div class="d-grid gap-2 mt-2">
                  <button class="btn btn-outline-secondary btn-sm" id="quickPayBtn">
                    <i class="bi bi-cash"></i> Quick Pay (Exact Amount)
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- FOOTER -->
  <?=include_page("cashier/footer")?>
</div>

<!-- RECEIPT MODAL -->
<div class="modal fade" id="receiptModal" tabindex="-1" aria-labelledby="receiptModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 rounded-4 shadow-lg">
      <div class="modal-header bg-success text-white rounded-top-4 border-0">
        <h5 class="modal-title fw-bold"><i class="bi bi-check-circle-fill me-2"></i> Payment Successful!</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4" id="receiptContent">
        <!-- Dynamic receipt content -->
      </div>
      <div class="modal-footer bg-light border-0 rounded-bottom-4">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-autoparts-primary" id="printReceiptBtn">
          <i class="bi bi-printer"></i> Print Receipt
        </button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
<?=js()?>