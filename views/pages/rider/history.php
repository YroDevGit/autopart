<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes, viewport-fit=cover">
    <title>AutoParts | Delivery History</title>
    <!-- Bootstrap 5 CSS + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* ===== HISTORY PAGE THEME (Rider) ===== */
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
        /* Filter section */
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
        /* Table styles */
        .table-history {
            background: white;
            border-radius: 1.2rem;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        .table-history th {
            background: #f0f5f3;
            color: #0e2a24;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #00a896;
            padding: 0.9rem 0.75rem;
        }
        .table-history td {
            vertical-align: middle;
            padding: 0.75rem;
            border-bottom: 1px solid #e9ecef;
        }
        .table-history tr:last-child td {
            border-bottom: none;
        }
        .table-history tbody tr:hover {
            background: #f8fbfa;
        }
        .status-badge-history {
            padding: 0.3rem 0.75rem;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
        }
        .status-delivered {
            background: #00a89620;
            color: #028090;
            border: 1px solid #00a89660;
        }
        .status-cancelled {
            background: #dc354520;
            color: #a71d2a;
            border: 1px solid #dc354560;
        }
        .address-truncate {
            max-width: 180px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: inline-block;
            vertical-align: middle;
        }
        .btn-outline-rider {
            color: #00a896;
            border-color: #00a896;
        }
        .btn-outline-rider:hover {
            background: #00a896;
            color: white;
        }
        .mobile-nav-items {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        footer {
            background: #0e2a24;
            color: #b8d9d0;
            margin-top: 2rem;
            padding: 1rem;
            text-align: center;
        }
        /* Product list in modal */
        .product-list-item {
            display: flex;
            justify-content: space-between;
            padding: 0.4rem 0;
            border-bottom: 1px dashed #dee2e6;
            font-size: 0.9rem;
        }
        .product-list-item:last-child {
            border-bottom: none;
        }
        .product-name {
            font-weight: 500;
        }
        .product-meta {
            color: #6c757d;
            font-size: 0.8rem;
        }
        /* Responsive table wrap */
        .table-responsive-custom {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
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
        }
        @media (max-width: 768px) {
            .content-inner {
                padding: 1rem;
                margin-top: 60px;
            }
            .filter-section-rider {
                margin-top: 55px;
            }
            .brand-text {
                font-size: 1rem;
            }
            .navbar-rider .container-fluid {
                padding-left: 1rem;
                padding-right: 1rem;
            }
            .table-history td, .table-history th {
                padding: 0.5rem 0.4rem;
                font-size: 0.8rem;
            }
            .address-truncate {
                max-width: 120px;
            }
            .status-badge-history {
                font-size: 0.6rem;
                padding: 0.2rem 0.5rem;
            }
        }
        @media (max-width: 576px) {
            .content-inner {
                padding: 0.75rem;
            }
            .filter-label-rider {
                font-size: 0.7rem;
            }
            .btn-rider-primary {
                font-size: 0.8rem;
                padding: 0.4rem 0.75rem;
            }
            .table-history td, .table-history th {
                font-size: 0.7rem;
                padding: 0.4rem 0.3rem;
            }
            .address-truncate {
                max-width: 80px;
            }
        }
    </style>
</head>
<body>

<!-- Sidebar Backdrop -->
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<!-- SIDEBAR (Rider Theme) -->
<?=include_page('rider/sidebar')?>

<!-- MAIN CONTENT WRAPPER -->
<div class="main-content-wrapper" id="mainContentWrapper">
    <!-- Navbar -->
   <?=include_page('rider/nav')?>

    <!-- CONTENT -->
    <div class="content-inner">
        <!-- Page title -->
        <div class="d-flex align-items-center gap-2 mb-3">
            <i class="bi bi-clock-history fs-3 text-success"></i>
            <h4 class="fw-bold mb-0 text-dark">Delivery History</h4>
            <span class="badge bg-success ms-2" id="historyCount">0</span>
        </div>

        <!-- FILTER SECTION -->
        <div class="filter-section-rider">
            <div class="row g-3 align-items-end">
                <div class="col-md-5 col-12">
                    <div class="filter-label-rider"><i class="bi bi-search"></i> Search History</div>
                    <div class="input-group">
                        <input type="text" class="form-control" id="historySearchInput"
                               placeholder="Order ID, customer, address..."
                               autocomplete="off">
                        <button class="btn btn-rider-primary" id="searchHistoryBtn" type="button">
                            <i class="bi bi-search"></i>
                        </button>
                        <button class="btn btn-outline-secondary" id="clearHistorySearchBtn" type="button">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <div class="filter-label-rider"><i class="bi bi-funnel"></i> Status Filter</div>
                    <select class="form-select" id="historyStatusFilter">
                        <option value="all">All History</option>
                        <option value="delivered">Delivered</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3 col-12">
                    <div class="filter-label-rider">&nbsp;</div>
                    <button class="btn btn-outline-secondary w-100" id="clearHistoryFiltersBtn">
                        <i class="bi bi-eraser"></i> Clear Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- TABLE HISTORY -->
        <div id="historyContainer">
            <div class="table-responsive-custom">
                <table class="table table-history table-hover mb-0" id="historyTable">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Address</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="historyTableBody">
                        <!-- Sample Data Rows -->
                        
        
                    </tbody>
                </table>
            </div>
            <!-- Empty state -->
            <div id="emptyHistoryMessage" class="text-center py-5 d-none">
                <i class="bi bi-inbox fs-1 text-muted"></i>
                <p class="mt-2 text-muted">No delivery history found matching your filters.</p>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="py-3">
        <div class="container text-center">
            <p class="mb-0 small">&copy; 2026 AutoParts - Rider Dashboard. Stay safe on the road!</p>
        </div>
    </footer>
</div>

<!-- Modal: History Details with Products -->
<div class="modal fade" id="historyDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-dark text-white rounded-top-4 border-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-clock-history me-2 text-success"></i> Delivery Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="historyDetailBody">
                <div class="text-center py-3">Loading...</div>
            </div>
            <div class="modal-footer bg-light border-0 rounded-bottom-4">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?=js()?>