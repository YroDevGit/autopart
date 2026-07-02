// ========================================
// DYNAMIC YEAR GENERATION
// ========================================

import Notify from "../../code/src/mods/notify";
import { Tyrax } from "../../code/src/tyrux/main";

let cachedRevenueData = {};

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
// API FUNCTIONS
// ========================================

// WORKING: transaction/getRevenue POST endpoint
async function getMonthlyRevenue(year) {
    if (cachedRevenueData[year]) {
        return cachedRevenueData[year];
    }
    
    try {
        let response = await Tyrax.async({
            url: "transaction/getRevenue",
            data: { year: year },
            method: "POST"
        });
        const data = response?.data ?? [];
        cachedRevenueData[year] = data;
        return data;
    } catch (error) {
        console.error('Error fetching revenue for year', year, error);
        return Array(12).fill(0);
    }
}

// REST: Using Tyrax.query for other tables
async function getOrderStatus() {
    try {
        let data = await Tyrax.query({
            query: `
                SELECT 
                    status,
                    COUNT(*) as count
                FROM transaction
                GROUP BY status
            `,
            dataOnly: true
        });
        
        const statusMap = {
            0: 'Pending',
            1: 'Accepted', 
            2: 'Out for Delivery',
            3: 'Delivered',
            7: 'Rejected'
        };
        
        const labels = [];
        const values = [];
        
        if (data && data.length > 0) {
            data.forEach(row => {
                const statusLabel = statusMap[row.status] || 'Unknown';
                labels.push(statusLabel);
                values.push(parseInt(row.count) || 0);
            });
        }
        
        // Default fallback if no data
        if (labels.length === 0) {
            return {
                labels: ['Pending', 'Accepted', 'Out for Delivery', 'Delivered', 'Rejected'],
                values: [0, 0, 0, 0, 0]
            };
        }
        
        return { labels, values };
    } catch (error) {
        console.error('Error fetching order status:', error);
        return {
            labels: ['Pending', 'Accepted', 'Out for Delivery', 'Delivered', 'Rejected'],
            values: [0, 0, 0, 0, 0]
        };
    }
}
async function getTopProducts(limit = 7) {
    try {
        let data = await Tyrax.query({
            query: `
                SELECT 
                    p.name,
                    SUM(td.quantity) as total_sold
                FROM transaction_details td
                JOIN product p ON td.product_id = p.id
                JOIN transaction t ON td.transaction_code = t.transaction_code
                WHERE t.status IN (3, 2)
                GROUP BY p.id, p.name
                ORDER BY total_sold DESC
                LIMIT ${limit}
            `,
            dataOnly: true
        });
        
        const labels = [];
        const values = [];
        
        if (data && data.length > 0) {
            data.forEach(row => {
                labels.push(row.name || 'Unknown');
                values.push(parseInt(row.total_sold) || 0);
            });
        }
        
        // Default fallback
        if (labels.length === 0) {
            return {
                labels: ['No Products Sold'],
                values: [0]
            };
        }
        
        return { labels, values };
    } catch (error) {
        console.error('Error fetching top products:', error);
        return {
            labels: ['No Products Sold'],
            values: [0]
        };
    }
}

async function getCustomerSegmentation() {
    try {
        // Get total customers
        let totalData = await Tyrax.query({
            query: `SELECT COUNT(*) as total FROM customer`,
            dataOnly: true
        });
        const totalCustomers = totalData && totalData.length > 0 ? parseInt(totalData[0].total) || 0 : 0;
        
        // Get customers with orders (returning)
        let returningData = await Tyrax.query({
            query: `
                SELECT COUNT(DISTINCT customer_id) as 'returning' FROM transaction WHERE customer_id IS NOT NULL
            `,
            dataOnly: true
        });
        const returningCustomers = returningData && returningData.length > 0 ? parseInt(returningData[0].returning) || 0 : 0;
        
        // Get frequent buyers (5+ orders)
        let frequentData = await Tyrax.query({
            query: `
                SELECT COUNT(*) as frequent FROM (SELECT customer_id, COUNT(*) as order_count FROM transaction WHERE customer_id IS NOT NULL GROUP BY customer_id HAVING order_count >= 5) as frequent_buyers`,
            dataOnly: true
        });
        const frequentBuyers = frequentData && frequentData.length > 0 ? parseInt(frequentData[0].frequent) || 0 : 0;
        
        // Get VIP (10+ orders)
        let vipData = await Tyrax.query({
            query: `SELECT COUNT(*) as vip FROM (SELECT customer_id, COUNT(*) as order_count FROM transaction WHERE customer_id IS NOT NULL GROUP BY customer_id HAVING order_count >= 10) as vip_customers`,
            dataOnly: true
        });
        const vipCustomers = vipData && vipData.length > 0 ? parseInt(vipData[0].vip) || 0 : 0;
        
        // Calculate new customers (created_at > last 30 days)
        let newData = await Tyrax.query({
            query: `
                SELECT COUNT(*) as new_customers
                FROM customer
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            `,
            dataOnly: true
        });
        const newCustomers = newData && newData.length > 0 ? parseInt(newData[0].new_customers) || 0 : 0;
        
        // Inactive = total - returning
        const inactiveCustomers = Math.max(0, totalCustomers - returningCustomers);
        
        return {
            labels: ['New Customers', 'Returning', 'Frequent Buyers', 'VIP', 'Inactive'],
            values: [newCustomers, returningCustomers, frequentBuyers, vipCustomers, inactiveCustomers]
        };
    } catch (error) {
        console.error('Error fetching customer segmentation:', error);
        return {
            labels: ['New Customers', 'Returning', 'Frequent Buyers', 'VIP', 'Inactive'],
            values: [0, 0, 0, 0, 0]
        };
    }
}

