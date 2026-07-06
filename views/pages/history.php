<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order History · Products + Ratings</title>
  <!-- Font Awesome 6 (Free) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
    }

    body {
      background: #f4f6fa;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 2rem 1rem;
    }

    .card {
      max-width: 1100px;
      width: 100%;
      background: white;
      border-radius: 32px;
      box-shadow: 0 20px 40px -12px rgba(0, 20, 30, 0.25);
      padding: 2rem 2rem 2.5rem;
      transition: 0.2s;
    }

    h1 {
      font-size: 1.9rem;
      font-weight: 600;
      letter-spacing: -0.02em;
      color: #0b1a2b;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      margin-bottom: 1.5rem;
      border-bottom: 2px solid #eef2f6;
      padding-bottom: 1.2rem;
    }

    h1 i {
      color: #2563eb;
      font-size: 1.7rem;
    }

    .order-list {
      display: flex;
      flex-direction: column;
      gap: 0.9rem;
    }

    .order-row {
      background: #f9fafc;
      border-radius: 18px;
      padding: 1rem 1.5rem;
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      border: 1px solid #e9edf2;
      transition: all 0.15s;
      cursor: pointer;
      box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }

    .order-row:hover {
      background: #ffffff;
      border-color: #cbd5e1;
      box-shadow: 0 6px 14px rgba(0, 20, 30, 0.06);
      transform: translateY(-1px);
    }

    .order-info {
      display: flex;
      flex-wrap: wrap;
      align-items: baseline;
      gap: 0.75rem 1.5rem;
    }

    .order-id {
      font-weight: 600;
      color: #0f2a44;
      letter-spacing: -0.01em;
      font-size: 1.05rem;
    }

    .order-date {
      color: #4e5f71;
      font-size: 0.9rem;
      background: #eef2f6;
      padding: 0.2rem 0.8rem;
      border-radius: 30px;
      font-weight: 450;
    }

    .order-total {
      font-weight: 600;
      color: #1b2f44;
      background: #e6edf5;
      padding: 0.2rem 0.9rem;
      border-radius: 30px;
      font-size: 0.9rem;
    }

    .status-badge {
      font-size: 0.75rem;
      font-weight: 600;
      padding: 0.35rem 1rem;
      border-radius: 40px;
      background: #dbeafe;
      color: #1a4a8a;
      text-transform: uppercase;
      letter-spacing: 0.02em;
      display: inline-flex;
      align-items: center;
      gap: 0.3rem;
    }

    .status-badge i { font-size: 0.65rem; }
    .status-badge.shipped { background: #d1fae5; color: #0b6e4f; }
    .status-badge.delivered { background: #d1fae5; color: #0b6e4f; }
    .status-badge.processing { background: #fef3c7; color: #a15813; }
    .status-badge.cancelled { background: #fee2e2; color: #991b1b; }
    .status-badge.pending { background: #e0f2fe; color: #1a4a8a; }

    .view-icon {
      color: #5f7d9c;
      font-size: 0.9rem;
      opacity: 0.7;
      transition: 0.2s;
    }

    .order-row:hover .view-icon {
      opacity: 1;
      color: #2563eb;
    }

    /* ----- MODAL ----- */
    .modal-overlay {
      position: fixed;
      inset: 0;
      background: rgba(10, 20, 35, 0.6);
      backdrop-filter: blur(4px);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 1000;
      visibility: hidden;
      opacity: 0;
      transition: all 0.2s ease;
      padding: 1.5rem;
    }

    .modal-overlay.active {
      visibility: visible;
      opacity: 1;
    }

    .modal-box {
      background: white;
      border-radius: 32px;
      max-width: 640px;
      width: 100%;
      max-height: 90vh;
      overflow-y: auto;
      padding: 2rem 2rem 1.8rem;
      box-shadow: 0 40px 60px -20px rgba(0,0,0,0.4);
      transform: scale(0.96) translateY(8px);
      transition: all 0.2s ease;
    }

    .modal-overlay.active .modal-box {
      transform: scale(1) translateY(0);
    }

    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
      border-bottom: 1px solid #eef2f6;
      padding-bottom: 1rem;
    }

    .modal-header h2 {
      font-size: 1.4rem;
      font-weight: 600;
      color: #0b1a2b;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .modal-header h2 i { color: #2563eb; }

    .modal-close {
      background: #f1f4f9;
      border: none;
      width: 38px;
      height: 38px;
      border-radius: 40px;
      font-size: 1.2rem;
      color: #2b4058;
      cursor: pointer;
      transition: 0.2s;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .modal-close:hover {
      background: #e2e8f0;
      color: #0b1a2b;
    }

    .modal-body {
      display: flex;
      flex-direction: column;
      gap: 1.2rem;
    }

    .detail-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px dashed #e6ecf3;
      padding-bottom: 0.6rem;
    }

    .detail-label {
      color: #4b627c;
      font-weight: 450;
      font-size: 0.95rem;
    }

    .detail-value {
      font-weight: 500;
      color: #0f2a44;
    }

    .detail-value.status {
      display: flex;
      align-items: center;
      gap: 0.4rem;
    }

    .modal-status-badge {
      font-size: 0.8rem;
      font-weight: 600;
      padding: 0.25rem 1rem;
      border-radius: 40px;
      text-transform: uppercase;
    }

    /* product list */
    .product-list {
      margin: 0.5rem 0 0.2rem;
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    .product-item {
      display: flex;
      align-items: center;
      gap: 1rem;
      background: #f9fafc;
      padding: 0.7rem 1rem;
      border-radius: 16px;
      border: 1px solid #eef2f6;
    }

    .product-img {
      width: 52px;
      height: 52px;
      border-radius: 12px;
      object-fit: cover;
      background: #eef2f6;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.6rem;
      color: #7b8da0;
      flex-shrink: 0;
    }

    .product-details {
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 0.2rem;
    }

    .product-name {
      font-weight: 550;
      color: #0b1a2b;
      font-size: 0.95rem;
    }

    .product-meta {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 0.8rem;
      font-size: 0.8rem;
      color: #4b627c;
    }

    .product-meta span {
      background: #eef2f6;
      padding: 0.1rem 0.6rem;
      border-radius: 30px;
    }

    .product-price {
      font-weight: 550;
      color: #0f2a44;
    }

    /* star rating (only for delivered) */
    .rating-stars {
      display: inline-flex;
      align-items: center;
      gap: 0.1rem;
      margin-left: 0.3rem;
    }

    .rating-stars i {
      color: #d1d9e6;
      font-size: 0.9rem;
      cursor: pointer;
      transition: 0.15s;
    }

    .rating-stars i.active {
      color: #fbbf24;
    }

    .rating-stars i:hover {
      transform: scale(1.15);
    }

    .rating-stars i.active:hover {
      color: #f59e0b;
    }

    .rating-label {
      font-size: 0.7rem;
      color: #7b8da0;
      margin-left: 0.2rem;
      font-weight: 450;
    }

    .modal-actions {
      margin-top: 1.2rem;
      display: flex;
      flex-wrap: wrap;
      justify-content: flex-end;
      gap: 0.8rem;
      border-top: 1px solid #eef2f6;
      padding-top: 1.5rem;
    }

    .btn {
      border: none;
      padding: 0.6rem 1.6rem;
      border-radius: 40px;
      font-weight: 550;
      font-size: 0.9rem;
      background: #f1f4f9;
      color: #1e2f40;
      cursor: pointer;
      transition: 0.15s;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
    }

    .btn-primary {
      background: #2563eb;
      color: white;
    }
    .btn-primary:hover { background: #1d4ed8; transform: scale(0.97); }

    .btn-danger {
      background: #dc2626;
      color: white;
    }
    .btn-danger:hover { background: #b91c1c; transform: scale(0.97); }
    .btn-danger:disabled {
      opacity: 0.5;
      pointer-events: none;
      filter: grayscale(0.3);
    }

    .btn-secondary { background: #eef2f6; }
    .btn-secondary:hover { background: #e2e8f0; }

    .toast-message {
      margin-top: 0.8rem;
      padding: 0.5rem 1rem;
      background: #e6f7e6;
      color: #0b6e4f;
      border-radius: 40px;
      font-size: 0.85rem;
      font-weight: 500;
      text-align: center;
      border-left: 4px solid #22a06b;
      display: none;
    }
    .toast-message.show { display: block; }

    .empty-msg {
      text-align: center;
      color: #62748b;
      padding: 2.5rem 0;
    }

    @media (max-width: 600px) {
      .card { padding: 1.2rem; }
      .order-row { flex-direction: column; align-items: stretch; gap: 0.6rem; }
      .order-info { gap: 0.4rem; }
      .modal-box { padding: 1.5rem; }
      .modal-actions { justify-content: center; }
      .product-item { flex-wrap: wrap; }
    }
  </style>
</head>
<body>

<div class="card" id="app">
  <h1><i class="fas fa-clock-rotate-left"></i> Order history</h1>
  <div id="orderListContainer" class="order-list"></div>
</div>

<!-- MODAL -->
<div class="modal-overlay" id="orderModal">
  <div class="modal-box">
    <div class="modal-header">
      <h2><i class="fas fa-receipt"></i> Order details</h2>
      <button class="modal-close" id="modalCloseBtn"><i class="fas fa-xmark"></i></button>
    </div>
    <div class="modal-body" id="modalBody"></div>
    <div class="modal-actions">
      <button class="btn btn-secondary" id="modalCloseBtn2"><i class="fas fa-arrow-left"></i> Back to orders</button>
      <button class="btn btn-danger" id="cancelOrderBtn"><i class="fas fa-ban"></i> Cancel order</button>
    </div>
    <div id="modalToast" class="toast-message">✅ Order cancelled</div>
  </div>
</div>

<script>
  (function() {
    // ---------- MOCK DATA with products & images ----------
    const orders = [
      { 
        id: '#ORD-1004', date: '2026-06-28', total: '$ 184.50', status: 'delivered', 
        products: [
          { name: 'Wireless Headphones Pro', price: 79.99, qty: 1, img: '🎧' },
          { name: 'USB-C Hub 6-in-1', price: 44.50, qty: 2, img: '🔌' },
          { name: 'Screen Protector (2 pack)', price: 19.99, qty: 1, img: '📱' }
        ]
      },
      { 
        id: '#ORD-1003', date: '2026-06-25', total: '$ 67.20', status: 'shipped',
        products: [
          { name: 'Bluetooth Speaker Mini', price: 34.99, qty: 1, img: '🔊' },
          { name: 'Silicone Case', price: 12.99, qty: 2, img: '📦' }
        ]
      },
      { 
        id: '#ORD-1002', date: '2026-06-20', total: '$ 312.00', status: 'processing',
        products: [
          { name: 'Mechanical Keyboard RGB', price: 89.99, qty: 2, img: '⌨️' },
          { name: 'Gaming Mouse', price: 59.99, qty: 1, img: '🖱️' },
          { name: 'Mouse Pad XL', price: 24.99, qty: 2, img: '🧩' }
        ]
      },
      { 
        id: '#ORD-1001', date: '2026-06-15', total: '$ 49.90', status: 'pending',
        products: [
          { name: 'Phone Stand', price: 19.99, qty: 1, img: '📱' },
          { name: 'Cable Organizer', price: 9.99, qty: 3, img: '🔗' }
        ]
      },
      { 
        id: '#ORD-1000', date: '2026-06-10', total: '$ 129.75', status: 'cancelled',
        products: [
          { name: 'Smart Watch Band', price: 29.99, qty: 2, img: '⌚' },
          { name: 'Charging Dock', price: 39.99, qty: 1, img: '⚡' },
          { name: 'Screen Cleaner Kit', price: 14.99, qty: 1, img: '🧴' }
        ]
      }
    ];

    // store ratings per product (key: orderId + productName)
    const ratingsStore = new Map();

    let currentOrder = null;
    const orderListEl = document.getElementById('orderListContainer');
    const modalOverlay = document.getElementById('orderModal');
    const modalBody = document.getElementById('modalBody');
    const modalCloseBtn = document.getElementById('modalCloseBtn');
    const modalCloseBtn2 = document.getElementById('modalCloseBtn2');
    const cancelBtn = document.getElementById('cancelOrderBtn');
    const toastEl = document.getElementById('modalToast');

    // ---------- RENDER ORDER LIST ----------
    function renderOrders() {
      if (!orderListEl) return;
      if (orders.length === 0) {
        orderListEl.innerHTML = `<div class="empty-msg"><i class="fas fa-box-open" style="font-size: 2rem; opacity: 0.4; display: block; margin-bottom: 0.6rem;"></i> No orders found</div>`;
        return;
      }

      let html = '';
      orders.forEach((order, index) => {
        const statusClass = order.status.toLowerCase();
        let badgeIcon = 'fa-circle';
        if (statusClass === 'delivered' || statusClass === 'shipped') badgeIcon = 'fa-check-circle';
        else if (statusClass === 'processing') badgeIcon = 'fa-spinner';
        else if (statusClass === 'cancelled') badgeIcon = 'fa-circle-xmark';
        else badgeIcon = 'fa-clock';

        html += `
          <div class="order-row" data-index="${index}" data-orderid="${order.id}">
            <div class="order-info">
              <span class="order-id">${order.id}</span>
              <span class="order-date"><i class="far fa-calendar-alt" style="margin-right: 4px;"></i>${order.date}</span>
              <span class="order-total">${order.total}</span>
              <span class="status-badge ${statusClass}"><i class="fas ${badgeIcon}"></i> ${order.status}</span>
            </div>
            <span class="view-icon"><i class="fas fa-chevron-right"></i> view</span>
          </div>
        `;
      });

      orderListEl.innerHTML = html;

      document.querySelectorAll('.order-row').forEach(row => {
        row.addEventListener('click', function() {
          const idx = this.dataset.index;
          if (idx !== undefined && orders[idx]) {
            openModal(orders[idx]);
          }
        });
      });
    }

    // ---------- RATING HELPERS ----------
    function getRatingKey(orderId, productName) {
      return `${orderId}::${productName}`;
    }

    function getProductRating(orderId, productName) {
      const key = getRatingKey(orderId, productName);
      return ratingsStore.get(key) || 0;
    }

    function setProductRating(orderId, productName, rating) {
      const key = getRatingKey(orderId, productName);
      ratingsStore.set(key, rating);
      // persist in order products (optional but we keep in store)
    }

    // ---------- MODAL ----------
    function openModal(order) {
      if (!order) return;
      currentOrder = order;
      populateModal(order);
      modalOverlay.classList.add('active');
      toastEl.classList.remove('show');
      toastEl.style.display = 'none';

      // cancel button state
      const cancellable = ['pending', 'processing', 'shipped'];
      const isCancellable = cancellable.includes(order.status.toLowerCase());
      cancelBtn.disabled = !isCancellable;
      cancelBtn.innerHTML = `<i class="fas fa-ban"></i> Cancel order`;
      cancelBtn.title = isCancellable ? 'Cancel this order' : 'This order cannot be cancelled';
    }

    function populateModal(order) {
      if (!modalBody) return;
      const statusLower = order.status.toLowerCase();
      let badgeIcon = 'fa-circle';
      if (statusLower === 'delivered' || statusLower === 'shipped') badgeIcon = 'fa-check-circle';
      else if (statusLower === 'processing') badgeIcon = 'fa-spinner';
      else if (statusLower === 'cancelled') badgeIcon = 'fa-circle-xmark';
      else badgeIcon = 'fa-clock';

      // ---- build product list with stars if delivered ----
      let productsHtml = '';
      if (order.products && order.products.length) {
        productsHtml = `<div class="product-list">`;
        order.products.forEach((p, idx) => {
          const currentRating = getProductRating(order.id, p.name);
          // generate stars
          let starsHtml = '';
          const isDelivered = order.status.toLowerCase() === 'delivered';
          if (isDelivered) {
            for (let i = 1; i <= 5; i++) {
              const active = i <= currentRating ? 'active' : '';
              starsHtml += `<i class="fas fa-star ${active}" data-star="${i}" data-product="${p.name}" data-orderid="${order.id}" style="cursor:pointer;"></i>`;
            }
            starsHtml += `<span class="rating-label">${currentRating > 0 ? currentRating + '★' : 'rate'}</span>`;
          } else {
            starsHtml = `<span style="color:#9aaebf; font-size:0.8rem;">—</span>`;
          }

          productsHtml += `
            <div class="product-item">
              <div class="product-img">${p.img || '📦'}</div>
              <div class="product-details">
                <div class="product-name">${p.name}</div>
                <div class="product-meta">
                  <span>Qty: ${p.qty}</span>
                  <span class="product-price">$${p.price.toFixed(2)}</span>
                  ${isDelivered ? `<span class="rating-stars" style="display:inline-flex; align-items:center;">${starsHtml}</span>` : ''}
                </div>
              </div>
            </div>
          `;
        });
        productsHtml += `</div>`;
      } else {
        productsHtml = `<div style="color:#7b8da0; font-size:0.9rem; padding:0.3rem 0;"><i class="fas fa-box-open"></i> No products</div>`;
      }

      // order details + product list
      modalBody.innerHTML = `
        <div class="detail-row">
          <span class="detail-label"><i class="far fa-hashtag"></i> Order ID</span>
          <span class="detail-value">${order.id}</span>
        </div>
        <div class="detail-row">
          <span class="detail-label"><i class="far fa-calendar-alt"></i> Date</span>
          <span class="detail-value">${order.date}</span>
        </div>
        <div class="detail-row">
          <span class="detail-label"><i class="fas fa-tag"></i> Total</span>
          <span class="detail-value">${order.total}</span>
        </div>
        <div class="detail-row" style="border-bottom: none; padding-bottom: 0;">
          <span class="detail-label"><i class="fas fa-truck"></i> Status</span>
          <span class="detail-value status">
            <span class="modal-status-badge ${statusLower}"><i class="fas ${badgeIcon}"></i> ${order.status}</span>
          </span>
        </div>
        <div style="margin-top: 0.2rem;">
          <div style="font-weight: 500; color: #0b1a2b; margin-bottom: 0.4rem; font-size:0.95rem;"><i class="fas fa-list-ul"></i> Products</div>
          ${productsHtml}
        </div>
      `;

      // ---- attach star click events (delegation) ----
      if (order.status.toLowerCase() === 'delivered') {
        const productItems = modalBody.querySelectorAll('.product-item');
        productItems.forEach(item => {
          const stars = item.querySelectorAll('.rating-stars i.fa-star');
          const productName = item.querySelector('.product-name')?.textContent;
          if (!productName) return;
          stars.forEach(star => {
            star.addEventListener('click', function(e) {
              e.stopPropagation();
              const rating = parseInt(this.dataset.star, 10);
              const orderId = this.dataset.orderid;
              const pName = this.dataset.product;
              if (orderId && pName) {
                setProductRating(orderId, pName, rating);
                // re-render modal to reflect updated stars
                populateModal(currentOrder);
                // show small toast (optional)
                toastEl.textContent = `⭐ Rated ${rating}★ for ${pName}`;
                toastEl.style.display = 'block';
                toastEl.classList.add('show');
                toastEl.style.borderLeftColor = '#fbbf24';
                toastEl.style.background = '#fffbeb';
                toastEl.style.color = '#92400e';
                setTimeout(() => {
                  toastEl.classList.remove('show');
                  toastEl.style.display = 'none';
                }, 2000);
              }
            });
          });
        });
      }
    }

    function closeModal() {
      modalOverlay.classList.remove('active');
      currentOrder = null;
      toastEl.classList.remove('show');
      toastEl.style.display = 'none';
    }

    // ---------- CANCEL ----------
    function handleCancelOrder() {
      if (!currentOrder) return;
      const status = currentOrder.status.toLowerCase();
      const cancellable = ['pending', 'processing', 'shipped'];
      if (!cancellable.includes(status)) {
        toastEl.textContent = '⚠️ This order cannot be cancelled';
        toastEl.style.display = 'block';
        toastEl.classList.add('show');
        toastEl.style.borderLeftColor = '#dc2626';
        toastEl.style.background = '#fee2e2';
        toastEl.style.color = '#991b1b';
        return;
      }

      // update status
      currentOrder.status = 'cancelled';
      const idx = orders.findIndex(o => o.id === currentOrder.id);
      if (idx !== -1) orders[idx].status = 'cancelled';

      renderOrders();
      populateModal(currentOrder);
      cancelBtn.disabled = true;
      cancelBtn.title = 'Order already cancelled';

      toastEl.textContent = '✅ Order cancelled successfully';
      toastEl.style.display = 'block';
      toastEl.classList.add('show');
      toastEl.style.borderLeftColor = '#22a06b';
      toastEl.style.background = '#e6f7e6';
      toastEl.style.color = '#0b6e4f';
    }

    // ---------- EVENTS ----------
    modalCloseBtn.addEventListener('click', closeModal);
    modalCloseBtn2.addEventListener('click', closeModal);
    modalOverlay.addEventListener('click', function(e) {
      if (e.target === modalOverlay) closeModal();
    });
    cancelBtn.addEventListener('click', handleCancelOrder);
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && modalOverlay.classList.contains('active')) closeModal();
    });

    renderOrders();
    toastEl.style.display = 'none';
  })();
</script>
</body>
</html>