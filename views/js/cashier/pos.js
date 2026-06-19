import Ctr from "../../code/src/mods/ctr.js";
import Currency from "../../code/src/mods/currency.js";
import CtrDATE from "../../code/src/mods/date.js";
import Toast from "../../code/src/mods/toast.js";
import { Twal } from "../../code/src/mods/twal.js";
import { Tyrax } from "../../code/src/tyrux/main.js";
import { tbl_inventory, tbl_product } from "../classes/db/tables.js";
import { displayCategoryOnCB } from "../classes/functions/category.js";
import { getProducts, getStocks } from "../classes/functions/products.js";

let cart = [];
let products = [];
let selectedCategory = '';

await displayCategoryOnCB("#categoryFilter");

async function loadProducts(search = '', category = '') {
    try {
        let prods = await getProducts(search);
        let pr = [];

        for (let p in prods) {
            let row = prods[p];
            row['stock'] = await getStocks(row.id);
            if (row.stock > 0) {
                pr.push(row);
            }
        }

        if (category) {
            pr = pr.filter(p => p.category_id == category || p.category == category);
        }

        products = pr;
        renderProductGrid(products);
    } catch (error) {
        console.error('Error loading products:', error);
        Toast.error({ text: 'Failed to load products' });
    }
}

function renderProductGrid(prods = []) {
    const grid = document.getElementById('productGrid');
    if (!grid) return;

    grid.innerHTML = '';

    if (prods.length === 0) {
        grid.innerHTML = `
      <div class="col-12">
        <div class="text-center py-5 text-muted">
          <i class="bi bi-box-seam display-3 d-block mb-3"></i>
          <p>No products available</p>
        </div>
      </div>
    `;
        document.getElementById('posRowCount').innerText = '0';
        return;
    }

    prods.forEach(prod => {
        const col = document.createElement('div');
        col.className = 'col-6 col-md-4';
        const inCart = cart.find(item => item.id === prod.id);
        const cartQty = inCart ? inCart.quantity : 0;

        col.innerHTML = `
      <div class="product-card text-center p-3 border rounded-3 h-100 ${prod.stock <= 0 ? 'out-of-stock' : ''}" 
           data-id="${prod.id}" data-stock="${prod.stock}">
        <img src="${prod.image || 'https://via.placeholder.com/80?text=Part'}" 
             class="product-img mb-2" alt="${prod.name}" 
             style="width: 80px; height: 80px; object-fit: contain;"
             onerror="this.src='https://via.placeholder.com/80?text=No+Image'">
        <h6 class="fw-bold mb-1 text-truncate" title="${prod.name}">${prod.name}</h6>
        <span class="badge bg-secondary bg-opacity-25 text-dark px-2 py-1 mb-2">${prod.category}</span>
        <div class="fw-bold text-success">${Currency.peso(prod.price.toFixed(2))}</div>
        <small class="text-muted d-block">Stock: ${prod.stock}</small>
        <button class="btn btn-sm btn-autoparts-primary mt-2 add-to-cart-btn w-100" 
                data-id="${prod.id}" ${prod.stock <= 0 ? 'disabled' : ''}>
          <i class="bi bi-cart-plus"></i> ${inCart ? `Add More (${cartQty})` : 'Add to Cart'}
        </button>
      </div>
    `;
        grid.appendChild(col);
    });

    document.getElementById('posRowCount').innerText = prods.length;

    document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const id = parseInt(this.dataset.id);
            addToCart(id);
        });
    });

    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('click', function () {
            const id = parseInt(this.dataset.id);
            if (!this.classList.contains('out-of-stock')) {
                addToCart(id);
            }
        });
    });
}

function addToCart(productId) {
    const product = products.find(p => p.id === productId);
    if (!product) return;

    const existingItem = cart.find(item => item.id === productId);

    if (existingItem) {
        if (existingItem.quantity < product.stock) {
            existingItem.quantity++;
            Toast.info({ text: `Added another ${product.name} to cart` });
        } else {
            Toast.error({ text: 'Not enough stock available' });
            return;
        }
    } else {
        cart.push({
            id: product.id,
            name: product.name,
            price: product.price,
            quantity: 1,
            maxStock: product.stock,
            image: product.image
        });
        Toast.ok({ text: `${product.name} added to cart` });
    }

    updateCartUI();
    updateProductGrid();
}

