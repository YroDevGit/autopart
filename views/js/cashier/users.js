import Ctr from "../../code/src/mods/ctr.js";
import { Twal } from "../../code/src/mods/twal.js";
import { Tyrax } from "../../code/src/tyrux/main.js";
import { deleteUser, getAllUsers } from "../classes/functions/users.js";

// Mock data for demo - replace with actual backend data
let usersData = await getAllUsers();

let curActive = 0;

function formatDate(dateStr) {
    if (!dateStr) return 'Never';
    let d = new Date(dateStr);
    return d.toLocaleDateString('en-PH') + " " + d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

function getInitials(firstName, lastName) {
    return (firstName?.[0] || '') + (lastName?.[0] || '');
}

function getRoleBadge(role) {
    const roleMap = {
        '1': 'role-admin',
        '2': 'role-user',
        '3': 'role-rider',
    };
    const labelMap = {
        '1': 'Administrator',
        '2': 'Cashier',
        '3': "Rider"

    };
    const className = roleMap[role] || 'role-user';
    return `<span class="role-badge ${className}">${labelMap[role] || role}</span>`;
}

function getStatusBadge(status) {
    if (status === 1) {
        return '<span class="status-badge-user status-active"><i class="bi bi-check-circle"></i> Active</span>';
    }
    return '<span class="status-badge-user status-inactive"><i class="bi bi-x-circle"></i> Inactive</span>';
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function(m) {
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

function renderUsers() {
    const searchVal = document.getElementById('userSearchInput').value.toLowerCase();
    
    let filtered = [...usersData];
    
    if (searchVal) {
        filtered = filtered.filter(user => 
            user.fullname.toLowerCase().includes(searchVal) ||
            user.username.toLowerCase().includes(searchVal)
        );
    }
    
    const container = document.getElementById('usersContainer');
    document.getElementById('userCount').innerText = filtered.length;
    
    if (filtered.length === 0) {
        container.innerHTML = `
            <div class="empty-users">
                <i class="bi bi-people fs-1 text-muted"></i>
                <h5 class="mt-3 text-muted">No users found</h5>
                <p class="text-muted">Click the "Add New User" button to create one</p>
            </div>`;
        return;
    }
    
    let html = '<div class="user-grid">';
    filtered.forEach(user => {
        const initials = getInitials(user.fullname);
        const fullName = user.fullname;
        let actbtn = `<button class="btn btn-sm btn-outline-danger delete-user-btn" data-active='${user.active}' data-id="${user.id}">
                        <i class="bi bi-trash"></i> Delete
                    </button>`;

        if(user.active == 0){
            actbtn = `<button class="btn btn-sm btn-outline-success delete-user-btn" data-active='${user.active}' data-id="${user.id}">
                        <i class="bi bi-recycle"></i> Restore
                    </button>`;
        }
        
        html += `
            <div class="user-card" data-user-id="${user.id}">
                <div class="user-card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        ${user.avatar ? 
                            `<img src="${user.avatar}" class="user-avatar" alt="${escapeHtml(fullName)}">` :
                            `<div class="avatar-placeholder">${initials}</div>`
                        }
                        <div>
                            <h6 class="mb-0 fw-bold">${escapeHtml(fullName)}</h6>
                            <small class="text-muted">@${escapeHtml(user.username)}</small>
                        </div>
                    </div>
                    ${getRoleBadge(user.role)}
                </div>
                <div class="user-card-body">
                    <div class="mb-2">
                        <small class="text-muted"><i class="bi bi-envelope"></i> Email</small>
                        <p class="mb-1 small">${escapeHtml(user.username)}</p>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6">
                            <small class="text-muted"><i class="bi bi-clock-history"></i> Status</small>
                            <p class="mb-0 small">${getStatusBadge(user.active)}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted"><i class="bi bi-calendar-plus"></i> Joined</small>
                            <p class="mb-0 small">${formatDate(user.created_at)}</p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted"><i class="bi bi-clock"></i> Last Login</small>
                            <p class="mb-0 small">${formatDate(user.updated_at)}</p>
                        </div>
                    </div>
                </div>
                <div class="user-card-footer">
                    <button class="btn btn-sm btn-outline-primary edit-user-btn" style='display:none;' disabled data-id="${user.id}">
                        <i class="bi bi-pencil"></i> Edit
                    </button>
                    ${actbtn}
                </div>
            </div>
        `;
    });
    html += '</div>';
    container.innerHTML = html;
    
    document.querySelectorAll('.edit-user-btn').forEach(btn => {
        btn.addEventListener('click', () => openEditUserModal(parseInt(btn.dataset.id)));
    });
    document.querySelectorAll('.delete-user-btn').forEach(btn => {
        btn.addEventListener('click', () => openDeleteUserModal(parseInt(btn.dataset.id), btn.dataset.active));
    });
}

function openEditUserModal(userId) {
    const user = usersData.find(u => u.id === userId);
    if (!user) return;
    
    document.getElementById('editUserId').value = user.id;
    document.getElementById('editUserFirstName').value = user.first_name;
    document.getElementById('editUserLastName').value = user.last_name;
    document.getElementById('editUserEmail').value = user.email;
    document.getElementById('editUserContact').value = user.contact || '';
    document.getElementById('editUserUsername').value = user.username;
    document.getElementById('editUserRole').value = user.role;
    document.getElementById('editUserStatus').value = user.status;
    document.getElementById('editUserForceReset').checked = false;
    document.getElementById('editUserPassword').value = '';
    document.getElementById('editUserConfirmPassword').value = '';
    
    new bootstrap.Modal(document.getElementById('editUserModal')).show();
}

function openDeleteUserModal(userId, active) {
    document.getElementById('deleteUserId').value = userId;
    curActive = active;
    let question = "Are you sure you want to delete this user?";
    console.log(active);
    if(active == 0){
        question = "Are you sure you want to restore this user?"
    }
    Twal.ask(question).then(async(click)=>{
        if(click.confirm){
            await deleteUser(userId, active);
        }
    });
}

// Password toggle functions
function togglePasswordVisibility(inputId, toggleId) {
    const input = document.getElementById(inputId);
    const toggle = document.getElementById(toggleId);
    if (input && toggle) {
        toggle.addEventListener('click', function() {
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });
    }
}

// Initialize password toggles
togglePasswordVisibility('userPassword', 'togglePassword');
togglePasswordVisibility('userConfirmPassword', 'toggleConfirmPassword');
togglePasswordVisibility('editUserPassword', 'toggleEditPassword');
togglePasswordVisibility('editUserConfirmPassword', 'toggleEditConfirmPassword');

// Add User Form Submit
document.getElementById('addUserForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = {
        fullname: document.getElementById('userFirstName').value,
        email: document.getElementById('userEmail').value,
        role: document.getElementById('userRole').value,
        status: document.getElementById('userStatus').value,
    };
    
    Tyrax.post({
        url: "user/add",
        data: formData,
        res: (send, code, message, data, errors)=>{
            if(code == 401){
                for(let i in errors){
                    Ctr.errStrSet(i, errors[i]);
                }
            }
            else if(code == 200){
                Twal.ok("User added", true);
            }else{
                Twal.err(message);
            }
        }
    });
});

// Edit User Form Submit
document.getElementById('editUserForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const userId = parseInt(document.getElementById('editUserId').value);
    const newPassword = document.getElementById('editUserPassword').value;
    const confirmPassword = document.getElementById('editUserConfirmPassword').value;
    
    if (newPassword && newPassword !== confirmPassword) {
        showToast('Passwords do not match!', 'danger');
        return;
    }
    
    if (newPassword && newPassword.length < 6) {
        showToast('Password must be at least 6 characters!', 'danger');
        return;
    }
    
    const formData = {
        id: userId,
        first_name: document.getElementById('editUserFirstName').value,
        last_name: document.getElementById('editUserLastName').value,
        email: document.getElementById('editUserEmail').value,
        contact: document.getElementById('editUserContact').value,
        username: document.getElementById('editUserUsername').value,
        role: document.getElementById('editUserRole').value,
        status: document.getElementById('editUserStatus').value,
        force_reset: document.getElementById('editUserForceReset').checked
    };
    
    if (newPassword) {
        formData.password = newPassword;
    }
    
    // TODO: Implement your actual update logic here
    /*
    try {
        const response = await fetch('/api/users/' + userId, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });
        const result = await response.json();
        
        if (result.success) {
            const index = usersData.findIndex(u => u.id === userId);
            if (index !== -1) {
                usersData[index] = { ...usersData[index], ...formData };
                delete usersData[index].password;
            }
            renderUsers();
            bootstrap.Modal.getInstance(document.getElementById('editUserModal')).hide();
            showToast('User updated successfully!', 'success');
        } else {
            showToast(result.message || 'Failed to update user', 'danger');
        }
    } catch (error) {
        console.error('Update error:', error);
        showToast('Failed to update user. Please try again.', 'danger');
    }
    */
    
    // Temporary demo - remove this and implement above
    const index = usersData.findIndex(u => u.id === userId);
    if (index !== -1) {
        usersData[index] = { ...usersData[index], ...formData };
        delete usersData[index].password;
    }
    renderUsers();
    bootstrap.Modal.getInstance(document.getElementById('editUserModal')).hide();
    showToast('User updated successfully! (Demo)', 'success');
});

// Delete User
document.getElementById('confirmDeleteUserBtn').addEventListener('click', async function() {
    const userId = parseInt(document.getElementById('deleteUserId').value);
    await deleteUser(userId, curActive);
});

// Search functionality
document.getElementById('searchButton').addEventListener('click', () => renderUsers());
document.getElementById('clearSearchButton').addEventListener('click', () => {
    document.getElementById('userSearchInput').value = '';
    renderUsers();
});
document.getElementById('userSearchInput').addEventListener('keyup', (e) => {
    if (e.key === 'Enter') renderUsers();
});

renderUsers();