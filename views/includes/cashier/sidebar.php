<?=translation_icon()?>
<aside class="admin-sidebar" id="adminSidebar">
  <div class="sidebar-brand d-flex align-items-center justify-content-center gap-2">
    <i class="bi bi-gear-wide-connected fs-3 text-warning"></i>
    <span class="fw-bold fs-5 text-white">KYG <span class="text-warning">ADMIN</span></span>
  </div>
  <ul class="nav flex-column mb-auto">
    <li class="nav-item">
      <a href="/cashier/orders" class="nav-link-admin <?=current_page() == "cashier/orders" ? 'active' : ''?> nav-link d-flex align-items-center">
        <i class="bi bi-truck"></i> <span><?=t('Orders')?></span>
      </a>
    </li>
    <li class="nav-item">
      <a href="/cashier/uploads" class="nav-link-admin <?=current_page() == "cashier/uploads" ? 'active' : ''?> nav-link d-flex align-items-center">
        <i class="bi bi-upload"></i> <span>Upload</span>
      </a>
    </li>
    <li class="nav-item">
      <a href="/cashier/products" class="nav-link-admin nav-link <?=current_page() == "cashier/products" ? 'active' : ''?> d-flex align-items-center">
        <i class="bi bi-box-seam"></i> <span><?=t('Products')?></span>
      </a>
    </li>
    <li class="nav-item">
      <a href="/cashier/shipping" class="nav-link-admin nav-link d-flex align-items-center <?=current_page() == "cashier/shipping" ? 'active' : ''?>">
        <i class="bi bi-truck"></i> <span><?=t('Shipping')?></span>
      </a>
    </li>
    <li class="nav-item">
      <a href="/cashier/pos" class="nav-link-admin nav-link d-flex align-items-center <?=current_page() == "cashier/pos" ? 'active' : ''?>">
        <i class="bi bi-laptop"></i> <span><?=t('POS')?></span>
      </a>
    </li>
    <!--
    <li class="nav-item">
      <a href="#" class="nav-link-admin nav-link d-flex align-items-center">
        <i class="bi bi-people"></i> <span><?=t('Customers')?></span>
      </a>
    </li>
    
    <li class="nav-item">
      <a href="#" class="nav-link-admin nav-link d-flex align-items-center">
        <i class="bi bi-bar-chart-steps"></i> <span>Analytics</span>
      </a>
    </li>
    -->
    <li class="nav-item">
      <a href="/ctrxtools/db" class="nav-link-admin <?=current_page() == "ctrxtools/db" ? 'active' : ''?> nav-link d-flex align-items-center">
        <i class="bi bi-database"></i> <span>Database</span>
      </a>
    </li>
    <li class="nav-item">
      <a href="/ctrxtools/translations" class="nav-link-admin <?=current_page() == "/ctrxtools/translations" ? 'active' : ''?> nav-link d-flex align-items-center">
        <i class="bi bi-flag"></i> <span>Translations</span>
      </a>
    </li>
    <!--
    <li class="nav-item mt-4">
      <a href="#" class="nav-link-admin nav-link d-flex align-items-center">
        <i class="bi bi-gear"></i> <span><?=t('Settings')?></span>
      </a>
    </li>
    -->
  </ul>
  <hr class="text-secondary mx-3 my-3">
  <!--
  <div class="mt-auto pb-4">
    <a href="#" class="nav-link-admin nav-link d-flex align-items-center">
      <i class="bi bi-box-arrow-right"></i> <span>Logout</span>
    </a>
  </div>
  -->
</aside>