<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
  <title>AutoParts Admin | Orders Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?=assets('cashier.css')?>">
  <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
  <style>
    .status-badge {
      padding: 0.35rem 0.75rem;
      border-radius: 50px;
      font-size: 0.75rem;
      font-weight: 600;
      display: inline-block;
    }
    .status-pending { background: #ffc10720; color: #b78103; border: 1px solid #ffc10760; }
    .status-accepted { background: #19875420; color: #0a5c36; border: 1px solid #19875460; }
    .status-out-for-delivery { background: #0dcaf020; color: #0a6e7c; border: 1px solid #0dcaf060; }
    .status-delivered { background: #6f42c120; color: #4a2a8a; border: 1px solid #6f42c160; }
    .status-rejected { background: #dc354520; color: #a71d2a; border: 1px solid #dc354560; }
    .status-walkin { background: #ffeb3bba; color: black; border: 1px solid #ffeb3bdb; }
    
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
    .order-detail-img {
      width: 50px;
      height: 50px;
      object-fit: contain;
      background: #f8f9fa;
      border-radius: 8px;
      padding: 5px;
    }
    .btn-action-group .btn {
      padding: 0.25rem 0.6rem;
      font-size: 0.75rem;
      margin: 0 2px;
    }
    .orders-table-container {
      background: white;
      border-radius: 1.2rem;
      padding: 1.2rem;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    .table-orders th {
      background: #f8f9fa;
      font-weight: 600;
      border-bottom: 2px solid #e25822;
    }
    .btn-scan {
      background: linear-gradient(135deg, #e25822 0%, #c9471a 100%);
      color: white;
      border: none;
      transition: all 0.2s;
    }
    .btn-scan:hover {
      transform: scale(1.02);
      box-shadow: 0 4px 12px rgba(226, 88, 34, 0.3);
      color: white;
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

    #printSection {
      display: none !important;
    }

    .print-invoice {
      padding: 15px;
      font-family: Arial, sans-serif;
      max-width: 700px;
      margin: 0 auto;
    }
    .print-invoice .header {
      text-align: center;
      border-bottom: 2px solid #e25822;
      padding-bottom: 10px;
      margin-bottom: 12px;
    }
    .print-invoice .header h3 {
      color: #e25822;
      margin: 0;
      font-size: 20px;
      font-weight: bold;
    }
    .print-invoice .header small {
      color: #6c757d;
      font-size: 11px;
    }
    .print-invoice .main-row {
      display: flex;
      gap: 15px;
    }
    .print-invoice .left-col {
      flex: 1;
    }
    .print-invoice .right-col {
      flex: 0 0 180px;
      text-align: center;
      border-left: 2px dashed #dee2e6;
      padding-left: 15px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }
    .print-invoice .info-line {
      display: flex;
      padding: 3px 0;
      font-size: 12px;
      border-bottom: 1px solid #f1f1f1;
    }
    .print-invoice .info-line .label {
      color: #6c757d;
      width: 80px;
      font-weight: 600;
      flex-shrink: 0;
    }
    .print-invoice .info-line .value {
      flex: 1;
    }
    .print-invoice .items-table {
      width: 100%;
      margin: 10px 0;
      font-size: 11px;
      border-collapse: collapse;
    }
    .print-invoice .items-table th {
      background: #f8f9fa;
      padding: 5px 8px;
      text-align: left;
      border-bottom: 1px solid #dee2e6;
      font-weight: 600;
    }
    .print-invoice .items-table td {
      padding: 4px 8px;
      border-bottom: 1px solid #f1f1f1;
    }
    .print-invoice .items-table .text-right {
      text-align: right;
    }
    .print-invoice .items-table .text-center {
      text-align: center;
    }
    .print-invoice .totals {
      text-align: right;
      font-size: 12px;
      padding: 8px 0;
      border-top: 1px solid #dee2e6;
      margin-top: 5px;
    }
    .print-invoice .totals .grand-total {
      font-size: 16px;
      font-weight: bold;
      color: #e25822;
    }
    .print-invoice .right-col .qr-label {
      font-size: 10px;
      color: #6c757d;
      margin-top: 5px;
    }
    .print-invoice .right-col #qrCodeContainer {
      display: inline-block;
    }
    .print-invoice .right-col #qrCodeContainer canvas {
      width: 160px !important;
      height: 160px !important;
    }
    .print-invoice .footer {
      text-align: center;
      font-size: 10px;
      color: #6c757d;
      margin-top: 10px;
      padding-top: 10px;
      border-top: 1px solid #dee2e6;
    }

    @media print {
      body * {
        visibility: hidden;
      }
      #printSection, #printSection * {
        visibility: visible;
      }
      #printSection {
        display: block !important;
        position: fixed;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: white;
        padding: 10px;
      }
      .no-print {
        display: none !important;
      }
      .print-invoice .right-col #qrCodeContainer canvas {
        width: 160px !important;
        height: 160px !important;
      }
    }
  </style>
</head>
<body>

<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<?=include_page("cashier/sidebar")?>

<div class="main-content-wrapper" id="mainContentWrapper">
  <?=include_page('cashier/navbar', ["pagename"=>"Orders"])?>

  <div class="content-inner">
    <div class="card admin-card border-0 shadow-sm">
      <div class="card-header bg-white border-0 pt-4 pb-0">
        <div class="header-actions">
          <div class="search-section">
            <div class="search-container">
              <div class="input-group search-input-group">
                <input type="text" class="form-control search-input" id="orderSearchInput" 
                       placeholder="🔍 Search by customer name, email, or order ID..." 
                       aria-label="Search orders">
                <button class="btn search-btn" id="searchButton" type="button">
                  <i class="bi bi-search"></i>
                </button>
                <button class="btn clear-search-btn" id="clearSearchButton" type="button" title="Clear search">
                  <i class="bi bi-x-lg"></i>
                </button>
              </div>
            </div>
          </div>
          
          <div class="add-button-section">
            <button class="btn btn-autoparts-primary rounded-pill px-4 py-2 shadow-sm" id="refreshOrdersBtn">
              <i class="bi bi-arrow-repeat"></i> Refresh
            </button>
          </div>
        </div>
        <div class="mt-2">
          <p class="text-muted small mb-0"><i class="bi bi-info-circle"></i> <?=t('Manage customer orders, update status, and track deliveries')?></p>
        </div>
      </div>
      
      <div class="card-body p-3 p-md-4">
        <div class="filter-section">
          <div class="row g-3 align-items-end">
            <div class="col-md-4 col-12">
              <div class="filter-label"><i class="bi bi-funnel"></i> Status Filter</div>
              <select class="form-select" id="statusFilterSelect">
                <option value="all">All Status</option>
                <option value="0">Pending</option>
                <option value="1">Accepted</option>
                <option value="2">Out for Delivery</option>
                <option value="3">Delivered</option>
                <option value="7">Rejected</option>
                <option value="11">Walk-In</option>
              </select>
            </div>
            <div class="col-md-4 col-12">
              <div class="filter-label"><i class="bi bi-upc-scan"></i> Quick Scan</div>
              <button class="btn btn-scan w-100 py-2" id="scanQrBtn" data-bs-toggle="modal" data-bs-target="#qrScannerModal">
                <i class="bi bi-qr-code-scan"></i> Scan QR Code
              </button>
            </div>
            <div class="col-md-4 col-12">
              <div class="d-flex gap-2">
                <button class="btn btn-autoparts-primary flex-grow-1" id="applyFilterBtn">
                  <i class="bi bi-funnel-fill"></i> Apply Filters
                </button>
                <button class="btn btn-outline-secondary" id="clearFilterBtn" title="Clear filters">
                  <i class="bi bi-eraser"></i>
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="orders-table-container">
          <div class="table-responsive">
            <table class="table table-hover align-middle table-orders">
              <thead>
                <tr>
                  <th>Order ID</th>
                  <th>Customer</th>
                  <th>Subtotal</th>
                  <th>Shipping</th>
                  <th>Total</th>
                  <th>Status</th>
                  <th>Order Date</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="ordersTableBody">
                <tr><td colspan="8" class="text-center py-5 text-muted">Loading orders...</td></tr>
              </tbody>
            </table>
          </div>
          <div class="d-flex justify-content-between align-items-center mt-3">
            <small class="text-muted">Showing <span id="rowCount">0</span> orders</small>
            <small class="text-muted" id="searchStatus"></small>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="qrScannerModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 rounded-4 shadow-lg">
      <div class="modal-header bg-dark text-white rounded-top-4 border-0">
        <h5 class="modal-title fw-bold"><i class="bi bi-qr-code-scan me-2 text-warning"></i> Scan QR Code</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" id="closeScannerBtn"></button>
      </div>
      <div class="modal-body p-4">
        <div class="scanner-container text-center">
          <div id="qr-reader" style="width:100%;"></div>
          <div id="qr-reader-results" class="mt-3"></div>
          <div class="mt-3">
            <button class="btn btn-outline-secondary" id="stopScannerBtn">
              <i class="bi bi-camera-video-off"></i> Stop Camera
            </button>
            <button class="btn btn-autoparts-primary" id="startScannerBtn" style="display:none;">
              <i class="bi bi-camera-video"></i> Start Camera
            </button>
          </div>
          <div class="alert alert-info mt-3 small">
            <i class="bi bi-info-circle"></i> Position the QR code in front of the camera. Scanned order ID will automatically search.
          </div>
        </div>
      </div>
      <div class="modal-footer bg-light border-0 rounded-bottom-4">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="orderItemsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 rounded-4 shadow-lg">
      <div class="modal-header bg-dark text-white rounded-top-4 border-0">
        <h5 class="modal-title fw-bold"><i class="bi bi-box-seam me-2 text-warning"></i> Order Details</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4" id="orderItemsModalBody">
        <div class="text-center py-3">Loading...</div>
      </div>
      <div class="modal-footer bg-light border-0 rounded-bottom-4">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-autoparts-primary" id="saveStatusBtn">Save Status</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="rejectionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 rounded-4 shadow-lg">
      <div class="modal-header bg-danger text-white rounded-top-4">
        <h5 class="modal-title fw-bold"><i class="bi bi-x-octagon me-2"></i> Rejection Reason</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <input type="hidden" id="rejectOrderId">
        <div class="mb-3">
          <label class="form-label fw-semibold">Reason for rejection</label>
          <textarea class="form-control" id="rejectionReasonText" rows="3" placeholder="e.g., Out of stock, Payment issue, Invalid address..."></textarea>
        </div>
        <div class="form-text">This reason will be visible to the customer.</div>
      </div>
      <div class="modal-footer bg-light border-0 rounded-bottom-4">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmRejectionBtn">Confirm Rejection</button>
      </div>
    </div>
  </div>
</div>

<div id="printSection"></div>

<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
  <div id="statusToast" class="toast align-items-center text-white bg-success border-0" role="alert">
    <div class="d-flex">
      <div class="toast-body" id="toastMsg">✅ Action completed.</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<?=js()?>
</body>
</html>