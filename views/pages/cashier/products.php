<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
  <title>AutoParts Admin | Product Manager</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?=assets('cashier.css')?>">
</head>
<body>

<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<?=include_page("cashier/sidebar")?>

<div class="main-content-wrapper" id="mainContentWrapper">
  <?=include_page('cashier/navbar', ["pagename"=>"Products / Inventory", "icon"=>"bi-box-seam"])?>

  <div class="content-inner">
    <!-- PRODUCT MANAGEMENT CARD (TABLE INSIDE) -->
    <div class="card admin-card border-0 shadow-sm">
      <div class="card-header bg-white border-0 pt-4 pb-0">
        <div class="header-actions">
          <!-- LEFT SIDE: SEARCH BAR -->
          <div class="search-section">
            <div class="search-container">
              <div class="input-group search-input-group">
                <input type="text" class="form-control search-input" id="productSearchInput" 
                       placeholder="🔍 <?=t('Search by name, category, ID...')?>" 
                       aria-label="Search products" value="<?=$_GET['search'] ?? null ?>">
                <button class="btn search-btn" id="searchButton" type="button">
                  <i class="bi bi-search"></i>
                </button>
                <button class="btn clear-search-btn" id="clearSearchButton" type="button" title="Clear search">
                  <i class="bi bi-x-lg"></i>
                </button>
              </div>
            </div>
          </div>
          
          <!-- RIGHT SIDE: ADD BUTTON -->
          <div class="add-button-section">
            <button class="btn btn-autoparts-primary rounded-pill px-4 py-2 shadow-sm" data-bs-toggle="modal" id="addproductbtn" data-bs-target="#addProductModal">
              <i class="bi bi-plus-circle"></i> <?=t('Add New Product')?>
            </button>
          </div>
        </div>
        <div class="mt-2">
          <p class="text-muted small mb-0"><i class="bi bi-info-circle"></i> <?=t('Product catalog management')?></p>
        </div>
      </div>
      <div class="card-body p-3 p-md-4">
        <!-- PRODUCTS TABLE (standard html table, action buttons included) -->
        <div class="table-responsive" id="product-container">
          <table class="table table-hover align-middle mb-0" id="productTable">
            <thead class="table-light">
              <tr>
                <th>#<?=t('ID')?></th>
                <th><?=t('IMAGE')?></th>
                <th><?=t('PRODUCT NAME')?></th>
                <th><?=t("CATEGORY")?></th>
                <th><?=t('PRICE')?></th>
                <th><?=t('STOCK')?></th>
                <th><?=t('ACTIONS')?></th>
              </tr>
            </thead>
            <tbody id="productTableBody">
              <!-- dynamic content from JS -->
            </tbody>
          </table>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-3">
          <small class="text-muted">Showing <span id="rowCount">0</span> products</small>
          <small class="text-muted" id="searchStatus"></small>
        </div>
      </div>
    </div>
  </div>

  <!-- FOOTER -->
  <?=include_page("cashier/footer")?>
</div>

<!-- ADD PRODUCT MODAL (Bootstrap 5) -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 rounded-4 shadow-lg">
      <div class="modal-header bg-dark text-white rounded-top-4 border-0">
        <h5 class="modal-title fw-bold" id="addProductModalLabel"><i class="bi bi-plus-circle-fill me-2 text-warning"></i> Add Auto Part Product</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="addProductForm">
        <div class="modal-body p-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Product Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" name="name" id="prodName">
              <div class="text-danger err" id="_name"></div>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Category</label>
              <select class="form-select" name="category" id="prodCategory">
                
              </select>
              <div class="text-danger err" id="_category"></div>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Price (₱)</label>
              <input type="text" name="price" class="form-control" id="prodPrice" >
              <div class="text-danger err" id="_price"></div>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Details</label>
              <textarea class="form-control" name="details" id="" cols="30" rows="5"></textarea>
              <div class="text-danger err" id="_details"></div>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Image URL (optional)</label>
              <input type="text" class="form-control" name="image" id="prodImage" placeholder="https://via.placeholder.com/60?text=AutoPart">
              <small class="text-muted">Enter direct image link or use placeholder</small>
              <div class="mt-2" id="imagePreviewPlaceholder" style="display: none;">
                <img id="previewImg" src="#" width="100" height="100" class="rounded border">
              </div>
              <div class="text-danger err" id="_image"></div>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light border-0 rounded-bottom-4">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-autoparts-primary px-4"><i class="bi bi-save"></i> Add Product</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- EDIT PRODUCT MODAL -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 rounded-4 shadow-lg">
      <div class="modal-header bg-dark text-white rounded-top-4">
        <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2 text-warning"></i> Edit Product</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="editProductForm">
        <input type="hidden" id="editProductId">
        <div class="modal-body p-4">
          <div class="row g-3">
            <div class="col-md-12">
              <label class="form-label">Product Name</label>
              <input type="text" class="form-control" id="editProdName" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Category</label>
              <select class="form-select" id="editProdCategory">
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Price (₱)</label>
              <input type="number" step="0.01" class="form-control" id="editProdPrice" required>
            </div>
            <div class="col-12">
              <label class="form-label">Image URL</label>
              <input type="text" class="form-control" id="editProdImage">
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-autoparts-primary">Update Product</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
<?=js()?>