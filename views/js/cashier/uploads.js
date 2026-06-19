import Ctr from "../../code/src/mods/ctr.js";
import Toast from "../../code/src/mods/toast.js";
import { Twal } from "../../code/src/mods/twal.js";
import { Tyrax } from "../../code/src/tyrux/main.js";
import { getUploads } from "../classes/functions/uploadsModel.js";

// Mock data for demo - replace with actual backend data

let photosData = await getUploads();

const baseUrl = window.location.origin;

function formatDate(dateStr) {
    let d = new Date(dateStr);
    return d.toLocaleDateString('en-PH') + " " + d.toLocaleTimeString([], {
        hour: '2-digit',
        minute: '2-digit'
    });
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
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

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function () {
        Toast.ok("✅ Link copied to clipboard!");
    }).catch(function () {
        // Fallback for older browsers
        const textarea = document.createElement('textarea');
        textarea.value = text;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        Toast.ok('✅ Link copied to clipboard!');
    });
}

function showToast(msg, type = 'success') {
    const toastEl = document.getElementById('copyToast');
    const toastMsg = document.getElementById('toastMsg');
    toastMsg.innerText = msg;
    toastEl.classList.remove('bg-success', 'bg-danger', 'bg-warning');
    if (type === 'success') toastEl.classList.add('bg-success');
    else if (type === 'danger') toastEl.classList.add('bg-danger');
    else toastEl.classList.add('bg-warning');
    const bsToast = new bootstrap.Toast(toastEl, {
        delay: 2000
    });
    bsToast.show();
}

function renderPhotos() {
    const searchVal = document.getElementById('photoSearchInput').value.toLowerCase();

    let filtered = [...photosData];

    if (searchVal) {
        filtered = filtered.filter(photo =>
            photo.name.toLowerCase().includes(searchVal) ||
            (photo.original_name && photo.original_name.toLowerCase().includes(searchVal))
        );
    }

    const container = document.getElementById('photosContainer');
    document.getElementById('photoCount').innerText = filtered.length;

    if (filtered.length === 0) {
        container.innerHTML = `
<div class="empty-gallery">
    <i class="bi bi-images fs-1 text-muted"></i>
    <h5 class="mt-3 text-muted">No photos found</h5>
    <p class="text-muted">Upload your first photo using the Upload button above</p>
</div>`;
        return;
    }

    let html = '<div class="gallery-grid">';
    filtered.forEach(photo => {
        const internalLink = `${photo.url}`;
        html += `
<div class="photo-card" data-photo-id="${photo.id}">
    <div style="position: relative;">
        <img src="${photo.url}" class="photo-img" alt="${escapeHtml(photo.name)}" onerror="this.src='https://placehold.co/400x300?text=Image+Not+Found'">
        <div class="photo-overlay">
            <button class="copy-link-btn" data-link="${internalLink}">
                <i class="bi bi-link-45deg"></i> Copy Link
            </button>
        </div>
    </div>
    <div class="photo-info">
        <div class="photo-name" title="${escapeHtml(photo.name)}">📷 ${escapeHtml(photo.name)}</div>
        <div class="photo-date mt-1">
            <i class="bi bi-calendar3"></i> ${formatDate(photo.uploaded_at)}
            <span class="ms-2"><i class="bi bi-hdd"></i> ${formatFileSize(photo.size)}</span>
        </div>
        <div class='mt-2'><button imgid='${photo.url}' class='btn btn-primary addimg'>Add this product</button><button class='btn btn-danger m-2 delete-upload' data-id='${photo.id}'><i class='bi bi-trash'></i></button></div>
    </div>
</div>
`;
    });
    html += '</div>';
    container.innerHTML = html;

    document.querySelectorAll('.copy-link-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const link = btn.getAttribute('data-link');
            copyToClipboard(link);
        });
    });
}

// File preview handler
document.getElementById('photoFile').addEventListener('change', function (e) {
    const file = e.target.files[0];
    const previewContainer = document.getElementById('previewContainer');

    if (file) {
        const reader = new FileReader();
        reader.onload = function (event) {
            previewContainer.innerHTML = `
    <img src="${event.target.result}" class="preview-img" alt="Preview">
    <p class="selected-file-name mt-2">${escapeHtml(file.name)} (${formatFileSize(file.size)})</p>
`;
        };
        reader.readAsDataURL(file);
    } else {
        previewContainer.innerHTML = `
<i class="bi bi-image fs-1 text-muted"></i>
<p class="mb-0 small text-muted">No file selected</p>
`;
    }
});

Ctr.submit("#uploadPhotoForm", (data) => {
    Tyrax.post({
        url: "photo/upload",
        data: data,
        response: (send) => {
            if (send.code == 200) {
                Twal.ok("Image uploaded", true);
            }
        }
    });
});
// Upload form submit - Initial function for you to complete
/*
document.getElementById('uploadPhotoForms').addEventListener('submit', async function(e) {
e.preventDefault();

const fileInput = document.getElementById('photoFile');
const photoName = document.getElementById('photoName').value;
const file = fileInput.files[0];

if (!file) {
    showToast('Please select a file to upload', 'warning');
    return;
}

//dirini

// Temporary demo response - remove this and implement above
showToast('Upload function ready! Implement your backend logic here.', 'info');

// For demo purposes only - add a mock photo
const newPhoto = {
    id: photosData.length + 1,
    name: photoName || file.name,
    original_name: file.name,
    url: URL.createObjectURL(file),
    size: file.size,
    uploaded_at: new Date().toISOString()
};
photosData.unshift(newPhoto);
renderPhotos();
bootstrap.Modal.getInstance(document.getElementById('uploadPhotoModal')).hide();
document.getElementById('uploadPhotoForm').reset();
document.getElementById('previewContainer').innerHTML = `
<i class="bi bi-image fs-1 text-muted"></i>
<p class="mb-0 small text-muted">No file selected</p>
`;
showToast('Photo uploaded successfully! (Demo)', 'success');
});
*/

// Search functionality
document.getElementById('searchButton').addEventListener('click', () => renderPhotos());
document.getElementById('clearSearchButton').addEventListener('click', () => {
    document.getElementById('photoSearchInput').value = '';
    renderPhotos();
});
document.getElementById('photoSearchInput').addEventListener('keyup', (e) => {
    if (e.key === 'Enter') renderPhotos();
});

// Initial render
renderPhotos();

let allPictButton = document.querySelectorAll(".addimg");
allPictButton.forEach(element => {
    element.addEventListener("click", () => {
        let imgpath = element.getAttribute("imgid");
        localStorage.setItem("imgpath", imgpath);
        location.href = "/cashier/products";
    });
});

document.querySelectorAll(".delete-upload").forEach(elem => {
    elem.addEventListener("click", ()=>{
        Twal.ask("Are you sure to delete this photo?").then((click)=>{
            let id = elem.getAttribute("data-id");
            if(click.confirm){
                Tyrax.delete({
                    url: "photo/delete",
                    data: {id: id},
                    response: (send)=>{
                        if(send.code == 404){
                            Twal.err(send.message);
                        }else if(send.code == 200){
                            Twal.ok("Photo deleted", true);
                        }
                    }
                })
            }
        })
    });
});