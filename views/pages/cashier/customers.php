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
          <div class="add-button-section">
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
        <div class="customer-stats" id="customerStats">
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

<script>
// Mock data for demo - replace with actual backend data matching your table structure
let customersData = [
    {
        id: 1,
        fullname: "John Dela Cruz",
        contact: "09123456789",
        address: "Barangay San Juan, Quezon City",
        fulladdress: "123 Mabini St., Barangay San Juan, Quezon City, Metro Manila",
        username: "john_dc",
        email: "john.delacruz@example.com",
        total_orders: 12,
        total_spent: 12450.00,
        created_at: "2025-01-15T10:30:00"
    },
    {
        id: 2,
        fullname: "Maria Santos",
        contact: "09234567890",
        address: "Barangay Poblacion, Makati City",
        fulladdress: "456 Rizal Ave., Barangay Poblacion, Makati City, Metro Manila",
        username: "maria_santos",
        email: "maria.santos@example.com",
        total_orders: 8,
        total_spent: 8750.00,
        created_at: "2025-02-20T14:20:00"
    },
    {
        id: 3,
        fullname: "Ramon Villanueva",
        contact: "09345678901",
        address: "Barangay San Vicente, Cebu City",
        fulladdress: "789 Luna St., Barangay San Vicente, Cebu City, Cebu",
        username: "ramon_v",
        email: "ramon.v@example.com",
        total_orders: 3,
        total_spent: 2350.00,
        created_at: "2025-03-10T09:15:00"
    },
    {
        id: 4,
        fullname: "Lisa Garcia",
        contact: "09456789012",
        address: "Barangay San Pedro, Davao City",
        fulladdress: "321 Bonifacio St., Barangay San Pedro, Davao City, Davao del Sur",
        username: "lisa_garcia",
        email: "lisa.g@example.com",
        total_orders: 0,
        total_spent: 0.00,
        created_at: "2025-04-05T16:45:00"
    },
    {
        id: 5,
        fullname: "Michael Tan",
        contact: "09567890123",
        address: "Barangay San Lorenzo, Pasig City",
        fulladdress: "654 Andres St., Barangay San Lorenzo, Pasig City, Metro Manila",
        username: "michael_tan",
        email: "michael.tan@example.com",
        total_orders: 5,
        total_spent: 4250.50,
        created_at: "2025-05-01T11:00:00"
    }
];

