class ImageSelector {
    static instances = [];

    constructor(element, config = {}) {
        this.element = typeof element === 'string' 
            ? document.querySelector(element) 
            : element;
        
        if (!this.element) {
            throw new Error('Element not found');
        }

        this.config = {
            path: config.path || 'public',
            action: config.action || '0',
            selection: config.selection || 'single',
            quality: config.quality || 95,
            maxWidth: config.maxWidth || 1920,
            maxHeight: config.maxHeight || 1920,
            compressThreshold: config.compressThreshold || 100,
            title: config.title || 'Select Image',
            type: config.type || '*',
            onSelect: config.onSelect || null,
            onUpload: config.onUpload || null,
            onDelete: config.onDelete || null,
            ...config
        };

        this._callbacks = [];
        this._initialized = false;
        this._value = this.element.value || '';
        this._originalValue = this._value;
        this._isOpen = false;
        
        // Picker state
        this._selectedImages = [];
        this._images = [];
        this._filteredImages = [];
        this._overlay = null;
        this._modal = null;
        this._grid = null;
        this._searchInput = null;
        this._selectBtn = null;
        this._cancelBtn = null;
        this._addBtn = null;
        this._infoText = null;
        this._uploadArea = null;
        this._fileInput = null;
        this._progressBar = null;
        this._nameProgress = null;
        this._previewOverlay = null;
        this._currentImageContainer = null;
        this._isUploading = false;
        this._clickHandler = null;

        // Store instance
        ImageSelector.instances.push(this);
    }

    /**
     * Initialize the image selector
     */
    init() {
        if (this._initialized) return this;

        this._ensureStyle();
        this.element.setAttribute("readonly", "");
        
        this._clickHandler = (e) => {
            e.preventDefault();
            this.open();
        };
        
        this.element.addEventListener("click", this._clickHandler);
        this._initialized = true;
        return this;
    }

