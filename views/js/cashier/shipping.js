import Ctr from "../../code/src/mods/ctr.js";
import { Twal } from "../../code/src/mods/twal.js";
import { Tyrax } from "../../code/src/tyrux/main.js";
import { tbl_address, tbl_category } from "../classes/db/tables.js";
import { NegrosCode, getAllAddress } from "../classes/functions/address.js";
import { getBaranggay, getMunicipality } from "../classes/functions/addresses.js";

let cities = await getMunicipality(NegrosCode());

Ctr.setOptions("#city", cities, {
    value: "code", label: "name", onChange: async (element) => {
        let cityCode = element.value;
        showbrgys(cityCode);
    }
});

async function showbrgys(cityCode) {
    Loading.load(true);
    let bgrys = await getBaranggay(cityCode);
    Ctr.setOptions("#brgy", bgrys, { value: "code", label: "name" });
    Loading.load(false);
}


// Mock data for demo - replace with actual backend data
let shippingAddresses = await getAllAddress();
console.log(shippingAddresses);

function formatDate(dateStr) {
    let d = new Date(dateStr);
    return d.toLocaleDateString('en-PH') + " " + d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
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

function showToast(msg, type = 'success') {
    const toastEl = document.getElementById('actionToast');
    const toastMsg = document.getElementById('toastMsg');
    toastMsg.innerText = msg;
    toastEl.classList.remove('bg-success', 'bg-danger', 'bg-warning');
    if (type === 'success') toastEl.classList.add('bg-success');
    else if (type === 'danger') toastEl.classList.add('bg-danger');
    else toastEl.classList.add('bg-warning');
    const bsToast = new bootstrap.Toast(toastEl, { delay: 2000 });
    bsToast.show();
}

function renderAddresses() {
    const searchVal = document.getElementById('addressSearchInput').value.toLowerCase();

    let filtered = [...shippingAddresses];

    if (searchVal) {
        filtered = filtered.filter(address =>
            address.brgy_code.toLowerCase().includes(searchVal) ||
            address.city_code.toLowerCase().includes(searchVal) ||
            address.city.toLowerCase().includes(searchVal) ||
            address.brgy.toLowerCase().includes(searchVal)
        );
    }

    const container = document.getElementById('addressesContainer');
    document.getElementById('addressCount').innerText = filtered.length;

    if (filtered.length === 0) {
        container.innerHTML = `
            <div class="empty-address">
                <i class="bi bi-geo-alt fs-1 text-muted"></i>
                <h5 class="mt-3 text-muted">No shipping addresses found</h5>
                <p class="text-muted">Click the "Add Shipping Address" button to create one</p>
            </div>`;
        return;
    }

    let html = '<div class="shipping-grid">';
    filtered.forEach(address => {
        html += `
            <div class="address-card" data-address-id="${address.id}">
                <div class="address-card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 fw-bold">${address.city} ${address.brgy}</h6>
                    </div>
                    <span class="address-type-badge"><i class="bi bi-truck"></i> Shipping</span>
                </div>
                <div class="address-card-body">
                    <div class="mb-2">
                        <small class="text-muted"><i class="bi bi-building"></i> Full Address</small>
                        <p class="mb-1 small">${address.city} ${address.brgy}</p>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6">
                            <small class="text-muted"><i class="bi bi-geo"></i> Barangay</small>
                            <p class="mb-0 small fw-medium">${address.brgy}</p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted"><i class="bi bi-map"></i> City</small>
                            <p class="mb-0 small fw-medium">${address.city}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted"><i class="bi bi-cash"></i> Shipping Fee</small>
                            <p class="price-tag mb-0">₱${address.shipping.toFixed(2)}</p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted"><i class="bi bi-clock"></i> Est. Delivery</small>
                            <p class="mb-0 small">${address.estimated || 'N/A'}</p>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted"><i class="bi bi-calendar3"></i> Added: ${formatDate(address.created_at)}</small>
                    </div>
                </div>
                <div class="address-card-footer">
                    <button class="btn btn-sm btn-outline-primary edit-address-btn" data-id="${address.id}">
                        <i class="bi bi-pencil"></i> Edit
                    </button>
                    <button class="btn btn-sm btn-outline-danger delete-address-btn" data-id="${address.id}">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                </div>
            </div>
        `;
    });
    html += '</div>';
    container.innerHTML = html;

    // Add event listeners for edit and delete buttons
    document.querySelectorAll('.edit-address-btn').forEach(btn => {
        btn.addEventListener('click', () => openEditModal(parseInt(btn.dataset.id)));
    });
    document.querySelectorAll('.delete-address-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            let id = btn.getAttribute("data-id");
            Twal.ask("Do you want to proceed deleting address").then((click) => {
                if (click.confirm) {
                    Tyrax.update({
                        table: tbl_address(),
                        where: { id: id },
                        update: {active: 0},
                        response: (send) => {
                            if (send.code == 200) {
                                Twal.ok("Address has been deleted", true);
                            } else {
                                Twal.err(send.message);
                            }
                        }
                    });
                }
            })
        });
    });
}

