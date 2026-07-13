<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
  <title>AutoParts Admin | Shipping Address Management</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?=assets('cashier.css')?>">
  <style>
    /* Shipping Address Custom Styles */
    .address-card {
      background: white;
      border-radius: 1rem;
      transition: transform 0.2s, box-shadow 0.2s;
      border: 1px solid #e9ecef;
      overflow: hidden;
    }
    
    .address-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }
    
    .address-card-header {
      background: linear-gradient(135deg, #00a89615 0%, #02809010 100%);
      padding: 1rem;
      border-bottom: 2px solid #00a896;
    }
    
    .address-card-body {
      padding: 1.25rem;
    }
    
    .address-card-footer {
      padding: 1rem;
      background: #f8f9fa;
      border-top: 1px solid #e9ecef;
      display: flex;
      gap: 0.5rem;
      justify-content: flex-end;
    }
    
    .shipping-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
      gap: 1.5rem;
    }
    
    .price-tag {
      font-size: 1.25rem;
      font-weight: 700;
      color: #00a896;
    }
    
    .address-type-badge {
      padding: 0.25rem 0.75rem;
      border-radius: 50px;
      font-size: 0.7rem;
      font-weight: 600;
      background: #00a89620;
      color: #028090;
    }
    
    .empty-address {
      text-align: center;
      padding: 4rem;
      background: white;
      border-radius: 1rem;
    }
    
    .toast-custom {
      position: fixed;
      bottom: 20px;
      right: 20px;
      z-index: 1100;
    }
    
    .default-badge {
      background: #00a896;
      color: white;
      padding: 0.25rem 0.5rem;
      border-radius: 50px;
      font-size: 0.65rem;
      font-weight: 600;
    }
  </style>
</head>
<body>

<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<?=include_page("cashier/sidebar")?>

<div class="main-content-wrapper" id="mainContentWrapper">
  <?=include_page('cashier/navbar', ["pagename"=>"Shipping Address", "icon"=>"bi-truck"])?>

  <div class="content-inner">
    <!-- SHIPPING ADDRESS MANAGEMENT CARD -->
    <div class="card admin-card border-0 shadow-sm">
      <div class="card-header bg-white border-0 pt-4 pb-0">
        <div class="header-actions">
          <!-- LEFT SIDE: SEARCH BAR -->
          <div class="search-section">
            <div class="search-container">
              <div class="input-group search-input-group">
                <input type="text" class="form-control search-input" id="addressSearchInput" 
                       placeholder="🔍 Search by location, city, or province..." 
                       aria-label="Search addresses">
                <button class="btn search-btn" id="searchButton" type="button">
                  <i class="bi bi-search"></i>
                </button>
                <button class="btn clear-search-btn" id="clearSearchButton" type="button" title="Clear search">
                  <i class="bi bi-x-lg"></i>
                </button>
              </div>
            </div>
          </div>
          
          <!-- RIGHT SIDE: ADD BUTTON -->
          <div class="add-button-section">
            <button class="btn btn-autoparts-primary rounded-pill px-4 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#addAddressModal">
              <i class="bi bi-plus-circle"></i> Add Shipping Address
            </button>
          </div>
        </div>
        <div class="mt-2">
          <p class="text-muted small mb-0"><i class="bi bi-info-circle"></i> Manage shipping addresses and delivery fees for customer checkout</p>
        </div>
      </div>
      
      <div class="card-body p-3 p-md-4">
        <!-- SHIPPING ADDRESSES GRID -->
        <div id="addressesContainer">
          <div class="text-center py-5">
            <div class="spinner-border text-success" role="status"></div>
            <p class="mt-2 text-muted">Loading shipping addresses...</p>
          </div>
        </div>
        
        <div class="d-flex justify-content-between align-items-center mt-3">
          <small class="text-muted">Showing <span id="addressCount">0</span> shipping addresses</small>
          <small class="text-muted" id="searchStatus"></small>
        </div>
      </div>
    </div>
  </div>

  <!-- FOOTER -->
  <?=include_page("cashier/footer")?>
</div>

