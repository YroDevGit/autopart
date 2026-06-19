import Ctr from "../code/src/mods/ctr.js";
import Currency from "../code/src/mods/currency.js";
import Loading from "../code/src/mods/loading.js";
import Toast from "../code/src/mods/toast.js";
import { Twal } from "../code/src/mods/twal.js";
import { Tyrax } from "../code/src/tyrux/main.js";
import { NegrosCode, getAllAddress, getShippingById, getShippingDetailsById, setAddressOnCB } from "./classes/functions/address.js";
import { getBaranggay, getMunicipality, getProvince } from "./classes/functions/addresses.js";
import { displayCategoryOnCB } from "./classes/functions/category.js";
import { getCustomerDetails } from "./classes/functions/customer.js";
import { addProducts, getProductLeft, getProducts } from "./classes/functions/products.js";

Loading.load(true);
let shippingDetails = [];
let current_user = localStorage.getItem("userid");
let cities = await getMunicipality(NegrosCode());

Ctr.setOptions("#city",cities,{value: "code", label: "name", onChange: async(element)=>{
  let cityCode = element.value;
  showbrgys(cityCode);
}});

async function showbrgys(cityCode){
  Loading.load(true);
  let bgrys = await getBaranggay(cityCode);
  Ctr.setOptions("#brgy", bgrys, {value: "code", label: "name"});
  Loading.load(false);
}

let customerDetails = [];

if (localStorage.getItem("userid")) {
  customerDetails = await getCustomerDetails(localStorage.getItem("userid"));
  if (customerDetails.fullname) {
    Ctr.set_html("#user_name", `${customerDetails.fullname} <small style="font-size: 12px;"><a href="/logout" class='exitbtn'>(Exit)</a></small>`);
  }
}

const clearFilterBtn = document.getElementById("clearFilterBtn");
if (clearFilterBtn) clearFilterBtn.addEventListener("click", clearFilters);

function clearFilters() {
  location.reload();
}

await displayCategoryOnCB("#categorySelect");
const modalEl = document.getElementById('checkoutModal');
const modal = new bootstrap.Modal(modalEl);
const cartOffcanvas = new bootstrap.Offcanvas(document.getElementById('cartOffcanvas'));

let products = await getProducts();

Ctr.click("#searchButton", async () => {
  let category = Ctr.value("#categorySelect") ?? null;
  let value = Ctr.value("#searchInput") ?? null;
  products = await getProducts(value, category);
  renderProducts(products);
});

let cart = [];

let currentShippingId = null;
let currentShippingFee = 0;

await setAddressOnCB("#shippingaddress");

function saveCartToLocal() {
  localStorage.setItem('orderCart', JSON.stringify(cart));
}

document.querySelector("#startover").addEventListener("click", () => {
  Twal.ask("Are you sure to start over? This will clear your cart.").then((click) => {
    if (click.confirm) {
      localStorage.removeItem("orderCart");
      location.reload();
    }
  });
});

function loadCartFromLocal() {
  const stored = localStorage.getItem('orderCart');
  if (stored) {
    cart = JSON.parse(stored);
  } else {
    cart = [];
  }
  renderAllCarts();
}

// Add product to cart
function addToCart(productId, quantity = 1) {
  const product = products.find(p => p.id === productId);
  if (!product) return;
  const existing = cart.find(item => item.id === productId);
  if (existing) {
    const newQty = existing.quantity + quantity;
    if (newQty > product.quantity) {
      Toast.error(`⚠️ Only ${product.quantity} units available in stock!`);
      return;
    }
    existing.quantity = newQty;
  } else {
    if (quantity > product.quantity) {
      Toast.error(`⚠️ Only ${product.quantity} units available.`);
      return;
    }
    cart.push({
      id: product.id,
      name: product.name,
      price: product.price,
      quantity: quantity,
      image: product.image,
      stock: product.quantity
    });
  }
  saveCartToLocal();
  renderAllCarts();
  Toast.success(`${product.name} added to cart`);
}

// Update quantity
function updateCartItemQuantity(productId, newQuantity) {
  const item = cart.find(i => i.id === productId);
  if (!item) return;
  const originalProduct = products.find(p => p.id === productId);
  if (newQuantity <= 0) {
    removeCartItem(productId);
    return;
  }
  if (originalProduct && newQuantity > originalProduct.quantity) {
    Toast.error(`Max stock available: ${originalProduct.quantity}`);
    return;
  }
  item.quantity = newQuantity;
  saveCartToLocal();
  renderAllCarts();
}

