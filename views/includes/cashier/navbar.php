<!-- NAVBAR TOP (Admin header) -->
<nav class="top-navbar navbar navbar-light bg-white py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
  <div class="d-flex align-items-center gap-3">
    <button class="btn btn-outline-secondary d-md-none" id="menuToggleBtn" type="button">
      <i class="bi bi-list fs-4"></i>
    </button>
    <h5 class="mb-0 fw-semibold"><i class="bi bi-speedometer2 text-warning"></i> Cashier / <span class="text-dark"><?= $pagename ?? "Page" ?></span></h5>
  </div>

  <div class="d-flex gap-3 align-items-center">
    <!-- Notification Bell with Number -->
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
</nav>

<!-- Additional CSS for notification bell -->
<style>
.notification-btn {
  position: relative;
  transition: all 0.2s;
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
</style>

<?= js('includes/cashier/navbar') ?>

