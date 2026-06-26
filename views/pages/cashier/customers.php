<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
  <title>AutoParts Admin | Customer Management</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?=assets('cashier.css')?>">
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
    
    .status-badge-customer {
      padding: 0.25rem 0.75rem;
      border-radius: 50px;
      font-size: 0.7rem;
      font-weight: 600;
    }
    .status-active { background: #19875420; color: #0a5c36; border: 1px solid #19875460; }
    .status-inactive { background: #dc354520; color: #a71d2a; border: 1px solid #dc354560; }
    .status-verified { background: #00a89620; color: #028090; border: 1px solid #00a89660; }
    .status-unverified { background: #ffc10720; color: #b78103; border: 1px solid #ffc10760; }
    
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
  </style>
</head>
<body>

<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<?=include_page("cashier/sidebar")?>

<div class="main-content-wrapper" id="mainContentWrapper">
  <?=include_page('cashier/navbar', ["pagename"=>"Customer Management"])?>

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
                       placeholder="🔍 Search by name, email, phone, or address..." 
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
          
          <!-- RIGHT SIDE: FILTERS -->
          <div class="add-button-section d-flex gap-2">
            <select class="form-select form-select-sm" id="statusFilterSelect" style="width: auto;">
              <option value="all">All Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
              <option value="verified">Verified</option>
              <option value="unverified">Unverified</option>
            </select>
            <button class="btn btn-autoparts-primary rounded-pill px-4 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
              <i class="bi bi-person-plus"></i> Add Customer
            </button>
          </div>
        </div>
        <div class="mt-2">
          <p class="text-muted small mb-0"><i class="bi bi-info-circle"></i> Manage customer accounts and view purchase history</p>
        </div>
      </div>
      
      <div class="card-body p-3 p-md-4">
        <!-- STATISTICS -->
        <div class="customer-stats" id="customerStats">
          <div class="stat-box">
            <div class="number" id="totalCustomers">0</div>
            <div class="label">Total Customers</div>
          </div>
          <div class="stat-box">
            <div class="number text-success" id="activeCustomers">0</div>
            <div class="label">Active</div>
          </div>
          <div class="stat-box">
            <div class="number text-warning" id="verifiedCustomers">0</div>
            <div class="label">Verified</div>
          </div>
          <div class="stat-box">
            <div class="number text-danger" id="inactiveCustomers">0</div>
            <div class="label">Inactive</div>
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
  <?=include_page("cashier/footer")?>
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
            <div class="col-md-6">
              <label class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="customerFirstName" name="first_name" placeholder="e.g., Juan" required>
              <div class="text-danger err" id="_first_name"></div>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="customerLastName" name="last_name" placeholder="e.g., Dela Cruz" required>
              <div class="text-danger err" id="_last_name"></div>
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
            <div class="col-md-6">
              <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="customerUsername" name="username" placeholder="juan_delacruz" required>
              <div class="text-danger err" id="_username"></div>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Status</label>
              <select class="form-select" id="customerStatus" name="status">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
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
            <label class="form-label fw-semibold">Shipping Address</label>
            <textarea class="form-control" id="customerAddress" name="address" rows="2" placeholder="Street, Barangay, City, Province"></textarea>
            <div class="text-danger err" id="_address"></div>
          </div>
          
          <div class="mt-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="customerVerified" name="verified">
              <label class="form-check-label" for="customerVerified">
                Mark as verified customer
              </label>
            </div>
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

<!-- EDIT CUSTOMER MODAL -->
<div class="modal fade" id="editCustomerModal" tabindex="-1" aria-labelledby="editCustomerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 rounded-4 shadow-lg">
      <div class="modal-header bg-dark text-white rounded-top-4 border-0">
        <h5 class="modal-title fw-bold">
          <i class="bi bi-pencil-square me-2 text-warning"></i> Edit Customer
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="editCustomerForm">
        <input type="hidden" id="editCustomerId">
        <div class="modal-body p-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="editCustomerFirstName" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="editCustomerLastName" required>
            </div>
          </div>
          
          <div class="row g-3 mt-1">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
              <input type="email" class="form-control" id="editCustomerEmail" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Contact Number <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="editCustomerContact" required>
            </div>
          </div>
          
          <div class="row g-3 mt-1">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="editCustomerUsername" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Status</label>
              <select class="form-select" id="editCustomerStatus">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
          </div>
          
          <div class="mt-3">
            <label class="form-label fw-semibold">Shipping Address</label>
            <textarea class="form-control" id="editCustomerAddress" rows="2"></textarea>
          </div>
          
          <div class="mt-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="editCustomerVerified">
              <label class="form-check-label" for="editCustomerVerified">
                Verified customer
              </label>
            </div>
          </div>
          
          <div class="mt-3">
            <div class="alert alert-info small">
              <i class="bi bi-info-circle"></i> Leave password fields empty to keep current password
            </div>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold">New Password</label>
                <div class="form-password-wrapper">
                  <input type="password" class="form-control" id="editCustomerPassword" placeholder="Leave empty to keep current">
                  <i class="bi bi-eye-slash password-toggle" id="toggleEditCustomerPassword"></i>
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Confirm New Password</label>
                <div class="form-password-wrapper">
                  <input type="password" class="form-control" id="editCustomerConfirmPassword" placeholder="Confirm new password">
                  <i class="bi bi-eye-slash password-toggle" id="toggleEditCustomerConfirmPassword"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light border-0 rounded-bottom-4">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-autoparts-primary px-4">
            <i class="bi bi-save"></i> Update Customer
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- DELETE CONFIRMATION MODAL -->
<div class="modal fade" id="deleteCustomerModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 rounded-4 shadow-lg">
      <div class="modal-header bg-danger text-white rounded-top-4">
        <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i> Delete Customer</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <input type="hidden" id="deleteCustomerId">
        <p>Are you sure you want to delete this customer?</p>
        <p class="text-muted small">This action cannot be undone. All associated data including orders will be permanently removed.</p>
        <div class="alert alert-warning small">
          <i class="bi bi-exclamation-triangle"></i> Consider deactivating instead of deleting if unsure.
        </div>
      </div>
      <div class="modal-footer bg-light border-0 rounded-bottom-4">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteCustomerBtn">Delete Customer</button>
      </div>
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

<script>
// Mock data for demo - replace with actual backend data
let customersData = [
    {
        id: 1,
        first_name: "John",
        last_name: "Dela Cruz",
        email: "john.delacruz@example.com",
        contact: "09123456789",
        username: "john_dc",
        status: "active",
        verified: true,
        address: "123 Mabini St., Barangay San Juan, Quezon City, Metro Manila",
        total_orders: 12,
        total_spent: 12450.00,
        created_at: "2025-01-15T10:30:00",
        last_login: "2025-06-08T14:20:00"
    },
    {
        id: 2,
        first_name: "Maria",
        last_name: "Santos",
        email: "maria.santos@example.com",
        contact: "09234567890",
        username: "maria_santos",
        status: "active",
        verified: true,
        address: "456 Rizal Ave., Barangay Poblacion, Makati City, Metro Manila",
        total_orders: 8,
        total_spent: 8750.00,
        created_at: "2025-02-20T14:20:00",
        last_login: "2025-06-07T09:15:00"
    },
    {
        id: 3,
        first_name: "Ramon",
        last_name: "Villanueva",
        email: "ramon.v@example.com",
        contact: "09345678901",
        username: "ramon_v",
        status: "active",
        verified: false,
        address: "789 Luna St., Barangay San Vicente, Cebu City, Cebu",
        total_orders: 3,
        total_spent: 2350.00,
        created_at: "2025-03-10T09:15:00",
        last_login: "2025-06-05T08:00:00"
    },
    {
        id: 4,
        first_name: "Lisa",
        last_name: "Garcia",
        email: "lisa.g@example.com",
        contact: "09456789012",
        username: "lisa_garcia",
        status: "inactive",
        verified: false,
        address: "321 Bonifacio St., Barangay San Pedro, Davao City, Davao del Sur",
        total_orders: 0,
        total_spent: 0.00,
        created_at: "2025-04-05T16:45:00",
        last_login: "2025-05-01T11:30:00"
    },
    {
        id: 5,
        first_name: "Michael",
        last_name: "Tan",
        email: "michael.tan@example.com",
        contact: "09567890123",
        username: "michael_tan",
        status: "active",
        verified: true,
        address: "654 Andres St., Barangay San Lorenzo, Pasig City, Metro Manila",
        total_orders: 5,
        total_spent: 4250.50,
        created_at: "2025-05-01T11:00:00",
        last_login: "2025-06-08T10:00:00"
    }
];

function formatDate(dateStr) {
    if (!dateStr) return 'Never';
    let d = new Date(dateStr);
    return d.toLocaleDateString('en-PH') + " " + d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

function getInitials(firstName, lastName) {
    return (firstName?.[0] || '') + (lastName?.[0] || '');
}

function getStatusBadge(status, verified) {
    if (status === 'active' && verified) {
        return '<span class="status-badge-customer status-verified"><i class="bi bi-check-circle"></i> Verified</span>';
    } else if (status === 'active' && !verified) {
        return '<span class="status-badge-customer status-unverified"><i class="bi bi-clock"></i> Unverified</span>';
    } else if (status === 'inactive') {
        return '<span class="status-badge-customer status-inactive"><i class="bi bi-x-circle"></i> Inactive</span>';
    }
    return '<span class="status-badge-customer status-active"><i class="bi bi-check-circle"></i> Active</span>';
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

function showToast(msg, type = 'success') {
    const toastEl = document.getElementById('actionToast');
    const toastMsg = document.getElementById('toastMsg');
    toastMsg.innerText = msg;
    toastEl.classList.remove('bg-success', 'bg-danger', 'bg-warning');
    if (type === 'success') toastEl.classList.add('bg-success');
    else if (type === 'danger') toastEl.classList.add('bg-danger');
    else toastEl.classList.add('bg-warning');
    const bsToast = new bootstrap.Toast(toastEl, { delay: 2000 });
    bsToast.show();
}

function updateStats() {
    const total = customersData.length;
    const active = customersData.filter(c => c.status === 'active').length;
    const verified = customersData.filter(c => c.verified === true).length;
    const inactive = customersData.filter(c => c.status === 'inactive').length;
    
    document.getElementById('totalCustomers').innerText = total;
    document.getElementById('activeCustomers').innerText = active;
    document.getElementById('verifiedCustomers').innerText = verified;
    document.getElementById('inactiveCustomers').innerText = inactive;
}

function renderCustomers() {
    const searchVal = document.getElementById('customerSearchInput').value.toLowerCase();
    const statusVal = document.getElementById('statusFilterSelect').value;
    
    let filtered = [...customersData];
    
    if (searchVal) {
        filtered = filtered.filter(customer => 
            customer.first_name.toLowerCase().includes(searchVal) ||
            customer.last_name.toLowerCase().includes(searchVal) ||
            customer.email.toLowerCase().includes(searchVal) ||
            customer.contact.toLowerCase().includes(searchVal) ||
            (customer.address && customer.address.toLowerCase().includes(searchVal))
        );
    }
    
    if (statusVal !== 'all') {
        if (statusVal === 'verified') {
            filtered = filtered.filter(c => c.verified === true);
        } else if (statusVal === 'unverified') {
            filtered = filtered.filter(c => c.verified === false);
        } else {
            filtered = filtered.filter(c => c.status === statusVal);
        }
    }
    
    const container = document.getElementById('customersContainer');
    document.getElementById('customerCount').innerText = filtered.length;
    updateStats();
    
    if (filtered.length === 0) {
        container.innerHTML = `
            <div class="empty-customers">
                <i class="bi bi-people fs-1 text-muted"></i>
                <h5 class="mt-3 text-muted">No customers found</h5>
                <p class="text-muted">Click the "Add Customer" button to create one</p>
            </div>`;
        return;
    }
    
    let html = '<div class="customer-grid">';
    filtered.forEach(customer => {
        const initials = getInitials(customer.first_name, customer.last_name);
        const fullName = `${customer.first_name} ${customer.last_name}`;
        
        html += `
            <div class="customer-card" data-customer-id="${customer.id}">
                <div class="customer-card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-placeholder">${initials}</div>
                        <div>
                            <h6 class="mb-0 fw-bold">${escapeHtml(fullName)}</h6>
                            <small class="text-muted">@${escapeHtml(customer.username)}</small>
                        </div>
                    </div>
                    ${getStatusBadge(customer.status, customer.verified)}
                </div>
                <div class="customer-card-body">
                    <div class="mb-2">
                        <small class="text-muted"><i class="bi bi-envelope"></i> Email</small>
                        <p class="mb-1 small">${escapeHtml(customer.email)}</p>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted"><i class="bi bi-phone"></i> Contact</small>
                        <p class="mb-1 small">${escapeHtml(customer.contact)}</p>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted"><i class="bi bi-geo-alt"></i> Address</small>
                        <p class="mb-1 small">${escapeHtml(customer.address) || 'No address set'}</p>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted"><i class="bi bi-bag"></i> Orders</small>
                            <p class="mb-0 small fw-bold">${customer.total_orders || 0}</p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted"><i class="bi bi-cash"></i> Total Spent</small>
                            <p class="mb-0 small fw-bold text-success">₱${(customer.total_spent || 0).toFixed(2)}</p>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-6">
                            <small class="text-muted"><i class="bi bi-calendar-plus"></i> Joined</small>
                            <p class="mb-0 small">${formatDate(customer.created_at)}</p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted"><i class="bi bi-clock"></i> Last Login</small>
                            <p class="mb-0 small">${formatDate(customer.last_login)}</p>
                        </div>
                    </div>
                </div>
                <div class="customer-card-footer">
                    <button class="btn btn-sm btn-outline-info view-customer-btn" data-id="${customer.id}">
                        <i class="bi bi-eye"></i> View
                    </button>
                    <button class="btn btn-sm btn-outline-primary edit-customer-btn" data-id="${customer.id}">
                        <i class="bi bi-pencil"></i> Edit
                    </button>
                    <button class="btn btn-sm btn-outline-danger delete-customer-btn" data-id="${customer.id}">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                </div>
            </div>
        `;
    });
    html += '</div>';
    container.innerHTML = html;
    
    document.querySelectorAll('.view-customer-btn').forEach(btn => {
        btn.addEventListener('click', () => viewCustomer(parseInt(btn.dataset.id)));
    });
    document.querySelectorAll('.edit-customer-btn').forEach(btn => {
        btn.addEventListener('click', () => openEditCustomerModal(parseInt(btn.dataset.id)));
    });
    document.querySelectorAll('.delete-customer-btn').forEach(btn => {
        btn.addEventListener('click', () => openDeleteCustomerModal(parseInt(btn.dataset.id)));
    });
}

function viewCustomer(customerId) {
    const customer = customersData.find(c => c.id === customerId);
    if (!customer) return;
    
    const modalBody = document.getElementById('viewCustomerBody');
    modalBody.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <h6 class="fw-bold"><i class="bi bi-person-circle text-success"></i> Personal Information</h6>
                <table class="table table-sm">
                    <tr><td class="text-muted">Full Name</td><td><strong>${escapeHtml(customer.first_name)} ${escapeHtml(customer.last_name)}</strong></td></tr>
                    <tr><td class="text-muted">Username</td><td>@${escapeHtml(customer.username)}</td></tr>
                    <tr><td class="text-muted">Email</td><td>${escapeHtml(customer.email)}</td></tr>
                    <tr><td class="text-muted">Contact</td><td>${escapeHtml(customer.contact)}</td></tr>
                    <tr><td class="text-muted">Status</td><td>${getStatusBadge(customer.status, customer.verified)}</td></tr>
                    <tr><td class="text-muted">Member Since</td><td>${formatDate(customer.created_at)}</td></tr>
                    <tr><td class="text-muted">Last Login</td><td>${formatDate(customer.last_login)}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="fw-bold"><i class="bi bi-geo-alt text-danger"></i> Address</h6>
                <div class="bg-light p-3 rounded-3">
                    ${escapeHtml(customer.address) || 'No address set'}
                </div>
                <hr>
                <h6 class="fw-bold"><i class="bi bi-shopping-bag text-warning"></i> Purchase Summary</h6>
                <table class="table table-sm">
                    <tr><td class="text-muted">Total Orders</td><td class="fw-bold">${customer.total_orders || 0}</td></tr>
                    <tr><td class="text-muted">Total Spent</td><td class="fw-bold text-success">₱${(customer.total_spent || 0).toFixed(2)}</td></tr>
                    <tr><td class="text-muted">Average Order</td><td class="fw-bold">₱${customer.total_orders > 0 ? (customer.total_spent / customer.total_orders).toFixed(2) : '0.00'}</td></tr>
                </table>
            </div>
        </div>
    `;
    
    new bootstrap.Modal(document.getElementById('viewCustomerModal')).show();
}

function openEditCustomerModal(customerId) {
    const customer = customersData.find(c => c.id === customerId);
    if (!customer) return;
    
    document.getElementById('editCustomerId').value = customer.id;
    document.getElementById('editCustomerFirstName').value = customer.first_name;
    document.getElementById('editCustomerLastName').value = customer.last_name;
    document.getElementById('editCustomerEmail').value = customer.email;
    document.getElementById('editCustomerContact').value = customer.contact;
    document.getElementById('editCustomerUsername').value = customer.username;
    document.getElementById('editCustomerStatus').value = customer.status;
    document.getElementById('editCustomerAddress').value = customer.address || '';
    document.getElementById('editCustomerVerified').checked = customer.verified || false;
    document.getElementById('editCustomerPassword').value = '';
    document.getElementById('editCustomerConfirmPassword').value = '';
    
    new bootstrap.Modal(document.getElementById('editCustomerModal')).show();
}

function openDeleteCustomerModal(customerId) {
    document.getElementById('deleteCustomerId').value = customerId;
    new bootstrap.Modal(document.getElementById('deleteCustomerModal')).show();
}

// Password toggle functions
function togglePasswordVisibility(inputId, toggleId) {
    const input = document.getElementById(inputId);
    const toggle = document.getElementById(toggleId);
    if (input && toggle) {
        toggle.addEventListener('click', function() {
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });
    }
}

// Initialize password toggles
togglePasswordVisibility('customerPassword', 'toggleCustomerPassword');
togglePasswordVisibility('customerConfirmPassword', 'toggleCustomerConfirmPassword');
togglePasswordVisibility('editCustomerPassword', 'toggleEditCustomerPassword');
togglePasswordVisibility('editCustomerConfirmPassword', 'toggleEditCustomerConfirmPassword');

// Add Customer Form Submit
document.getElementById('addCustomerForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const password = document.getElementById('customerPassword').value;
    const confirmPassword = document.getElementById('customerConfirmPassword').value;
    
    if (password !== confirmPassword) {
        showToast('Passwords do not match!', 'danger');
        return;
    }
    
    if (password.length < 6) {
        showToast('Password must be at least 6 characters!', 'danger');
        return;
    }
    
    const formData = {
        first_name: document.getElementById('customerFirstName').value,
        last_name: document.getElementById('customerLastName').value,
        email: document.getElementById('customerEmail').value,
        contact: document.getElementById('customerContact').value,
        username: document.getElementById('customerUsername').value,
        status: document.getElementById('customerStatus').value,
        address: document.getElementById('customerAddress').value,
        verified: document.getElementById('customerVerified').checked,
        password: password
    };
    
    // TODO: Implement your actual save logic here
    // Temporary demo
    const newId = Math.max(...customersData.map(c => c.id), 0) + 1;
    const newCustomer = {
        id: newId,
        ...formData,
        total_orders: 0,
        total_spent: 0,
        created_at: new Date().toISOString(),
        last_login: null
    };
    delete newCustomer.password;
    
    customersData.push(newCustomer);
    renderCustomers();
    bootstrap.Modal.getInstance(document.getElementById('addCustomerModal')).hide();
    document.getElementById('addCustomerForm').reset();
    showToast('Customer created successfully! (Demo)', 'success');
});

// Edit Customer Form Submit
document.getElementById('editCustomerForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const customerId = parseInt(document.getElementById('editCustomerId').value);
    const newPassword = document.getElementById('editCustomerPassword').value;
    const confirmPassword = document.getElementById('editCustomerConfirmPassword').value;
    
    if (newPassword && newPassword !== confirmPassword) {
        showToast('Passwords do not match!', 'danger');
        return;
    }
    
    if (newPassword && newPassword.length < 6) {
        showToast('Password must be at least 6 characters!', 'danger');
        return;
    }
    
    const formData = {
        id: customerId,
        first_name: document.getElementById('editCustomerFirstName').value,
        last_name: document.getElementById('editCustomerLastName').value,
        email: document.getElementById('editCustomerEmail').value,
        contact: document.getElementById('editCustomerContact').value,
        username: document.getElementById('editCustomerUsername').value,
        status: document.getElementById('editCustomerStatus').value,
        address: document.getElementById('editCustomerAddress').value,
        verified: document.getElementById('editCustomerVerified').checked
    };
    
    if (newPassword) {
        formData.password = newPassword;
    }
    
    // TODO: Implement your actual update logic here
    // Temporary demo
    const index = customersData.findIndex(c => c.id === customerId);
    if (index !== -1) {
        customersData[index] = { ...customersData[index], ...formData };
        delete customersData[index].password;
    }
    renderCustomers();
    bootstrap.Modal.getInstance(document.getElementById('editCustomerModal')).hide();
    showToast('Customer updated successfully! (Demo)', 'success');
});

// Delete Customer
document.getElementById('confirmDeleteCustomerBtn').addEventListener('click', async function() {
    const customerId = parseInt(document.getElementById('deleteCustomerId').value);
    
    // TODO: Implement your actual delete logic here
    // Temporary demo
    customersData = customersData.filter(c => c.id !== customerId);
    renderCustomers();
    bootstrap.Modal.getInstance(document.getElementById('deleteCustomerModal')).hide();
    showToast('Customer deleted successfully! (Demo)', 'success');
});

// Search functionality
document.getElementById('searchButton').addEventListener('click', () => renderCustomers());
document.getElementById('clearSearchButton').addEventListener('click', () => {
    document.getElementById('customerSearchInput').value = '';
    renderCustomers();
});
document.getElementById('statusFilterSelect').addEventListener('change', () => renderCustomers());
document.getElementById('customerSearchInput').addEventListener('keyup', (e) => {
    if (e.key === 'Enter') renderCustomers();
});

// Initial render
renderCustomers();
</script>

</body>
</html>
<?=js()?>