Ctr.click("#exitButtonMain", ()=>{
  location.href = "/logout";
});


function removeCartItem(productId) {
  cart = cart.filter(i => i.id !== productId);
  saveCartToLocal();
  renderAllCarts();
  Toast.info(`Item removed from cart`);
}

// Clear cart after checkout
function clearCart() {
  cart = [];
  saveCartToLocal();
  renderAllCarts();
}

// Compute subtotal
function getSubtotal() {
  return cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
}

// Render products grid
function renderProducts(prod = null) {
  const container = document.getElementById('productsContainer');
  if (!container) return;
  container.innerHTML = '';
  if (prod && prod != null) {
    products = prod;
  }
  products.forEach(prod => {
    if(prod.quantity < 1) return;
    const col = document.createElement('div');
    col.className = 'col-md-6 col-lg-4';
    col.innerHTML = `
            <div class="card product-card h-100 shadow-sm">
                <img src="${prod.image}" class="product-img card-img-top" alt="${prod.name}">
                <div class="card-body">
                    <h6 class="fw-bold">${prod.name}</h6>
                    <p class="small text-muted mb-1">${prod.category}</p>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <span class="fw-bold text-warning fs-5">${Currency.peso(prod.price)}</span>
                        <span class="badge bg-secondary bg-opacity-10 text-dark badge-stock"><i class="bi bi-box-seam"></i> ${prod.quantity} left</span>
                    </div>
                    <div class="input-group mt-3">
                        <input type="number" id="qty-${prod.id}" class="form-control form-control-sm text-center" value="1" min="1" max="${prod.quantity}" style="max-width: 70px;">
                        <button class="btn btn-autoparts-primary btn-sm add-to-cart-btn" data-id="${prod.id}">Add to Cart <i class="bi bi-cart-plus"></i></button>
                    </div>
                </div>
            </div>
        `;
    container.appendChild(col);
  });

  // Attach add to cart events
  document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      const id = parseInt(btn.getAttribute('data-id'));
      const qtyInput = document.getElementById(`qty-${id}`);
      let qty = parseInt(qtyInput.value);
      if (isNaN(qty) || qty < 1) qty = 1;
      addToCart(id, qty);
    });
  });
}

// Render cart (unified - only one cart view in offcanvas)
async function renderCart() {
  const container = document.getElementById('cartItemsList');
  const subtotalSpan = document.getElementById('cartSubtotal');
  const cartCountBadge = document.getElementById('cartFloatBadge');
  const cartCountDesktop = document.getElementById('cartCountDesktopFixed');
  const shippingFeeSpan = document.getElementById('cartShippingFee');
  const cartTotalSpan = document.getElementById('cartTotalAmount');

  if (!container) return;

  if (cart.length === 0) {
    container.innerHTML = '<div class="text-center text-muted py-4">Your cart is empty. Select products above.</div>';
    if (subtotalSpan) subtotalSpan.innerText = Currency.peso(0);
    if (cartCountBadge) cartCountBadge.innerText = '0';
    if (cartCountDesktop) { cartCountDesktop.innerText = '0'; document.querySelector("#cartBadge").innerHTML = "0" };
    if (shippingFeeSpan) shippingFeeSpan.innerText = Currency.peso(0);
    if (cartTotalSpan) cartTotalSpan.innerText = Currency.peso(0);
    return;
  }

  let html = '';
  cart.forEach(item => {
    html += `
            <div class="d-flex gap-2 mb-3 border-bottom pb-2">
                <img src="${item.image}" class="cart-item-img" alt="${item.name}">
                <div class="flex-grow-1">
                    <div class="fw-semibold small">${item.name}</div>
                    <div class="text-warning fw-bold">${Currency.peso(item.price)}</div>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <button class="btn btn-sm btn-outline-secondary dec-qty-cart" data-id="${item.id}">-</button>
                        <input type="number" value="${item.quantity}" class="form-control form-control-sm quantity-input text-center cart-qty-input-cart" data-id="${item.id}" min="1" max="${item.stock}" style="width: 60px;">
                        <button class="btn btn-sm btn-outline-secondary inc-qty-cart" data-id="${item.id}">+</button>
                        <button class="btn btn-sm btn-outline-danger ms-auto remove-item-cart" data-id="${item.id}"><i class="bi bi-trash3"></i></button>
                    </div>
                </div>
            </div>
        `;
  });
  container.innerHTML = html;

  const totalItems = cart.reduce((sum, i) => sum + i.quantity, 0);
  if (cartCountBadge) cartCountBadge.innerText = totalItems;
  if (cartCountDesktop) { cartCountDesktop.innerText = totalItems; document.querySelector("#cartBadge").innerHTML = totalItems; }

  const subtotal = getSubtotal();
  if (subtotalSpan) subtotalSpan.innerText = Currency.peso(subtotal);

  // Update shipping and total
  await updateShippingAndTotal();

  // Attach cart events
  attachCartEvents();
}