function removeFromCart(productId) {
    const item = cart.find(i => i.id === productId);
    if (item) {
        Toast.info({ text: `${item.name} removed from cart` });
    }
    cart = cart.filter(item => item.id !== productId);
    updateCartUI();
    updateProductGrid();
}

function updateQuantity(productId, change) {
    const item = cart.find(i => i.id === productId);
    if (!item) return;

    const newQty = item.quantity + change;
    if (newQty <= 0) {
        removeFromCart(productId);
        return;
    }
    if (newQty > item.maxStock) {
        Toast.error({ text: 'Not enough stock available' });
        return;
    }

    item.quantity = newQty;
    updateCartUI();
    updateProductGrid();
}

function updateProductGrid() {
    renderProductGrid(products);
}

function updateCartUI() {
    const cartContainer = document.getElementById('cartItems');

    const badge = document.getElementById('cartBadge');
    if (cart.length > 0) {
        badge.style.display = 'inline';
        badge.textContent = cart.reduce((sum, item) => sum + item.quantity, 0);
    } else {
        badge.style.display = 'none';
    }

    if (cart.length === 0) {
        cartContainer.innerHTML = `
      <div class="text-center text-muted py-4">
        <i class="bi bi-cart-plus display-6 d-block mb-2"></i>
        <small>No items in cart</small>
      </div>
    `;
        updateTotals();
        return;
    }

    let html = '';
    cart.forEach(item => {
        html += `
      <div class="cart-item d-flex align-items-center gap-2 p-2 border-bottom">
        <img src="${item.image || 'https://via.placeholder.com/40?text=Part'}" 
             style="width: 40px; height: 40px; object-fit: contain;" 
             onerror="this.src='https://via.placeholder.com/40?text=No+Image'">
        <div class="flex-grow-1" style="min-width: 0;">
          <div class="fw-bold small text-truncate">${item.name}</div>
          <div class="text-success small">${Currency.peso((item.price * item.quantity).toFixed(2))}</div>
        </div>
        <div class="d-flex align-items-center gap-1">
          <button class="btn btn-sm btn-outline-secondary qty-btn" data-id="${item.id}" data-change="-1">
            <i class="bi bi-dash"></i>
          </button>
          <span class="fw-bold mx-1" style="min-width: 20px; text-align: center;">${item.quantity}</span>
          <button class="btn btn-sm btn-outline-secondary qty-btn" data-id="${item.id}" data-change="1">
            <i class="bi bi-plus"></i>
          </button>
        </div>
        <button class="btn btn-sm btn-outline-danger remove-item" data-id="${item.id}">
          <i class="bi bi-x"></i>
        </button>
      </div>
    `;
    });

    cartContainer.innerHTML = html;
    document.querySelectorAll('.qty-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const id = parseInt(this.dataset.id);
            const change = parseInt(this.dataset.change);
            updateQuantity(id, change);
        });
    });

    document.querySelectorAll('.remove-item').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const id = parseInt(this.dataset.id);
            removeFromCart(id);
        });
    });

    updateTotals();
}

function updateTotals() {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const tax = 0;
    const total = subtotal;

    document.getElementById('cartSubtotal').textContent = Currency.peso(subtotal.toFixed(2));
    document.getElementById('cartTax').textContent = Currency.peso(tax.toFixed(2));
    document.getElementById('cartTotal').textContent = Currency.peso(total.toFixed(2));
}

function processPayment() {
    const paymentInput = document.getElementById('paymentAmount');
    const amount = parseFloat(paymentInput.value);

    if (cart.length === 0) {
        Toast.error({ text: 'Cart is empty' });
        return;
    }

    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const tax = subtotal * 0.12;
    const total = subtotal;

    if (!amount || amount <= 0) {
        Toast.error({ text: 'Please enter a valid payment amount' });
        return;
    }

    if (amount < total) {
        Toast.error({ text: `Insufficient payment. Total is ${Currency.peso(total.toFixed(2))}` });
        return;
    }

    const change = amount - total;
    document.getElementById('changeDisplay').style.display = 'block';
    document.getElementById('changeAmount').textContent = Currency.peso(change.toFixed(2));

    processOrder(total, amount, change);
}