    /**
     * Ensure CSS styles are loaded
     * @private
     */
    _ensureStyle() {
        const styleId = "imageselector-style";
        if (document.getElementById(styleId)) return;

        const style = document.createElement("style");
        style.id = styleId;
        style.textContent = `
            .imageselector-overlay {
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.5);
                backdrop-filter: blur(4px);
                -webkit-backdrop-filter: blur(4px);
                display: none;
                align-items: center;
                justify-content: center;
                z-index: 9999999;
                opacity: 0;
                transition: opacity 0.3s ease;
                padding: 20px;
                max-height: 100%;
                overflow-y: scroll;
            }
            .imageselector-overlay.imageselector-show {
                display: flex;
                opacity: 1;
            }
            .imageselector-modal {
                width: 95%;
                max-width: 1100px;
                max-height: 90vh;
                background: #ffffff;
                border-radius: 16px;
                overflow: hidden;
                box-shadow: 0 30px 80px rgba(0,0,0,0.2);
                display: flex;
                flex-direction: column;
                transform: scale(0.95) translateY(20px);
                opacity: 0;
                transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            }
            .imageselector-overlay.imageselector-show .imageselector-modal {
                transform: scale(1) translateY(0);
                opacity: 1;
            }
            .imageselector-header {
                padding: 20px 28px;
                background: #f8f9fa;
                border-bottom: 1px solid #e9ecef;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-shrink: 0;
                flex-wrap: wrap;
                gap: 12px;
            }
            .imageselector-header-left {
                display: flex;
                align-items: center;
                gap: 8px;
            }
            .imageselector-header h2 {
                margin: 0;
                font-size: 20px;
                font-weight: 600;
                color: #212529;
                letter-spacing: 0.3px;
            }
            .imageselector-header-actions {
                display: flex;
                gap: 12px;
                align-items: center;
                flex-wrap: wrap;
            }
            .imageselector-search {
                padding: 8px 16px;
                border-radius: 8px;
                border: 1px solid #dee2e6;
                background: #ffffff;
                color: #212529;
                font-size: 14px;
                width: 220px;
                transition: all 0.2s ease;
                outline: none;
            }
            .imageselector-search:focus {
                border-color: #0066ff;
                box-shadow: 0 0 0 3px rgba(0,102,255,0.1);
            }
            .imageselector-btn-add {
                padding: 8px 20px;
                border: none;
                border-radius: 8px;
                background: #28a745;
                color: #fff;
                font-weight: 600;
                font-size: 14px;
                cursor: pointer;
                transition: all 0.2s ease;
                display: flex;
                align-items: center;
                gap: 6px;
            }
            .imageselector-btn-add:hover {
                background: #218838;
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(40,167,69,0.3);
            }
            .imageselector-close {
                border: none;
                background: none;
                border-radius: 50%;
                width: 40px;
                height: 40px;
                font-size: 35px;
                cursor: pointer;
                color: #495057;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: transform 0.3s ease;
            }
            .imageselector-close:hover {
                color: #212529;
                transform: rotate(90deg);
            }
            .imageselector-body {
                padding: 20px 24px;
                overflow-y: auto;
                flex: 1;
                background: #ffffff;
                max-height: 60vh;
            }
            .imageselector-body::-webkit-scrollbar {
                width: 6px;
            }
            .imageselector-body::-webkit-scrollbar-track {
                background: #f8f9fa;
            }
            .imageselector-body::-webkit-scrollbar-thumb {
                background: #dee2e6;
                border-radius: 10px;
            }
            .imageselector-current-image {
                display: none;
                margin-bottom: 20px;
                padding: 16px;
                background: #f8f9fa;
                border-radius: 12px;
                border: 2px solid #e9ecef;
            }
            .imageselector-current-image.imageselector-show {
                display: block;
            }
            .imageselector-current-image-wrapper {
                display: flex;
                gap: 12px;
                overflow-x: auto;
                padding: 8px 4px;
                scroll-behavior: smooth;
                max-width: 100%;
            }
            .imageselector-current-image-wrapper::-webkit-scrollbar {
                height: 6px;
            }
            .imageselector-current-image-wrapper::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 10px;
            }
            .imageselector-current-image-wrapper::-webkit-scrollbar-thumb {
                background: #dee2e6;
                border-radius: 10px;
            }
            .imageselector-current-image-item {
                position: relative;
                flex: 0 0 auto;
                width: 120px;
                height: 120px;
                border-radius: 8px;
                overflow: hidden;
                border: 2px solid #e9ecef;
                background: #ffffff;
                transition: all 0.2s ease;
            }
            .imageselector-current-image-item:hover {
                border-color: #0066ff;
                transform: scale(1.02);
            }
            .imageselector-current-image-item img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }
            .imageselector-current-image-item .imageselector-current-image-actions {
                position: absolute;
                top: 4px;
                left: 4px;
                display: flex;
                gap: 4px;
                opacity: 0;
                transition: opacity 0.2s ease;
            }
            .imageselector-current-image-item:hover .imageselector-current-image-actions {
                opacity: 1;
            }
            .imageselector-current-image-eye {
                width: 24px;
                height: 24px;
                border-radius: 50%;
                background: rgba(0,0,0,0.6);
                border: none;
                color: #fff;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 11px;
                transition: all 0.2s ease;
                backdrop-filter: blur(4px);
            }
            .imageselector-current-image-eye:hover {
                background: rgba(0,0,0,0.8);
                transform: scale(1.1);
            }
            .imageselector-current-image-label {
                font-size: 13px;
                color: #6c757d;
                font-weight: 500;
                margin-bottom: 8px;
            }
            .imageselector-current-image-name {
                font-size: 13px;
                color: #212529;
                font-weight: 500;
                margin-top: 8px;
                word-break: break-all;
            }
            .imageselector-upload-area {
                display: none;
                padding: 30px;
                background: #f8f9fa;
                border-radius: 12px;
                border: 2px dashed #dee2e6;
                margin-bottom: 20px;
                text-align: center;
                transition: all 0.3s ease;
            }
            .imageselector-upload-area.imageselector-show {
                display: block;
            }
            .imageselector-upload-area.dragover {
                border-color: #0066ff;
                background: #f0f7ff;
            }
            .imageselector-upload-area input[type="file"] {
                display: none;
            }
            .imageselector-upload-label {
                display: inline-block;
                padding: 12px 32px;
                background: #0066ff;
                color: #fff;
                border-radius: 8px;
                cursor: pointer;
                font-weight: 600;
                transition: all 0.2s ease;
            }
            .imageselector-upload-label:hover {
                background: #0052cc;
                transform: translateY(-2px);
            }
            .imageselector-upload-text {
                color: #6c757d;
                margin: 12px 0;
                font-size: 14px;
            }
            .imageselector-upload-progress {
                display: none;
                margin-top: 16px;
                height: 4px;
                background: #e9ecef;
                border-radius: 2px;
                overflow: hidden;
            }
            .imageselector-upload-progress.imageselector-show {
                display: block;
            }
            .imageselector-upload-progress-bar {
                height: 100%;
                background: #0066ff;
                width: 0%;
                transition: width 0.3s ease;
            }
            .imageselector-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
                gap: 16px;
            }
            .imageselector-item {
                background: #ffffff;
                border-radius: 10px;
                overflow: hidden;
                cursor: pointer;
                transition: all 0.25s ease;
                border: 2px solid #e9ecef;
                position: relative;
            }
            .imageselector-item:hover {
                transform: translateY(-4px);
                box-shadow: 0 8px 25px rgba(0,0,0,0.1);
                border-color: #dee2e6;
            }
            .imageselector-item.imageselector-selected {
                border-color: #0066ff;
                box-shadow: 0 0 0 3px rgba(0,102,255,0.2);
            }
            .imageselector-item img {
                width: 100%;
                height: 160px;
                object-fit: cover;
                display: block;
                background: #f8f9fa;
            }
            .imageselector-item-info {
                padding: 12px 14px;
                background: #ffffff;
            }
            .imageselector-item-name {
                font-size: 13px;
                color: #212529;
                font-weight: 500;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                margin-bottom: 4px;
            }
            .imageselector-item-size {
                font-size: 11px;
                color: #6c757d;
            }
            .imageselector-item-check {
                position: absolute;
                bottom: 8px;
                right: 8px;
                width: 24px;
                height: 24px;
                border-radius: 50%;
                background: #0066ff;
                color: #fff;
                display: none;
                align-items: center;
                justify-content: center;
                font-size: 14px;
                font-weight: bold;
            }
            .imageselector-item.imageselector-selected .imageselector-item-check {
                display: flex;
            }
            .imageselector-item-actions {
                position: absolute;
                top: 8px;
                left: 8px;
                display: flex;
                gap: 6px;
                opacity: 0;
                transition: opacity 0.2s ease;
            }
            .imageselector-item:hover .imageselector-item-actions {
                opacity: 1;
            }
            .imageselector-item-eye {
                width: 24px;
                height: 24px;
                border-radius: 50%;
                background: rgba(0,0,0,0.6);
                border: none;
                color: #fff;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 14px;
                transition: all 0.2s ease;
                backdrop-filter: blur(4px);
            }
            .imageselector-item-eye:hover {
                background: rgba(0,0,0,0.8);
                transform: scale(1.1);
            }
            .imageselector-item-delete {
                width: 24px;
                height: 24px;
                border-radius: 50%;
                background: rgba(220, 53, 69, 0.85);
                border: none;
                color: #fff;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 14px;
                transition: all 0.2s ease;
                backdrop-filter: blur(4px);
            }
            .imageselector-item-delete:hover {
                background: rgba(200, 35, 51, 0.95);
                transform: scale(1.1);
            }
            .imageselector-empty {
                grid-column: 1 / -1;
                text-align: center;
                padding: 60px 20px;
                color: #adb5bd;
                font-size: 16px;
            }
            .imageselector-footer {
                padding: 16px 28px;
                background: #f8f9fa;
                border-top: 1px solid #e9ecef;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-shrink: 0;
                flex-wrap: wrap;
                gap: 12px;
            }
            .imageselector-footer-info {
                color: #6c757d;
                font-size: 14px;
            }
            .imageselector-footer-info span {
                color: #212529;
                font-weight: 600;
            }
            .imageselector-footer-actions {
                display: flex;
                gap: 10px;
            }
            .imageselector-btn {
                padding: 10px 24px;
                border: none;
                border-radius: 8px;
                cursor: pointer;
                font-size: 14px;
                font-weight: 600;
                transition: all 0.2s ease;
                font-family: inherit;
            }
            .imageselector-btn-cancel {
                background: #e9ecef;
                color: #495057;
            }
            .imageselector-btn-cancel:hover {
                background: #dee2e6;
                color: #212529;
            }
            .imageselector-btn-select {
                background: #0066ff;
                color: #fff;
            }
            .imageselector-btn-select:hover {
                background: #0052cc;
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0,102,255,0.3);
            }
            .imageselector-preview-overlay {
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.9);
                display: none;
                align-items: center;
                justify-content: center;
                z-index: 99999999;
                padding: 20px;
                opacity: 0;
                transition: opacity 0.3s ease;
            }
            .imageselector-preview-overlay.imageselector-show {
                display: flex;
                opacity: 1;
            }
            .imageselector-preview-overlay img {
                max-width: 95%;
                max-height: 95%;
                object-fit: contain;
                border-radius: 4px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.5);
                transform: scale(0.95);
                transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            }
            .imageselector-preview-overlay.imageselector-show img {
                transform: scale(1);
            }
            .imageselector-preview-close {
                position: absolute;
                top: 20px;
                right: 30px;
                font-size: 40px;
                color: #fff;
                cursor: pointer;
                background: none;
                border: none;
                padding: 10px;
                line-height: 1;
            }
            .imageselector-preview-close:hover {
                color: #ccc;
            }
            @media (max-width:768px) {
                .imageselector-modal {
                    width: 100%;
                    max-height: 100vh;
                    border-radius: 0;
                }
                .imageselector-header {
                    flex-direction: column;
                    align-items: stretch;
                    padding: 16px;
                }
                .imageselector-header-left {
                    flex-direction: row;
                    align-items: stretch;
                }
                .imageselector-header-actions {
                    flex-direction: column;
                }
                .imageselector-search {
                    width: 100%;
                }
                .imageselector-body {
                    padding: 12px;
                    max-height: 420px;
                }
                .imageselector-grid {
                    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
                    gap: 10px;
                }
                .imageselector-item img {
                    height: 120px;
                }
                .imageselector-footer {
                    flex-direction: column;
                    padding: 16px;
                }
                .imageselector-footer-actions {
                    width: 100%;
                }
                .imageselector-footer-actions button {
                    flex: 1;
                }
                .imageselector-current-image-item {
                    width: 80px;
                    height: 80px;
                }
            }
        `;
        document.head.appendChild(style);
    }

