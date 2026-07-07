//Js file for rider/history

import Ctr from "../../code/src/mods/ctr";
import Currency from "../../code/src/mods/currency";
import { orderStatusName } from "../classes/functions/constants";
import { getAllDeliveredByRider } from "../classes/functions/orderModel";

(async function() {


    if(! localStorage.getItem('userid')){
        location.href = "/logout";
    }

    
   
    let del = await getAllDeliveredByRider(localStorage.getItem("userid"));
    let productData = [];
    del.forEach(col => {
        productData[col.code] = [...col.items];
        col.fulladdress = col.fulladdress ?? "-";
        let stat = "bi-check-circle text-primary";
        let statClass = "status-delivered";
        if(col.status == 7){
            statClass = "status-cancelled"
            stat = "bi-x-circle text-danger";
        }
        Ctr.add_html("#historyTableBody", `
        <tr data-order-id="${col.code}" data-status="delivered">
            <td><strong>#${col.code}</strong></td>
            <td>${col.customerName}</td>
            <td><span class="address-truncate" title="${col.fulladdress}">${col.fulladdress}</span></td>
            <td>${col.date_delivered}</td>
            <td>3</td>
            <td class="text-success fw-bold">${Currency.peso(col.total)}</td>
            <td>
                <span class="status-badge-history ${statClass}">
                    <i class="bi ${stat}"></i> ${orderStatusName(col.status).statusText}
                </span>
            </td>
            <td class="text-center">
                <button class="btn btn-sm btn-outline-rider view-history-detail" data-id="${col.code}">
                    <i class="bi bi-eye"></i>
                </button>
            </td>
        </tr>`)
    });
    // ===== SIDEBAR TOGGLE =====
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    const menuToggle = document.getElementById('menuToggle');

    function toggleSidebar() {
        const isOpen = sidebar.classList.contains('open');
        if (isOpen) {
            sidebar.classList.remove('open');
            backdrop.classList.remove('show');
            document.body.style.overflow = '';
        } else {
            sidebar.classList.add('open');
            backdrop.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    }
    function closeSidebar() {
        sidebar.classList.remove('open');
        backdrop.classList.remove('show');
        document.body.style.overflow = '';
    }
    menuToggle.addEventListener('click', toggleSidebar);
    backdrop.addEventListener('click', closeSidebar);
    window.addEventListener('resize', function() {
        if (window.innerWidth > 992) closeSidebar();
    });

    

    // ===== HISTORY FILTER & SEARCH =====
    const searchInput = document.getElementById('historySearchInput');
    const searchBtn = document.getElementById('searchHistoryBtn');
    const clearSearchBtn = document.getElementById('clearHistorySearchBtn');
    const statusFilter = document.getElementById('historyStatusFilter');
    const clearFiltersBtn = document.getElementById('clearHistoryFiltersBtn');
    const tbody = document.getElementById('historyTableBody');
    const emptyMsg = document.getElementById('emptyHistoryMessage');
    const historyCount = document.getElementById('historyCount');

    function filterHistory() {
        const query = searchInput.value.toLowerCase().trim();
        const status = statusFilter.value;
        const rows = tbody.querySelectorAll('tr');
        let visibleCount = 0;

        rows.forEach(row => {
            const rowStatus = row.dataset.status || '';
            const orderId = row.dataset.orderId || '';
            const textContent = row.textContent.toLowerCase();
            const matchesSearch = !query || textContent.includes(query);
            const matchesStatus = status === 'all' || rowStatus === status;
            const visible = matchesSearch && matchesStatus;
            row.style.display = visible ? '' : 'none';
            if (visible) visibleCount++;
        });

        emptyMsg.classList.toggle('d-none', visibleCount > 0);
        historyCount.textContent = visibleCount;
    }

    searchBtn.addEventListener('click', filterHistory);
    searchInput.addEventListener('keyup', function(e) {
        if (e.key === 'Enter') filterHistory();
    });
    clearSearchBtn.addEventListener('click', function() {
        searchInput.value = '';
        filterHistory();
    });
    statusFilter.addEventListener('change', filterHistory);
    clearFiltersBtn.addEventListener('click', function() {
        searchInput.value = '';
        statusFilter.value = 'all';
        filterHistory();
    });

    // ===== VIEW DETAILS (modal with product list) =====
    const detailModal = new bootstrap.Modal(document.getElementById('historyDetailModal'));
    const detailBody = document.getElementById('historyDetailBody');

    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.view-history-detail');
        if (!btn) return;
        e.preventDefault();
        const orderId = btn.dataset.id || 'N/A';
        const row = document.querySelector(`#historyTableBody tr[data-order-id="${orderId}"]`);
        if (!row) {
            detailBody.innerHTML = `<div class="alert alert-warning">Order not found.</div>`;
            detailModal.show();
            return;
        }
        // Extract data from row cells
        const cells = row.querySelectorAll('td');
        if (cells.length < 7) {
            detailBody.innerHTML = `<div class="alert alert-danger">Incomplete data.</div>`;
            detailModal.show();
            return;
        }
        const customer = cells[1]?.textContent?.trim() || 'N/A';
        const address = cells[2]?.querySelector('.address-truncate')?.textContent?.trim() || cells[1]?.textContent?.trim() || 'N/A';
        const date = cells[3]?.textContent?.trim() || 'N/A';
        const total = cells[5]?.textContent || 'N/A';
        const statusEl = cells[6]?.querySelector('.status-badge-history');
        const status = statusEl ? statusEl.textContent.trim() : 'unknown';
        const statusClean = status.replace(/[✓✗]/g, '').trim();
        const statusIcon = statusClean.toLowerCase() === 'delivered' ? 'check-circle' : 'x-circle';
        const statusColor = statusClean.toLowerCase() === 'delivered' ? 'success' : 'danger';

        // Get product list
        const products = [...productData[orderId]] || [];
        let productHtml = '';
        if (products.length > 0) {
            productHtml = `<div class="mt-3"><h6 class="text-success"><i class="bi bi-box-seam"></i> Products Delivered</h6><div class="border rounded p-2 bg-light">`;
            products.forEach(p => {
                productHtml += `
                    <div class="product-list-item">
                        <span class="product-name"><img height='40' width='40' src='${p.image}'> ${p.name}</span>
                        <span class="product-meta">${p.qty} x ${p.price}</span>
                    </div>
                `;
            });
            
            productHtml += `</div></div>`;
        } else {
            productHtml = `<div class="mt-3 text-muted small"><i class="bi bi-info-circle"></i> No product details available for this order.</div>`;
        }

        detailBody.innerHTML = `
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <h6 class="text-success"><i class="bi bi-receipt"></i> Order #${orderId}</h6>
                        <hr>
                        <div class="info-row d-flex justify-content-between py-1 border-bottom"><span class="text-muted">Customer</span><span class="fw-semibold">${customer}</span></div>
                        <div class="info-row d-flex justify-content-between py-1 border-bottom"><span class="text-muted">Address</span><span class="fw-semibold">${address}</span></div>
                        <div class="info-row d-flex justify-content-between py-1"><span class="text-muted">Date</span><span class="fw-semibold">${date}</span></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <h6 class="text-success"><i class="bi bi-box-seam"></i> Delivery Summary</h6>
                        <hr>
                        <div class="info-row d-flex justify-content-between py-1 border-bottom"><span class="text-muted">Total</span><span class="fw-semibold text-success">${total}</span></div>
                        <div class="info-row d-flex justify-content-between py-1"><span class="text-muted">Status</span>
                            <span class="badge bg-${statusColor}"><i class="bi bi-${statusIcon}"></i> ${statusClean.toUpperCase()}</span>
                        </div>
                    </div>
                </div>
            </div>
            ${productHtml}
            <div class="mt-3 text-center text-muted small">
                <i class="bi bi-clock-history"></i> This delivery is part of your history.
            </div>
        `;
        detailModal.show();
    });

    // ===== LOGOUT (dummy) =====
    document.getElementById('exitButtonMain').addEventListener('click', function() {
        if (confirm('Logout from rider dashboard?')) {
            window.location.href = 'logout';
        }
    });

    // initial filter
    filterHistory();
})();