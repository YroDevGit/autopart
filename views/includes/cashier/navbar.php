

<!-- NAVBAR TOP (Admin header) -->
  <nav class="top-navbar navbar navbar-light bg-white py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div class="d-flex align-items-center gap-3">
      <button class="btn btn-outline-secondary d-md-none" id="menuToggleBtn" type="button">
        <i class="bi bi-list fs-4"></i>
      </button>
      <h5 class="mb-0 fw-semibold"><i class="bi bi-speedometer2 text-warning"></i> Cashier / <span class="text-dark"><?=$pagename ?? "Page"?></span></h5>
    </div>
    <div class="d-flex gap-3 align-items-center">
      <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
          <img src="https://ui-avatars.com/api/?background=e25822&color=fff&name=<?=gval('fullname')?>" width="38" height="38" class="rounded-circle border">
          <span class="d-none d-sm-block ms-2 fw-semibold"><?=gval("fullname")?></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end shadow">
          <li><a class="dropdown-item cursor-pointer" id="userprofilelink" href="#"><i class="bi bi-person-circle"></i> Profile</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger cursor-pointer" id="logout_btn"><i class="bi bi-lock"></i> Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>
  <?=js('includes/cashier/navbar')?>