function quickPay() {
    if (cart.length === 0) {
        Toast.error({ text: 'Cart is empty' });
        return;
    }

    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const tax = subtotal * 0.12;
    const total = subtotal + tax;

    document.getElementById('paymentAmount').value = total.toFixed(2);
    document.getElementById('changeDisplay').style.display = 'block';
    document.getElementById('changeAmount').textContent = '₱0.00';

    processOrder(total, total, 0);
}

async function processOrder(total, payment, change) {
    Twal.ask("Do you want to proceed?").then( async(click) => {
        if (click.confirm) {
            const orderData = {
                subtotal: total,
                payment_amount: payment,
                change_amount: change,
                shippingFee: 0,
                total: total,
                code: await Ctr.shortHash(15),
                order_date: CtrDATE.get_date(),
                status: 'completed',
                cart: cart.map(item => ({
                    id: item.id,
                    quantity: item.quantity,
                    price: item.price,
                    subtotal: item.price * item.quantity
                }))
            };

            Tyrax.post({
                url: "transaction/pos",
                request: orderData,
                response: (send) => {
                    if (send.code == 200) {
                        Twal.ok('Order completed successfully!', true);
                    } else {
                        Twal.err(send.message || 'Failed to process order');
                    }
                }
            });
        }
    });
}

function showReceipt(orderData) {
    const receiptContent = document.getElementById('receiptContent');
    const itemsHtml = orderData.items.map(item => `
    <div class="d-flex justify-content-between small">
      <span>${item.quantity}x ${item.name}</span>
      <span>${Currency.peso(item.subtotal.toFixed(2))}</span>
    </div>
  `).join('');

    receiptContent.innerHTML = `
    <div class="text-center mb-3">
      <h6 class="fw-bold">AUTOPARTS STORE</h6>
      <small class="text-muted">Order #${orderData.id || 'N/A'}</small><br>
      <small class="text-muted">${CtrDATE.get_date()}</small>
    </div>
    <hr>
    <div class="mb-2">
      ${itemsHtml}
    </div>
    <hr>
    <div class="d-flex justify-content-between">
      <span>Subtotal:</span>
      <span>${Currency.peso(orderData.subtotal.toFixed(2))}</span>
    </div>
    <div class="d-flex justify-content-between">
      <span>Tax (12%):</span>
      <span>${Currency.peso(orderData.tax.toFixed(2))}</span>
    </div>
    <div class="d-flex justify-content-between fw-bold">
      <span>Total:</span>
      <span>${Currency.peso(orderData.total.toFixed(2))}</span>
    </div>
    <div class="d-flex justify-content-between text-success">
      <span>Payment:</span>
      <span>${Currency.peso(orderData.payment.toFixed(2))}</span>
    </div>
    <div class="d-flex justify-content-between text-success fw-bold">
      <span>Change:</span>
      <span>${Currency.peso(orderData.change.toFixed(2))}</span>
    </div>
    <hr>
    <div class="text-center text-muted small">
      <i class="bi bi-check-circle-fill text-success"></i> Payment Successful
    </div>
  `;

    const receiptModal = new bootstrap.Modal(document.getElementById('receiptModal'));
    receiptModal.show();
}

function clearCart() {
    cart = [];
    updateCartUI();
    updateProductGrid();
    document.getElementById('paymentAmount').value = '';
    document.getElementById('changeDisplay').style.display = 'none';
}

loadProducts();

document.getElementById('posSearchButton').addEventListener('click', function () {
    const search = document.getElementById('posSearchInput').value.trim();
    loadProducts(search, selectedCategory);
});

document.getElementById('posSearchInput').addEventListener('keypress', function (e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('posSearchButton').click();
    }
});

document.getElementById('posClearSearch').addEventListener('click', function () {
    document.getElementById('posSearchInput').value = '';
    loadProducts('', selectedCategory);
});

document.getElementById('categoryFilter').addEventListener('change', function () {
    selectedCategory = this.value;
    const search = document.getElementById('posSearchInput').value.trim();
    loadProducts(search, selectedCategory);
});

document.getElementById('payNowBtn').addEventListener('click', processPayment);
document.getElementById('quickPayBtn').addEventListener('click', quickPay);

document.getElementById('clearCartBtn').addEventListener('click', function () {
    if (cart.length > 0) {
        Twal.ask("Clear all items from cart?").then((result) => {
            if (result.confirm) {
                clearCart();
            }
        });
    }
});

document.getElementById('printReceiptBtn').addEventListener('click', function () {
    window.print();
});

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function (m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}