async function updateShippingAndTotal() {
  const shippingSelect = document.getElementById('shippingaddress');
  if (shippingSelect && shippingSelect.value) {
    currentShippingId = shippingSelect.value;
    currentShippingFee = await getShippingById(currentShippingId) || 0;
  } else {
    currentShippingFee = 0;
  }

  const subtotal = getSubtotal();
  const total = subtotal + currentShippingFee;

  const shippingFeeSpan = document.getElementById('cartShippingFee');
  const cartTotalSpan = document.getElementById('cartTotalAmount');

  if (shippingFeeSpan) shippingFeeSpan.innerText = Currency.peso(currentShippingFee);
  if (cartTotalSpan) cartTotalSpan.innerText = Currency.peso(total);
}

function attachCartEvents() {
  document.querySelectorAll('.dec-qty-cart').forEach(btn => {
    btn.removeEventListener('click', handleDecCart);
    btn.addEventListener('click', handleDecCart);
  });
  document.querySelectorAll('.inc-qty-cart').forEach(btn => {
    btn.removeEventListener('click', handleIncCart);
    btn.addEventListener('click', handleIncCart);
  });
  document.querySelectorAll('.cart-qty-input-cart').forEach(inp => {
    inp.removeEventListener('change', handleQtyChangeCart);
    inp.addEventListener('change', handleQtyChangeCart);
  });
  document.querySelectorAll('.remove-item-cart').forEach(btn => {
    btn.removeEventListener('click', handleRemoveCart);
    btn.addEventListener('click', handleRemoveCart);
  });
}

function handleDecCart(e) {
  const id = parseInt(e.currentTarget.getAttribute('data-id'));
  updateCartItemQuantity(id, (cart.find(i => i.id === id)?.quantity || 1) - 1);
}
function handleIncCart(e) {
  const id = parseInt(e.currentTarget.getAttribute('data-id'));
  updateCartItemQuantity(id, (cart.find(i => i.id === id)?.quantity || 0) + 1);
}
function handleQtyChangeCart(e) {
  const id = parseInt(e.currentTarget.getAttribute('data-id'));
  let newVal = parseInt(e.currentTarget.value);
  if (isNaN(newVal)) newVal = 1;
  updateCartItemQuantity(id, newVal);
}
function handleRemoveCart(e) {
  const id = parseInt(e.currentTarget.getAttribute('data-id'));
  removeCartItem(id);
}

function renderAllCarts() {
  renderCart();
  updateCheckoutModalPreview();
}

async function updateCheckoutModalPreview() {
  const subtotal = getSubtotal();
  const shippingFee = currentShippingFee;
  const total = subtotal + shippingFee;
  const totalItems = cart.reduce((sum, i) => sum + i.quantity, 0);

  document.getElementById('checkoutTotalItems').innerText = totalItems;
  document.getElementById('checkoutSubtotal').innerText = Currency.peso(subtotal);
  document.getElementById('checkoutShippingFee').innerText = Currency.peso(shippingFee);
  document.getElementById('checkoutTotal').innerText = Currency.peso(total);
}

// Shipping address change listener
document.getElementById('shippingaddress').addEventListener('change', async () => {
  const shippingSelect = document.getElementById('shippingaddress');
  if (shippingSelect.value) {
    currentShippingId = shippingSelect.value;
    currentShippingFee = await getShippingById(currentShippingId) || 0;
  } else {
    currentShippingFee = 0;
  }
  await updateShippingAndTotal();
  updateCheckoutModalPreview();
});