    /**
     * Open the image selector
     */
    open() {
        if (this._isOpen) return;
        if (!this._initialized) this.init();

        if (!this._overlay) {
            this._buildOverlay();
        }

        this._displayCurrentImage();
        this._overlay.classList.add("imageselector-show");
        this._isOpen = true;
        document.body.style.overflow = "hidden";
        this._loadImages();
    }

    /**
     * Close the image selector
     */
    close() {
        if (!this._isOpen) return;
        this._overlay.classList.remove("imageselector-show");
        this._isOpen = false;
        document.body.style.overflow = "";
        if (this._uploadArea) {
            this._uploadArea.classList.remove("imageselector-show");
        }
        this._closePreview();
    }

    /**
     * Get the current value
     */
    getValue() {
        return this._value || this.element.value || '';
    }

    /**
     * Get image paths as array
     */
    getValues() {
        const value = this.getValue();
        return value ? value.split('||').filter(p => p.trim()) : [];
    }

    /**
     * Get first image path
     */
    getFirst() {
        const values = this.getValues();
        return values.length > 0 ? values[0] : '';
    }

    /**
     * Set value
     */
    setValue(value) {
        if (Array.isArray(value)) {
            this._value = value.join('||');
        } else {
            this._value = value || '';
        }
        this.element.value = this._value;
        this.element.dispatchEvent(new Event('change', { bubbles: true }));
        this._triggerCallbacks();
        
        if (this._isOpen) {
            this._displayCurrentImage();
        }
    }

