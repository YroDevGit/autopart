<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
  <title>AutoParts Admin | Customer Management</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= assets('cashier.css') ?>">
  <style>
    /* Customer Management Custom Styles */
    .customer-card {
      background: white;
      border-radius: 1rem;
      transition: transform 0.2s, box-shadow 0.2s;
      border: 1px solid #e9ecef;
      overflow: hidden;
    }

    .customer-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .customer-card-header {
      background: linear-gradient(135deg, #00a89615 0%, #02809010 100%);
      padding: 1rem;
      border-bottom: 2px solid #00a896;
    }

    .customer-card-body {
      padding: 1.25rem;
    }

    .customer-card-footer {
      padding: 1rem;
      background: #f8f9fa;
      border-top: 1px solid #e9ecef;
      display: flex;
      gap: 0.5rem;
      justify-content: flex-end;
    }

    .customer-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
      gap: 1.5rem;
    }

    .empty-customers {
      text-align: center;
      padding: 4rem;
      background: white;
      border-radius: 1rem;
    }

    .customer-avatar {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      object-fit: cover;
      background: #e9ecef;
    }

    .avatar-placeholder {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      background: linear-gradient(135deg, #00a896 0%, #028090 100%);
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      font-weight: 700;
    }

    .toast-custom {
      position: fixed;
      bottom: 20px;
      right: 20px;
      z-index: 1100;
    }

    .customer-stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
      margin-bottom: 1.5rem;
    }

    .stat-box {
      background: white;
      border-radius: 1rem;
      padding: 1.25rem;
      text-align: center;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
      border: 1px solid #e9ecef;
    }

    .stat-box .number {
      font-size: 2rem;
      font-weight: 700;
      color: #00a896;
    }

    .stat-box .label {
      font-size: 0.8rem;
      color: #6c757d;
      margin-top: 0.25rem;
    }

    /* View Customer Modal Styles */
    .detail-row {
      display: flex;
      padding: 0.5rem 0;
      border-bottom: 1px solid #f0f0f0;
    }

    .detail-row:last-child {
      border-bottom: none;
    }

    .detail-label {
      width: 140px;
      color: #6c757d;
      font-weight: 500;
      flex-shrink: 0;
    }

    .detail-value {
      flex: 1;
      font-weight: 500;
    }
  </style>
</head>

