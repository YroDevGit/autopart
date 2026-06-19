<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
  <title>AutoParts Admin | Dashboard Landing</title>
  <!-- Bootstrap 5 CSS + Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    /* custom overrides with auto parts industrial feel */
    body {
      background: #eef2f5;
      font-family: 'Segoe UI', system-ui, -apple-system, 'Roboto', sans-serif;
      overflow-x: hidden;
    }

    /* ===== SIDEBAR STYLES - FIXED POSITION ===== */
    .admin-sidebar {
      position: fixed;
      top: 0;
      left: 0;
      width: 280px;
      height: 100vh;
      background: linear-gradient(180deg, #0f2128 0%, #0a181f 100%);
      box-shadow: 4px 0 20px rgba(0,0,0,0.12);
      border-right: 1px solid #2c4a55;
      z-index: 1030;
      overflow-y: auto;
      transition: transform 0.25s ease;
    }

    /* main content wrapper - shifted to the right */
    .main-content-wrapper {
      margin-left: 280px;
      transition: margin-left 0.25s ease;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .sidebar-brand {
      padding: 1.5rem 1rem;
      border-bottom: 1px solid #2a4b57;
      margin-bottom: 1.2rem;
    }

    .nav-link-admin {
      color: #cbd8e0;
      padding: 0.75rem 1rem;
      margin: 0.3rem 0.8rem;
      border-radius: 0.75rem;
      transition: all 0.2s;
      font-weight: 500;
    }

    .nav-link-admin:hover, .nav-link-admin.active {
      background: #e25822;
      color: white;
      box-shadow: 0 6px 12px rgba(226,88,34,0.25);
    }

    .nav-link-admin i {
      width: 1.8rem;
      font-size: 1.2rem;
    }

    /* top navbar */
    .top-navbar {
      background: white;
      border-bottom: 1px solid #dee2e6;
      box-shadow: 0 2px 8px rgba(0,0,0,0.02);
      border-radius: 0;
    }

    /* stats cards */
    .stat-card {
      border: none;
      border-radius: 1.2rem;
      transition: transform 0.2s, box-shadow 0.2s;
      overflow: hidden;
    }
    .stat-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 24px rgba(0,0,0,0.08);
    }
    .stat-icon {
      width: 55px;
      height: 55px;
      border-radius: 1rem;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.8rem;
    }
    .stat-value {
      font-size: 1.8rem;
      font-weight: 800;
      line-height: 1.2;
    }

    /* recent orders table */
    .recent-card {
      border: none;
      border-radius: 1.2rem;
      box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    }
    .badge-status {
      padding: 0.4rem 0.8rem;
      border-radius: 30px;
      font-size: 0.75rem;
      font-weight: 600;
    }
    .badge-completed { background: #d4edda; color: #155724; }
    .badge-pending { background: #fff3cd; color: #856404; }
    .badge-shipped { background: #d1ecf1; color: #0c5460; }

    footer {
      background: #0f2128;
      color: #9bb7c2;
      font-size: 0.85rem;
      margin-top: auto;
    }

    /* responsive sidebar */
    @media (max-width: 768px) {
      .admin-sidebar {
        transform: translateX(-100%);
        width: 280px;
      }
      .admin-sidebar.show-sidebar {
        transform: translateX(0);
      }
      .main-content-wrapper {
        margin-left: 0 !important;
      }
      .sidebar-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1020;
        display: none;
      }
      .sidebar-backdrop.show {
        display: block;
      }
    }

    .content-inner {
      padding: 1.5rem 2rem;
      flex: 1;
    }
    
    @media (max-width: 576px) {
      .content-inner {
        padding: 1rem;
      }
    }

    /* welcome banner */
    .welcome-banner {
      background: linear-gradient(135deg, #e25822 0%, #ff7b2c 100%);
      border-radius: 1.2rem;
      color: white;
    }
    .activity-item {
      border-left: 3px solid #e25822;
      transition: all 0.2s;
    }
    .activity-item:hover {
      background: #f8f9fa;
    }
  </style>
</head>
<body>

<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<!-- SIDEBAR (Fixed left panel) -->
<aside class="admin-sidebar" id="adminSidebar">
  <div class="sidebar-brand d-flex align-items-center justify-content-center gap-2">
    <i class="bi bi-gear-wide-connected fs-3 text-warning"></i>
    <span class="fw-bold fs-5 text-white">AutoParts<span class="text-warning">ADMIN</span></span>
  </div>
  <ul class="nav flex-column mb-auto">
    <li class="nav-item">
      <a href="#" class="nav-link-admin nav-link active d-flex align-items-center">
        <i class="bi bi-speedometer2"></i> <span>Dashboard</span>
      </a>
    </li>
    <li class="nav-item">
      <a href="#" class="nav-link-admin nav-link d-flex align-items-center" id="productsNavLink">
        <i class="bi bi-box-seam"></i> <span>Products</span>
      </a>
    </li>
    <li class="nav-item">
      <a href="#" class="nav-link-admin nav-link d-flex align-items-center">
        <i class="bi bi-truck"></i> <span>Orders</span>
      </a>
    </li>
    <li class="nav-item">
      <a href="#" class="nav-link-admin nav-link d-flex align-items-center">
        <i class="bi bi-people"></i> <span>Customers</span>
      </a>
    </li>
    <li class="nav-item">
      <a href="#" class="nav-link-admin nav-link d-flex align-items-center">
        <i class="bi bi-bar-chart-steps"></i> <span>Analytics</span>
      </a>
    </li>
    <li class="nav-item mt-4">
      <a href="#" class="nav-link-admin nav-link d-flex align-items-center">
        <i class="bi bi-gear"></i> <span>Settings</span>
      </a>
    </li>
  </ul>
  <hr class="text-secondary mx-3 my-3">
  <div class="mt-auto pb-4">
    <a href="#" class="nav-link-admin nav-link d-flex align-items-center" id="logoutBtn">
      <i class="bi bi-box-arrow-right"></i> <span>Logout</span>
    </a>
  </div>
</aside>

<!-- MAIN CONTENT AREA -->
<div class="main-content-wrapper" id="mainContentWrapper">
  <!-- NAVBAR TOP -->
  <nav class="top-navbar navbar navbar-light bg-white py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div class="d-flex align-items-center gap-3">
      <button class="btn btn-outline-secondary d-md-none" id="menuToggleBtn" type="button">
        <i class="bi bi-list fs-4"></i>
      </button>
      <h5 class="mb-0 fw-semibold"><i class="bi bi-house-door-fill text-warning"></i> <span class="text-dark">Landing / Overview</span></h5>
    </div>
    <div class="d-flex gap-3 align-items-center">
      <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
          <img src="https://ui-avatars.com/api/?background=e25822&color=fff&name=ALEX" width="38" height="38" class="rounded-circle border">
          <span class="d-none d-sm-block ms-2 fw-semibold">Alex Turner</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end shadow">
          <li><a class="dropdown-item" href="#"><i class="bi bi-person-circle"></i> Profile</a></li>
          <li><a class="dropdown-item" href="#"><i class="bi bi-bell"></i> Notifications</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger" href="#" id="logoutDropdown"><i class="bi bi-lock"></i> Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- MAIN CONTENT -->
  <div class="content-inner">
    <!-- Welcome Banner -->
    <div class="welcome-banner p-4 mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
      <div>
        <h3 class="fw-bold mb-1"><i class="bi bi-stars"></i> Welcome back, Alex!</h3>
        <p class="mb-0 opacity-75">Your auto parts empire is thriving. Here's today's performance snapshot.</p>
      </div>
      <div class="text-center">
        <i class="bi bi-tools fs-1 opacity-50"></i>
      </div>
    </div>

    <!-- STATS CARDS ROW -->
    <div class="row g-4 mb-4">
      <div class="col-sm-6 col-xl-3">
        <div class="stat-card card h-100 p-3 bg-white">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <p class="text-muted mb-1 small text-uppercase">Total Products</p>
              <h2 class="stat-value mb-0" id="totalProductsStat">187</h2>
              <small class="text-success"><i class="bi bi-arrow-up"></i> +12 this month</small>
            </div>
            <div class="stat-icon bg-warning bg-opacity-10 text-warning">
              <i class="bi bi-box-seam"></i>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-xl-3">
        <div class="stat-card card h-100 p-3 bg-white">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <p class="text-muted mb-1 small text-uppercase">Total Orders</p>
              <h2 class="stat-value mb-0">1,284</h2>
              <small class="text-success"><i class="bi bi-arrow-up"></i> +23% vs last month</small>
            </div>
            <div class="stat-icon bg-info bg-opacity-10 text-info">
              <i class="bi bi-cart-check"></i>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-xl-3">
        <div class="stat-card card h-100 p-3 bg-white">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <p class="text-muted mb-1 small text-uppercase">Revenue</p>
              <h2 class="stat-value mb-0">$48.2k</h2>
              <small class="text-success"><i class="bi bi-graph-up"></i> +18.4%</small>
            </div>
            <div class="stat-icon bg-success bg-opacity-10 text-success">
              <i class="bi bi-currency-dollar"></i>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-xl-3">
        <div class="stat-card card h-100 p-3 bg-white">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <p class="text-muted mb-1 small text-uppercase">Low Stock Items</p>
              <h2 class="stat-value mb-0 text-warning" id="lowStockStat">8</h2>
              <small class="text-danger"><i class="bi bi-exclamation-triangle"></i> Needs attention</small>
            </div>
            <div class="stat-icon bg-danger bg-opacity-10 text-danger">
              <i class="bi bi-exclamation-diamond"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- TWO COLUMN LAYOUT: Recent Orders + Top Products / Activity -->
    <div class="row g-4">
      <!-- Recent Orders Table -->
      <div class="col-lg-7">
        <div class="card recent-card">
          <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0"><i class="bi bi-clock-history me-2 text-warning"></i>Recent Orders</h5>
            <a href="#" class="text-decoration-none small link-autoparts">View all <i class="bi bi-chevron-right"></i></a>
          </div>
          <div class="card-body p-3 p-md-4">
            <div class="table-responsive">
              <table class="table table-hover align-middle">
                <thead class="table-light">
                  <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="fw-semibold">#ORD-001</td>
                    <td>Michael Chen</td>
                    <td>$289.00</td>
                    <td><span class="badge-status badge-completed">Completed</span></td>
                    <td>2026-06-02</td>
                  </tr>
                  <tr>
                    <td class="fw-semibold">#ORD-002</td>
                    <td>Sarah Johnson</td>
                    <td>$1,245.50</td>
                    <td><span class="badge-status badge-shipped">Shipped</span></td>
                    <td>2026-06-02</td>
                  </tr>
                  <tr>
                    <td class="fw-semibold">#ORD-003</td>
                    <td>Robert Taylor</td>
                    <td>$89.99</td>
                    <td><span class="badge-status badge-pending">Pending</span></td>
                    <td>2026-06-01</td>
                  </tr>
                  <tr>
                    <td class="fw-semibold">#ORD-004</td>
                    <td>Emily Davis</td>
                    <td>$567.30</td>
                    <td><span class="badge-status badge-completed">Completed</span></td>
                    <td>2026-06-01</td>
                  </tr>
                  <tr>
                    <td class="fw-semibold">#ORD-005</td>
                    <td>James Wilson</td>
                    <td>$1,890.00</td>
                    <td><span class="badge-status badge-shipped">Shipped</span></td>
                    <td>2026-05-31</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: Top Products & Recent Activity -->
      <div class="col-lg-5">
        <!-- Top Selling Products -->
        <div class="card recent-card mb-4">
          <div class="card-header bg-white border-0 pt-4 pb-0">
            <h5 class="fw-bold mb-0"><i class="bi bi-trophy-fill me-2 text-warning"></i>Top Selling Parts</h5>
          </div>
          <div class="card-body p-3 p-md-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <div>
                <p class="mb-0 fw-semibold">Ceramic Brake Pads</p>
                <small class="text-muted">Sold: 342 units</small>
              </div>
              <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill">+24%</span>
            </div>
            <div class="d-flex align-items-center justify-content-between mb-3">
              <div>
                <p class="mb-0 fw-semibold">Performance Oil Filter</p>
                <small class="text-muted">Sold: 287 units</small>
              </div>
              <span class="badge bg-success bg-opacity-10 text-success rounded-pill">+12%</span>
            </div>
            <div class="d-flex align-items-center justify-content-between mb-3">
              <div>
                <p class="mb-0 fw-semibold">Coilover Suspension Kit</p>
                <small class="text-muted">Sold: 156 units</small>
              </div>
              <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill">+8%</span>
            </div>
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <p class="mb-0 fw-semibold">Spark Plugs (Set of 4)</p>
                <small class="text-muted">Sold: 421 units</small>
              </div>
              <span class="badge bg-info bg-opacity-10 text-info rounded-pill">+31%</span>
            </div>
          </div>
        </div>

        <!-- Recent Activity Feed -->
        <div class="card recent-card">
          <div class="card-header bg-white border-0 pt-4 pb-0">
            <h5 class="fw-bold mb-0"><i class="bi bi-activity me-2 text-warning"></i>Live Activity</h5>
          </div>
          <div class="card-body p-3 p-md-4">
            <div class="activity-item p-2 mb-2 rounded">
              <div class="d-flex gap-2">
                <i class="bi bi-plus-circle text-success mt-1"></i>
                <div><small class="fw-semibold">New product added:</small> <span class="small">High-Performance Air Filter</span><br><small class="text-muted">2 hours ago</small></div>
              </div>
            </div>
            <div class="activity-item p-2 mb-2 rounded">
              <div class="d-flex gap-2">
                <i class="bi bi-truck text-info mt-1"></i>
                <div><small class="fw-semibold">Order #ORD-002 shipped</small><br><small class="text-muted">5 hours ago</small></div>
              </div>
            </div>
            <div class="activity-item p-2 mb-2 rounded">
              <div class="d-flex gap-2">
                <i class="bi bi-person-plus text-warning mt-1"></i>
                <div><small class="fw-semibold">New customer registered</small><br><small class="text-muted">Yesterday</small></div>
              </div>
            </div>
            <div class="activity-item p-2 rounded">
              <div class="d-flex gap-2">
                <i class="bi bi-exclamation-triangle text-danger mt-1"></i>
                <div><small class="fw-semibold">Low stock alert:</small> <span class="small">Brake Pads (only 12 left)</span><br><small class="text-muted">Yesterday</small></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- FOOTER -->
  <footer class="py-3 px-4 d-flex justify-content-between align-items-center flex-wrap border-top border-secondary">
    <span>© 2026 AutoParts Elite Admin — Heavy Duty Performance</span>
    <span><i class="bi bi-wrench"></i> <i class="bi bi-tools"></i> Support: admin@autoparts.com</span>
  </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Simulate dynamic stats update (just for visual, no actual backend)
  // Update low stock items and total products based on mock data
  function updateStats() {
    // Example dynamic calculation (just for demonstration)
    const totalProductsSpan = document.getElementById('totalProductsStat');
    const lowStockSpan = document.getElementById('lowStockStat');
    if(totalProductsSpan && lowStockSpan) {
      // For demonstration: any number of products could be updated later
      // Keep as static but consistent
    }
  }
  
  // Sidebar mobile toggle
  const menuToggle = document.getElementById('menuToggleBtn');
  const sidebar = document.getElementById('adminSidebar');
  const backdrop = document.getElementById('sidebarBackdrop');
  if(menuToggle && sidebar && backdrop) {
    menuToggle.addEventListener('click', () => {
      sidebar.classList.toggle('show-sidebar');
      backdrop.classList.toggle('show');
    });
    backdrop.addEventListener('click', () => {
      sidebar.classList.remove('show-sidebar');
      backdrop.classList.remove('show');
    });
  }

  // Navigation: Products link (simulate page navigation)
  const productsNavLink = document.getElementById('productsNavLink');
  if(productsNavLink) {
    productsNavLink.addEventListener('click', (e) => {
      e.preventDefault();
      // Redirect to products page (replace with actual product page when integrated)
      // For standalone demo we show a message that it would navigate
      showToastMessage('🔧 Navigating to Products Manager (integration ready)', 'info');
      // In real implementation: window.location.href = 'products.html';
      setTimeout(() => {
        // Optional simulation - could redirect, but for demo we just alert
        // window.location.href = 'products.html';
      }, 500);
    });
  }

  // Logout functionality simulation
  const logoutBtn = document.getElementById('logoutBtn');
  const logoutDropdown = document.getElementById('logoutDropdown');
  const handleLogout = (e) => {
    e.preventDefault();
    if(confirm('Are you sure you want to logout from AutoParts Admin?')) {
      showToastMessage('🚪 Logging out... Redirecting to login page.', 'success');
      setTimeout(() => {
        // Simulate redirect to login page (replace with actual login page URL)
        alert('Demo: Redirect to login page. In production, redirect to login.html');
        // window.location.href = 'login.html';
      }, 1000);
    }
  };
  if(logoutBtn) logoutBtn.addEventListener('click', handleLogout);
  if(logoutDropdown) logoutDropdown.addEventListener('click', handleLogout);

  // Toast message helper
  function showToastMessage(msg, type = 'info') {
    const toastContainer = document.createElement('div');
    toastContainer.className = 'position-fixed bottom-0 end-0 p-3';
    toastContainer.style.zIndex = '1100';
    const toastDiv = document.createElement('div');
    let bgClass = 'bg-info';
    if(type === 'success') bgClass = 'bg-success';
    if(type === 'danger') bgClass = 'bg-danger';
    if(type === 'warning') bgClass = 'bg-warning';
    toastDiv.className = `toast align-items-center text-white ${bgClass} border-0 show`;
    toastDiv.role = 'alert';
    toastDiv.setAttribute('aria-live', 'assertive');
    toastDiv.setAttribute('aria-atomic', 'true');
    toastDiv.innerHTML = `
      <div class="d-flex">
        <div class="toast-body">${msg}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    `;
    toastContainer.appendChild(toastDiv);
    document.body.appendChild(toastContainer);
    setTimeout(() => { toastContainer.remove(); }, 3000);
    const bsToast = new bootstrap.Toast(toastDiv, { delay: 2800 });
    bsToast.show();
  }

  // Initialize any dynamic stats update
  updateStats();

  // Responsive: close sidebar on window resize above 768px
  window.addEventListener('resize', function() {
    if(window.innerWidth > 768) {
      if(sidebar) sidebar.classList.remove('show-sidebar');
      if(backdrop) backdrop.classList.remove('show');
    }
  });

  // For a more interactive feel: update stats based on session? but static is fine
  // Adding a small hover effect and tooltip for action
  const statCards = document.querySelectorAll('.stat-card');
  statCards.forEach(card => {
    card.addEventListener('click', () => {
      console.log('Stat card clicked - would navigate to detailed report');
      showToastMessage('Analytics module coming soon', 'info');
    });
  });
</script>
</body>
</html>