    /**
     * Clear the value
     */
    clear() {
        this.setValue('');
    }

    /**
     * Check if empty
     */
    isEmpty() {
        const value = this.getValue();
        return !value || value.trim() === '';
    }

    /**
     * Get count of selected images
     */
    count() {
        return this.getValues().length;
    }

    /**
     * Check if has specific image
     */
    hasImage(path) {
        const values = this.getValues();
        return values.includes(path);
    }

    /**
     * Register change callback
     */
    onChange(callback) {
        if (typeof callback !== 'function') {
            throw new Error('Callback must be a function');
        }
        this._callbacks.push(callback);
        return () => {
            const index = this._callbacks.indexOf(callback);
            if (index !== -1) {
                this._callbacks.splice(index, 1);
            }
        };
    }

    /**
     * Register select callback
     */
    onSelect(callback) {
        this.config.onSelect = callback;
        return this;
    }

    /**
     * Register upload callback
     */
    onUpload(callback) {
        this.config.onUpload = callback;
        return this;
    }

    /**
     * Register delete callback
     */
    onDelete(callback) {
        this.config.onDelete = callback;
        return this;
    }

    /**
     * Destroy instance
     */
    destroy() {
        if (this._clickHandler) {
            this.element.removeEventListener("click", this._clickHandler);
        }
        if (this._overlay && this._overlay.parentNode) {
            this._overlay.parentNode.removeChild(this._overlay);
        }
        if (this._previewOverlay && this._previewOverlay.parentNode) {
            this._previewOverlay.parentNode.removeChild(this._previewOverlay);
        }
        this._callbacks = [];
        this._initialized = false;
        
        const index = ImageSelector.instances.indexOf(this);
        if (index !== -1) {
            ImageSelector.instances.splice(index, 1);
        }
    }

