import Ctr from "../../code/src/mods/ctr.js";
import Currency from "../../code/src/mods/currency.js";
import CtrDATE from "../../code/src/mods/date.js";
import Loading from "../../code/src/mods/loading.js";
import TModal from "../../code/src/mods/modals/tmodal.js";
import CImagePicker from "../../code/src/mods/picker/imagepicker.js";
import Toast from "../../code/src/mods/toast.js";
import { Twal } from "../../code/src/mods/twal.js";
import { Tyrax } from "../../code/src/tyrux/main.js";
import { tbl_inventory, tbl_product } from "../classes/db/tables.js";
import { displayCategoryOnCB, getCategories } from "../classes/functions/category.js";
import { disableProduct, getProductLeft, getProducts, getStocks, productUpdated } from "../classes/functions/products.js";
import { getSupplier, getSupplierValueLabel } from "../classes/functions/supplier.js";


let supplier = await getSupplier();

//Add quantity modal
let modal = TModal.init({
  id: "qty_modal",
  title: "Add Supply",
  form_id: "qtyform",
  form: {
    "qty": { type: "number", label: "Quantity" },
    "supplier": { tag: "select", label: "Supplier", options: supplier, config: { value: "id", label: ['name', 'address'], separator: " - ", index: "Select Supplier" } }
  },
});

CImagePicker.init({
  element: ".prodImage"
});

let imgpath = localStorage.getItem("imgpath");
if (imgpath) {
  let imgprd = document.querySelector("#prodImage")
  imgprd.value = localStorage.getItem("imgpath");
  whenInputImage(imgprd);
  document.querySelector("#addproductbtn").click();
  localStorage.removeItem("imgpath")
}

//Display all products
let srch = document.querySelector("#productSearchInput");
let products = await productList();

document.querySelector("#searchButton").addEventListener("click", async () => {
  products = await productList(srch.value);
  renderProductTable(products);

});

async function productList() {
  Ctr.set_loading(true,"#product-container", 40);
  let prods = await getProducts(srch.value);
  let pr = [];

  for (let p in prods) {
    let row = prods[p];
    row['stock'] = await getStocks(row.id);
    pr[p] = row;
  }
  Ctr.set_loading(false,"#product-container");
  return pr;
}
let nextId = 6;
await displayCategoryOnCB("#prodCategory");
await displayCategoryOnCB("#editProdCategory");

function renderProductTable(pr = []) {
  const tbody = document.getElementById('productTableBody');
  if (pr == true) {
    products = pr;
  }
  if (!tbody) return;
  tbody.innerHTML = '';
  if (products.length === 0) {
    tbody.innerHTML = '<tr><td colspan="7" class="text-center py-5 text-muted">No products found. Click "Add New Product" to get started.</td></tr>';
    document.getElementById('rowCount').innerText = '0';
    return;
  }
  products.forEach(prod => {
    const row = document.createElement('tr');
    row.innerHTML = `
        <td class="fw-semibold">${prod.id}</td>
        <td><img src="${prod.image || ''}" class="product-img-preview" alt="product""></td>
        <td class="fw-medium">${prod.name}</td>
        <td><span class="badge bg-secondary bg-opacity-25 text-dark px-3 py-1 rounded-pill">${prod.category}</span></td>
        <td class="text-success fw-bold">${Currency.peso(prod.price.toFixed(2))}</td>
        <td><span class="badge-stock qtybox${prod.stock < 10 ? " bg-danger text-white" : ''}" attr=${prod.id}><i class="bi bi-box-seam"></i> ${prod.stock} units</span></td>
        <td class="table-actions">
          <button class="btn btn-sm btn-outline-warning rounded-circle me-2 edit-product-btn" data-id="${prod.id}" data-category='${prod.category_id}' title="Edit"><i class="bi bi-pencil-square"></i></button>
          <button class="btn btn-sm btn-outline-danger rounded-circle delete-product-btn" data-id="${prod.id}" title="Delete"><i class="bi bi-trash3"></i></button>
        </td>
      `;
    tbody.appendChild(row);
  });
  let qtybox = document.querySelectorAll(".qtybox");
  qtybox.forEach(element => {
    let id = element.getAttribute("attr");
    element.addEventListener("click", () => {
      modal.show();
      modal.form_submit((data) => {
        if (!data.qty) {
          Toast.error("Quantity is required to proceed"); return;
        }
        if (data.qty < 0) {
          Toast.error("Please input valid quantity"); return;
        }
        if (!data.supplier || data.supplier == "") {
          Toast.error("Please select supplier"); return;
        }
        let request = {
          product_id: id,
          quantity: data.qty,
          supplier_id: data.supplier,
          created_at: CtrDATE.get_date()
        };
        Tyrax.ctrql({
          action: "insert",
          table: tbl_inventory(),
          data: request,
          response: async (send) => {
            if (send.code == 200) {
              Twal.ok("Quantity updated", true);
              await productUpdated(id);
            }
          }
        })
      });
    });
  });
  document.getElementById('rowCount').innerText = products.length;

  document.querySelectorAll('.edit-product-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      const id = parseInt(btn.getAttribute('data-id'));
      const category = parseInt(btn.getAttribute('data-category'));
      openEditModal(id, category);
    });
  });
  document.querySelectorAll('.delete-product-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      const id = parseInt(btn.getAttribute('data-id'));
      Twal.ask("Are you sure to delete selected product?").then(async (click) => {
        if (click.confirm) {
          let result = await disableProduct(id);
          if (result.code == 200) {
            //Twal.ok("Product deleted", true);
            Toast.ok({ text: "Product deleted, the page will reload in a while", closed: () => location.reload() });
          }
        }
      });
      //deleteProductById(id);
    });
  });
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

