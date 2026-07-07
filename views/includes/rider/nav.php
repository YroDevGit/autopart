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

  <?=js('includes/rider/nav')?>