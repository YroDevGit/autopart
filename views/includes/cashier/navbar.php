<nav class="top-navbar navbar navbar-light bg-white py-3 px-4">
  <div class="container-fluid">
    <div class="d-flex align-items-center gap-3">
      <button class="btn btn-outline-secondary d-md-none" id="menuToggleBtn" type="button" aria-expanded="false" aria-label="Toggle sidebar">
        <i class="bi bi-list fs-4"></i>
      </button>
      <h5 class="mb-0 fw-semibold"><i class="bi <?=$icon ?? 'bi-speedometer2'?> text-warning"></i>  <span class="text-dark"><?= $pagename ?? "Page" ?></span></h5>
    </div>

    <div class="d-flex gap-3 align-items-center">
      <div class="dropdown">
        <i class="bi bi-bell fs-5 notification-btn position-relative" id="notificationBell" data-bs-toggle="dropdown" aria-expanded="false"></i>
        <span class="notification-badge position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notificationCount">
          0
        </span>

        <ul class="dropdown-menu dropdown-menu-end shadow-lg" aria-labelledby="notificationBell" style="width: 320px; max-height: 400px; overflow-y: auto;" id="notip">
          <li>
            <h6 class="dropdown-header fw-bold">Notifications</h6>
          </li>
          <li>
            <hr class="dropdown-divider">
          </li>
          <div id="notifContent">
            <li align='center'>No Notifications</li>
          </div>
          <li>
            <hr class="dropdown-divider">
          </li>
          <li>
            <a class="dropdown-item text-center text-primary fw-semibold" href="/cashier/orders">
              View All Orders
            </a>
          </li>
        </ul>
      </div>

      <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
          <img src="https://ui-avatars.com/api/?background=e25822&color=fff&name=<?= gval('fullname') ?>" width="38" height="38" class="rounded-circle border">
          <span class="d-none d-sm-block ms-2 fw-semibold"><?= gval("fullname") ?></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end shadow">
          <li><a class="dropdown-item cursor-pointer" id="userprofilelink" href="#"><i class="bi bi-person-circle"></i> Profile</a></li>
          <li>
            <hr class="dropdown-divider">
          </li>
          <li><a class="dropdown-item text-danger cursor-pointer" id="logout_btn"><i class="bi bi-lock"></i> Logout</a></li>
        </ul>
      </div>
    </div>
  </div>
</nav>

<style>
  .notification-btn {
    position: relative;
    transition: all 0.2s;
    cursor: pointer;
    padding: 5px;
    border-radius: 50%;
  }

  .notification-btn:hover {
    background-color: #f8f9fa;
  }

  .notification-btn .bi-bell {
    font-size: 1.25rem;
    color: #6c757d;
  }

  .notification-badge {
    font-size: 0.65rem;
    padding: 0.25rem 0.5rem;
    min-width: 20px;
    min-height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid white;
    animation: pulse 2s infinite;
  }

  @keyframes pulse {
    0% {
      transform: scale(1);
    }

    50% {
      transform: scale(1.1);
    }

    100% {
      transform: scale(1);
    }
  }

  .dropdown-menu {
    border: none;
    border-radius: 12px;
  }

  .dropdown-item {
    padding: 10px 15px;
    transition: background-color 0.2s;
  }

  .dropdown-item:hover {
    background-color: #f8f9fa;
  }

  .dropdown-item small {
    font-size: 0.8rem;
  }

  .dropdown-header {
    color: #333;
  }

  .admin-sidebar {
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    width: 250px;
    background: #1a1a2e;
    z-index: 1040;
    overflow-y: auto;
    padding-top: 20px;
    transition: transform 0.3s ease-in-out;
  }

  .main-content-wrapper {
    margin-left: 250px;
    transition: margin-left 0.3s ease-in-out;
  }

  @media (max-width: 767.98px) {
    .admin-sidebar {
      transform: translateX(-100%);
      width: 280px;
      z-index: 1050;
      box-shadow: 2px 0 10px rgba(0, 0, 0, 0.3);
    }

    .admin-sidebar.show {
      transform: translateX(0);
    }

    .main-content-wrapper {
      margin-left: 0 !important;
      transition: transform 0.3s ease-in-out;
    }

    .main-content-wrapper.sidebar-open {
      transform: translateX(280px);
    }

    .sidebar-backdrop {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.5);
      z-index: 1045;
      backdrop-filter: blur(2px);
    }

    .sidebar-backdrop.show {
      display: block !important;
    }

    body.no-scroll {
      overflow: hidden;
    }

    #menuToggleBtn {
      border: none !important;
      background: transparent !important;
      padding: 8px 10px;
      font-size: 1.5rem;
      color: #333;
      transition: all 0.2s;
    }

    #menuToggleBtn:hover {
      background: #f0f0f0 !important;
      border-radius: 8px;
    }

    #menuToggleBtn:focus {
      box-shadow: none !important;
    }

    #menuToggleBtn i {
      transition: transform 0.3s ease;
    }
  }

  @media (min-width: 768px) {
    .admin-sidebar {
      transform: translateX(0) !important;
    }

    .main-content-wrapper {
      margin-left: 250px;
    }

    .sidebar-backdrop {
      display: none !important;
    }

    /* Hide hamburger on desktop */
    #menuToggleBtn {
      display: none !important;
    }
  }

  /* ============================================
     SIDEBAR NAVIGATION STYLES
     ============================================ */
  .nav-link-admin {
    color: rgba(255, 255, 255, 0.7);
    padding: 12px 20px;
    transition: all 0.2s;
    border-left: 3px solid transparent;
    text-decoration: none;
    display: flex;
    align-items: center;
  }

  .nav-link-admin:hover {
    color: #fff;
    background: rgba(255, 255, 255, 0.1);
    border-left-color: #e25822;
  }

  .nav-link-admin.active {
    color: #fff;
    background: rgba(226, 88, 34, 0.2);
    border-left-color: #e25822;
  }

  .nav-link-admin i {
    width: 24px;
    margin-right: 12px;
    font-size: 1.1rem;
  }

  .sidebar-brand {
    padding: 15px 20px 25px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    margin-bottom: 15px;
  }

  .sidebar-brand span {
    color: #fff;
  }

  .sidebar-brand .text-warning {
    color: #e25822 !important;
  }

  .admin-sidebar hr {
    border-color: rgba(255, 255, 255, 0.1);
    margin: 10px 20px;
  }

  .top-navbar {
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    position: sticky;
    top: 0;
    z-index: 1030;
    background: white;
  }

  .top-navbar .container-fluid {
    padding: 0;
  }
</style>

<?= js('includes/cashier/navbar') ?>