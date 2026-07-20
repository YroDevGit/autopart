import Ctr from "../../../code/src/mods/ctr.js";
import Notify from "../../../code/src/mods/notify.js";
import TModal from "../../../code/src/mods/modals/tmodal.js";
import Toast from "../../../code/src/mods/toast.js";
import { Twal } from "../../../code/src/mods/twal.js";
import { Tyrax } from "../../../code/src/tyrux/main.js";
import { tbl_address, tbl_transaction } from "../../classes/db/tables.js";
import { getCustomerDetails } from "../../classes/functions/customer.js";
import { getUserDetails } from "../../classes/functions/users.js";

let id = localStorage.getItem("userid");

let userDetails = await getUserDetails(id);

let fullname = userDetails.fullname ?? "Unknown"

const menuToggle = document.getElementById('menuToggleBtn');
const sidebar = document.getElementById('adminSidebar');
const backdrop = document.getElementById('sidebarBackdrop');
const mainContent = document.getElementById('mainContentWrapper');

// Toggle sidebar on hamburger click
if (menuToggle && sidebar) {
    menuToggle.addEventListener('click', function (e) {
        e.stopPropagation();
        sidebar.classList.toggle('show');

        if (backdrop) {
            backdrop.classList.toggle('show');
        }

        if (mainContent) {
            mainContent.classList.toggle('sidebar-open');
        }

        // Change icon
        const icon = this.querySelector('i');
        if (icon) {
            if (sidebar.classList.contains('show')) {
                icon.className = 'bi bi-x-lg fs-4';
            } else {
                icon.className = 'bi bi-list fs-4';
            }
        }

        // Prevent body scroll when sidebar is open
        document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
    });
}

// Close sidebar when clicking backdrop
if (backdrop) {
    backdrop.addEventListener('click', function () {
        if (sidebar) {
            sidebar.classList.remove('show');
        }
        this.classList.remove('show');

        if (mainContent) {
            mainContent.classList.remove('sidebar-open');
        }

        // Reset hamburger icon
        const icon = menuToggle?.querySelector('i');
        if (icon) {
            icon.className = 'bi bi-list fs-4';
        }

        document.body.style.overflow = '';
    });
}

// Close sidebar when pressing Escape key
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && sidebar?.classList.contains('show')) {
        sidebar.classList.remove('show');
        if (backdrop) backdrop.classList.remove('show');
        if (mainContent) mainContent.classList.remove('sidebar-open');

        const icon = menuToggle?.querySelector('i');
        if (icon) {
            icon.className = 'bi bi-list fs-4';
        }
        document.body.style.overflow = '';
    }
});

let profileModal = TModal.init({
    title: "<i>User Profile</i>",
    form_id: "userprofileid",
    form: {
        fullname: { label: "Full name", type: "text", value: fullname},
        password: { label: "New Password", type: "password" },
        rpassword: { label: "Re-Enter password", type: "password" }
    }
})

Ctr.click("#logout_btn", () => {
    Twal.ask("Are you sure to logout?").then((click) => {
        if (click.confirm) {
            Ctr.redirect("ctrx/logout");
        }
    });
});

Ctr.click("#userprofilelink", () => {
    profileModal.show();
});

profileModal.form_submit((data) => {
    Ctr.set_html(".error_text", '');
    Tyrax.post({
        url: "user/update",
        loading: {id: profileModal.form_id},
        data: data,
        res: (send, code, message, data, errors) => {
            if (code == 402) {
                Toast.err("Validation failed");
                profileModal.displayErrors(errors);
            }
            if (code == 401) {
                console.log(message);
                Twal.err(message);
                profileModal.displayErrors(errors);
            }
            if (code == 200) {
                Twal.ok("User profile updated", true);
            }
        }
    })
});


async function getOrderIds() {
    let data = await Tyrax.select({
        table: tbl_transaction(),
        dataOnly: true
    });

    let ret = [];

    if (!Ctr.is_empty(data)) {
        for (let x in data) {
            let row = data[x];
            if (row.id) {
                ret.push(row.id);
            }
        }
        return ret;
    }
    return [];
}