    /**
     * Build overlay
     * @private
     */
    _buildOverlay() {
        const overlay = document.createElement("div");
        overlay.className = "imageselector-overlay";

        const modal = document.createElement("div");
        modal.className = "imageselector-modal";

        // Header
        const header = document.createElement("div");
        header.className = "imageselector-header";

        const headerLeft = document.createElement("div");
        headerLeft.className = "imageselector-header-left";

        const title = document.createElement("h2");
        title.textContent = this.config.title;

        const addBtn = document.createElement("button");
        addBtn.className = "imageselector-btn-add";
        addBtn.innerHTML = "➕ Add Image";
        addBtn.addEventListener("click", () => {
            this._toggleUpload();
        });

        headerLeft.appendChild(title);
        headerLeft.appendChild(addBtn);

        const headerActions = document.createElement("div");
        headerActions.className = "imageselector-header-actions";

        const search = document.createElement("input");
        search.type = "text";
        search.className = "imageselector-search";
        search.placeholder = "Search images...";
        search.addEventListener("input", (e) => {
            this._filterImages(e.target.value);
        });

        const closeBtn = document.createElement("button");
        closeBtn.className = "imageselector-close";
        closeBtn.innerHTML = "×";
        closeBtn.addEventListener("click", () => this.close());

        headerActions.appendChild(search);
        headerActions.appendChild(closeBtn);
        header.appendChild(headerLeft);
        header.appendChild(headerActions);

        // Body
        const body = document.createElement("div");
        body.className = "imageselector-body";

        // Upload area
        const uploadArea = document.createElement("div");
        uploadArea.className = "imageselector-upload-area";

        const uploadLabel = document.createElement("label");
        uploadLabel.className = "imageselector-upload-label";
        uploadLabel.textContent = "Choose Image";

        const fileInput = document.createElement("input");
        fileInput.type = "file";
        fileInput.multiple = false;
        fileInput.accept = "image/*";

        const uploadText = document.createElement("div");
        uploadText.className = "imageselector-upload-text";
        uploadText.textContent = "or drag and drop here";

        const progressWrapper = document.createElement("div");
        progressWrapper.className = "imageselector-upload-progress";

        const nameProgress = document.createElement("div");
        nameProgress.style.display = 'none';
        nameProgress.innerText = "Uploading, please wait...";

        const progressBar = document.createElement("div");
        progressBar.className = "imageselector-upload-progress-bar";

        progressWrapper.appendChild(progressBar);
        uploadLabel.appendChild(fileInput);
        uploadArea.appendChild(uploadLabel);
        uploadArea.appendChild(uploadText);
        uploadArea.appendChild(progressWrapper);
        uploadArea.appendChild(nameProgress);

        // Drag and drop
        uploadArea.addEventListener("dragover", (e) => {
            e.preventDefault();
            uploadArea.classList.add("dragover");
        });
        uploadArea.addEventListener("dragleave", () => {
            uploadArea.classList.remove("dragover");
        });
        uploadArea.addEventListener("drop", (e) => {
            e.preventDefault();
            uploadArea.classList.remove("dragover");
            if (e.dataTransfer.files.length > 0) {
                this._handleUpload(e.dataTransfer.files[0]);
            }
        });

        fileInput.addEventListener("change", (e) => {
            if (e.target.files.length > 0) {
                this._handleUpload(e.target.files[0]);
            }
        });

        // Current image display
        const currentImageContainer = document.createElement("div");
        currentImageContainer.className = "imageselector-current-image";

        const currentLabel = document.createElement("div");
        currentLabel.className = "imageselector-current-image-label";
        currentLabel.textContent = "Currently selected:";

        const wrapper = document.createElement("div");
        wrapper.className = "imageselector-current-image-wrapper";
        wrapper.id = "imageselector-current-wrapper";

        const currentName = document.createElement("div");
        currentName.className = "imageselector-current-image-name";
        currentName.id = "imageselector-current-name";

        currentImageContainer.appendChild(currentLabel);
        currentImageContainer.appendChild(wrapper);
        currentImageContainer.appendChild(currentName);

        // Grid
        const grid = document.createElement("div");
        grid.className = "imageselector-grid";

        body.appendChild(uploadArea);
        body.appendChild(currentImageContainer);
        body.appendChild(grid);

        // Footer
        const footer = document.createElement("div");
        footer.className = "imageselector-footer";

        const info = document.createElement("div");
        info.className = "imageselector-footer-info";
        info.innerHTML = `Selected: <span id="imageselector-count">0</span>`;

        const footerActions = document.createElement("div");
        footerActions.className = "imageselector-footer-actions";

        const cancelBtn = document.createElement("button");
        cancelBtn.className = "imageselector-btn imageselector-btn-cancel";
        cancelBtn.textContent = "Clear";
        cancelBtn.addEventListener("click", () => {
            this._selectedImages = [];
            this._updateSelectionInfo();
            document.querySelectorAll(".imageselector-item.imageselector-selected")
                .forEach(el => el.classList.remove("imageselector-selected"));
        });

        const selectBtn = document.createElement("button");
        selectBtn.className = "imageselector-btn imageselector-btn-select";
        selectBtn.textContent = "Select";
        selectBtn.addEventListener("click", () => this._confirmSelection());

        footerActions.appendChild(cancelBtn);
        footerActions.appendChild(selectBtn);
        footer.appendChild(info);
        footer.appendChild(footerActions);

        modal.appendChild(header);
        modal.appendChild(body);
        modal.appendChild(footer);
        overlay.appendChild(modal);

        overlay.addEventListener("click", (e) => {
            if (e.target === overlay) this.close();
        });

        document.body.appendChild(overlay);

        // Preview overlay
        const previewOverlay = document.createElement("div");
        previewOverlay.className = "imageselector-preview-overlay";

        const previewClose = document.createElement("button");
        previewClose.className = "imageselector-preview-close";
        previewClose.innerHTML = "×";
        previewClose.addEventListener("click", () => this._closePreview());

        const previewImg = document.createElement("img");
        previewImg.alt = "Preview";

        previewOverlay.appendChild(previewImg);
        previewOverlay.appendChild(previewClose);

        previewOverlay.addEventListener("click", (e) => {
            if (e.target === previewOverlay) this._closePreview();
        });

        document.body.appendChild(previewOverlay);

        this._overlay = overlay;
        this._modal = modal;
        this._grid = grid;
        this._searchInput = search;
        this._selectBtn = selectBtn;
        this._cancelBtn = cancelBtn;
        this._addBtn = addBtn;
        this._infoText = info.querySelector("#imageselector-count");
        this._uploadArea = uploadArea;
        this._fileInput = fileInput;
        this._progressBar = progressBar;
        this._nameProgress = nameProgress;
        this._previewOverlay = previewOverlay;
        this._currentImageContainer = currentImageContainer;
    }

