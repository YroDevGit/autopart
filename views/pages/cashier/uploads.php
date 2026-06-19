<?=include_page("cashier/filter")?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>AutoParts Admin | Media Gallery</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= assets('cashier.css') ?>">
    <style>
        .photo-card {
            position: relative;
            background: white;
            border-radius: 1rem;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            cursor: pointer;
        }

        .photo-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        }

        .photo-card .photo-img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            background: #f8f9fa;
        }

        .photo-card .photo-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .photo-card:hover .photo-overlay {
            opacity: 1;
        }

        .photo-card .photo-info {
            padding: 0.75rem;
            background: white;
            border-top: 1px solid #e9ecef;
        }

        .photo-card .photo-name {
            font-size: 0.85rem;
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .photo-card .photo-date {
            font-size: 0.7rem;
            color: #6c757d;
        }

        .copy-link-btn {
            background: #00a896;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 0.85rem;
            transition: all 0.2s;
        }

        .copy-link-btn:hover {
            background: #028090;
            transform: scale(1.05);
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .empty-gallery {
            text-align: center;
            padding: 4rem;
            background: white;
            border-radius: 1rem;
        }

        .toast-custom {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1100;
        }

        .preview-img {
            max-width: 100%;
            max-height: 200px;
            object-fit: contain;
            border-radius: 0.5rem;
        }

        .selected-file-name {
            font-size: 0.85rem;
            color: #00a896;
            margin-top: 0.5rem;
        }
    </style>
</head>

<body>
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <?= include_page("cashier/sidebar") ?>

    <div class="main-content-wrapper" id="mainContentWrapper">
        <?= include_page('cashier/navbar', ["pagename" => "Media Gallery / Uploads"]) ?>

        <div class="content-inner">
            <div class="card admin-card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <div class="header-actions">
                        <div class="search-section">
                            <div class="search-container">
                                <div class="input-group search-input-group">
                                    <input type="text" class="form-control search-input" id="photoSearchInput"
                                        placeholder="🔍 Search by filename..."
                                        aria-label="Search photos">
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
                            <button class="btn btn-autoparts-primary rounded-pill px-4 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#uploadPhotoModal">
                                <i class="bi bi-cloud-upload"></i> Upload Photo
                            </button>
                        </div>
                    </div>
                    <div class="mt-2">
                        <p class="text-muted small mb-0"><i class="bi bi-info-circle"></i> Manage your media gallery - upload, view, and copy image links</p>
                    </div>
                </div>

                <div class="card-body p-3 p-md-4">
                    <div id="photosContainer">
                        <div class="text-center py-5">
                            <div class="spinner-border text-success" role="status"></div>
                            <p class="mt-2 text-muted">Loading photos...</p>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <small class="text-muted">Showing <span id="photoCount">0</span> photos</small>
                        <small class="text-muted" id="searchStatus"></small>
                    </div>
                </div>
            </div>
        </div>

        <?= include_page("cashier/footer") ?>
    </div>

    <div class="modal fade" id="uploadPhotoModal" tabindex="-1" aria-labelledby="uploadPhotoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg">
                <div class="modal-header bg-dark text-white rounded-top-4 border-0">
                    <h5 class="modal-title fw-bold" id="uploadPhotoModalLabel">
                        <i class="bi bi-cloud-upload-fill me-2 text-warning"></i> Upload Photo
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="uploadPhotoForm" enctype="multipart/form-data">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Select Photo <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="photoFile" name="photo" accept="image/*" required>
                            <small class="text-muted">Supported formats: JPG, PNG, GIF, WebP (Max: 5MB)</small>
                            <div class="text-danger err" id="_photo"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Photo Name (Optional)</label>
                            <input type="text" class="form-control" id="photoName" name="name" placeholder="Enter custom name for this photo">
                            <small class="text-muted">Leave empty to use original filename</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Preview</label>
                            <div class="text-center p-3 bg-light rounded-3" id="previewContainer">
                                <i class="bi bi-image fs-1 text-muted"></i>
                                <p class="mb-0 small text-muted">No file selected</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 rounded-bottom-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-autoparts-primary px-4">
                            <i class="bi bi-cloud-upload"></i> Upload Photo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="toast-custom">
        <div id="copyToast" class="toast align-items-center text-white bg-success border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body" id="toastMsg">✅ Link copied to clipboard!</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <?= js() ?>

</body>

</html>
<?= js() ?>