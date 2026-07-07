import Ctr from "../../code/src/mods/ctr.js";
import CtrDATE from "../../code/src/mods/date.js";
import { Twal } from "../../code/src/mods/twal.js";
import { forRiderOrders } from "../classes/functions/constants.js";
import { getAllOrders, getAllOrdersByRider, updateStatus } from "../classes/functions/orderModel.js";


document.querySelector("#statusFilterSelect").value = 2;

let currentUser = localStorage.getItem("userid");



let ordersData = await getAllOrdersByRider(currentUser);
let currentMap = null;
let currentMarker = null;

let html5QrCode = null;
let isScanning = false;

function getRiderOrders() {
    return ordersData.filter(order => forRiderOrders().includes(order.status));
}

function updateStats() {
    const orders = getRiderOrders();
    const pending = orders.filter(o => o.status === 1).length;
    const outForDelivery = orders.filter(o => o.status === 2).length;
    const completedToday = orders.filter(o => {
        if (o.status !== 3) return false;
        const today = new Date().toDateString();
        const orderDate = new Date(o.updatedAt || o.orderDate).toDateString();
        return orderDate === today;
    }).length;

    document.getElementById('pendingCount').innerText = pending;
    document.getElementById('outForDeliveryCount').innerText = outForDelivery;
    document.getElementById('completedToday').innerText = completedToday;
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function (m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

function formatDate(dateStr) {
    let d = new Date(dateStr);
    return d.toLocaleDateString('en-PH') + " " + d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

function getStatusBadge(status) {
    if (status === 1) return '<span class="status-badge-rider status-pending-delivery"><i class="bi bi-clock"></i> Ready for Pickup</span>';
    if (status === 2) return '<span class="status-badge-rider status-out-for-delivery"><i class="bi bi-truck"></i> Out for Delivery</span>';
    if (status === 3) return '<span class="status-badge-rider status-delivered"><i class="bi bi-check-circle"></i> Delivered</span>';
    if (status === 7) return '<span class="status-badge-rider status-cancelled"><i class="bi bi-x-circle"></i> Cancelled</span>';
    return '<span class="status-badge-rider">Unknown</span>';
}

async function geocodeAddress(address) {
    if (!address) return null;
    
    try {
        const encodedAddress = encodeURIComponent(address + ', Philippines');
        const response = await fetch(`https://nominatim.openstreetmap.org/search?q=${encodedAddress}&format=json&limit=1`, {
            headers: {
                'User-Agent': 'AutoParts Delivery App/1.0'
            }
        });
        const data = await response.json();
        
        if (data && data.length > 0) {
            return {
                lat: parseFloat(data[0].lat),
                lng: parseFloat(data[0].lon),
                displayName: data[0].display_name
            };
        }
        return null;
    } catch (error) {
        console.error('Geocoding error:', error);
        return null;
    }
}

async function initMap(address, savedLat = null, savedLng = null) {
    const mapContainer = document.getElementById('orderMap');
    if (!mapContainer) return;

    if (currentMap) {
        currentMap.remove();
        currentMap = null;
    }

    mapContainer.innerHTML = '<div class="text-center py-5"><i class="bi bi-geo-alt-fill fs-1 text-muted"></i><p class="mt-2">Loading map...</p></div>';
    
    let targetLat = null;
    let targetLng = null;
    let locationName = address;

    if (savedLat && savedLng) {
        targetLat = savedLat;
        targetLng = savedLng;
    } 

    else if (address) {
        const geocodeResult = await geocodeAddress(address);
        if (geocodeResult) {
            targetLat = geocodeResult.lat;
            targetLng = geocodeResult.lng;
            locationName = geocodeResult.displayName;
        }
    }

    if (!targetLat || !targetLng) {
        targetLat = 14.5995;
        targetLng = 120.9842;
        locationName = 'Manila, Philippines (Default location)';
    }

    mapContainer.innerHTML = '';
    currentMap = L.map('orderMap').setView([targetLat, targetLng], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(currentMap);

    currentMarker = L.marker([targetLat, targetLng], {
        draggable: false
    }).addTo(currentMap);
    
    const popupContent = `<b>📍 Delivery Address</b><br>${escapeHtml(address)}<br><br><small class="text-muted">📍 ${targetLat.toFixed(6)}, ${targetLng.toFixed(6)}</small>`;
    currentMarker.bindPopup(popupContent).openPopup();
    
    L.circle([targetLat, targetLng], {
        color: '#00a896',
        fillColor: '#00a896',
        fillOpacity: 0.1,
        radius: 200
    }).addTo(currentMap);
    
    L.control.scale().addTo(currentMap);
    
    setTimeout(() => {
        if (currentMap) {
            currentMap.invalidateSize();
        }
    }, 100);
}

function showOrderDetails(orderId) {
    const order = ordersData.find(o => o.id === orderId);
    if (!order) return;

    let productsHtml = '';
    if (order.items && order.items.length > 0) {
        productsHtml = `
            <div class="products-section mt-3">
                <h6 class="fw-bold mb-2"><i class="bi bi-box-seam text-success"></i> Order Items</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-borderless">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
        `;
        order.items.forEach(item => {
            productsHtml += `
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="${item.image}" class="order-detail-img" alt="" onerror="this.src='https://placehold.co/50x50?text=No+Image'">
                            <span class="fw-medium">${escapeHtml(item.name)}</span>
                        </div>
                    </td>
                    <td class="text-center">${item.qty}</td>
                    <td class="text-end">₱${item.price.toFixed(2)}</td>
                    <td class="text-end fw-bold">₱${(item.qty * item.price).toFixed(2)}</td>
                </tr>
            `;
        });
        productsHtml += `
                        </tbody>
                    </table>
                </div>
            </div>
        `;
    }

    const paymentHtml = `
        <div class="payment-section mt-3">
            <h6 class="fw-bold mb-2"><i class="bi bi-credit-card text-success"></i> Payment Summary</h6>
            <div class="bg-light p-3 rounded-3">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal:</span>
                    <span class="fw-medium">₱${order.subtotal.toFixed(2)}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Shipping Fee:</span>
                    <span class="fw-medium">₱${order.shippingFee.toFixed(2)}</span>
                </div>
                <hr class="my-2">
                <div class="d-flex justify-content-between">
                    <span class="fw-bold">Total Amount:</span>
                    <span class="fw-bold text-success fs-5">₱${order.total.toFixed(2)}</span>
                </div>
            </div>
        </div>
    `;

    const modalBody = document.getElementById('orderDetailsModalBody');
    modalBody.innerHTML = `
        <div class="row g-4">
            <!-- LEFT COLUMN: Customer Details, Products, Payment -->
            <div class="col-md-6">
                <!-- Customer Information Card -->
                <div class="customer-card p-3 bg-light rounded-3">
                    <h6 class="fw-bold mb-3"><i class="bi bi-person-circle text-success"></i> Customer Information</h6>
                    <div class="mb-2">
                        <label class="text-muted small mb-1">Full Name</label>
                        <p class="mb-0 fw-semibold">${escapeHtml(order.customerName)}</p>
                    </div>
                    <div class="mb-2">
                        <label class="text-muted small mb-1">Contact Number</label>
                        <p class="mb-0">${escapeHtml(order.contact)}</p>
                    </div>
                    <div class="mb-2">
                        <label class="text-muted small mb-1">Email Address</label>
                        <p class="mb-0">${escapeHtml(order.email)}</p>
                    </div>
                </div>
                
                <!-- Delivery Address Card -->
                <div class="address-card p-3 bg-light rounded-3 mt-3">
                    <h6 class="fw-bold mb-3"><i class="bi bi-geo-alt text-danger"></i> Delivery Address</h6>
                    <p class="mb-1 fw-semibold">${order.fulladdress ?? order.address ?? "No address provided"}</p>
                    ${order.deliveryInstructions ? `<small class="text-muted"><i class="bi bi-info-circle"></i> Instructions: ${escapeHtml(order.deliveryInstructions)}</small>` : '<small class="text-muted">No delivery instructions</small>'}
                </div>
                
                <!-- Order Items -->
                ${productsHtml}
                
                <!-- Payment Summary -->
                ${paymentHtml}
                
                <!-- Current Status -->
                <div class="status-card mt-3 p-3 rounded-3" style="background: #e8f5e9;">
                    <h6 class="fw-bold mb-2"><i class="bi bi-truck"></i> Current Status</h6>
                    <div>${getStatusBadge(order.status)}</div>
                    ${order.cancelReason ? `<div class="alert alert-danger mt-2 small mb-0"><strong>Cancellation Reason:</strong> ${escapeHtml(order.cancelReason)}</div>` : ''}
                </div>
            </div>
            
            <!-- RIGHT COLUMN: Map -->
            <div class="col-md-6">
                <div class="map-card">
                    <h6 class="fw-bold mb-3"><i class="bi bi-map text-success"></i> Delivery Location Map</h6>
                    <div id="orderMap" style="height: 350px; border-radius: 1rem; margin-bottom: 1rem; background: #f0f2f5;"></div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-secondary flex-grow-1" id="openInGoogleMaps">
                            <i class="bi bi-google"></i> Open in Google Maps
                        </button>
                        <button class="btn btn-sm btn-outline-secondary flex-grow-1" id="openInWaze">
                            <i class="bi bi-geo-alt"></i> Open in Waze
                        </button>
                    </div>
                    <div class="mt-2 small text-muted text-center">
                        <i class="bi bi-info-circle"></i> Click on marker to see full address
                    </div>
                </div>
            </div>
        </div>
    `;

    const modal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
    modal.show();

    setTimeout(async () => {
        await initMap(order.address, order.latitude || null, order.longitude || null);

        const googleBtn = document.getElementById('openInGoogleMaps');
        if (googleBtn) {
            googleBtn.addEventListener('click', () => {
                const address = encodeURIComponent(order.address + ', Philippines');
                window.open(`https://www.google.com/maps/search/${address}`, '_blank');
            });
        }
        
        const wazeBtn = document.getElementById('openInWaze');
        if (wazeBtn) {
            wazeBtn.addEventListener('click', () => {
                const address = encodeURIComponent(order.address + ', Philippines');
                window.open(`https://www.waze.com/ul?q=${address}&navigate=yes`, '_blank');
            });
        }
    }, 300);

    const updateBtn = document.getElementById('updateDeliveryStatusBtn');
    const newUpdateBtn = updateBtn.cloneNode(true);
    updateBtn.parentNode.replaceChild(newUpdateBtn, updateBtn);
    
    newUpdateBtn.onclick = () => {
        bootstrap.Modal.getInstance(document.getElementById('orderDetailsModal')).hide();
        openUpdateStatusModal(order.id, order.status);
    };
}

function renderOrders() {
    const searchVal = document.getElementById('orderSearchInput').value.toLowerCase();
    const statusVal = document.getElementById('statusFilterSelect').value;

    let filtered = getRiderOrders();

    if (searchVal) {
        filtered = filtered.filter(order =>
            (order.customerName && order.customerName.toLowerCase().includes(searchVal)) ||
            (order.email && order.email.toLowerCase().includes(searchVal)) ||
            (order.code && order.code.toString().includes(searchVal)) ||
            (order.address && order.address.toLowerCase().includes(searchVal)) ||
            (order.code && order.code.toLowerCase().includes(searchVal))
        );
    }
    if (statusVal !== 'all') {
        filtered = filtered.filter(order => order.status === parseInt(statusVal));
    }

    const container = document.getElementById('ordersContainer');

    if (filtered.length === 0) {
        container.innerHTML = `
            <div class="text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted"></i>
                <h5 class="mt-3 text-muted">No orders found</h5>
                <p class="text-muted">Try adjusting your search or filter</p>
            </div>`;
        return;
    }

    let html = '<div class="orders-grid">';
    
    filtered.forEach(order => {
        const statusText = getStatusBadge(order.status);
        let stt = document.querySelector("#statusFilterSelect").value;
        
        let del = "";
        let btn = `
                <div class="order-card-footer">
                    <button class="btn btn-rider-primary w-100 view-order-btn" data-order-id="${order.id}">
                        <i class="bi bi-eye"></i> View & Update Delivery
                    </button>
                </div>`;
        if(order.date_delivered && stt == 3){
            del = `
                    <div class="info-row">
                        <span class="info-label"><i class="bi bi-truck"></i> Date Delivered</span>
                        <span class="info-value">${formatDate(order.date_delivered)}</span>
                    </div>`;
            btn = `
                <div class="order-card-footer">
                    <button class="btn btn-rider-primary w-100 view-order-btn" disabled>
                        <i class="bi bi-eye"></i> View & Update Delivery
                    </button>
                </div>`
        }
        html += `
            <div class="order-card">
                <div class="order-card-header d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Order #${order.code || order.id}</small>
                        <h6 class="mb-0 mt-1">${escapeHtml(order.customerName)}</h6>
                    </div>
                    ${statusText}
                </div>
                <div class="order-card-body">
                    <div class="mb-2">
                        <small class="text-muted"><i class="bi bi-geo-alt"></i> Delivery Address</small>
                        <p class="address-text mb-1">${order.fulladdress ?? order.address ?? "No address provided"}</p>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Subtotal</span>
                        <span class="info-value">₱${order.subtotal.toFixed(2)}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Shipping</span>
                        <span class="info-value">₱${order.shippingFee.toFixed(2)}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Total</span>
                        <span class="info-value text-success fw-bold">₱${order.total.toFixed(2)}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="bi bi-calendar"></i> Order Date</span>
                        <span class="info-value">${formatDate(order.orderDate)}</span>
                    </div>
                    ${del}
                </div>
                ${btn}
            </div>
        `;
    });
    html += '</div>';
    container.innerHTML = html;

    document.querySelectorAll('.view-order-btn').forEach(btn => {
        btn.addEventListener('click', () => showOrderDetails(parseInt(btn.dataset.orderId)));
    });

    updateStats();
}

let currentUpdateOrderId = null;

function openUpdateStatusModal(orderId, currentStatus) {
    currentUpdateOrderId = orderId;
    const selectEl = document.getElementById('deliveryStatusSelect');
    if (currentStatus === 1) selectEl.value = '2';
    else selectEl.value = currentStatus;
    new bootstrap.Modal(document.getElementById('updateStatusOptionsModal')).show();
}

document.getElementById('confirmDeliveryUpdate').addEventListener('click', async () => {
    const newStatus = parseInt(document.getElementById('deliveryStatusSelect').value);
    const orderId = currentUpdateOrderId;
    if (!orderId) return;

    if (newStatus === 7) {
        bootstrap.Modal.getInstance(document.getElementById('updateStatusOptionsModal')).hide();
        document.getElementById('cancelOrderId').value = orderId;
        new bootstrap.Modal(document.getElementById('cancellationReasonModal')).show();
    } else {
        await performStatusUpdate(orderId, newStatus, null);
        bootstrap.Modal.getInstance(document.getElementById('updateStatusOptionsModal')).hide();
    }
});

document.getElementById('confirmCancellationBtn').addEventListener('click', async () => {
    const orderId = parseInt(document.getElementById('cancelOrderId').value);
    const reason = document.getElementById('cancellationReasonText').value.trim();
    if (!reason) {
        showToast('Please provide a cancellation reason', 'warning');
        return;
    }
    await performStatusUpdate(orderId, 7, reason);
    bootstrap.Modal.getInstance(document.getElementById('cancellationReasonModal')).hide();
    document.getElementById('cancellationReasonText').value = '';
});

async function performStatusUpdate(orderId, newStatus, cancelReason = null) {
    const result = await updateStatus(orderId, newStatus, cancelReason);
    if (result.code == 200) {
        const orderIndex = ordersData.findIndex(o => o.id === orderId);
        if (orderIndex !== -1) {
            ordersData[orderIndex].status = newStatus;
            if (cancelReason) ordersData[orderIndex].cancelReason = cancelReason;
        }
        const statusNames = { 1: 'Accepted', 2: 'Out for Delivery', 3: 'Delivered', 7: 'Cancelled' };
        Twal.ok(`Order #${orderId} status updated to ${statusNames[newStatus]}`);
        renderOrders();
    } else {
        showToast(result.message || 'Failed to update status', 'danger');
    }
}

function showToast(msg, type = 'success') {
    const toastEl = document.getElementById('statusToast');
    const toastBody = document.getElementById('toastMsg');
    toastBody.innerText = msg;
    toastEl.classList.remove('bg-success', 'bg-danger', 'bg-warning');
    if (type === 'success') toastEl.classList.add('bg-success');
    else if (type === 'danger') toastEl.classList.add('bg-danger');
    else toastEl.classList.add('bg-warning');
    const bsToast = new bootstrap.Toast(toastEl, { delay: 2500 });
    bsToast.show();
}

async function startQrScanner() {
    if (html5QrCode && isScanning) return;
    try {
        html5QrCode = new Html5Qrcode("qr-reader");
        const config = { fps: 10, qrbox: { width: 250, height: 250 } };
        await html5QrCode.start({ facingMode: "environment" }, config,
            (decodedText) => handleQrScanResult(decodedText),
            (errorMessage) => console.log("QR scan error:", errorMessage)
        );
        isScanning = true;
        document.getElementById('startScannerBtn').style.display = 'none';
        document.getElementById('stopScannerBtn').style.display = 'inline-block';
    } catch (err) {
        console.error("Failed to start scanner:", err);
        showToast("Failed to start camera. Please check permissions.", "danger");
    }
}

function stopQrScanner() {
    if (html5QrCode && isScanning) {
        html5QrCode.stop().then(() => {
            isScanning = false;
            document.getElementById('startScannerBtn').style.display = 'inline-block';
            document.getElementById('stopScannerBtn').style.display = 'none';
        }).catch(err => console.error("Failed to stop scanner:", err));
    }
}

function handleQrScanResult(scannedText) {
    stopQrScanner();
    const modal = bootstrap.Modal.getInstance(document.getElementById('qrScannerModal'));
    if (modal) modal.hide();
    document.getElementById('orderSearchInput').value = scannedText;
    showToast(`QR Scanned: ${scannedText}`, 'success');
    renderOrders();
}

const scanBtn = document.getElementById('scanQrBtn');
if (scanBtn) {
    scanBtn.addEventListener('click', () => {
        setTimeout(() => startQrScanner(), 500);
    });
}

const stopBtn = document.getElementById('stopScannerBtn');
if (stopBtn) {
    stopBtn.addEventListener('click', () => stopQrScanner());
}

const startBtn = document.getElementById('startScannerBtn');
if (startBtn) {
    startBtn.addEventListener('click', () => startQrScanner());
}

const qrModal = document.getElementById('qrScannerModal');
if (qrModal) {
    qrModal.addEventListener('hidden.bs.modal', () => stopQrScanner());
}

const searchBtn = document.getElementById('searchButton');
if (searchBtn) {
    searchBtn.addEventListener('click', () => renderOrders());
}

const clearBtn = document.getElementById('clearSearchButton');
if (clearBtn) {
    clearBtn.addEventListener('click', () => {
        document.getElementById('orderSearchInput').value = '';
        renderOrders();
    });
}

const statusFilter = document.getElementById('statusFilterSelect');
if (statusFilter) {
    statusFilter.addEventListener('change', () => renderOrders());
}

const refreshBtn = document.getElementById('refreshOrdersBtn');
if (refreshBtn) {
    refreshBtn.addEventListener('click', async () => {
        ordersData = await getAllOrdersByRider(currentUser);
        renderOrders();
    });
}

const searchInput = document.getElementById('orderSearchInput');
if (searchInput) {
    searchInput.addEventListener('keyup', (e) => {
        if (e.key === 'Enter') renderOrders();
    });
}

const exitBtn = document.getElementById('exitButtonMain');
if (exitBtn) {
    exitBtn.addEventListener('click', () => {
        if (confirm('Exit rider dashboard?')) window.location.href = '/logout';
    });
}

renderOrders();