//Js file for history
import Currency from "../code/src/mods/currency";
import { Twal } from "../code/src/mods/twal";
import { Tyrax } from "../code/src/tyrux/main";
import { orderStatusName } from "./classes/functions/constants";
import { getAllOrdersByCustomer, updateStatus } from "./classes/functions/orderModel";



(async function () {
    let id = localStorage.getItem("userid");

    let orders = await getAllOrdersByCustomer(id);


    

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
            const statusClass = order.status;
            console.log(statusClass);
            let badgeIcon = 'fa-circle';
            if (statusClass == 3) badgeIcon = 'fa-check-circle';
            else if (statusClass == 1) badgeIcon = 'fa-spinner';
            else if (statusClass == 7) badgeIcon = 'fa-circle-xmark';
            else badgeIcon = 'fa-clock';

            html += `
        <div class="order-row" data-index="${index}" data-orderid="${order.id}">
          <div class="order-info">
            <span class="order-id">#${order.code}</span>
            <span class="order-date"><i class="far fa-calendar-alt" style="margin-right: 4px;"></i>${order.date}</span>
            <span class="order-total">${Currency.peso(order.total)}</span>
            <span class="status-badge ${statusClass}"><i class="fas ${badgeIcon}"></i> ${orderStatusName(order.status).statusText}</span>
          </div>
          <span class="view-icon"><i class="fas fa-chevron-right"></i> view</span>
        </div>
      `;
        });

        orderListEl.innerHTML = html;

        document.querySelectorAll('.order-row').forEach(row => {
            row.addEventListener('click', function () {
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
        const cancellable = ['Pending', 'Accepted'];
        const isCancellable = cancellable.includes(orderStatusName(order.status).statusText);
        cancelBtn.disabled = !isCancellable;
        cancelBtn.innerHTML = `<i class="fas fa-ban"></i> Cancel order`;
        cancelBtn.title = isCancellable ? 'Cancel this order' : 'This order cannot be cancelled';
    }

    function populateModal(order) {
        if (!modalBody) return;
        const statusLower = orderStatusName(order.status).statusText;
        let badgeIcon = 'fa-circle';
        if (statusLower == 'Delivered' || statusLower === 'shipped') badgeIcon = 'fa-check-circle';
        else if (statusLower == 'Accepted') badgeIcon = 'fa-spinner';
        else if (statusLower == 'Rejected') badgeIcon = 'fa-circle-xmark';
        else badgeIcon = 'fa-clock';

        // ---- build product list with stars if delivered ----
        let productsHtml = '';
        if (order.products && order.products.length) {
            productsHtml = `<div class="product-list">`;
            order.products.forEach((p, idx) => {
                const currentRating = getProductRating(order.id, p.name);
                // generate stars
                let starsHtml = '';
                const isDelivered = orderStatusName(order.staus) == 'Delivered';
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
            <div class="product-img"><img height='50' width='50' src="${p.image}" alt=""></div>
            <div class="product-details">
              <div class="product-name">${p.name}</div>
              <div class="product-meta">
                <span>Qty: ${p.qty}</span>
                <span class="product-price">${Currency.peso(p.price)}</span>
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
        <span class="detail-value">${order.code}</span>
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
          <span class="modal-status-badge ${statusLower}"><i class="fas ${badgeIcon}"></i> ${orderStatusName(order.status).statusText}</span>
        </span>
      </div>
      <div style="margin-top: 0.2rem;">
        <div style="font-weight: 500; color: #0b1a2b; margin-bottom: 0.4rem; font-size:0.95rem;"><i class="fas fa-list-ul"></i> Products</div>
        ${productsHtml}
      </div>
    `;

        // ---- attach star click events (delegation) ----
        if (orderStatusName(order.status).statusText == 'delivered') {
            const productItems = modalBody.querySelectorAll('.product-item');
            productItems.forEach(item => {
                const stars = item.querySelectorAll('.rating-stars i.fa-star');
                const productName = item.querySelector('.product-name')?.textContent;
                if (!productName) return;
                stars.forEach(star => {
                    star.addEventListener('click', function (e) {
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
        const status = orderStatusName(currentOrder.status).statusText;
        const cancellable = ['Pending', 'Accepted'];
        if (!cancellable.includes(status)) {
            toastEl.textContent = '⚠️ This order cannot be cancelled';
            toastEl.style.display = 'block';
            toastEl.classList.add('show');
            toastEl.style.borderLeftColor = '#dc2626';
            toastEl.style.background = '#fee2e2';
            toastEl.style.color = '#991b1b';
            return;
        }

       Twal.ask(`Do you want to cancel order #${currentOrder.code}?`).then((click)=>{
        if(click.confirm){
            updateStatus(currentOrder.id, 7);

            Twal.ok("Order cancelled", true);
        }
       });
    }

    // ---------- EVENTS ----------
    modalCloseBtn.addEventListener('click', closeModal);
    modalCloseBtn2.addEventListener('click', closeModal);
    modalOverlay.addEventListener('click', function (e) {
        if (e.target === modalOverlay) closeModal();
    });
    cancelBtn.addEventListener('click', handleCancelOrder);
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modalOverlay.classList.contains('active')) closeModal();
    });

    renderOrders();
    toastEl.style.display = 'none';
})();