function formatDate(dateStr) {
    if (!dateStr) return 'N/A';
    let d = new Date(dateStr);
    return d.toLocaleDateString('en-PH') + " " + d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

function getInitials(fullname) {
    if (!fullname) return '';
    const parts = fullname.split(' ');
    if (parts.length === 1) return parts[0][0] || '';
    return (parts[0]?.[0] || '') + (parts[parts.length - 1]?.[0] || '');
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
    const withOrders = customersData.filter(c => c.total_orders > 0).length;
    const noOrders = total - withOrders;
    
    document.getElementById('totalCustomers').innerText = total;
    document.getElementById('customersWithOrders').innerText = withOrders;
    document.getElementById('customersNoOrders').innerText = noOrders;
}

function renderCustomers() {
    const searchVal = document.getElementById('customerSearchInput').value.toLowerCase();
    
    let filtered = [...customersData];
    
    if (searchVal) {
        filtered = filtered.filter(customer => 
            customer.fullname.toLowerCase().includes(searchVal) ||
            customer.email.toLowerCase().includes(searchVal) ||
            customer.contact.toLowerCase().includes(searchVal) ||
            customer.address.toLowerCase().includes(searchVal) ||
            customer.fulladdress.toLowerCase().includes(searchVal)
        );
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
        const initials = getInitials(customer.fullname);
        
        html += `
            <div class="customer-card" data-customer-id="${customer.id}">
                <div class="customer-card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-placeholder">${initials}</div>
                        <div>
                            <h6 class="mb-0 fw-bold">${escapeHtml(customer.fullname)}</h6>
                            <small class="text-muted">@${escapeHtml(customer.username)}</small>
                        </div>
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">
                        <i class="bi bi-person"></i> Customer
                    </span>
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
                        <p class="mb-1 small">${escapeHtml(customer.address)}</p>
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
                    <div class="mt-2">
                        <small class="text-muted"><i class="bi bi-calendar-plus"></i> Member Since</small>
                        <p class="mb-0 small">${formatDate(customer.created_at)}</p>
                    </div>
                </div>
                <div class="customer-card-footer">
                    <button class="btn btn-sm btn-outline-info view-customer-btn" data-id="${customer.id}">
                        <i class="bi bi-eye"></i> View Details
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
}

function viewCustomer(customerId) {
    const customer = customersData.find(c => c.id === customerId);
    if (!customer) return;
    
    const modalBody = document.getElementById('viewCustomerBody');
    modalBody.innerHTML = `
        <div class="row g-4">
            <div class="col-md-6">
                <div class="bg-light p-3 rounded-3">
                    <h6 class="fw-bold mb-3"><i class="bi bi-person-circle text-success"></i> Personal Information</h6>
                    <div class="detail-row">
                        <span class="detail-label">Full Name</span>
                        <span class="detail-value">${escapeHtml(customer.fullname)}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Username</span>
                        <span class="detail-value">@${escapeHtml(customer.username)}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Email</span>
                        <span class="detail-value">${escapeHtml(customer.email)}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Contact</span>
                        <span class="detail-value">${escapeHtml(customer.contact)}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Member Since</span>
                        <span class="detail-value">${formatDate(customer.created_at)}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="bg-light p-3 rounded-3">
                    <h6 class="fw-bold mb-3"><i class="bi bi-geo-alt text-danger"></i> Address Information</h6>
                    <div class="detail-row">
                        <span class="detail-label">Barangay/City</span>
                        <span class="detail-value">${escapeHtml(customer.address)}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Full Address</span>
                        <span class="detail-value">${escapeHtml(customer.fulladdress)}</span>
                    </div>
                </div>
                
                <div class="bg-light p-3 rounded-3 mt-3">
                    <h6 class="fw-bold mb-3"><i class="bi bi-shopping-bag text-warning"></i> Purchase Summary</h6>
                    <div class="detail-row">
                        <span class="detail-label">Total Orders</span>
                        <span class="detail-value fw-bold">${customer.total_orders || 0}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Total Spent</span>
                        <span class="detail-value fw-bold text-success">₱${(customer.total_spent || 0).toFixed(2)}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Average Order</span>
                        <span class="detail-value fw-bold">₱${customer.total_orders > 0 ? (customer.total_spent / customer.total_orders).toFixed(2) : '0.00'}</span>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    new bootstrap.Modal(document.getElementById('viewCustomerModal')).show();
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
        fullname: document.getElementById('customerFullname').value,
        email: document.getElementById('customerEmail').value,
        contact: document.getElementById('customerContact').value,
        username: document.getElementById('customerUsername').value,
        address: document.getElementById('customerAddress').value,
        fulladdress: document.getElementById('customerFulladdress').value,
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
        created_at: new Date().toISOString()
    };
    delete newCustomer.password;
    
    customersData.push(newCustomer);
    renderCustomers();
    bootstrap.Modal.getInstance(document.getElementById('addCustomerModal')).hide();
    document.getElementById('addCustomerForm').reset();
    showToast('Customer created successfully! (Demo)', 'success');
});

// Search functionality
document.getElementById('searchButton').addEventListener('click', () => renderCustomers());
document.getElementById('clearSearchButton').addEventListener('click', () => {
    document.getElementById('customerSearchInput').value = '';
    renderCustomers();
});
document.getElementById('customerSearchInput').addEventListener('keyup', (e) => {
    if (e.key === 'Enter') renderCustomers();
});

// Initial render
renderCustomers();
</script>

</body>
</html>
<?=js()?>