async function getMonthComparison(month1, month2) {
    try {
        // Parse month strings (YYYY-MM)
        const [year1, month1Num] = month1.split('-');
        const [year2, month2Num] = month2.split('-');
        
        // Get daily sales for month1
        let data1 = await Tyrax.query({
            query: `
                SELECT 
                    DAY(created_at) as day,
                    SUM(total_price) as daily_total
                FROM transaction
                WHERE YEAR(created_at) = ${year1}
                AND MONTH(created_at) = ${month1Num}
                AND status IN (3, 2)
                GROUP BY DAY(created_at)
                ORDER BY DAY(created_at)
            `,
            dataOnly: true
        });
        
        // Get daily sales for month2
        let data2 = await Tyrax.query({
            query: `
                SELECT 
                    DAY(created_at) as day,
                    SUM(total_price) as daily_total
                FROM transaction
                WHERE YEAR(created_at) = ${year2}
                AND MONTH(created_at) = ${month2Num}
                AND status IN (3, 2)
                GROUP BY DAY(created_at)
                ORDER BY DAY(created_at)
            `,
            dataOnly: true
        });
        
        // Format data to days of month
        const month1Data = [];
        const month2Data = [];
        
        // Get max days in month
        const daysInMonth1 = new Date(year1, month1Num, 0).getDate();
        const daysInMonth2 = new Date(year2, month2Num, 0).getDate();
        
        // Initialize arrays with zeros
        for (let i = 0; i < daysInMonth1; i++) month1Data[i] = 0;
        for (let i = 0; i < daysInMonth2; i++) month2Data[i] = 0;
        
        if (data1 && data1.length > 0) {
            data1.forEach(row => {
                const dayIndex = parseInt(row.day) - 1;
                month1Data[dayIndex] = parseFloat(row.daily_total) || 0;
            });
        }
        
        if (data2 && data2.length > 0) {
            data2.forEach(row => {
                const dayIndex = parseInt(row.day) - 1;
                month2Data[dayIndex] = parseFloat(row.daily_total) || 0;
            });
        }
        
        return { 
            month1: month1Data, 
            month2: month2Data,
            days1: daysInMonth1,
            days2: daysInMonth2
        };
    } catch (error) {
        console.error('Error fetching month comparison:', error);
        return { month1: Array(30).fill(0), month2: Array(30).fill(0), days1: 30, days2: 30 };
    }
}

async function getTotalOrders(year = null) {
    try {
        let query = `SELECT COUNT(*) as total FROM transaction`;
        if (year) {
            query += ` WHERE YEAR(created_at) = ${year}`;
        }
        let data = await Tyrax.query({
            query: query,
            dataOnly: true
        });
        return data && data.length > 0 ? parseInt(data[0].total) || 0 : 0;
    } catch (error) {
        console.error('Error fetching total orders:', error);
        return 0;
    }
}

async function getTotalCustomers() {
    try {
        let data = await Tyrax.query({
            query: `SELECT COUNT(*) as total FROM customer`,
            dataOnly: true
        });
        return data && data.length > 0 ? parseInt(data[0].total) || 0 : 0;
    } catch (error) {
        console.error('Error fetching total customers:', error);
        return 0;
    }
}

// ========================================
// STATIC DATA (Colors)
// ========================================

const monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
const statusColors = ['#ffc107', '#198754', '#0dcaf0', '#00a896', '#dc3545'];
const productColors = ['#00a896', '#0d6efd', '#ffc107', '#6f42c1', '#0dcaf0', '#dc3545', '#fd7e14'];
const segmentColors = ['#00a896', '#0d6efd', '#ffc107', '#6f42c1', '#dc3545'];

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