    /**
     * Display current image
     * @private
     */
    _displayCurrentImage() {
        if (!this._currentImageContainer) return;

        const currentValue = this.element.value;
        const wrapper = this._currentImageContainer.querySelector("#imageselector-current-wrapper");
        const nameEl = this._currentImageContainer.querySelector("#imageselector-current-name");

        wrapper.innerHTML = "";

        if (currentValue && currentValue.trim() !== "") {
            const urls = currentValue.split('||').map(u => u.trim()).filter(u => u !== "");

            if (urls.length > 0) {
                urls.forEach((url, index) => {
                    const item = document.createElement("div");
                    item.className = "imageselector-current-image-item";

                    const img = document.createElement("img");
                    img.src = url;
                    img.alt = `Image ${index + 1}`;
                    img.onerror = function() { this.style.display = "none"; };

                    const actions = document.createElement("div");
                    actions.className = "imageselector-current-image-actions";

                    const eyeBtn = document.createElement("button");
                    eyeBtn.className = "imageselector-current-image-eye";
                    eyeBtn.innerHTML = "👁";
                    eyeBtn.title = "Preview";
                    eyeBtn.addEventListener("click", (e) => {
                        e.stopPropagation();
                        this._openPreview({ url: url, name: `Image ${index + 1}` });
                    });

                    actions.appendChild(eyeBtn);
                    item.appendChild(img);
                    item.appendChild(actions);
                    wrapper.appendChild(item);
                });

                nameEl.textContent = `${urls.length} image${urls.length > 1 ? 's' : ''} selected`;
                this._currentImageContainer.classList.add("imageselector-show");
                return;
            }
        }

        nameEl.textContent = "";
        this._currentImageContainer.classList.remove("imageselector-show");
    }

    /**
     * Toggle upload area
     * @private
     */
    _toggleUpload() {
        if (this._uploadArea) {
            this._uploadArea.classList.toggle("imageselector-show");
            if (!this._uploadArea.classList.contains("imageselector-show")) {
                this._fileInput.value = "";
                this._progressBar.style.width = "0%";
                this._progressBar.parentElement.classList.remove("imageselector-show");
                this._isUploading = false;
            }
        }
    }

    /**
     * Handle upload
     * @private
     */
    async _handleUpload(file) {
        if (this._isUploading) return;

        if (!file.type.startsWith("image/")) {
            alert("Please upload an image file");
            return;
        }

        // Check allowed types
        if (this.config.type !== "*") {
            const ext = file.name.split(".").pop().toLowerCase();
            const allowed = this.config.type.split("|").map(t => t.trim().toLowerCase());
            if (!allowed.includes(ext)) {
                alert(`Image type not allowed. Allowed: ${this.config.type}`);
                return;
            }
        }

        this._isUploading = true;
        this._fileInput.disabled = true;
        this._addBtn.disabled = true;
        this._progressBar.parentElement.classList.add("imageselector-show");
        this._progressBar.style.width = "1%";
        this._nameProgress.style.display = "";

        try {
            // Simulate upload - replace with actual upload logic
            const formData = new FormData();
            formData.append("image", file);
            formData.append("path", this.config.path);

            // For demo purposes, create a fake image object
            const image = {
                name: file.name,
                size: file.size,
                url: URL.createObjectURL(file),
                path: URL.createObjectURL(file),
                source_dir: this.config.path
            };

            // Simulate progress
            let progress = 0;
            const interval = setInterval(() => {
                progress += 10;
                if (progress <= 100) {
                    this._progressBar.style.width = progress + "%";
                }
                if (progress >= 100) {
                    clearInterval(interval);
                }
            }, 100);

            // Wait for "upload" to complete
            await new Promise(resolve => setTimeout(resolve, 1000));

            clearInterval(interval);
            this._progressBar.style.width = "100%";

            // Add to images
            this._images.unshift(image);
            this._filteredImages.unshift(image);
            this._renderGrid();

            // Close upload area
            this._uploadArea.classList.remove("imageselector-show");
            this._fileInput.value = "";
            this._progressBar.style.width = "0%";
            this._progressBar.parentElement.classList.remove("imageselector-show");

            if (typeof this.config.onUpload === "function") {
                this.config.onUpload(image, this);
            }

        } catch (error) {
            alert("Upload failed: " + error.message);
        }

        this._nameProgress.style.display = "none";
        this._isUploading = false;
        this._fileInput.disabled = false;
        this._addBtn.disabled = false;
    }