function openEditModal(addressId) {
    const address = shippingAddresses.find(a => a.id === addressId);
    if (!address) return;

    document.getElementById('editAddressId').value = address.id;
    document.getElementById('editAddressLocation').value = address.location;
    document.getElementById('editAddressFull').value = address.full_address;
    document.getElementById('editAddressCity').value = address.city;
    document.getElementById('editAddressProvince').value = address.province;
    document.getElementById('editAddressFee').value = address.fee;
    document.getElementById('editAddressDays').value = address.estimated_days || '';
    document.getElementById('editAddressDefault').checked = address.is_default || false;

    new bootstrap.Modal(document.getElementById('editAddressModal')).show();
}

function openDeleteModal(addressId) {
    document.getElementById('deleteAddressId').value = addressId;
    new bootstrap.Modal(document.getElementById('deleteAddressModal')).show();
}

Ctr.submit("#addAddressForm", (data, form) => {
    form = {
        ...form,
        city: Ctr.get_selected("#city", "label"), brgy: Ctr.get_selected("#brgy", "label"), brgy_code: Ctr.get_selected("#brgy", "value"), city_code: Ctr.get_selected("#city", "value")
    };
    Tyrax.insert({
        table: tbl_address(),
        data: form,
        response: (send) => {
            if (send.code == 200) {
                Twal.ok("Shipping address has been added", true);
            } else {
                Twal.err(send.message);
            }
        }
    })
});

// Edit Address Form Submit
document.getElementById('editAddressForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const addressId = parseInt(document.getElementById('editAddressId').value);
    const formData = {
        id: addressId,
        location: document.getElementById('editAddressLocation').value,
        full_address: document.getElementById('editAddressFull').value,
        city: document.getElementById('editAddressCity').value,
        province: document.getElementById('editAddressProvince').value,
        fee: parseFloat(document.getElementById('editAddressFee').value),
        estimated_days: document.getElementById('editAddressDays').value,
        is_default: document.getElementById('editAddressDefault').checked
    };

    // TODO: Implement your actual update logic here
    /*
    try {
        const response = await fetch('/api/shipping-address/' + addressId, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });
        const result = await response.json();
        
        if (result.success) {
            const index = shippingAddresses.findIndex(a => a.id === addressId);
            if (index !== -1) {
                shippingAddresses[index] = { ...shippingAddresses[index], ...formData };
            }
            renderAddresses();
            bootstrap.Modal.getInstance(document.getElementById('editAddressModal')).hide();
            showToast('Shipping address updated successfully!', 'success');
        } else {
            showToast(result.message || 'Failed to update address', 'danger');
        }
    } catch (error) {
        console.error('Update error:', error);
        showToast('Failed to update address. Please try again.', 'danger');
    }
    */

    // Temporary demo - remove this and implement above
    const index = shippingAddresses.findIndex(a => a.id === addressId);
    if (index !== -1) {
        // If this is set as default, remove default from others
        if (formData.is_default) {
            shippingAddresses.forEach(a => a.is_default = false);
        }
        shippingAddresses[index] = { ...shippingAddresses[index], ...formData };
    }
    renderAddresses();
    bootstrap.Modal.getInstance(document.getElementById('editAddressModal')).hide();
    showToast('Shipping address updated successfully! (Demo)', 'success');
});

// Delete Address
document.getElementById('confirmDeleteBtn').addEventListener('click', async function () {
    const addressId = parseInt(document.getElementById('deleteAddressId').value);

    // TODO: Implement your actual delete logic here
    /*
    try {
        const response = await fetch('/api/shipping-address/' + addressId, {
            method: 'DELETE'
        });
        const result = await response.json();
        
        if (result.success) {
            shippingAddresses = shippingAddresses.filter(a => a.id !== addressId);
            renderAddresses();
            bootstrap.Modal.getInstance(document.getElementById('deleteAddressModal')).hide();
            showToast('Shipping address deleted successfully!', 'success');
        } else {
            showToast(result.message || 'Failed to delete address', 'danger');
        }
    } catch (error) {
        console.error('Delete error:', error);
        showToast('Failed to delete address. Please try again.', 'danger');
    }
    */

    // Temporary demo - remove this and implement above
    shippingAddresses = shippingAddresses.filter(a => a.id !== addressId);
    renderAddresses();
    bootstrap.Modal.getInstance(document.getElementById('deleteAddressModal')).hide();
    showToast('Shipping address deleted successfully! (Demo)', 'success');
});

// Search functionality
document.getElementById('searchButton').addEventListener('click', () => renderAddresses());
document.getElementById('clearSearchButton').addEventListener('click', () => {
    document.getElementById('addressSearchInput').value = '';
    renderAddresses();
});
document.getElementById('addressSearchInput').addEventListener('keyup', (e) => {
    if (e.key === 'Enter') renderAddresses();
});

// Initial render
renderAddresses();