<!-- ADD SHIPPING ADDRESS MODAL -->
<div class="modal fade" id="addAddressModal" tabindex="-1" aria-labelledby="addAddressModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 rounded-4 shadow-lg">
      <div class="modal-header bg-dark text-white rounded-top-4 border-0">
        <h5 class="modal-title fw-bold" id="addAddressModalLabel">
          <i class="bi bi-geo-alt-fill me-2 text-warning"></i> Add Shipping Address
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="addAddressForm">
        <div class="modal-body p-4">
        <!--
          <div class="mb-3">
            <label class="form-label fw-semibold">Location Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="addressLocation" name="location" placeholder="e.g., Metro Manila, Cebu, Davao" required>
            <div class="text-danger err" id="_location"></div>
          </div>
          
          <div class="mb-3">
            <label class="form-label fw-semibold">Full Address <span class="text-danger">*</span></label>
            <textarea class="form-control" id="addressFull" name="full_address" rows="2" placeholder="Street, Barangay, City, Province" required></textarea>
            <div class="text-danger err" id="_full_address"></div>
          </div>

-->
          
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">City/Municipality <span class="text-danger">*</span></label>
              <select required class="form-control" name="city" id="city"></select>
              <div class="text-danger err" id="_city"></div>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Barangay <span class="text-danger">*</span></label>
              <select required class="form-control" name="brgy" id="brgy"></select>
              <div class="text-danger err" id="_province"></div>
            </div>
          </div>
          
          <div class="row g-3 mt-2">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Shipping Fee <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">₱</span>
                <input type="number" class="form-control" id="addressFee" name="shipping" step="0.01" min="0" placeholder="0.00" required>
              </div>
              <div class="text-danger err" id="_fee"></div>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Estimated Days</label>
              <input type="number" class="form-control"  id="addressDays" name="estimated" placeholder="e.g., 2-3 business days">
              <div class="text-danger err" id="_estimated_days"></div>
            </div>
          </div>
          
          <div class="mt-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="addressDefault" name="is_default" value="1">
              <label class="form-check-label" for="addressDefault">
                Set as default shipping address
              </label>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light border-0 rounded-bottom-4">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-autoparts-primary px-4">
            <i class="bi bi-save"></i> Add Address
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- EDIT SHIPPING ADDRESS MODAL -->
<div class="modal fade" id="editAddressModal" tabindex="-1" aria-labelledby="editAddressModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 rounded-4 shadow-lg">
      <div class="modal-header bg-dark text-white rounded-top-4 border-0">
        <h5 class="modal-title fw-bold">
          <i class="bi bi-pencil-square me-2 text-warning"></i> Edit Shipping Address
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="editAddressForm">
        <input type="hidden" id="editAddressId">
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label fw-semibold">Location Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="editAddressLocation" name="location" required>
          </div>
          
          <div class="mb-3">
            <label class="form-label fw-semibold">Full Address <span class="text-danger">*</span></label>
            <textarea class="form-control" id="editAddressFull" name="full_address" rows="2" required></textarea>
          </div>
          
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">City/Municipality</label>
              <input type="text" class="form-control" id="editAddressCity" name="city">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Province</label>
              <input type="text" class="form-control" id="editAddressProvince" name="province">
            </div>
          </div>
          
          <div class="row g-3 mt-2">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Shipping Fee <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">₱</span>
                <input type="number" class="form-control" id="editAddressFee" name="fee" step="0.01" min="0" required>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Estimated Days</label>
              <input type="text" class="form-control" id="editAddressDays" name="estimated_days">
            </div>
          </div>
          
          <div class="mt-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="editAddressDefault" name="is_default" value="1">
              <label class="form-check-label" for="editAddressDefault">
                Set as default shipping address
              </label>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light border-0 rounded-bottom-4">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-autoparts-primary px-4">
            <i class="bi bi-save"></i> Update Address
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- DELETE CONFIRMATION MODAL -->
<div class="modal fade" id="deleteAddressModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 rounded-4 shadow-lg">
      <div class="modal-header bg-danger text-white rounded-top-4">
        <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i> Delete Shipping Address</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <input type="hidden" id="deleteAddressId">
        <p>Are you sure you want to delete this shipping address?</p>
        <p class="text-muted small">This action cannot be undone. Orders using this address will be affected.</p>
      </div>
      <div class="modal-footer bg-light border-0 rounded-bottom-4">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete Address</button>
      </div>
    </div>
  </div>
</div>

<!-- Toast Notification -->
<div class="toast-custom">
  <div id="actionToast" class="toast align-items-center text-white bg-success border-0" role="alert">
    <div class="d-flex">
      <div class="toast-body" id="toastMsg">✅ Action completed!</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>

</script>

</body>
</html>
<?=js()?>