const addForm = document.getElementById('addProductForm');
Ctr.submit("#addProductForm", (data) => {
  Ctr.set_html(".err", ``);
  Tyrax.post({
    url: "product/add",
    request: data,
    response: (send) => {
      if (send.code == 402) {
        Toast.error({ text: "validation failed, please check fields", effect: "center" });
        let errors = send.errors;
        for (let er in errors) {
          Ctr.set_html(`#_${er}`, errors[er]);
        }
      }
      else if (send.code == 200) {
        Twal.ok("Product added", true);
      } else {
        Twal.err(send.message);
      }
    }
  })
});


// Edit modal functions
function openEditModal(id, category = null) {
  const product = products.find(p => p.id === id);
  if (!product) return;
  document.getElementById('editProductId').value = product.id;
  document.getElementById('editProdName').value = product.name;
  document.getElementById('editProdCategory').value = category;
  document.getElementById('editProdPrice').value = product.price;
  document.getElementById('editProdImage').value = product.image || '';
  const editModal = new bootstrap.Modal(document.getElementById('editProductModal'));
  editModal.show();
}

const editForm = document.getElementById('editProductForm');
if (editForm) {
  editForm.addEventListener('submit', function (e) {
    e.preventDefault();
    const id = parseInt(document.getElementById('editProductId').value);
    const updatedName = document.getElementById('editProdName').value.trim();
    const updatedCategory = document.getElementById('editProdCategory').value;
    const updatedPrice = parseFloat(document.getElementById('editProdPrice').value);
    const updatedImage = document.getElementById('editProdImage').value.trim() || 'https://via.placeholder.com/45?text=Part';
    if (!updatedName) { showToastAlert('Name required', 'danger'); return; }
    const index = products.findIndex(p => p.id === id);
    if (index !== -1) {
      Tyrax.update({
        table: tbl_product(),
        where: {id: id},
        update: {
          name: updatedName,
          category: updatedCategory,
          price: updatedPrice,
          image: updatedImage
        },
        response: (send)=>{
          if(send.code == 200){
            Twal.ok("Product updated", true);
          }else{
            Twal.err(send.message);
          }
        }
      })
    } else {
      showToastAlert('Product not found', 'danger');
    }
  });
}

function deleteProductById(id) {
  if (confirm("⚠️ Permanently delete this auto part? This action cannot be undone.")) {
    products = products.filter(p => p.id !== id);
    renderProductTable();
    showToastAlert('🗑️ Product removed from inventory', 'danger');
    document.getElementById('searchStatus').innerHTML = '';
    // Clear search input after deletion for better UX
    const searchInput = document.getElementById('productSearchInput');
    if (searchInput) searchInput.value = '';
  }
}