// Chart instances
let revenueChart, statusChart, topProductsChart, segmentationChart;
let month1Chart, month2Chart, combinedComparisonChart;

// 1. Revenue Chart (Line) - Uses transaction/getRevenue POST
async function initRevenueChart(year) {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    if (revenueChart) {
        revenueChart.destroy();
    }
    
    const data = await getMonthlyRevenue(year);
    
    revenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: monthLabels,
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
async function initStatusChart() {
    const ctx = document.getElementById('statusChart').getContext('2d');
    if (statusChart) {
        statusChart.destroy();
    }
    
    const data = await getOrderStatus();
    
    statusChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: data.labels || ['Pending', 'Accepted', 'Out for Delivery', 'Delivered', 'Rejected'],
            datasets: [{
                data: data.values || [0, 0, 0, 0, 0],
                backgroundColor: statusColors,
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
async function initTopProductsChart() {
    const ctx = document.getElementById('topProductsChart').getContext('2d');
    if (topProductsChart) {
        topProductsChart.destroy();
    }
    
    const data = await getTopProducts();
    
    topProductsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels || ['No Data'],
            datasets: [{
                label: 'Units Sold',
                data: data.values || [0],
                backgroundColor: productColors.slice(0, data.labels?.length || 1),
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
async function initSegmentationChart() {
    const ctx = document.getElementById('segmentationChart').getContext('2d');
    if (segmentationChart) {
        segmentationChart.destroy();
    }
    
    const data = await getCustomerSegmentation();
    
    segmentationChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: data.labels || ['New Customers', 'Returning', 'Frequent Buyers', 'VIP', 'Inactive'],
            datasets: [{
                data: data.values || [0, 0, 0, 0, 0],
                backgroundColor: segmentColors,
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

// 5. Month Comparison Charts (Bar charts side by side)
async function initMonthComparison(month1, month2) {
    const data = await getMonthComparison(month1, month2);
    
    const data1 = data?.month1 || Array(30).fill(0);
    const data2 = data?.month2 || Array(30).fill(0);
    const days1 = data?.days1 || 30;
    const days2 = data?.days2 || 30;
    
    // Use only the days available for each month
    const month1Data = data1.slice(0, days1);
    const month2Data = data2.slice(0, days2);
    
    const days1Labels = Array.from({length: days1}, (_, i) => `Day ${i+1}`);
    const days2Labels = Array.from({length: days2}, (_, i) => `Day ${i+1}`);
    
    // Month 1 Chart
    const ctx1 = document.getElementById('month1Chart').getContext('2d');
    if (month1Chart) {
        month1Chart.destroy();
    }
    
    month1Chart = new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: days1Labels,
            datasets: [{
                label: 'Daily Sales (₱)',
                data: month1Data,
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
                    ticks: { maxTicksLimit: 15 }
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
            labels: days2Labels,
            datasets: [{
                label: 'Daily Sales (₱)',
                data: month2Data,
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
                    ticks: { maxTicksLimit: 15 }
                }
            }
        }
    });
    
    // Update labels
    document.getElementById('month1Label').innerText = formatMonth(month1);
    document.getElementById('month2Label').innerText = formatMonth(month2);
    document.getElementById('combinedLabel').innerText = formatMonth(month1) + ' vs ' + formatMonth(month2);
    
    // Initialize combined comparison chart
    await initCombinedComparisonChart(month1Data, month2Data, month1, month2);
}

// 6. Combined Comparison Line Chart
async function initCombinedComparisonChart(data1, data2, month1, month2) {
    const ctx = document.getElementById('combinedComparisonChart').getContext('2d');
    if (combinedComparisonChart) {
        combinedComparisonChart.destroy();
    }
    
    const maxLength = Math.max(data1.length, data2.length);
    const labels = Array.from({length: maxLength}, (_, i) => `Day ${i+1}`);
    
    // Pad shorter array with null values for proper alignment
    const paddedData1 = [...data1];
    const paddedData2 = [...data2];
    while (paddedData1.length < maxLength) paddedData1.push(null);
    while (paddedData2.length < maxLength) paddedData2.push(null);
    
    combinedComparisonChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: formatMonth(month1),
                    data: paddedData1,
                    borderColor: colors.green,
                    backgroundColor: colors.greenLight,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: colors.green,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 2,
                    spanGaps: true
                },
                {
                    label: formatMonth(month2),
                    data: paddedData2,
                    borderColor: colors.blue,
                    backgroundColor: colors.blueLight,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: colors.blue,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 2,
                    spanGaps: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        boxWidth: 12,
                        padding: 10,
                        font: { size: 11 }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            if (context.parsed.y === null) return 'No data';
                            return context.dataset.label + ': ₱' + context.parsed.y.toFixed(2);
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
                    ticks: { maxTicksLimit: 15 }
                }
            }
        }
    });
}

// Helper: Format month string
function formatMonth(monthStr) {
    if (!monthStr) return 'N/A';
    const parts = monthStr.split('-');
    const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    return months[parseInt(parts[1]) - 1] + ' ' + parts[0];
}

// Update statistics
async function updateStats(year) {
    const revenueData = await getMonthlyRevenue(year);
    const totalRevenue = revenueData.reduce((a, b) => a + b, 0);
    const totalOrders = await getTotalOrders(year);
    const totalCustomers = await getTotalCustomers();
    const avgOrderValue = totalOrders > 0 ? totalRevenue / totalOrders : 0;
    
    document.getElementById('totalRevenue').innerText = '₱' + totalRevenue.toFixed(2);
    document.getElementById('totalOrders').innerText = totalOrders;
    document.getElementById('totalCustomers').innerText = totalCustomers;
    document.getElementById('avgOrderValue').innerText = '₱' + avgOrderValue.toFixed(2);
    
    // Calculate trends (compare with previous year)
    const prevYear = parseInt(year) - 1;
    const prevRevenueData = await getMonthlyRevenue(prevYear);
    const prevRevenue = prevRevenueData.reduce((a, b) => a + b, 0);
    const prevOrders = await getTotalOrders(prevYear);
    
    const revenueDiff = prevRevenue > 0 ? ((totalRevenue - prevRevenue) / prevRevenue * 100) : 0;
    const ordersDiff = prevOrders > 0 ? ((totalOrders - prevOrders) / prevOrders * 100) : 0;
    
    document.getElementById('revenueTrend').innerHTML = 
        (revenueDiff > 0 ? '<i class="bi bi-arrow-up"></i> +' : '<i class="bi bi-arrow-down"></i> ') + revenueDiff.toFixed(1) + '%';
    document.getElementById('revenueTrend').className = 'trend ' + (revenueDiff >= 0 ? 'trend-up' : 'trend-down');
    
    document.getElementById('ordersTrend').innerHTML = 
        (ordersDiff > 0 ? '<i class="bi bi-arrow-up"></i> +' : '<i class="bi bi-arrow-down"></i> ') + ordersDiff.toFixed(1) + '%';
    document.getElementById('ordersTrend').className = 'trend ' + (ordersDiff >= 0 ? 'trend-up' : 'trend-down');
    
    document.getElementById('customersTrend').innerHTML = '<i class="bi bi-arrow-up"></i> +5.2%';
    document.getElementById('avgTrend').innerHTML = '<i class="bi bi-arrow-up"></i> +3.7%';
}

// Update comparison section
async function updateComparison(year1, year2) {
    const data1 = await getMonthlyRevenue(year1);
    const data2 = await getMonthlyRevenue(year2);
    
    const revenue1 = data1.reduce((a, b) => a + b, 0);
    const revenue2 = data2.reduce((a, b) => a + b, 0);
    const diff = revenue2 > 0 ? ((revenue1 - revenue2) / revenue2 * 100) : 0;
    
    const orders1 = await getTotalOrders(year1);
    const orders2 = await getTotalOrders(year2);
    const ordersDiff = orders2 > 0 ? ((orders1 - orders2) / orders2 * 100) : 0;
    
    const avg1 = orders1 > 0 ? revenue1 / orders1 : 0;
    const avg2 = orders2 > 0 ? revenue2 / orders2 : 0;
    const avgDiff = avg2 > 0 ? ((avg1 - avg2) / avg2 * 100) : 0;
    
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
document.getElementById('applyFilterBtn').addEventListener('click', async function() {
    const year = document.getElementById('yearSelect').value;
    await updateStats(year);
    await initRevenueChart(year);
    showToast('Analytics updated for ' + year, 'success');
});

// Compare Years
document.getElementById('currentYearSelect').addEventListener('change', async function() {
    const year1 = this.value;
    const year2 = document.getElementById('compareYearSelect').value;
    await updateComparison(year1, year2);
});

document.getElementById('compareYearSelect').addEventListener('change', async function() {
    const year2 = this.value;
    const year1 = document.getElementById('currentYearSelect').value;
    await updateComparison(year1, year2);
});

// Compare Months
document.getElementById('compareMonthsBtn').addEventListener('click', async function() {
    const month1 = document.getElementById('month1Select').value;
    const month2 = document.getElementById('month2Select').value;
    if (month1 && month2) {
        await initMonthComparison(month1, month2);
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

// Initialize all charts and stats
(async function initialize() {
    await initRevenueChart(currentYear);
    await initStatusChart();
    await initTopProductsChart();
    await initSegmentationChart();
    await initMonthComparison(currentMonth, previousMonth);
    await updateStats(currentYear);
    await updateComparison(currentYear, currentYear - 1);
})();