// Checkout button in cart
const checkoutBtnCart = document.getElementById('checkoutBtnCart');
if (checkoutBtnCart) {
  checkoutBtnCart.addEventListener('click', async () => {
    if (cart.length === 0) {
      Toast.error("Cart is empty, add items first");
      return;
    }

    const shippingSelect = document.getElementById('shippingaddress');
    const valShipping = shippingSelect.value;
    if (!valShipping || valShipping === "") {
      Toast.error("Please select shipping address");
      return;
    }

    // Check stock availability for all items
    for (let crt of cart) {
      let name = crt.name;
      let qqty = crt.quantity;
      let id = crt.id;
      let prleft = await getProductLeft(id);
      if (prleft < qqty) {
        Twal.error({
          text: `<b>${name}</b><br> has not enough supply, you can buy in maximum of <b>${prleft}</b>`,
          html: true
        });
        return;
      }
    }

    
    if(current_user){
      let userdata = await getCustomerDetails(current_user);

      checkoutName.value = userdata.fullname ?? "";
      contactnum.value = userdata.contact ?? "";
      checkoutEmail.value = userdata.email ?? "";
    }
    
    Loading.load(true);
    shippingDetails = await getShippingDetailsById(Ctr.value("#shippingaddress"));
    let citycode = shippingDetails.city_code;
    let brgycode = shippingDetails.brgy_code;
    document.querySelector("#city").value = citycode;
    await showbrgys(citycode);
    document.querySelector("#brgy").value = brgycode;
    cartOffcanvas.hide();
    if (modal) modal.show();
  });
}

// Checkout form submission
const checkoutForm = document.getElementById('checkoutForm');
if (checkoutForm) {

  checkoutForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    if (cart.length === 0) {
      Toast.error('Cart is empty! Add products before checkout.');
      return;
    }

    Twal.ask("Do you want to complete the order?").then(async (click) => {
      if (click.confirm) {
        const name = document.getElementById('checkoutName').value;
        const email = document.getElementById('checkoutEmail').value;
        if (!name || !email) {
          Toast.error('Please fill in all required fields');
          return;
        }

        const subtotal = getSubtotal();
        const total = subtotal + currentShippingFee;
        const shippingAddressText = document.getElementById('shippingaddress');
        const fulladdress = document.getElementById('fulladdress')?.value ?? "";
        const selectedOption = shippingAddressText.options[shippingAddressText.selectedIndex];
        const addressText = selectedOption ? selectedOption.text : '';
        if(! Ctr.get_selected("#brgy").value || ! Ctr.get_selected("#city").value){
          Toast.error("City and Baranggay is required");
          return;
        }
        const shippingId = shippingAddressText.value;
        
        const city_code = Ctr.get_selected("#city").value;
        const brgy_code = Ctr.get_selected("#brgy").value;
        
        const f_address = Ctr.get_selected("#brgy").label+", "+Ctr.get_selected("#city").label+", Negros Occidental";
        const fl_address = fulladdress+", "+Ctr.get_selected("#brgy").label+", "+Ctr.get_selected("#city").label+", Negros Occidental";
        const orderDetails = {
          code: await Ctr.shortHash(15),
          fullname: name,
          email: email,
          subtotal: subtotal,
          shippingFee: currentShippingFee,
          total: total,
          contact: document.querySelector("#contactnum")?.value ?? "",
          address: f_address,
          fulladdress: fl_address,
          cart: cart
        };

        let result = await addProducts(orderDetails);
        if (result.code == 205 || result.code == 400) {
          Twal.err(result.message);
          return;
        }
        if (result.code == 401) {
          Twal.err(result.message);
          return;
        }
        if (result.code == 402) {
          Twal.err(result.message);
          return;
        }
        if (result.code != 200) {
          Twal.err(result.message ?? "Server error");
          return;
        }
        if (result.userid) {
          localStorage.setItem("userid", result.userid);
        }
        clearCart();
        if (modal) modal.hide();
        checkoutForm.reset();
        Twal.ok("Order placed, thank you for your order", true);
      }
    });

  });
}

// Initialize page
function init() {
  renderProducts();
  loadCartFromLocal();
}

init();