<body>

  <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

  <?= include_page("cashier/sidebar") ?>

  <div class="main-content-wrapper" id="mainContentWrapper">
    <?= include_page('cashier/navbar', ["pagename" => "Customer Management"]) ?>

    <div class="content-inner">
      <!-- CUSTOMER MANAGEMENT CARD -->
      <div class="card admin-card border-0 shadow-sm">
        <div class="card-header bg-white border-0 pt-4 pb-0">
          <div class="header-actions">
            <!-- LEFT SIDE: SEARCH BAR -->
            <div class="search-section">
              <div class="search-container">
                <div class="input-group search-input-group">
                  <input type="text" class="form-control search-input" id="customerSearchInput"
                    placeholder="🔍 Search by name, email, contact, or address..."
                    aria-label="Search customers">
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
            <div class="add-button-section" style="display: none;">
              <button class="btn btn-autoparts-primary rounded-pill px-4 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                <i class="bi bi-person-plus"></i> Add Customer
              </button>
            </div>
          </div>
          <div class="mt-2">
            <p class="text-muted small mb-0"><i class="bi bi-info-circle"></i> Manage customer accounts and view customer details</p>
          </div>
        </div>

        <div class="card-body p-3 p-md-4">
          <!-- STATISTICS -->
          <div class="customer-stats" id="customerStats" style="display: none;">
            <div class="stat-box">
              <div class="number" id="totalCustomers">0</div>
              <div class="label">Total Customers</div>
            </div>
            <div class="stat-box">
              <div class="number text-success" id="customersWithOrders">0</div>
              <div class="label">With Orders</div>
            </div>
            <div class="stat-box">
              <div class="number text-warning" id="customersNoOrders">0</div>
              <div class="label">No Orders</div>
            </div>
          </div>

          <!-- CUSTOMERS GRID -->
          <div id="customersContainer">
            <div class="text-center py-5">
              <div class="spinner-border text-success" role="status"></div>
              <p class="mt-2 text-muted">Loading customers...</p>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center mt-3">
            <small class="text-muted">Showing <span id="customerCount">0</span> customers</small>
            <small class="text-muted" id="searchStatus"></small>
          </div>
        </div>
      </div>
    </div>

    <!-- FOOTER -->
    <?= include_page("cashier/footer") ?>
  </div>

  <!-- ADD CUSTOMER MODAL -->
  <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content border-0 rounded-4 shadow-lg">
        <div class="modal-header bg-dark text-white rounded-top-4 border-0">
          <h5 class="modal-title fw-bold" id="addCustomerModalLabel">
            <i class="bi bi-person-plus-fill me-2 text-warning"></i> Add New Customer
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="addCustomerForm">
          <div class="modal-body p-4">
            <div class="row g-3">
              <div class="col-md-12">
                <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="customerFullname" name="fullname" placeholder="e.g., Juan Dela Cruz" required>
                <div class="text-danger err" id="_fullname"></div>
              </div>
            </div>

            <div class="row g-3 mt-1">
              <div class="col-md-6">
                <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="customerEmail" name="email" placeholder="customer@example.com" required>
                <div class="text-danger err" id="_email"></div>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Contact Number <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="customerContact" name="contact" placeholder="09123456789" required>
                <div class="text-danger err" id="_contact"></div>
              </div>
            </div>

            <div class="row g-3 mt-1">
              <div class="col-md-12">
                <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="customerUsername" name="username" placeholder="juan_delacruz" required>
                <div class="text-danger err" id="_username"></div>
              </div>
            </div>

            <div class="row g-3 mt-1">
              <div class="col-md-6">
                <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                <div class="form-password-wrapper">
                  <input type="password" class="form-control" id="customerPassword" name="password" placeholder="Min 6 characters" required>
                  <i class="bi bi-eye-slash password-toggle" id="toggleCustomerPassword"></i>
                </div>
                <small class="text-muted">Minimum 6 characters with at least one number</small>
                <div class="text-danger err" id="_password"></div>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Confirm Password <span class="text-danger">*</span></label>
                <div class="form-password-wrapper">
                  <input type="password" class="form-control" id="customerConfirmPassword" name="confirm_password" placeholder="Confirm password" required>
                  <i class="bi bi-eye-slash password-toggle" id="toggleCustomerConfirmPassword"></i>
                </div>
                <div class="text-danger err" id="_confirm_password"></div>
              </div>
            </div>

            <div class="mt-3">
              <label class="form-label fw-semibold">Address <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="customerAddress" name="address" placeholder="Barangay, City/Municipality">
              <div class="text-danger err" id="_address"></div>
            </div>

            <div class="mt-2">
              <label class="form-label fw-semibold">Full Address <span class="text-danger">*</span></label>
              <textarea class="form-control" id="customerFulladdress" name="fulladdress" rows="2" placeholder="Street, Barangay, City, Province" required></textarea>
              <div class="text-danger err" id="_fulladdress"></div>
            </div>
          </div>
          <div class="modal-footer bg-light border-0 rounded-bottom-4">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-autoparts-primary px-4">
              <i class="bi bi-person-plus"></i> Create Customer
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- VIEW CUSTOMER DETAILS MODAL -->
  <div class="modal fade" id="viewCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content border-0 rounded-4 shadow-lg">
        <div class="modal-header bg-dark text-white rounded-top-4 border-0">
          <h5 class="modal-title fw-bold"><i class="bi bi-person-badge me-2 text-warning"></i> Customer Details</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4" id="viewCustomerBody">
          <div class="text-center py-3">Loading...</div>
        </div>
        <div class="modal-footer bg-light border-0 rounded-bottom-4">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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

</body>

</html>
<?= js() ?>