    /**
     * Handle delete
     * @private
     */
    async _handleDelete(image, event) {
        event.stopPropagation();

        if (!confirm(`Are you sure you want to delete "${image.name}"?`)) {
            return;
        }

        try {
            // Simulate delete - replace with actual delete logic
            const index = this._images.findIndex(f => f.name === image.name);
            if (index !== -1) {
                this._images.splice(index, 1);
            }

            const filteredIndex = this._filteredImages.findIndex(f => f.name === image.name);
            if (filteredIndex !== -1) {
                this._filteredImages.splice(filteredIndex, 1);
            }

            const selectedIndex = this._selectedImages.findIndex(f => f.name === image.name);
            if (selectedIndex !== -1) {
                this._selectedImages.splice(selectedIndex, 1);
            }

            this._renderGrid();
            this._updateSelectionInfo();

            if (typeof this.config.onDelete === "function") {
                this.config.onDelete(image, this);
            }

            alert("Image deleted successfully");

        } catch (error) {
            alert("Failed to delete image: " + error.message);
        }
    }

    /**
     * Open preview
     * @private
     */
    _openPreview(image) {
        if (!this._previewOverlay) return;

        const img = this._previewOverlay.querySelector("img");
        if (img) {
            img.src = image.url || image.path || "";
        }

        this._previewOverlay.classList.add("imageselector-show");
        document.body.style.overflow = "hidden";
    }

    /**
     * Close preview
     * @private
     */
    _closePreview() {
        if (!this._previewOverlay) return;
        this._previewOverlay.classList.remove("imageselector-show");
        document.body.style.overflow = "";
    }

    /**
     * Filter images
     * @private
     */
    _filterImages(query) {
        const q = query.toLowerCase().trim();
        if (!q) {
            this._filteredImages = [...this._images];
        } else {
            this._filteredImages = this._images.filter(f =>
                f.name.toLowerCase().includes(q)
            );
        }
        this._renderGrid();
    }

    /**
     * Render grid
     * @private
     */
    _renderGrid() {
        this._grid.innerHTML = "";

        if (this._filteredImages.length === 0) {
            const empty = document.createElement("div");
            empty.className = "imageselector-empty";
            empty.innerHTML = `
                <div style="font-size: 48px; margin-bottom: 16px;">🖼️</div>
                <div>No images found</div>
            `;
            this._grid.appendChild(empty);
            return;
        }

        this._filteredImages.forEach(image => {
            const item = document.createElement("div");
            item.className = "imageselector-item";
            item.dataset.filename = image.name;

            const img = document.createElement("img");
            img.src = image.url || image.path || "";
            img.setAttribute("loading", "lazy");
            img.alt = image.name;
            img.onerror = function() {
                this.style.display = "none";
            };

            const actions = document.createElement("div");
            actions.className = "imageselector-item-actions";

            const eyeBtn = document.createElement("button");
            eyeBtn.className = "imageselector-item-eye";
            eyeBtn.innerHTML = "👁";
            eyeBtn.title = "Preview";
            eyeBtn.addEventListener("click", (e) => {
                e.stopPropagation();
                this._openPreview(image);
            });

            const deleteBtn = document.createElement("button");
            deleteBtn.className = "imageselector-item-delete";
            deleteBtn.innerHTML = "✕";
            deleteBtn.title = "Delete";
            deleteBtn.addEventListener("click", (e) => this._handleDelete(image, e));

            actions.appendChild(deleteBtn);
            actions.appendChild(eyeBtn);

            const check = document.createElement("div");
            check.className = "imageselector-item-check";
            check.textContent = "✓";

            const info = document.createElement("div");
            info.className = "imageselector-item-info";

            const name = document.createElement("div");
            name.className = "imageselector-item-name";
            name.textContent = image.name;

            const size = document.createElement("div");
            size.className = "imageselector-item-size";
            size.textContent = image.size ? this._formatSize(image.size) : "";

            info.appendChild(name);
            info.appendChild(size);
            item.appendChild(img);
            item.appendChild(actions);
            item.appendChild(check);
            item.appendChild(info);

            const isSelected = this._selectedImages.some(f => f.name === image.name);
            if (isSelected) {
                item.classList.add("imageselector-selected");
            }

            item.addEventListener("click", () => {
                this._toggleImage(image, item);
            });

            this._grid.appendChild(item);
        });

        this._updateSelectionInfo();
    }