// toast notification
function showToastAlert(msg, type) {
  const toastContainer = document.createElement('div');
  toastContainer.className = 'position-fixed bottom-0 end-0 p-3';
  toastContainer.style.zIndex = '1100';
  const toastDiv = document.createElement('div');
  let bgClass = 'bg-success';
  if (type === 'danger') bgClass = 'bg-danger';
  if (type === 'info') bgClass = 'bg-info';
  if (type === 'warning') bgClass = 'bg-warning';
  toastDiv.className = `toast align-items-center text-white ${bgClass} border-0 show`;
  toastDiv.role = 'alert';
  toastDiv.setAttribute('aria-live', 'assertive');
  toastDiv.setAttribute('aria-atomic', 'true');
  toastDiv.innerHTML = `
      <div class="d-flex">
        <div class="toast-body">${msg}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    `;
  toastContainer.appendChild(toastDiv);
  document.body.appendChild(toastContainer);
  setTimeout(() => { toastContainer.remove(); }, 3000);
  const bsToast = new bootstrap.Toast(toastDiv, { delay: 2800 });
  bsToast.show();
}

// image preview in add modal
const prodImageInput = document.getElementById('prodImage');
if (prodImageInput) {
  prodImageInput.addEventListener('input', function () {
    whenInputImage(this);
  });
}

function whenInputImage(theInput){
  const url = theInput.value.trim();
    const previewDiv = document.getElementById('imagePreviewPlaceholder');
    const previewImg = document.getElementById('previewImg');
    if (url) {
      previewDiv.style.display = 'block';
      if (url + "".startsWith("/")) {
        previewImg.src = Ctr.base_url(url);
      } else {
        previewImg.src = url;
      }
      previewImg.onerror = () => { previewImg.src = 'https://via.placeholder.com/50?text=Invalid'; };
    } else {
      previewDiv.style.display = 'none';
    }
}

// ========== SEARCH BAR - UI READY, NO FILTERING FUNCTION ==========
// The search interface is fully built. You can add your search logic here.
// Access search term via: document.getElementById('productSearchInput').value
const searchInput = document.getElementById('productSearchInput');
const searchBtn = document.getElementById('searchButton');
const clearBtn = document.getElementById('clearSearchButton');
const searchStatusSpan = document.getElementById('searchStatus');

if (searchBtn) {
  searchBtn.addEventListener('click', function () {
    const currentValue = searchInput.value.trim();
    if (currentValue !== '') {
      searchStatusSpan.innerHTML = `<i class="bi bi-search-heart"></i> Search ready: "${currentValue}" — add your filter logic here.`;
      searchStatusSpan.classList.add('text-info');
      console.log(`[Search Ready] Search term: "${currentValue}" - Implement your filtering logic`);
    } else {
      searchStatusSpan.innerHTML = `<i class="bi bi-search"></i> Enter a search term to filter products.`;
    }
  });
}

if (clearBtn) {
  clearBtn.addEventListener('click', function () {
    searchInput.value = '';
    searchStatusSpan.innerHTML = '';
    console.log('[Search Ready] Search cleared');
  });
}

if (searchInput) {
  searchInput.addEventListener('keypress', function (e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      if (searchBtn) searchBtn.click();
    }
  });
}

// sidebar mobile toggle
const menuToggle = document.getElementById('menuToggleBtn');
const sidebar = document.getElementById('adminSidebar');
const backdrop = document.getElementById('sidebarBackdrop');
if (menuToggle && sidebar && backdrop) {
  menuToggle.addEventListener('click', () => {
    sidebar.classList.toggle('show-sidebar');
    backdrop.classList.toggle('show');
  });
  backdrop.addEventListener('click', () => {
    sidebar.classList.remove('show-sidebar');
    backdrop.classList.remove('show');
  });
}

// initial render
renderProductTable();

// close sidebar on window resize above 768px
window.addEventListener('resize', function () {
  if (window.innerWidth > 768) {
    if (sidebar) sidebar.classList.remove('show-sidebar');
    if (backdrop) backdrop.classList.remove('show');
  }
});
