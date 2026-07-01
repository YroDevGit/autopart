// ========================================
// DYNAMIC YEAR GENERATION
// ========================================

function populateYearSelectors() {
    const currentYear = new Date().getFullYear();
    const startYear = 2020;
    const years = [];
    
    // Generate years from startYear to currentYear + 2 (to allow future years)
    for (let year = startYear; year <= currentYear + 2; year++) {
        years.push(year);
    }
    
    // Sort years in descending order (newest first)
    years.sort((a, b) => b - a);
    
    // Populate yearSelect
    const yearSelect = document.getElementById('yearSelect');
    yearSelect.innerHTML = '';
    years.forEach(year => {
        const option = document.createElement('option');
        option.value = year;
        option.textContent = year;
        if (year === currentYear) {
            option.selected = true;
        }
        yearSelect.appendChild(option);
    });
    
    // Populate currentYearSelect
    const currentYearSelect = document.getElementById('currentYearSelect');
    currentYearSelect.innerHTML = '';
    years.forEach(year => {
        const option = document.createElement('option');
        option.value = year;
        option.textContent = year;
        if (year === currentYear) {
            option.selected = true;
        }
        currentYearSelect.appendChild(option);
    });
    
    // Populate compareYearSelect (exclude current year, default to previous year)
    const compareYearSelect = document.getElementById('compareYearSelect');
    compareYearSelect.innerHTML = '';
    years.forEach(year => {
        if (year !== currentYear) {
            const option = document.createElement('option');
            option.value = year;
            option.textContent = year;
            if (year === currentYear - 1) {
                option.selected = true;
            }
            compareYearSelect.appendChild(option);
        }
    });
}

// ========================================
// DATE DISPLAY
// ========================================

