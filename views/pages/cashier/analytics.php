<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
  <title>AutoParts Admin | Analytics</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?=assets('cashier.css')?>">
  <!-- Chart.js for charts -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <style>
    /* Analytics Custom Styles */
    .analytics-stat-card {
      background: white;
      border-radius: 1rem;
      padding: 1.25rem;
      text-align: center;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
      border: 1px solid #e9ecef;
      transition: transform 0.2s, box-shadow 0.2s;
      height: 100%;
    }
    
    .analytics-stat-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }
    
    .analytics-stat-card .icon {
      font-size: 2.5rem;
      margin-bottom: 0.5rem;
    }
    
    .analytics-stat-card .number {
      font-size: 2rem;
      font-weight: 700;
    }
    
    .analytics-stat-card .label {
      font-size: 0.85rem;
      color: #6c757d;
      margin-top: 0.25rem;
    }
    
    .analytics-stat-card .trend {
      font-size: 0.75rem;
      font-weight: 600;
    }
    
    .trend-up { color: #00a896; }
    .trend-down { color: #dc3545; }
    .trend-neutral { color: #ffc107; }
    
    .chart-container {
      background: white;
      border-radius: 1rem;
      padding: 1.25rem;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
      border: 1px solid #e9ecef;
      height: 100%;
    }
    
    .chart-container .chart-title {
      font-size: 1rem;
      font-weight: 600;
      margin-bottom: 1rem;
      color: #2c3e50;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    
    .chart-container .chart-title .badge {
      font-size: 0.7rem;
      font-weight: 400;
    }
    
    .chart-container canvas {
      max-height: 280px;
      max-width: 100%;
    }
    
    .analytics-welcome {
      background: linear-gradient(135deg, #0e2a24 0%, #1a3c34 100%);
      border-radius: 1rem;
      padding: 2rem;
      color: white;
      margin-bottom: 1.5rem;
    }
    
    .analytics-welcome h2 {
      font-weight: 700;
    }
    
    .analytics-welcome .subtitle {
      color: #b8d9d0;
    }
    
    .filter-bar {
      background: white;
      border-radius: 1rem;
      padding: 1rem 1.25rem;
      margin-bottom: 1.5rem;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
      border: 1px solid #e9ecef;
    }
    
    .filter-bar .filter-label {
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      color: #6c757d;
      margin-bottom: 0.25rem;
    }
    
    .insight-box {
      background: #f8f9fa;
      border-radius: 0.75rem;
      padding: 1rem;
      border-left: 4px solid #00a896;
    }
    
    .insight-box .insight-icon {
      font-size: 1.5rem;
      color: #00a896;
    }
    
    .comparison-section {
      background: white;
      border-radius: 1rem;
      padding: 1.5rem;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
      border: 1px solid #e9ecef;
      margin-bottom: 1.5rem;
    }
    
    .comparison-section .section-title {
      font-size: 1.1rem;
      font-weight: 600;
      margin-bottom: 1rem;
    }
    
    .comparison-card {
      background: #f8f9fa;
      border-radius: 0.75rem;
      padding: 1rem;
      text-align: center;
      height: 100%;
    }
    
    .comparison-card .period-label {
      font-size: 0.8rem;
      color: #6c757d;
    }
    
    .comparison-card .value {
      font-size: 1.5rem;
      font-weight: 700;
    }
    
    .comparison-card .diff {
      font-size: 0.85rem;
      font-weight: 600;
    }
    
    .comparison-card .diff.positive { color: #00a896; }
    .comparison-card .diff.negative { color: #dc3545; }
    
    .compare-select {
      max-width: 200px;
    }
    
    .combined-chart-container {
      background: white;
      border-radius: 1rem;
      padding: 1.25rem;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
      border: 1px solid #e9ecef;
      margin-top: 1.5rem;
    }
    
    .combined-chart-container .chart-title {
      font-size: 1rem;
      font-weight: 600;
      margin-bottom: 1rem;
      color: #2c3e50;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    
    .combined-chart-container canvas {
      max-height: 300px;
      max-width: 100%;
    }
    
    @media (max-width: 768px) {
      .analytics-stat-card .number {
        font-size: 1.5rem;
      }
      .analytics-welcome {
        padding: 1.25rem;
      }
      .analytics-welcome h2 {
        font-size: 1.25rem;
      }
      .comparison-card .value {
        font-size: 1.2rem;
      }
      .compare-select {
        max-width: 100%;
      }
      .combined-chart-container canvas {
        max-height: 200px;
      }
    }
  </style>
</head>
<body>

<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<?=include_page("cashier/sidebar")?>

<div class="main-content-wrapper" id="mainContentWrapper">
  <?=include_page('cashier/navbar', ["pagename"=>"Analytics", "icon"=>"bi-bar-chart-steps"])?>

  <div class="content-inner">
    <!-- Welcome Banner -->
    <div class="analytics-welcome">
      <div class="row align-items-center">
        <div class="col-md-8">
          <h2><i class="bi bi-graph-up-arrow text-warning"></i> Analytics Dashboard</h2>
          <p class="subtitle mb-0">Gain insights into your business performance, sales trends, and customer behavior.</p>
        </div>
        <div class="col-md-4 text-md-end">
          <span class="badge bg-success bg-opacity-25 text-white px-4 py-2 rounded-pill">
            <i class="bi bi-calendar-range"></i> <span id="analyticsDate"></span>
          </span>
        </div>
      </div>
    </div>

    <!-- Filter Bar - Year Only -->
    <div class="filter-bar">
      <div class="row g-3 align-items-end">
        <div class="col-md-6 col-8">
          <div class="filter-label"><i class="bi bi-calendar3"></i> Select Year</div>
          <select class="form-select" id="yearSelect">
            <!-- Years will be dynamically generated by JavaScript -->
          </select>
        </div>
        <div class="col-md-3 col-4">
          <button class="btn btn-autoparts-primary w-100" id="applyFilterBtn">
            <i class="bi bi-funnel"></i> Apply
          </button>
        </div>
      </div>
    </div>

    <!-- KPI Cards -->
    <div class="row g-3 mb-4">
      <div class="col-md-3 col-6">
        <div class="analytics-stat-card">
          <div class="icon text-success"><i class="bi bi-currency-dollar"></i></div>
          <div class="number text-success" id="totalRevenue">₱0</div>
          <div class="label">Total Revenue</div>
          <div class="trend trend-up" id="revenueTrend"><i class="bi bi-arrow-up"></i> +12.5%</div>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="analytics-stat-card">
          <div class="icon text-primary"><i class="bi bi-bag"></i></div>
          <div class="number text-primary" id="totalOrders">0</div>
          <div class="label">Total Orders</div>
          <div class="trend trend-up" id="ordersTrend"><i class="bi bi-arrow-up"></i> +8.3%</div>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="analytics-stat-card">
          <div class="icon text-warning"><i class="bi bi-people"></i></div>
          <div class="number text-warning" id="totalCustomers">0</div>
          <div class="label">Active Customers</div>
          <div class="trend trend-up" id="customersTrend"><i class="bi bi-arrow-up"></i> +5.2%</div>
        </div>
      </div>
      <div class="col-md-3 col-6">
        <div class="analytics-stat-card">
          <div class="icon text-info"><i class="bi bi-star"></i></div>
          <div class="number text-info" id="avgOrderValue">₱0</div>
          <div class="label">Average Order Value</div>
          <div class="trend trend-up" id="avgTrend"><i class="bi bi-arrow-up"></i> +3.7%</div>
        </div>
      </div>
    </div>

    <!-- Insight Row -->
    <div class="row g-3 mb-4" style="display: none;">
      <div class="col-md-4">
        <div class="insight-box d-flex align-items-center">
          <div class="insight-icon me-3"><i class="bi bi-lightbulb"></i></div>
          <div>
            <small class="text-muted">Key Insight</small>
            <p class="mb-0 small fw-semibold">Brake Pads are your top-selling product this year with 45 units sold.</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="insight-box d-flex align-items-center">
          <div class="insight-icon me-3"><i class="bi bi-trending-up"></i></div>
          <div>
            <small class="text-muted">Growth Opportunity</small>
            <p class="mb-0 small fw-semibold">Order volume increased by 15% compared to last year.</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="insight-box d-flex align-items-center">
          <div class="insight-icon me-3"><i class="bi bi-exclamation-triangle"></i></div>
          <div>
            <small class="text-muted">Alert</small>
            <p class="mb-0 small fw-semibold">5 orders are pending for more than 3 days.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Comparison Section - Sales Comparison -->
    <div class="comparison-section">
      <div class="section-title"><i class="bi bi-arrow-left-right text-warning"></i> Sales Comparison</div>
      
      <!-- Comparison Selectors -->
      <div class="row g-3 mb-4">
        <div class="col-md-5">
          <div class="filter-label">Current Year Sales</div>
          <select class="form-select compare-select" id="currentYearSelect">
            <!-- Years will be dynamically generated by JavaScript -->
          </select>
        </div>
        <div class="col-md-2 text-center d-flex align-items-center justify-content-center">
          <span class="fs-4 fw-bold text-muted">VS</span>
        </div>
        <div class="col-md-5">
          <div class="filter-label">Compare to Previous Year</div>
          <select class="form-select compare-select" id="compareYearSelect">
            <!-- Years will be dynamically generated by JavaScript -->
          </select>
        </div>
      </div>
      
      <!-- Comparison Results -->
      <div class="row g-3">
        <div class="col-md-4">
          <div class="comparison-card">
            <div class="period-label">Total Revenue</div>
            <div class="value text-success" id="compareRevenue">₱0</div>
            <div class="diff positive" id="compareRevenueDiff"><i class="bi bi-arrow-up"></i> +25.6%</div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="comparison-card">
            <div class="period-label">Total Orders</div>
            <div class="value text-primary" id="compareOrders">0</div>
            <div class="diff positive" id="compareOrdersDiff"><i class="bi bi-arrow-up"></i> +18.3%</div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="comparison-card">
            <div class="period-label">Average Order Value</div>
            <div class="value text-info" id="compareAvg">₱0</div>
            <div class="diff positive" id="compareAvgDiff"><i class="bi bi-arrow-up"></i> +5.8%</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Month-Year Comparison Section -->
    <div class="comparison-section">
      <div class="section-title"><i class="bi bi-calendar-range text-info"></i> Month-to-Month Comparison</div>
      
      <div class="row g-3 align-items-end">
        <div class="col-md-4">
          <div class="filter-label">Select Month-Year 1</div>
          <input type="month" class="form-control" id="month1Select">
        </div>
        <div class="col-md-1 text-center d-flex align-items-center justify-content-center">
          <span class="fs-4 fw-bold text-muted">VS</span>
        </div>
        <div class="col-md-4">
          <div class="filter-label">Select Month-Year 2</div>
          <input type="month" class="form-control" id="month2Select">
        </div>
        <div class="col-md-3">
          <button class="btn btn-autoparts-primary w-100" id="compareMonthsBtn">
            <i class="bi bi-arrow-left-right"></i> Compare
          </button>
        </div>
      </div>
      
      <!-- Month Comparison Results - Side by Side Charts -->
      <div class="row g-3 mt-3">
        <div class="col-md-6">
          <div class="chart-container">
            <div class="chart-title">
              <span><i class="bi bi-bar-chart text-primary"></i> Month 1: <span id="month1Label">June 2025</span></span>
              <span class="badge bg-primary bg-opacity-10 text-primary">Sales</span>
            </div>
            <canvas id="month1Chart"></canvas>
          </div>
        </div>
        <div class="col-md-6">
          <div class="chart-container">
            <div class="chart-title">
              <span><i class="bi bi-bar-chart text-info"></i> Month 2: <span id="month2Label">June 2024</span></span>
              <span class="badge bg-info bg-opacity-10 text-info">Sales</span>
            </div>
            <canvas id="month2Chart"></canvas>
          </div>
        </div>
      </div>
      
      <!-- Combined Comparison Line Chart -->
      <div class="combined-chart-container">
        <div class="chart-title">
          <span><i class="bi bi-graph-up-arrow text-success"></i> Combined Comparison: <span id="combinedLabel">June 2025 vs June 2024</span></span>
          <span class="badge bg-success bg-opacity-10 text-success">Revenue Comparison</span>
        </div>
        <canvas id="combinedComparisonChart"></canvas>
      </div>
    </div>

    <!-- Charts -->
    <div class="row g-3 mb-4">
      <div class="col-lg-8">
        <div class="chart-container">
          <div class="chart-title">
            <span><i class="bi bi-graph-up text-success"></i> Monthly Revenue Trend</span>
            <span class="badge bg-success bg-opacity-10 text-success">Yearly overview</span>
          </div>
          <canvas id="revenueChart"></canvas>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="chart-container">
          <div class="chart-title">
            <span><i class="bi bi-pie-chart text-warning"></i> Order Status</span>
            <span class="badge bg-warning bg-opacity-10 text-warning">Distribution</span>
          </div>
          <canvas id="statusChart"></canvas>
        </div>
      </div>
    </div>

    <div class="row g-3 mb-4">
      <div class="col-lg-6">
        <div class="chart-container">
          <div class="chart-title">
            <span><i class="bi bi-grid text-info"></i> Top Products</span>
            <span class="badge bg-info bg-opacity-10 text-info">Best sellers</span>
          </div>
          <canvas id="topProductsChart"></canvas>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="chart-container">
          <div class="chart-title">
            <span><i class="bi bi-people-fill text-purple"></i> Customer Segmentation</span>
            <span class="badge bg-purple bg-opacity-10 text-purple">Demographics</span>
          </div>
          <canvas id="segmentationChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Footer Stats -->
    <div class="row g-3">
      <div class="col-12">
        <div class="chart-container">
          <div class="chart-title">
            <span><i class="bi bi-table text-secondary"></i> Detailed Statistics</span>
            <span class="badge bg-secondary bg-opacity-10 text-secondary">Summary</span>
          </div>
          <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
              <thead>
                <tr>
                  <th>Metric</th>
                  <th>Current Period</th>
                  <th>Previous Period</th>
                  <th>Change</th>
                </tr>
              </thead>
              <tbody id="statsTableBody">
                <tr><td>Revenue</td><td>₱12,450</td><td>₱10,320</td><td class="text-success"><i class="bi bi-arrow-up"></i> +20.6%</td></tr>
                <tr><td>Orders</td><td>78</td><td>62</td><td class="text-success"><i class="bi bi-arrow-up"></i> +25.8%</td></tr>
                <tr><td>New Customers</td><td>23</td><td>18</td><td class="text-success"><i class="bi bi-arrow-up"></i> +27.8%</td></tr>
                <tr><td>Returning Customers</td><td>55</td><td>44</td><td class="text-success"><i class="bi bi-arrow-up"></i> +25.0%</td></tr>
                <tr><td>Average Order Value</td><td>₱159.62</td><td>₱153.45</td><td class="text-success"><i class="bi bi-arrow-up"></i> +4.0%</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- FOOTER -->
  <?=include_page("cashier/footer")?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?=js()?>