<?=include_page("cashier/filter")?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
  <title>AutoParts Admin | User Management</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?=assets('cashier.css')?>">
  <style>
    /* User Management Custom Styles */
    .user-card {
      background: white;
      border-radius: 1rem;
      transition: transform 0.2s, box-shadow 0.2s;
      border: 1px solid #e9ecef;
      overflow: hidden;
    }
    
    .user-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }
    
    .user-card-header {
      background: linear-gradient(135deg, #00a89615 0%, #02809010 100%);
      padding: 1rem;
      border-bottom: 2px solid #00a896;
    }
    
    .user-card-body {
      padding: 1.25rem;
    }
    
    .user-card-footer {
      padding: 1rem;
      background: #f8f9fa;
      border-top: 1px solid #e9ecef;
      display: flex;
      gap: 0.5rem;
      justify-content: flex-end;
    }
    
    .user-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
      gap: 1.5rem;
    }
    
    .role-badge {
      padding: 0.25rem 0.75rem;
      border-radius: 50px;
      font-size: 0.7rem;
      font-weight: 600;
    }
    
    .role-admin { background: #dc354520; color: #a71d2a; border: 1px solid #dc354560; }
    .role-manager { background: #ffc10720; color: #b78103; border: 1px solid #ffc10760; }
    .role-cashier { background: #0dcaf020; color: #0a6e7c; border: 1px solid #0dcaf060; }
    .role-rider { background: #00a89620; color: #028090; border: 1px solid #00a89660; }
    .role-user { background: #6f42c120; color: #4a2a8a; border: 1px solid #6f42c160; }
    
    .status-badge-user {
      padding: 0.25rem 0.75rem;
      border-radius: 50px;
      font-size: 0.7rem;
      font-weight: 600;
    }
    .status-active { background: #19875420; color: #0a5c36; border: 1px solid #19875460; }
    .status-inactive { background: #dc354520; color: #a71d2a; border: 1px solid #dc354560; }
    
    .empty-users {
      text-align: center;
      padding: 4rem;
      background: white;
      border-radius: 1rem;
    }
    
    .user-avatar {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      object-fit: cover;
      background: #e9ecef;
    }
    
    .avatar-placeholder {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      background: linear-gradient(135deg, #00a896 0%, #028090 100%);
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      font-weight: 700;
    }
    
    .toast-custom {
      position: fixed;
      bottom: 20px;
      right: 20px;
      z-index: 1100;
    }
    
    .password-toggle {
      cursor: pointer;
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      z-index: 10;
      color: #6c757d;
    }
    
    .password-toggle:hover {
      color: #00a896;
    }
    
    .form-password-wrapper {
      position: relative;
    }
    
    .form-password-wrapper .form-control {
      padding-right: 40px;
    }
  </style>
</head>
<body>

<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<?=include_page("cashier/sidebar")?>

<div class="main-content-wrapper" id="mainContentWrapper">
  <?=include_page('cashier/navbar', ["pagename"=>"User Management"])?>

  <div class="content-inner">
    <!-- USER MANAGEMENT CARD -->
    <div class="card admin-card border-0 shadow-sm">
      <div class="card-header bg-white border-0 pt-4 pb-0">
        <div class="header-actions">
          <!-- LEFT SIDE: SEARCH BAR -->
          <div class="search-section">
            <div class="search-container">
              <div class="input-group search-input-group">
                <input type="text" class="form-control search-input" id="userSearchInput" 
                       placeholder="🔍 Search by name, email, or role..." 
                       aria-label="Search users">
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
            <button class="btn btn-autoparts-primary rounded-pill px-4 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
              <i class="bi bi-person-plus"></i> Add New User
            </button>
          </div>
        </div>
        <div class="mt-2">
          <p class="text-muted small mb-0"><i class="bi bi-info-circle"></i> Manage user accounts and access permissions</p>
        </div>
      </div>
      
      <div class="card-body p-3 p-md-4">
        <!-- USERS GRID -->
        <div id="usersContainer">
          <div class="text-center py-5">
            <div class="spinner-border text-success" role="status"></div>
            <p class="mt-2 text-muted">Loading users...</p>
          </div>
        </div>
        
        <div class="d-flex justify-content-between align-items-center mt-3">
          <small class="text-muted">Showing <span id="userCount">0</span> users</small>
          <small class="text-muted" id="searchStatus"></small>
        </div>
      </div>
    </div>
  </div>

  <!-- FOOTER -->
  <?=include_page("cashier/footer")?>
</div>

<!-- ADD USER MODAL -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 rounded-4 shadow-lg">
      <div class="modal-header bg-dark text-white rounded-top-4 border-0">
        <h5 class="modal-title fw-bold" id="addUserModalLabel">
          <i class="bi bi-person-plus-fill me-2 text-warning"></i> Add New User
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="addUserForm">
        <div class="modal-body p-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">FullName <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="userFirstName" name="fullname" placeholder="e.g., Juan">
              <?=error_text("fullname")?>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Status</label>
              <select class="form-select" id="userStatus" name="status">
                <option value="1">Active</option>
                <option value="0">Inactive</option>
              </select>
              <?=error_text("status")?>
            </div>
          </div>
          
          <div class="row g-3 mt-1">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
              <input type="email" class="form-control" id="userEmail" name="email" placeholder="user@example.com">
              <?=error_text("email")?>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
              <select class="form-select" id="userRole" name="role" >
                <option value="">Select Role...</option>
                <option value="2">Administrator</option>
                <option value="3">Rider</option>
              </select>
              <?=error_text("role")?>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light border-0 rounded-bottom-4">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-autoparts-primary px-4">
            <i class="bi bi-person-plus"></i> Create User
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- EDIT USER MODAL -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 rounded-4 shadow-lg">
      <div class="modal-header bg-dark text-white rounded-top-4 border-0">
        <h5 class="modal-title fw-bold">
          <i class="bi bi-pencil-square me-2 text-warning"></i> Edit User
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="editUserForm">
        <input type="hidden" id="editUserId">
        <div class="modal-body p-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="editUserFirstName" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="editUserLastName" required>
            </div>
          </div>
          
          <div class="row g-3 mt-1">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
              <input type="email" class="form-control" id="editUserEmail" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Contact Number</label>
              <input type="text" class="form-control" id="editUserContact">
            </div>
          </div>
          
          <div class="row g-3 mt-1">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="editUserUsername" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
              <select class="form-select" id="editUserRole">
                <option value="admin">Administrator</option>
                <option value="manager">Manager</option>
                <option value="cashier">Cashier</option>
                <option value="rider">Rider/Delivery</option>
                <option value="user">Customer</option>
              </select>
            </div>
          </div>
          
          <div class="row g-3 mt-1">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Status</label>
              <select class="form-select" id="editUserStatus">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
            <div class="col-md-6 d-flex align-items-end">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="editUserForceReset">
                <label class="form-check-label" for="editUserForceReset">
                  Require password reset on next login
                </label>
              </div>
            </div>
          </div>
          
          <div class="mt-3">
            <div class="alert alert-info small">
              <i class="bi bi-info-circle"></i> Leave password fields empty to keep current password
            </div>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold">New Password</label>
                <div class="form-password-wrapper">
                  <input type="password" class="form-control" id="editUserPassword" placeholder="Leave empty to keep current">
                  <i class="bi bi-eye-slash password-toggle" id="toggleEditPassword"></i>
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Confirm New Password</label>
                <div class="form-password-wrapper">
                  <input type="password" class="form-control" id="editUserConfirmPassword" placeholder="Confirm new password">
                  <i class="bi bi-eye-slash password-toggle" id="toggleEditConfirmPassword"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light border-0 rounded-bottom-4">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-autoparts-primary px-4">
            <i class="bi bi-save"></i> Update User
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- DELETE CONFIRMATION MODAL -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 rounded-4 shadow-lg">
      <div class="modal-header bg-danger text-white rounded-top-4">
        <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i> <span id="delquestion1">Delete User</span></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <input type="hidden" id="deleteUserId">
        <p id="delquestion2">Are you sure you want to delete this user?</p>
        <p class="text-muted small">This action cannot be undone. All associated data will be permanently removed.</p>
        <div class="alert alert-warning small">
          <i class="bi bi-exclamation-triangle"></i> Consider deactivating instead of deleting if unsure.
        </div>
      </div>
      <div class="modal-footer bg-light border-0 rounded-bottom-4">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteUserBtn">Delete User</button>
      </div>
    </div>
  </div>
</div>

<!-- Toast Notification -->
<div class="toast-custom">
  <div id="actionToast" class="toast align-items-center text-white bg-success border-0" role="alert">
    <div class="d-flex">
      <div class="toast-body" id="toastMsg">✅ Action completed!</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
<?=js()?>