function updateDate() {
    const now = new Date();
    const options = { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' };
    document.getElementById('analyticsDate').innerText = now.toLocaleDateString('en-US', options);
}
updateDate();

// ========================================
// MOCK DATA
// ========================================

// Monthly revenue data (12 months) - expanded for more years
const monthlyRevenueData = {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    '2020': [5500, 6100, 4900, 7200, 8500, 7800, 9200, 8500, 9800, 10500, 11500, 12500],
    '2021': [6200, 6800, 5500, 8200, 9500, 8800, 10500, 9800, 11200, 12000, 13000, 14000],
    '2022': [6800, 7500, 6200, 8900, 10200, 9500, 11500, 10500, 12500, 13200, 14200, 15200],
    '2023': [6500, 7200, 5800, 8200, 9800, 9200, 10800, 9800, 11500, 12200, 13500, 14200],
    '2024': [7200, 8100, 6900, 9500, 11800, 10800, 12500, 11500, 13800, 14500, 15800, 16500],
    '2025': [8500, 9200, 7800, 11200, 13500, 12450, 14500, 13200, 15600, 16800, 18200, 19500],
    '2026': [9000, 9800, 8200, 11800, 14200, 13000, 15200, 14000, 16200, 17500, 19000, 20500],
    '2027': [9500, 10500, 8800, 12500, 15000, 13800, 16000, 14800, 17200, 18500, 20000, 21500]
};

// Order status distribution
const statusData = {
    labels: ['Pending', 'Accepted', 'Out for Delivery', 'Delivered', 'Rejected'],
    values: [12, 18, 8, 35, 5],
    colors: ['#ffc107', '#198754', '#0dcaf0', '#00a896', '#dc3545']
};

// Top products
const topProductsData = {
    labels: ['Brake Pads', 'Oil Filter', 'Spark Plugs', 'Motor Oil', 'Air Filter', 'Battery', 'Wiper Blades'],
    values: [45, 38, 30, 25, 20, 15, 12],
    colors: ['#00a896', '#0d6efd', '#ffc107', '#6f42c1', '#0dcaf0', '#dc3545', '#fd7e14']
};

// Customer segmentation
const segmentationData = {
    labels: ['New Customers', 'Returning', 'Frequent Buyers', 'VIP', 'Inactive'],
    values: [23, 55, 32, 18, 28],
    colors: ['#00a896', '#0d6efd', '#ffc107', '#6f42c1', '#dc3545']
};

// Month comparison data (daily breakdown) - expanded for more months
const monthComparisonData = {
    '2025-06': [320, 450, 280, 520, 680, 750, 420, 580, 490, 620, 710, 480, 350, 460, 590, 820, 910, 560, 480, 720, 650, 580, 490, 620, 710, 850, 920, 680, 550, 490],
    '2024-06': [280, 390, 240, 460, 590, 650, 360, 500, 420, 540, 620, 410, 300, 400, 510, 710, 790, 480, 420, 630, 560, 500, 420, 540, 620, 740, 800, 590, 480, 420],
    '2025-05': [300, 420, 260, 490, 620, 680, 380, 530, 450, 580, 660, 440, 320, 420, 540, 750, 840, 510, 440, 650, 580, 520, 440, 560, 650, 780, 850, 620, 500, 440],
    '2024-05': [250, 360, 220, 420, 540, 590, 320, 450, 380, 490, 570, 370, 270, 360, 470, 650, 720, 440, 380, 570, 510, 450, 380, 490, 570, 680, 730, 540, 430, 380]
};

// ========================================
// CHARTS
// ========================================

const colors = {
    green: '#00a896',
    greenLight: '#00a89630',
    blue: '#0d6efd',
    blueLight: '#0d6efd30',
    yellow: '#ffc107',
    yellowLight: '#ffc10730',
    red: '#dc3545',
    redLight: '#dc354530',
    purple: '#6f42c1',
    purpleLight: '#6f42c130',
    cyan: '#0dcaf0',
    cyanLight: '#0dcaf030',
    orange: '#fd7e14',
    orangeLight: '#fd7e1430'
};

// Chart instances to update
let revenueChart, statusChart, topProductsChart, segmentationChart;
let month1Chart, month2Chart;

// 1. Revenue Chart (Line)
function initRevenueChart(year = '2025') {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    if (revenueChart) {
        revenueChart.destroy();
    }
    
    const data = monthlyRevenueData[year] || monthlyRevenueData['2025'];
    
    revenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: monthlyRevenueData.labels,
            datasets: [{
                label: 'Revenue (₱)',
                data: data,
                borderColor: colors.green,
                backgroundColor: colors.greenLight,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: colors.green,
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return '₱' + context.parsed.y.toFixed(2);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) { return '₱' + value; }
                    }
                }
            }
        }
    });
}

// 2. Status Chart (Doughnut)
function initStatusChart() {
    const ctx = document.getElementById('statusChart').getContext('2d');
    if (statusChart) {
        statusChart.destroy();
    }
    
    statusChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: statusData.labels,
            datasets: [{
                data: statusData.values,
                backgroundColor: statusData.colors,
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        boxWidth: 12,
                        padding: 8,
                        font: { size: 10 }
                    }
                }
            },
            cutout: '60%'
        }
    });
}

// 3. Top Products Chart (Horizontal Bar)
function initTopProductsChart() {
    const ctx = document.getElementById('topProductsChart').getContext('2d');
    if (topProductsChart) {
        topProductsChart.destroy();
    }
    
    topProductsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: topProductsData.labels,
            datasets: [{
                label: 'Units Sold',
                data: topProductsData.values,
                backgroundColor: topProductsData.colors,
                borderWidth: 0,
                borderRadius: 4
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: { beginAtZero: true }
            }
        }
    });
}

// 4. Segmentation Chart (Doughnut)
function initSegmentationChart() {
    const ctx = document.getElementById('segmentationChart').getContext('2d');
    if (segmentationChart) {
        segmentationChart.destroy();
    }
    
    segmentationChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: segmentationData.labels,
            datasets: [{
                data: segmentationData.values,
                backgroundColor: segmentationData.colors,
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 8,
                        font: { size: 10 }
                    }
                }
            },
            cutout: '55%'
        }
    });
}

