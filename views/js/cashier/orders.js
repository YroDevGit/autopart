import CtrDATE from "../../code/src/mods/date.js";
import TModal from "../../code/src/mods/modals/tmodal.js";
import Toast from "../../code/src/mods/toast.js";
import { Twal } from "../../code/src/mods/twal.js";
import { orderStatusName } from "../classes/functions/constants.js";
import { getAllOrders, updateDriver, updateStatus } from "../classes/functions/orderModel.js";
import { getRiderName, getRiders } from "../classes/functions/users.js";

let ordersData = await getAllOrders();
let oldStatus = 0;
let html5QrCode = null;
let isScanning = false;
let currentOrderForStatus = null;
let qrCodeInstance = null;

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function (m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

function renderOrdersTable() {
    const searchVal = document.getElementById('orderSearchInput').value.toLowerCase();
    const statusVal = document.getElementById('statusFilterSelect').value;

    let filtered = [...ordersData];

    if (searchVal) {
        filtered = filtered.filter(order =>
            (order.code && order.code.toLowerCase().includes(searchVal)) ||
            (order.customerName && order.customerName.toLowerCase().includes(searchVal)) ||
            (order.email && order.email.toLowerCase().includes(searchVal)) ||
            order.id.toString().includes(searchVal)
        );
    }
    if (statusVal !== 'all') {
        filtered = filtered.filter(order => {
            const statusNum = parseInt(statusVal);
            return order.status === statusNum;
        });
    }

    const tbody = document.getElementById('ordersTableBody');
    document.getElementById('rowCount').innerText = filtered.length;

    if (filtered.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center py-5 text-muted">No orders found</td></tr>';
        return;
    }

    let html = '';
    filtered.forEach(order => {
        let statusClass = orderStatusName(order.status).statusClass;
        let statusText = orderStatusName(order.status).statusText;

        html += `<tr>
      <td class="fw-bold">${order.code || order.id}</td>
      <td>${escapeHtml(order.customerName)}<br><small class="text-muted">${escapeHtml(order.email)}</small></td>
      <td>₱${order.subtotal.toFixed(2)}</td>
      <td>₱${order.shippingFee.toFixed(2)}</td>
      <td class="fw-bold text-warning">₱${order.total.toFixed(2)}</td>
      <td><span class="status-badge ${statusClass}">${statusText}</span></td>
      <td>${CtrDATE.get_name(order.orderDate, "F d, Y h:ia")}</td>
      <td class="btn-action-group">
        <button class="btn btn-sm btn-outline-primary view-items-btn" data-order-id="${order.id}" title="View Details">
          <i class="bi bi-eye"></i> View & Manage
        </button>
      </td>
    </tr>`;
    });
    tbody.innerHTML = html;

    document.querySelectorAll('.view-items-btn').forEach(btn => {
        btn.addEventListener('click', () => showOrderItems(parseInt(btn.dataset.orderId)));
    });
}

function printInvoice(order) {
    const printSection = document.getElementById('printSection');
    
    const transactionCode = order.transaction_code || order.code || order.id;
    
    const invoiceHTML = `
    <div class="print-invoice">
        <div class="header">
            <h3>KYG AUTOPARTS</h3>
            <small>Order #${order.code || order.id} | ${CtrDATE.get_name(order.orderDate, "M d, Y h:ia")}</small>
        </div>
        
        <div class="main-row">
            <div class="left-col">
                <div class="info-line">
                    <span class="label">Customer</span>
                    <span class="value">${escapeHtml(order.customerName)}</span>
                </div>
                <div class="info-line">
                    <span class="label">Email</span>
                    <span class="value">${escapeHtml(order.email)}</span>
                </div>
                <div class="info-line">
                    <span class="label">Contact</span>
                    <span class="value">${escapeHtml(order.contact)}</span>
                </div>
                <div class="info-line">
                    <span class="label">Address</span>
                    <span class="value">${escapeHtml(order.fulladdress ?? order.address)}</span>
                </div>
                <div class="info-line">
                    <span class="label">Status</span>
                    <span class="value">${orderStatusName(order.status).statusText}</span>
                </div>
                
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th class="text-center">Qty</th>
                            <th class="text-right">Price</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${order.items.map(item => `
                            <tr>
                                <td>${escapeHtml(item.name)}</td>
                                <td class="text-center">${item.qty}</td>
                                <td class="text-right">₱${item.price.toFixed(2)}</td>
                                <td class="text-right">₱${(item.qty * item.price).toFixed(2)}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
                
                <div class="totals">
                    <div>Subtotal: ₱${order.subtotal.toFixed(2)}</div>
                    <div>Shipping: ₱${order.shippingFee.toFixed(2)}</div>
                    <div class="grand-total">Total: ₱${order.total.toFixed(2)}</div>
                </div>
            </div>
            
            <div class="right-col">
                <div id="qrCodeContainer"></div>
                <div class="qr-label">Scan to track</div>
                <div class="qr-label" style="font-size:9px;font-weight:bold;margin-top:8px;">${transactionCode}</div>
            </div>
        </div>
        
        <div class="footer">
            Thank you for shopping with AutoParts!
        </div>
    </div>
    `;

    printSection.innerHTML = invoiceHTML;
    printSection.style.display = 'block';

    const qrContainer = printSection.querySelector('#qrCodeContainer');
    if (qrContainer) {
        qrContainer.innerHTML = '';
        qrCodeInstance = new QRCode(qrContainer, {
            text: transactionCode,
            width: 160,
            height: 160,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
    }

    setTimeout(() => {
        window.print();
    }, 300);

    printSection.addEventListener('afterprint', function() {
        printSection.style.display = 'none';
        printSection.innerHTML = '';
    });
}

async function showOrderItems(orderId) {
    const order = ordersData.find(o => o.id === orderId);
    if (!order) return;
    currentOrderForStatus = orderId;

    const statusValues = [
        { value: 0, label: 'Pending' },
        { value: 1, label: 'Accepted' },
        { value: 2, label: 'Out for Delivery' },
        { value: 3, label: 'Delivered' },
        { value: 7, label: 'Rejected' },
        { value: 11, label: 'Walk-In' }
    ];

    const displayStatusMap = {
        0: 'Pending',
        1: 'Accepted',
        2: 'Out for Delivery',
        3: 'Delivered',
        7: 'Rejected',
        11: "Walk-In"
    };

    let statusOptions = '';
    oldStatus = order.status;
    statusValues.forEach(s => {
        const selected = order.status === s.value ? 'selected' : '';
        statusOptions += `<option value="${s.value}" ${selected}>${s.label}</option>`;
    });

    let itemsHtml = `
    <div class="mb-4 p-3 bg-light rounded-3">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-pencil-square text-warning"></i> Update Order Status</h6>
        <button class="btn btn-sm btn-success no-print" id="printOrderBtn">
          <i class="bi bi-printer"></i> Print Invoice
        </button>
      </div>
      <div class="row">
        <div class="col-md-8">
          <label class="form-label fw-semibold">Change status to:</label>
          <select class="form-select" id="newStatusSelect">
            ${statusOptions}
          </select>
        </div>
        <div class="col-md-4 d-flex align-items-end">
          <small class="text-muted">Current: ${displayStatusMap[order.status] || 'Unknown'}</small>
        </div>
      </div>
    </div>
    
    <h6 class="fw-bold mb-3"><i class="bi bi-box-seam text-warning"></i> Order Products</h6>
    <div class="table-responsive">
      <table class="table table-sm table-hover">
        <thead class="table-light">
          <tr>
            <th>Product</th>
            <th>Qty</th>
            <th>Unit Price</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
  `;

    order.items.forEach(item => {
        itemsHtml += `
      <tr>
        <td><div class="d-flex align-items-center gap-2"><img src="${item.image}" class="order-detail-img" alt=""> ${escapeHtml(item.name)}</div></td>
        <td>${item.qty}</td>
        <td>₱${item.price.toFixed(2)}</td>
        <td>₱${(item.qty * item.price).toFixed(2)}</td>
      </tr>
    `;
    });

    const riderName = await getRiderName(order.rider);
    itemsHtml += `
        </tbody>
      </table>
    </div>
    <hr>
    <div class="mt-2">
      <strong>Customer:</strong> ${escapeHtml(order.customerName)} | ${escapeHtml(order.contact)}<br>
      <strong>Address:</strong> ${escapeHtml(order.address)}<br>
      <strong>Rider:</strong> ${riderName ?? "---"}<br>
  `;

    if (order.rejectReason) {
        itemsHtml += `<strong class="text-danger">Rejection Reason:</strong> ${escapeHtml(order.rejectReason)}<br>`;
    }

    itemsHtml += `</div>`;

    document.getElementById('orderItemsModalBody').innerHTML = itemsHtml;
    
    const printBtn = document.getElementById('printOrderBtn');
    if (printBtn) {
        printBtn.addEventListener('click', () => {
            printInvoice(order);
        });
    }
    
    new bootstrap.Modal(document.getElementById('orderItemsModal')).show();
    if(oldStatus == 11){
        document.querySelector("#saveStatusBtn").disabled = true;
        document.querySelector("#newStatusSelect").disabled = true;
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

let riderModal = TModal.init({
    id: "riderModal",
    title: "Select Rider",
    form_id: "riderMdl",
    form: {
        rider : {tag: "select", label: "Select rider", options : await getRiders(), config: {value: "id", label: "fullname", index: "Select Rider"}}
    }
})

document.getElementById('saveStatusBtn').addEventListener('click', () => {
    Twal.ask("Do you want to proceed updating status?").then((click) => {
        if (click.confirm) {
            const newStatus = parseInt(document.getElementById('newStatusSelect').value);
            const orderId = currentOrderForStatus;
            if (!orderId) return;
            if (newStatus === 7) {
                bootstrap.Modal.getInstance(document.getElementById('orderItemsModal')).hide();
                document.getElementById('rejectOrderId').value = orderId;
                updateStatus(orderId, newStatus);
                new bootstrap.Modal(document.getElementById('rejectionModal')).show();
            }else if(newStatus === 2){
                riderModal.show();
                riderModal.form_submit((data)=>{
                    if(! data.rider){
                        Toast.err("Please select rider first.");
                        return;
                    }
                    updateStatus(orderId, newStatus);
                    updateDriver(orderId,data.rider);
                    riderModal.hide();
                    orderStatusUpdated()
                });
            }
             else {
                updateStatus(orderId, newStatus);
                bootstrap.Modal.getInstance(document.getElementById('orderItemsModal')).hide();
                orderStatusUpdated();
            }
            
        }
    });
});

function orderStatusUpdated(){
    Toast.ok("Order status updated.");
    setTimeout(() => location.reload(), 1500);
}

document.getElementById('confirmRejectionBtn').addEventListener('click', () => {
    const orderId = parseInt(document.getElementById('rejectOrderId').value);
    const reason = document.getElementById('rejectionReasonText').value.trim();
    if (!reason) {
        showToast('Please provide a rejection reason', 'warning');
        return;
    }
    bootstrap.Modal.getInstance(document.getElementById('rejectionModal')).hide();
    document.getElementById('rejectionReasonText').value = '';
    orderStatusUpdated();
});

async function startQrScanner() {
    if (html5QrCode && isScanning) {
        return;
    }

    try {
        html5QrCode = new Html5Qrcode("qr-reader");
        const config = { fps: 10, qrbox: { width: 250, height: 250 } };

        await html5QrCode.start(
            { facingMode: "environment" },
            config,
            (decodedText, decodedResult) => {
                //console.log("Scanned:", decodedText);
                handleQrScanResult(decodedText);
            },
            (errorMessage) => {
                //console.log("QR scan error:", errorMessage);
            }
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
        }).catch(err => {
            console.error("Failed to stop scanner:", err);
        });
    }
}

function handleQrScanResult(scannedText) {
    stopQrScanner();
    const modal = bootstrap.Modal.getInstance(document.getElementById('qrScannerModal'));
    if (modal) modal.hide();
    document.getElementById('orderSearchInput').value = scannedText;
    showToast(`QR Scanned: ${scannedText}`, 'success');
    renderOrdersTable();
}

document.getElementById('scanQrBtn').addEventListener('click', () => {
    setTimeout(() => {
        startQrScanner();
    }, 500);
});

document.getElementById('stopScannerBtn').addEventListener('click', () => {
    stopQrScanner();
});

document.getElementById('startScannerBtn').addEventListener('click', () => {
    startQrScanner();
});

document.getElementById('qrScannerModal').addEventListener('hidden.bs.modal', () => {
    stopQrScanner();
});

document.getElementById('searchButton').addEventListener('click', () => renderOrdersTable());
document.getElementById('clearSearchButton').addEventListener('click', () => {
    document.getElementById('orderSearchInput').value = '';
    renderOrdersTable();
});
document.getElementById('applyFilterBtn').addEventListener('click', () => renderOrdersTable());
document.getElementById('clearFilterBtn').addEventListener('click', () => {
    document.getElementById('statusFilterSelect').value = 'all';
    renderOrdersTable();
});
document.getElementById('refreshOrdersBtn').addEventListener('click', () => location.reload());
document.getElementById('orderSearchInput').addEventListener('keyup', (e) => {
    if (e.key === 'Enter') renderOrdersTable();
});

renderOrdersTable();