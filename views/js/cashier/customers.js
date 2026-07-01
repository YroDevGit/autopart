import { getAllCustomers } from "../classes/functions/orderModel";

    let customersData = await getAllCustomers();

    function formatDate(dateStr) {
      if (!dateStr) return 'N/A';
      let d = new Date(dateStr);
      return d.toLocaleDateString('en-PH') + " " + d.toLocaleTimeString([], {
        hour: '2-digit',
        minute: '2-digit'
      });
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
      const bsToast = new bootstrap.Toast(toastEl, {
        delay: 2000
      });
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
          customer.contact.toLowerCase().includes(searchVal)
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