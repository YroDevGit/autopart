<?php

use Tables\User;
use Classes\Ccookie;
if(! Ccookie::get("user")){
  redirect("logout");
}

$user = User::findOne(["id"=> Ccookie::get("user")]);
if(! $user){
  redirect("logout");
}
if($user['role'] != 3){
  redirect("logout");
}
$fullname = $user['fullname'] ?? "USER";


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes, viewport-fit=cover">
  <title>AutoParts | Rider Dashboard</title>
  <!-- Bootstrap 5 CSS + Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!-- Leaflet CSS for Maps -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <!-- html5-qrcode library for QR scanning -->
  <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
  <link rel="stylesheet" href="<?=assets('cashier.css')?>">
  <style>
    /* Rider Dashboard Unique Styles */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      background: #e8eef2;
      font-family: 'Segoe UI', system-ui, 'Roboto', sans-serif;
      overflow-x: hidden;
    }

    /* Navbar Styling - Rider Theme */
    .navbar-rider {
      background: linear-gradient(135deg, #1a3c34 0%, #0e2a24 100%);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      position: fixed;
      top: 0;
      right: 0;
      left: 0;
      z-index: 1030;
    }
    
    .brand-text {
      font-weight: 800;
      letter-spacing: -0.5px;
    }
    
    .menu-toggle {
      background: transparent;
      border: none;
      color: white;
      font-size: 1.5rem;
      cursor: pointer;
      padding: 0.5rem;
      display: none;
    }
    
    .btn-rider-primary {
      background: #00a896;
      border: none;
      color: white;
      transition: all 0.2s;
    }
    .btn-rider-primary:hover {
      background: #028090;
      transform: translateY(-1px);
      box-shadow: 0 4px 10px rgba(0, 168, 150, 0.3);
    }

    /* Sidebar Rider Theme - Responsive */
    .sidebar-rider {
      background: #0e2a24;
      position: fixed;
      top: 0;
      left: 0;
      width: 260px;
      height: 100vh;
      z-index: 1040;
      transition: transform 0.3s ease-in-out;
      overflow-y: auto;
      box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    }
    
    .sidebar-rider.closed {
      transform: translateX(-100%);
    }
    
    .sidebar-rider-link {
      padding: 12px 20px;
      color: #b8d9d0;
      display: flex;
      align-items: center;
      gap: 10px;
      text-decoration: none;
      transition: all 0.2s;
      border-radius: 10px;
      margin: 4px 10px;
    }
    .sidebar-rider-link:hover {
      background: rgba(0, 168, 150, 0.2);
      color: #00a896;
    }
    .sidebar-rider-link.active {
      background: #00a896;
      color: white;
    }
    .sidebar-rider-link i {
      width: 24px;
      font-size: 1.2rem;
    }

    /* Sidebar Backdrop */
    .sidebar-backdrop {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      z-index: 1035;
      display: none;
      opacity: 0;
      transition: opacity 0.3s ease;
    }
    .sidebar-backdrop.show {
      display: block;
      opacity: 1;
    }

    /* Main Content Wrapper - Responsive */
    .main-content-wrapper {
      margin-left: 260px;
      transition: margin-left 0.3s ease-in-out;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    
    .main-content-wrapper.sidebar-closed {
      margin-left: 0;
    }
    
    .content-inner {
      flex: 1;
      padding: 1.5rem;
      margin-top: 70px;
    }

    /* Orders Cards - Responsive Grid */
    .orders-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
      gap: 1.5rem;
      margin-top: 0;
    }
    
    .order-card {
      background: white;
      border-radius: 1.2rem;
      transition: transform 0.2s, box-shadow 0.2s;
      border: none;
      overflow: hidden;
      height: 100%;
      display: flex;
      flex-direction: column;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    .order-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
    }
    .order-card-header {
      background: linear-gradient(135deg, #00a89615 0%, #02809010 100%);
      padding: 1rem 1.25rem;
      border-bottom: 2px solid #00a896;
    }
    .order-card-body {
      padding: 1.25rem;
      flex: 1;
    }
    .order-card-footer {
      padding: 1rem 1.25rem;
      background: #f8f9fa;
      border-top: 1px solid #e9ecef;
    }
    .status-badge-rider {
      padding: 0.35rem 0.85rem;
      border-radius: 50px;
      font-size: 0.7rem;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }
    .status-pending-delivery { background: #ffc10720; color: #b78103; border: 1px solid #ffc10760; }
    .status-out-for-delivery { background: #0dcaf020; color: #0a6e7c; border: 1px solid #0dcaf060; }
    .status-delivered { background: #00a89620; color: #028090; border: 1px solid #00a89660; }
    .status-cancelled { background: #dc354520; color: #a71d2a; border: 1px solid #dc354560; }

    /* Filter Section */
    .filter-section-rider {
      background: white;
      border-radius: 1rem;
      padding: 1.25rem;
      margin-bottom: 1.5rem;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
      border: 1px solid rgba(0, 168, 150, 0.2);
    }
    .filter-label-rider {
      font-size: 0.8rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: #028090;
      margin-bottom: 0.5rem;
    }
    .btn-scan-rider {
      background: linear-gradient(135deg, #00a896 0%, #028090 100%);
      color: white;
      border: none;
      transition: all 0.2s;
      padding: 0.5rem 1rem;
    }
    .btn-scan-rider:hover {
      transform: scale(1.02);
      box-shadow: 0 4px 12px rgba(0, 168, 150, 0.3);
    }

    /* Map Container */
    #orderMap {
      height: 250px;
      width: 100%;
      border-radius: 1rem;
      z-index: 1;
    }
    .order-detail-img {
      width: 50px;
      height: 50px;
      object-fit: contain;
      background: #f8f9fa;
      border-radius: 8px;
      padding: 5px;
    }
    .delivery-stats {
      background: linear-gradient(135deg, #00a89610 0%, #02809010 100%);
      border-radius: 1rem;
      padding: 1.25rem;
      margin-bottom: 1.5rem;
      display: none;
    }
    .stat-card {
      background: white;
      border-radius: 1rem;
      padding: 1rem;
      text-align: center;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    footer {
      background: #0e2a24;
      color: #b8d9d0;
      margin-top: 2rem;
      padding: 1rem;
      text-align: center;
    }
    #qr-reader {
      width: 100%;
      border-radius: 1rem;
      overflow: hidden;
    }
    #qr-reader video {
      border-radius: 1rem;
      width: 100%;
    }
    .scanner-container {
      background: #f8f9fa;
      border-radius: 1rem;
      padding: 1rem;
    }
    
    /* Info rows styling */
    .info-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0.5rem 0;
      border-bottom: 1px dashed #e9ecef;
    }
    .info-row:last-child {
      border-bottom: none;
    }
    .info-label {
      color: #6c757d;
      font-size: 0.8rem;
    }
    .info-value {
      font-weight: 600;
      font-size: 0.9rem;
    }
    
    /* Address text truncation */
    .address-text {
      font-size: 0.85rem;
      color: #495057;
      line-height: 1.4;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    /* Mobile Nav Items */
    .mobile-nav-items {
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    /* Responsive Breakpoints */
    @media (max-width: 992px) {
      .menu-toggle {
        display: block;
      }
      .sidebar-rider {
        transform: translateX(-100%);
      }
      .sidebar-rider.open {
        transform: translateX(0);
      }
      .main-content-wrapper {
        margin-left: 0;
      }
      .orders-grid {
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1rem;
      }
    }
    
    @media (max-width: 768px) {
      .content-inner {
        padding: 1rem;
        margin-top: 60px;
      }
      .orders-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
      }
      .filter-section-rider{
        margin-top: 55px;
      }
      .filter-section-rider .row {
        gap: 0.75rem;
      }
      .delivery-stats .row {
        gap: 0.75rem;
      }
      .brand-text {
        font-size: 1rem;
      }
      .navbar-rider .container-fluid {
        padding-left: 1rem;
        padding-right: 1rem;
      }
      .mobile-nav-items .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.8rem;
      }
      .stat-card h3 {
        font-size: 1.5rem;
      }
    }
    
    @media (max-width: 576px) {
      .content-inner {
        padding: 0.75rem;
      }
      .order-card-header h6 {
        font-size: 0.9rem;
      }
      .order-card-body {
        padding: 1rem;
      }
      .filter-label-rider {
        font-size: 0.7rem;
      }
      .btn-scan-rider, .btn-rider-primary {
        font-size: 0.8rem;
        padding: 0.4rem 0.75rem;
      }
      .mobile-nav-items .btn {
        padding: 0.2rem 0.4rem;
        font-size: 0.7rem;
      }
      .mobile-nav-items .btn i {
        font-size: 0.8rem;
      }
    }
  </style>
</head>
<body>

<!-- Sidebar Backdrop -->
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<!-- SIDEBAR (Rider Theme) -->
<div class="sidebar-rider" id="sidebar">
  <div class="pt-4 pb-2">
    <div class="text-center mb-4">
      <i class="bi bi-truck text-success fs-1"></i>
      <h6 class="text-white mt-2">Rider Panel</h6>
    </div>
    <a href="#" class="sidebar-rider-link active">
      <i class="bi bi-house-door-fill"></i> Dashboard
    </a>
    <a href="#" class="sidebar-rider-link">
      <i class="bi bi-truck"></i> My Deliveries
    </a>
    <a href="#" class="sidebar-rider-link">
      <i class="bi bi-clock-history"></i> Delivery History
    </a>
    <a href="#" class="sidebar-rider-link">
      <i class="bi bi-gear-fill"></i> Settings
    </a>
  </div>
</div>

<!-- MAIN CONTENT AREA -->
<div class="main-content-wrapper" id="mainContentWrapper">
  <!-- Navbar Rider -->
  <nav class="navbar navbar-rider navbar-dark py-3 sticky-top">
    <div class="container-fluid px-4">
      <div class="d-flex align-items-center gap-3">
        <button class="menu-toggle" id="menuToggle">
          <i class="bi bi-list"></i>
        </button>
        <a class="navbar-brand brand-text fs-4" href="#">
          <i class="bi bi-truck me-2 text-success"></i><span class="text-success"><?= variable('appname') ?> | Rider</span>
        </a>
      </div>
      <div class="mobile-nav-items">
        <div class="text-white me-2 d-none d-sm-block">
          <i class="bi bi-person-circle"></i> <span id="riderName">Michael Dela Cruz</span>
        </div>
        <button class="btn btn-outline-light rounded-pill" type="button" id="refreshOrdersBtn">
          <i class="bi bi-arrow-repeat"></i> <span class="d-none d-md-inline">Refresh</span>
        </button>
        <button class="btn btn-danger rounded-pill" type="button" id="exitButtonMain">
          <i class="bi bi-power"></i> <span class="d-none d-md-inline">Logout</span>
        </button>
      </div>
    </div>
  </nav>

  <!-- MAIN CONTENT -->
  <div class="content-inner">
    <!-- Welcome Stats -->
    <div class="delivery-stats">
      <div class="row g-3">
        <div class="col-md-4 col-12">
          <div class="stat-card">
            <i class="bi bi-clock-history fs-2 text-warning"></i>
            <h3 class="text-success mb-0 mt-2" id="pendingCount">0</h3>
            <small class="text-muted">Pending Deliveries</small>
          </div>
        </div>
        <div class="col-md-4 col-12">
          <div class="stat-card">
            <i class="bi bi-truck fs-2 text-info"></i>
            <h3 class="text-warning mb-0 mt-2" id="outForDeliveryCount">0</h3>
            <small class="text-muted">Out for Delivery</small>
          </div>
        </div>
        <div class="col-md-4 col-12">
          <div class="stat-card">
            <i class="bi bi-check-circle fs-2 text-success"></i>
            <h3 class="text-info mb-0 mt-2" id="completedToday">0</h3>
            <small class="text-muted">Completed Today</small>
          </div>
        </div>
      </div>
    </div>

    <!-- FILTER SECTION -->
    <div class="filter-section-rider">
      <div class="row g-3 align-items-end">
        <div class="col-md-5 col-12">
          <div class="filter-label-rider"><i class="bi bi-search"></i> Search Orders</div>
          <div class="input-group">
            <input type="text" class="form-control" id="orderSearchInput" 
                   placeholder="Search by customer name, order ID, or address..." 
                   autocomplete="off">
            <button class="btn btn-rider-primary" id="searchButton" type="button">
              <i class="bi bi-search"></i>
            </button>
            <button class="btn btn-outline-secondary" id="clearSearchButton" type="button">
              <i class="bi bi-x-lg"></i>
            </button>
          </div>
        </div>
        <div class="col-md-4 col-12">
          <div class="filter-label-rider"><i class="bi bi-funnel"></i> Status Filter</div>
          <select class="form-select" id="statusFilterSelect">
            <option value="1">Accepted (Ready for Pickup)</option>
            <option value="2">Out for Delivery</option>
            <option value="3">Delivered</option>
            <option value="7">Cancelled</option>
          </select>
        </div>
        <div class="col-md-3 col-12">
          <div class="filter-label-rider"><i class="bi bi-upc-scan"></i> Quick Scan</div>
          <button class="btn btn-scan-rider w-100 py-2" id="scanQrBtn" data-bs-toggle="modal" data-bs-target="#qrScannerModal">
            <i class="bi bi-qr-code-scan"></i> Scan QR Code
          </button>
        </div>
      </div>
    </div>

    <!-- ORDERS LIST (Card View for Riders) -->
    <div id="ordersContainer">
      <div class="text-center py-5">
        <div class="spinner-border text-success" role="status"></div>
        <p class="mt-2 text-muted">Loading orders...</p>
      </div>
    </div>
  </div>

  <!-- FOOTER -->
  <footer class="py-3">
    <div class="container text-center">
      <p class="mb-0 small">&copy; <?= date('Y') ?> <?= variable('appname') ?> - Rider Dashboard. Stay safe on the road!</p>
    </div>
  </footer>
</div>

<!-- Modal: QR Scanner -->
<div class="modal fade" id="qrScannerModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 rounded-4 shadow-lg">
      <div class="modal-header bg-dark text-white rounded-top-4 border-0">
        <h5 class="modal-title fw-bold"><i class="bi bi-qr-code-scan me-2 text-success"></i> Scan QR Code</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" id="closeScannerBtn"></button>
      </div>
      <div class="modal-body p-4">
        <div class="scanner-container text-center">
          <div id="qr-reader" style="width:100%;"></div>
          <div class="mt-3">
            <button class="btn btn-outline-secondary" id="stopScannerBtn">
              <i class="bi bi-camera-video-off"></i> Stop Camera
            </button>
            <button class="btn btn-rider-primary" id="startScannerBtn" style="display:none;">
              <i class="bi bi-camera-video"></i> Start Camera
            </button>
          </div>
          <div class="alert alert-info mt-3 small">
            <i class="bi bi-info-circle"></i> Scan order QR code to quickly find and update delivery status.
          </div>
        </div>
      </div>
      <div class="modal-footer bg-light border-0 rounded-bottom-4">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Order Details -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 rounded-4 shadow-lg">
      <div class="modal-header bg-dark text-white rounded-top-4 border-0">
        <h5 class="modal-title fw-bold"><i class="bi bi-truck me-2 text-success"></i> Delivery Order Details</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4" id="orderDetailsModalBody">
        <div class="text-center py-3">Loading...</div>
      </div>
      <div class="modal-footer bg-light border-0 rounded-bottom-4">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-rider-primary" id="updateDeliveryStatusBtn">Update Status</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Update Status Options -->
<div class="modal fade" id="updateStatusOptionsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 rounded-4 shadow-lg">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2 text-success"></i> Update Delivery Status</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <input type="hidden" id="updateOrderId">
        <div class="mb-3">
          <label class="form-label fw-semibold">Change status to:</label>
          <select class="form-select" id="deliveryStatusSelect">
            <option value="2">Out for Delivery</option>
            <option value="3">Delivered</option>
            <option value="7">Cancelled</option>
          </select>
        </div>
        <div class="alert alert-info small">
          <i class="bi bi-info-circle"></i> Update the delivery status for this order.
        </div>
      </div>
      <div class="modal-footer bg-light border-0 rounded-bottom-4">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-rider-primary" id="confirmDeliveryUpdate">Confirm Update</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Cancellation Reason -->
<div class="modal fade" id="cancellationReasonModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 rounded-4 shadow-lg">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title fw-bold"><i class="bi bi-x-octagon me-2"></i> Cancellation Reason</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <input type="hidden" id="cancelOrderId">
        <div class="mb-3">
          <label class="form-label fw-semibold">Reason for cancellation</label>
          <textarea class="form-control" id="cancellationReasonText" rows="3" placeholder="e.g., Customer requested cancellation, Wrong address, Unable to deliver..."></textarea>
        </div>
        <div class="form-text">This reason will be recorded for reference.</div>
      </div>
      <div class="modal-footer bg-light border-0 rounded-bottom-4">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmCancellationBtn">Confirm Cancellation</button>
      </div>
    </div>
  </div>
</div>

<!-- Toast Notification -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
  <div id="statusToast" class="toast align-items-center text-white bg-success border-0" role="alert">
    <div class="d-flex">
      <div class="toast-body" id="toastMsg">✅ Action completed.</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Sidebar Toggle Functionality
const sidebar = document.getElementById('sidebar');
const mainContent = document.getElementById('mainContentWrapper');
const backdrop = document.getElementById('sidebarBackdrop');
const menuToggle = document.getElementById('menuToggle');

function toggleSidebar() {
  const isOpen = sidebar.classList.contains('open');
  if (isOpen) {
    sidebar.classList.remove('open');
    backdrop.classList.remove('show');
    document.body.style.overflow = '';
  } else {
    sidebar.classList.add('open');
    backdrop.classList.add('show');
    document.body.style.overflow = 'hidden';
  }
}

function closeSidebar() {
  sidebar.classList.remove('open');
  backdrop.classList.remove('show');
  document.body.style.overflow = '';
}

menuToggle.addEventListener('click', toggleSidebar);
backdrop.addEventListener('click', closeSidebar);

// Handle window resize - close sidebar on desktop if needed
window.addEventListener('resize', function() {
  if (window.innerWidth > 992) {
    closeSidebar();
  }
});
</script>
<script type="module" src="<?=assets('js/pages/rider_orders.js')?>"></script>
<?=js()?>
</body>
</html>