let notip = document.querySelector("#notip");
let notificationReceived = true;
let kaisa = 0;
async function saveNotif() {
    let data = await getOrderIds();
    if (!Ctr.is_empty(data)) {
        localStorage.setItem("ordersRecord", JSON.stringify(data));
    }
}

async function checker(auto = 1) {
    if (notip.className == "dropdown-menu dropdown-menu-end shadow-lg show") {
        return;
    }
    let getOne = localStorage.getItem("ordersRecord");
    if (getOne) {
        getOne = JSON.parse(getOne);
    }
    let res = compare(await getOrderIds(), getOne);
    let sz = res.length;
    document.querySelector("#notificationCount").innerHTML = sz;
    if (sz > 0) {
        notificationReceived = false;
    }

    if (notificationReceived == false && kaisa == 0 && auto == 0) {
        Notify.fire({
            text: "New order received",
            icon: "order.png",
            click: () => {
                document.querySelector("#notificationBell").click();
            }
        });
        notificationReceived = true;
        kaisa = 1;
    }
    Tyrax.post({
        url: "transaction/getUpdate",
        data: { arr: JSON.stringify(res) },
        res: (send, code, message, data) => {
            if (code == 200) {
                Ctr.set_html("#notifContent", '');
                if (!Ctr.is_empty(data)) {
                    for (let x in data) {
                        let rw = data[x];
                        let nw = ``;
                        if (rw.isNew == "yes") {
                            nw = `<span class="badge bg-primary ms-2">New</span>`;
                        }
                        Ctr.add_html("#notifContent", `
                            <li>
                                <a class="dropdown-item" href="/cashier/orders?code=${rw.transaction_code ?? ''}">
                                    <div class="d-flex align-items-start gap-2">
                                    <i class="bi bi-info-circle text-primary mt-1"></i>
                                    <div>
                                        <small class="fw-semibold d-block">New order received ${nw}</small>
                                        <small class="text-muted">${rw.fullname} order created</small>
                                        <small class="text-muted d-block">${rw.created_at}</small>
                                    </div>
                                    </div>
                                </a>
                            </li>
                            `);
                    }
                } else {
                    Ctr.set_html("#notifContent", "<li>No Notifications</li>")
                }
            }
        }
    });
}

function compare(array1, array2) {
    const setA = new Set(array1);
    const setB = new Set(array2);

    const unique = [
        ...array1.filter(x => !setB.has(x))
    ];
    return unique;
}

checker();

setInterval(function () {
    checker(0);
}, 8000);

const notificationBell = document.getElementById('notificationBell');
const notificationCount = document.getElementById('notificationCount');

// Function to handle notification click
notificationBell.addEventListener('click', function (e) {
    saveNotif();
    checker();
    // You can add custom logic here
    // For example: mark notifications as read, fetch new notifications, etc.

    // Example: Simulate marking all as read and updating count
    // Uncomment the code below to test updating the badge number
    /*
    if (notificationCount.textContent !== '0') {
        notificationCount.textContent = '0';
        notificationCount.classList.remove('bg-danger');
        notificationCount.classList.add('bg-secondary');
    } else {
        notificationCount.textContent = '3';
        notificationCount.classList.remove('bg-secondary');
        notificationCount.classList.add('bg-danger');
    }
    */
});

// Example function to update notification count dynamically
window.updateNotificationCount = function (newCount) {
    const badge = document.getElementById('notificationCount');
    if (badge) {
        badge.textContent = newCount;
        if (parseInt(newCount) === 0) {
            badge.classList.remove('bg-danger');
            badge.classList.add('bg-secondary');
        } else {
            badge.classList.remove('bg-secondary');
            badge.classList.add('bg-danger');
        }
    }
};

// Example: Simulate receiving a new notification
// You can call this function when new notifications arrive
/*
setTimeout(() => {
    window.updateNotificationCount(5);
}, 5000);
*/