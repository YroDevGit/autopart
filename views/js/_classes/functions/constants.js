

export function orderStatus() {
    return {
        0: "Pending",
        1: "Accepted",
        2: "Out For Delivery",
        3: "Delivered",
        7: "Rejected",
        8: "Refund",
        11: "Walk-in"
    }
}

export function forRiderOrders() {
    return [2, 3, 7];
}

export function orderStatusName(statusId) {
    let statusClass = "";
    let statusText = "";
    if (statusId === 0) { statusClass = 'status-pending'; statusText = "Pending" }
    else if (statusId === 1) { statusClass = 'status-accepted'; statusText = "Accepted"; }
    else if (statusId === 2) { statusClass = 'status-out-for-delivery'; statusText = "Out for delivery"; }
    else if (statusId === 3) { statusClass = 'status-delivered'; statusText = "Delivered"; }
    else if (statusId === 7) { statusClass = 'status-rejected'; statusText = "Cancelled" }
    else if (statusId === 8) { statusClass = 'status-accepted'; statusText = "Refunded" }
    else if (statusId === 11) { statusClass = 'status-walkin'; statusText = "Walk-In" }
    else{ statusClass = 'none'; statusText = "None"}

    return {statusText: statusText, statusClass: statusClass};
}

export function generateProductCode(id){
    return `B${id}KYG${parseInt(id)+2}`
}