// 5. Month Comparison Charts
function initMonthComparison(month1 = '2025-06', month2 = '2024-06') {
    const data1 = monthComparisonData[month1] || monthComparisonData['2025-06'];
    const data2 = monthComparisonData[month2] || monthComparisonData['2024-06'];
    
    const days = Array.from({length: data1.length}, (_, i) => `Day ${i+1}`);
    
    // Month 1 Chart
    const ctx1 = document.getElementById('month1Chart').getContext('2d');
    if (month1Chart) {
        month1Chart.destroy();
    }
    
    month1Chart = new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: days,
            datasets: [{
                label: 'Daily Sales (₱)',
                data: data1,
                backgroundColor: colors.greenLight,
                borderColor: colors.green,
                borderWidth: 1,
                borderRadius: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return '₱' + context.parsed.y.toFixed(2);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) { return '₱' + value; }
                    }
                },
                x: {
                    ticks: { maxTicksLimit: 10 }
                }
            }
        }
    });
    
    // Month 2 Chart
    const ctx2 = document.getElementById('month2Chart').getContext('2d');
    if (month2Chart) {
        month2Chart.destroy();
    }
    
    month2Chart = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: days,
            datasets: [{
                label: 'Daily Sales (₱)',
                data: data2,
                backgroundColor: colors.blueLight,
                borderColor: colors.blue,
                borderWidth: 1,
                borderRadius: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return '₱' + context.parsed.y.toFixed(2);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) { return '₱' + value; }
                    }
                },
                x: {
                    ticks: { maxTicksLimit: 10 }
                }
            }
        }
    });
    
    // Update labels
    document.getElementById('month1Label').innerText = formatMonth(month1);
    document.getElementById('month2Label').innerText = formatMonth(month2);
}

// Helper: Format month string
function formatMonth(monthStr) {
    const parts = monthStr.split('-');
    const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    return months[parseInt(parts[1]) - 1] + ' ' + parts[0];
}

// Update statistics
function updateStats(year = '2025') {
    const data = monthlyRevenueData[year] || monthlyRevenueData['2025'];
    const totalRevenue = data.reduce((a, b) => a + b, 0);
    const totalOrders = 78; // Mock
    const totalCustomers = 156; // Mock
    const avgOrderValue = totalRevenue / totalOrders;
    
    document.getElementById('totalRevenue').innerText = '₱' + totalRevenue.toFixed(2);
    document.getElementById('totalOrders').innerText = totalOrders;
    document.getElementById('totalCustomers').innerText = totalCustomers;
    document.getElementById('avgOrderValue').innerText = '₱' + avgOrderValue.toFixed(2);
    
    // Update trends (mock)
    document.getElementById('revenueTrend').innerHTML = '<i class="bi bi-arrow-up"></i> +12.5%';
    document.getElementById('ordersTrend').innerHTML = '<i class="bi bi-arrow-up"></i> +8.3%';
    document.getElementById('customersTrend').innerHTML = '<i class="bi bi-arrow-up"></i> +5.2%';
    document.getElementById('avgTrend').innerHTML = '<i class="bi bi-arrow-up"></i> +3.7%';
}

// Update comparison section
function updateComparison(year1 = '2025', year2 = '2024') {
    const data1 = monthlyRevenueData[year1] || monthlyRevenueData['2025'];
    const data2 = monthlyRevenueData[year2] || monthlyRevenueData['2024'];
    
    const revenue1 = data1.reduce((a, b) => a + b, 0);
    const revenue2 = data2.reduce((a, b) => a + b, 0);
    const diff = ((revenue1 - revenue2) / revenue2 * 100);
    
    const orders1 = 78;
    const orders2 = 62;
    const ordersDiff = ((orders1 - orders2) / orders2 * 100);
    
    const avg1 = revenue1 / orders1;
    const avg2 = revenue2 / orders2;
    const avgDiff = ((avg1 - avg2) / avg2 * 100);
    
    document.getElementById('compareRevenue').innerText = '₱' + revenue1.toFixed(2);
    document.getElementById('compareOrders').innerText = orders1;
    document.getElementById('compareAvg').innerText = '₱' + avg1.toFixed(2);
    
    document.getElementById('compareRevenueDiff').innerHTML = 
        (diff > 0 ? '<i class="bi bi-arrow-up"></i> +' : '<i class="bi bi-arrow-down"></i> ') + diff.toFixed(1) + '%';
    document.getElementById('compareRevenueDiff').className = 'diff ' + (diff > 0 ? 'positive' : 'negative');
    
    document.getElementById('compareOrdersDiff').innerHTML = 
        (ordersDiff > 0 ? '<i class="bi bi-arrow-up"></i> +' : '<i class="bi bi-arrow-down"></i> ') + ordersDiff.toFixed(1) + '%';
    document.getElementById('compareOrdersDiff').className = 'diff ' + (ordersDiff > 0 ? 'positive' : 'negative');
    
    document.getElementById('compareAvgDiff').innerHTML = 
        (avgDiff > 0 ? '<i class="bi bi-arrow-up"></i> +' : '<i class="bi bi-arrow-down"></i> ') + avgDiff.toFixed(1) + '%';
    document.getElementById('compareAvgDiff').className = 'diff ' + (avgDiff > 0 ? 'positive' : 'negative');
}