    /**
     * Toggle image selection
     * @private
     */
    _toggleImage(image, item) {
        const isMultiple = this.config.selection === "multiple";

        if (!isMultiple) {
            if (this._selectedImages.length === 1 && this._selectedImages[0].name === image.name) {
                this._selectedImages = [];
                item.classList.remove("imageselector-selected");
            } else {
                this._selectedImages = [];
                document.querySelectorAll(".imageselector-item.imageselector-selected")
                    .forEach(el => el.classList.remove("imageselector-selected"));
                this._selectedImages.push(image);
                item.classList.add("imageselector-selected");
            }
        } else {
            const index = this._selectedImages.findIndex(f => f.name === image.name);
            if (index !== -1) {
                this._selectedImages.splice(index, 1);
                item.classList.remove("imageselector-selected");
            } else {
                this._selectedImages.push(image);
                item.classList.add("imageselector-selected");
            }
        }

        this._updateSelectionInfo();
    }

    /**
     * Update selection info
     * @private
     */
    _updateSelectionInfo() {
        const count = this._selectedImages.length;
        if (this._infoText) {
            this._infoText.textContent = count;
        }
        if (this._selectBtn) {
            this._selectBtn.textContent = count > 0
                ? `Select ${count} image${count > 1 ? "s" : ""}`
                : "Okay";
        }
        this._displayCurrentImage();
    }

    /**
     * Confirm selection
     * @private
     */
    _confirmSelection() {
        if (this._selectedImages.length === 0) {
            this.setValue("");
            if (typeof this.config.onSelect === "function") {
                this.config.onSelect([], this.element);
            }
            this.close();
            return;
        }

        const isMultiple = this.config.selection === "multiple";

        if (isMultiple) {
            const urls = this._selectedImages.map(f => f.url || f.path);
            this.setValue(urls.join("||"));
        } else {
            const image = this._selectedImages[0];
            this.setValue(image.url || image.path || image.name);
        }

        if (typeof this.config.onSelect === "function") {
            this.config.onSelect(this._selectedImages, this.element);
        }

        this.close();
    }

    /**
     * Load images
     * @private
     */
    async _loadImages() {
        try {
            // Simulate fetching images - replace with actual API call
            const response = await fetch(`/api/images?path=${encodeURIComponent(this.config.path)}`);
            const data = await response.json();
            this._images = data.images || [];
        } catch (error) {
            // Use sample images for demo
            this._images = [
                { name: 'sample1.jpg', size: 102400, url: 'https://picsum.photos/200/150', path: 'https://picsum.photos/200/150' },
                { name: 'sample2.jpg', size: 204800, url: 'https://picsum.photos/200/151', path: 'https://picsum.photos/200/151' },
                { name: 'sample3.jpg', size: 307200, url: 'https://picsum.photos/200/152', path: 'https://picsum.photos/200/152' },
            ];
        }

        if (this.config.type && this.config.type !== "*") {
            const allowed = this.config.type.split("|").map(t => t.trim().toLowerCase());
            this._images = this._images.filter(f => {
                const ext = f.extension || f.name.split(".").pop().toLowerCase();
                return allowed.includes(ext);
            });
        }

        this._filteredImages = [...this._images];
        this._renderGrid();
    }

    /**
     * Format file size
     * @private
     */
    _formatSize(bytes) {
        if (bytes === 0) return "0 B";
        const k = 1024;
        const sizes = ["B", "KB", "MB", "GB"];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
    }

    /**
     * Trigger callbacks
     * @private
     */
    _triggerCallbacks() {
        const value = this.getValue();
        this._callbacks.forEach(callback => {
            try {
                callback(value);
            } catch (error) {
                console.error('Error in onChange callback:', error);
            }
        });
    }

    /**
     * Static init method
     */
    static init(element, config = {}) {
        if (typeof element === 'string') {
            const elements = document.querySelectorAll(element);
            if (elements.length > 1) {
                return Array.from(elements).map(el => new ImageSelector(el, config));
            }
            if (elements.length === 0) {
                throw new Error('No elements found with selector: ' + element);
            }
            element = elements[0];
        }

        const instance = new ImageSelector(element, config);
        instance.init();
        return instance;
    }
}

export default ImageSelector;