// ========================================
// EVENT LISTENERS
// ========================================

// Apply Filters
document.getElementById('applyFilterBtn').addEventListener('click', function() {
    const year = document.getElementById('yearSelect').value;
    updateStats(year);
    initRevenueChart(year);
    showToast('Analytics updated for ' + year, 'success');
});

// Compare Years
document.getElementById('currentYearSelect').addEventListener('change', function() {
    const year1 = this.value;
    const year2 = document.getElementById('compareYearSelect').value;
    updateComparison(year1, year2);
});

document.getElementById('compareYearSelect').addEventListener('change', function() {
    const year2 = this.value;
    const year1 = document.getElementById('currentYearSelect').value;
    updateComparison(year1, year2);
});

// Compare Months
document.getElementById('compareMonthsBtn').addEventListener('click', function() {
    const month1 = document.getElementById('month1Select').value;
    const month2 = document.getElementById('month2Select').value;
    if (month1 && month2) {
        initMonthComparison(month1, month2);
        showToast('Comparing ' + formatMonth(month1) + ' vs ' + formatMonth(month2), 'success');
    } else {
        showToast('Please select both months to compare', 'warning');
    }
});

// Toast function
function showToast(msg, type = 'success') {
    let toastEl = document.getElementById('analyticsToast');
    if (!toastEl) {
        toastEl = document.createElement('div');
        toastEl.id = 'analyticsToast';
        toastEl.className = 'toast align-items-center text-white bg-success border-0 position-fixed bottom-0 end-0 p-3';
        toastEl.style.zIndex = '1100';
        toastEl.innerHTML = `
            <div class="d-flex">
                <div class="toast-body" id="toastMsg">✅ Action completed!</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        document.body.appendChild(toastEl);
    }
    
    const toastMsg = document.getElementById('toastMsg');
    toastMsg.innerText = msg;
    toastEl.classList.remove('bg-success', 'bg-danger', 'bg-warning');
    if (type === 'success') toastEl.classList.add('bg-success');
    else if (type === 'danger') toastEl.classList.add('bg-danger');
    else toastEl.classList.add('bg-warning');
    const bsToast = new bootstrap.Toast(toastEl, { delay: 2000 });
    bsToast.show();
}

// ========================================
// INITIALIZE
// ========================================

// Populate year selectors dynamically
populateYearSelectors();

// Get current year and month
const currentYear = new Date().getFullYear();
const currentMonth = String(currentYear) + '-' + String(new Date().getMonth() + 1).padStart(2, '0');
const previousMonth = String(currentYear - 1) + '-' + String(new Date().getMonth() + 1).padStart(2, '0');

// Set default month selects
document.getElementById('month1Select').value = currentMonth;
document.getElementById('month2Select').value = previousMonth;

// Initialize all charts
initRevenueChart(currentYear);
initStatusChart();
initTopProductsChart();
initSegmentationChart();
initMonthComparison(currentMonth, previousMonth);

updateStats(currentYear);
updateComparison(currentYear, currentYear - 1);