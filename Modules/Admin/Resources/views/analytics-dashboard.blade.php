@extends('admin::layouts.master')
@section('title', 'Analytics Dashboard')
@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Analytics Dashboard</h1>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-primary" onclick="refreshAnalytics()">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
            <button type="button" class="btn btn-success" onclick="exportAnalytics()">
                <i class="fas fa-download"></i> Export
            </button>
        </div>
    </div>

    <!-- Date Range Selector -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <label for="startDate">Start Date:</label>
                            <input type="date" id="startDate" class="form-control" value="{{ now()->subMonth()->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="endDate">End Date:</label>
                            <input type="date" id="endDate" class="form-control" value="{{ now()->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="analyticsType">Analytics Type:</label>
                            <select id="analyticsType" class="form-control">
                                <option value="overview">Overview</option>
                                <option value="sales">Sales</option>
                                <option value="users">Users</option>
                                <option value="products">Products</option>
                                <option value="content">Content</option>
                                <option value="marketing">Marketing</option>
                                <option value="performance">Performance</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-info btn-block" onclick="updateDateRange()">
                                <i class="fas fa-filter"></i> Apply Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overview Cards -->
    <div class="row" id="overviewCards">
        <!-- Revenue Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalRevenue">$0</div>
                            <div class="text-xs text-muted" id="revenueChange">+0% from last month</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Orders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalOrders">0</div>
                            <div class="text-xs text-muted" id="ordersChange">+0% from last month</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customers Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Customers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalCustomers">0</div>
                            <div class="text-xs text-muted" id="customersChange">+0% from last month</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Products</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalProducts">0</div>
                            <div class="text-xs text-muted" id="productsChange">Active products</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cubes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Revenue Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Revenue Overview</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow">
                            <a class="dropdown-item" href="#" onclick="exportChart('revenue')">Export Chart</a>
                            <a class="dropdown-item" href="#" onclick="printChart('revenue')">Print Chart</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Orders by Status</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="ordersPieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Tabs -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Detailed Analytics</h6>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="analyticsTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="sales-tab" data-toggle="tab" href="#sales" role="tab">Sales</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="users-tab" data-toggle="tab" href="#users" role="tab">Users</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="products-tab" data-toggle="tab" href="#products" role="tab">Products</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="marketing-tab" data-toggle="tab" href="#marketing" role="tab">Marketing</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="performance-tab" data-toggle="tab" href="#performance" role="tab">Performance</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="analyticsTabContent">
                        <!-- Sales Tab -->
                        <div class="tab-pane fade show active" id="sales" role="tabpanel">
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <h5>Top Selling Products</h5>
                                    <div class="table-responsive">
                                        <table class="table table-sm" id="topProductsTable">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Sold</th>
                                                    <th>Revenue</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Data will be loaded via AJAX -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h5>Sales by Month</h5>
                                    <div style="height: 300px; position: relative;">
                                        <canvas id="salesChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Users Tab -->
                        <div class="tab-pane fade" id="users" role="tabpanel">
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <h5>User Registrations</h5>
                                    <canvas id="userRegistrationsChart"></canvas>
                                </div>
                                <div class="col-md-6">
                                    <h5>User Segments</h5>
                                    <canvas id="userSegmentsChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Products Tab -->
                        <div class="tab-pane fade" id="products" role="tabpanel">
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <h5>Product Performance</h5>
                                    <div class="table-responsive">
                                        <table class="table table-sm" id="productPerformanceTable">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Clicks</th>
                                                    <th>Impressions</th>
                                                    <th>CTR</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Data will be loaded via AJAX -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h5>Inventory Status</h5>
                                    <canvas id="inventoryChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Marketing Tab -->
                        <div class="tab-pane fade" id="marketing" role="tabpanel">
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <h5>Email Campaign Performance</h5>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="card bg-primary text-white">
                                                <div class="card-body text-center">
                                                    <h4 id="emailOpenRate">0%</h4>
                                                    <p>Open Rate</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="card bg-success text-white">
                                                <div class="card-body text-center">
                                                    <h4 id="emailClickRate">0%</h4>
                                                    <p>Click Rate</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h5>Newsletter Statistics</h5>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="card bg-info text-white">
                                                <div class="card-body text-center">
                                                    <h4 id="totalSubscribers">0</h4>
                                                    <p>Total Subscribers</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="card bg-warning text-white">
                                                <div class="card-body text-center">
                                                    <h4 id="newSubscribers">0</h4>
                                                    <p>New This Month</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Performance Tab -->
                        <div class="tab-pane fade" id="performance" role="tabpanel">
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <h5>Page Views</h5>
                                    <canvas id="pageViewsChart"></canvas>
                                </div>
                                <div class="col-md-6">
                                    <h5>Traffic Sources</h5>
                                    <canvas id="trafficSourcesChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Real-time Updates -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Real-time Updates</h6>
                    <div class="float-right">
                        <span class="badge badge-success" id="connectionStatus">Connected</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 id="onlineUsers">0</h4>
                                <p>Online Users</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 id="currentOrders">0</h4>
                                <p>Current Orders</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 id="newUsersToday">0</h4>
                                <p>New Users Today</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 id="systemStatus">Healthy</h4>
                                <p>System Status</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .chart-area {
        position: relative;
        height: 300px;
    }
    
    .chart-pie {
        position: relative;
        height: 250px;
    }
    
    .card {
        transition: transform 0.2s;
    }
    
    .card:hover {
        transform: translateY(-2px);
    }
    
    .nav-tabs .nav-link.active {
        background-color: #4e73df;
        color: white;
        border-color: #4e73df;
    }
    
    .table-responsive {
        max-height: 300px;
        overflow-y: auto;
    }
    
    .badge {
        font-size: 0.8em;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Global variables
    let revenueChart, ordersPieChart, salesChart, userRegistrationsChart, userSegmentsChart;
    let inventoryChart, pageViewsChart, trafficSourcesChart;
    let realTimeInterval;

    // Initialize dashboard
    document.addEventListener('DOMContentLoaded', function() {
        loadAnalytics();
        initializeCharts();
        startRealTimeUpdates();
        
        // Add event listener for analytics type dropdown
        document.getElementById('analyticsType').addEventListener('change', function() {
            updateDateRange();
        });
    });

    // Load analytics data
    async function loadAnalytics() {
        try {
            const response = await fetch('/api/admin/analytics/dashboard');
            const data = await response.json();
            
            if (data.success && data.data) {
                if (data.data.overview) {
                    updateOverviewCards(data.data.overview);
                }
                updateCharts(data.data);
                updateTables(data.data);
            } else {
                console.warn('Analytics data not available');
                showNotification('Analytics data not available', 'warning');
            }
        } catch (error) {
            console.error('Error loading analytics:', error);
            showNotification('Error loading analytics data', 'error');
        }
    }

    // Update overview cards
    function updateOverviewCards(overview) {
        if (!overview) return;
        
        const totalRevenue = document.getElementById('totalRevenue');
        const totalOrders = document.getElementById('totalOrders');
        const totalCustomers = document.getElementById('totalCustomers');
        const totalProducts = document.getElementById('totalProducts');
        
        if (totalRevenue && overview.total_revenue) {
            totalRevenue.textContent = '$' + formatNumber(overview.total_revenue.current || 0);
        }
        if (totalOrders && overview.total_orders) {
            totalOrders.textContent = formatNumber(overview.total_orders.current || 0);
        }
        if (totalCustomers && overview.total_customers) {
            totalCustomers.textContent = formatNumber(overview.total_customers.current || 0);
        }
        if (totalProducts && overview.total_products) {
            totalProducts.textContent = formatNumber(overview.total_products.current || 0);
        }
        
        // Update change indicators
        if (overview.total_revenue) {
            updateChangeIndicator('revenueChange', overview.total_revenue.this_month, overview.total_revenue.last_month);
        }
        if (overview.total_orders) {
            updateChangeIndicator('ordersChange', overview.total_orders.this_month, overview.total_orders.last_month);
        }
        if (overview.total_customers) {
            updateChangeIndicator('customersChange', overview.total_customers.this_month, overview.total_customers.last_month);
        }
    }

    // Update change indicators
    function updateChangeIndicator(elementId, current, previous) {
        const element = document.getElementById(elementId);
        const change = previous > 0 ? ((current - previous) / previous * 100) : 0;
        const sign = change >= 0 ? '+' : '';
        const color = change >= 0 ? 'text-success' : 'text-danger';
        
        element.innerHTML = `<span class="${color}">${sign}${change.toFixed(1)}% from last month</span>`;
    }

    // Initialize charts
    function initializeCharts() {
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Revenue',
                    data: [],
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Orders Pie Chart
        const ordersPieCtx = document.getElementById('ordersPieChart').getContext('2d');
        ordersPieChart = new Chart(ordersPieCtx, {
            type: 'doughnut',
            data: {
                labels: [],
                datasets: [{
                    data: [],
                    backgroundColor: [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Initialize other charts...
        initializeOtherCharts();
    }

    // Initialize other charts
    function initializeOtherCharts() {
        // Sales Chart
        const salesElement = document.getElementById('salesChart');
        const salesCtx = salesElement.getContext('2d');
        salesChart = new Chart(salesCtx, {
            type: 'bar',
            data: {
                labels: ['No Data'],
                datasets: [{
                    label: 'Sales',
                    data: [0],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true
                    }
                }
            }
        });

        // User Registrations Chart
        const userRegCtx = document.getElementById('userRegistrationsChart').getContext('2d');
        userRegistrationsChart = new Chart(userRegCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'User Registrations',
                    data: [],
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Initialize remaining charts...
        initializeRemainingCharts();
    }

    // Initialize remaining charts
    function initializeRemainingCharts() {
        // User Segments Chart
        const userSegCtx = document.getElementById('userSegmentsChart').getContext('2d');
        userSegmentsChart = new Chart(userSegCtx, {
            type: 'pie',
            data: {
                labels: [],
                datasets: [{
                    data: [],
                    backgroundColor: [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Inventory Chart
        const inventoryCtx = document.getElementById('inventoryChart').getContext('2d');
        inventoryChart = new Chart(inventoryCtx, {
            type: 'doughnut',
            data: {
                labels: [],
                datasets: [{
                    data: [],
                    backgroundColor: [
                        '#28a745',
                        '#dc3545',
                        '#ffc107'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Page Views Chart
        const pageViewsCtx = document.getElementById('pageViewsChart').getContext('2d');
        pageViewsChart = new Chart(pageViewsCtx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Page Views',
                    data: [],
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Traffic Sources Chart
        const trafficCtx = document.getElementById('trafficSourcesChart').getContext('2d');
        trafficSourcesChart = new Chart(trafficCtx, {
            type: 'pie',
            data: {
                labels: [],
                datasets: [{
                    data: [],
                    backgroundColor: [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    // Update charts with data
    function updateCharts(data) {
        try {
            if (!data) {
                console.warn('No data provided to updateCharts');
                return;
            }

            // Update revenue chart
            if (data.sales && data.sales.revenue_by_month && revenueChart) {
                revenueChart.data.labels = data.sales.revenue_by_month.map(item => item.month);
                revenueChart.data.datasets[0].data = data.sales.revenue_by_month.map(item => item.revenue);
                revenueChart.update();
            }

            // Update orders pie chart
            if (data.sales && data.sales.sales_by_status && data.sales.sales_by_status.order_status && ordersPieChart) {
                ordersPieChart.data.labels = Object.keys(data.sales.sales_by_status.order_status);
                ordersPieChart.data.datasets[0].data = Object.values(data.sales.sales_by_status.order_status);
                ordersPieChart.update();
            }

            // Update sales chart
            if (data.sales && data.sales.orders_by_month && data.sales.orders_by_month.length > 0 && salesChart) {
                salesChart.data.labels = data.sales.orders_by_month.map(item => item.month);
                salesChart.data.datasets[0].data = data.sales.orders_by_month.map(item => item.orders);
                salesChart.update();
            } else if (salesChart) {
                // Show message when no data
                salesChart.data.labels = ['No Sales Data'];
                salesChart.data.datasets[0].data = [0];
                salesChart.update();
            }

            // Update user registrations chart
            if (data.users && data.users.user_registrations && userRegistrationsChart) {
                const registrations = data.users.user_registrations;
                userRegistrationsChart.data.labels = Object.keys(registrations);
                userRegistrationsChart.data.datasets[0].data = Object.values(registrations);
                userRegistrationsChart.update();
            }

            // Update user segments chart
            if (data.users && data.users.user_segments && userSegmentsChart) {
                userSegmentsChart.data.labels = Object.keys(data.users.user_segments);
                userSegmentsChart.data.datasets[0].data = Object.values(data.users.user_segments);
                userSegmentsChart.update();
            }

            // Update inventory chart
            if (data.products && data.products.inventory_status && inventoryChart) {
                const inventory = data.products.inventory_status;
                inventoryChart.data.labels = ['In Stock', 'Out of Stock', 'Low Stock'];
                inventoryChart.data.datasets[0].data = [
                    inventory.in_stock || 0,
                    inventory.out_of_stock || 0,
                    inventory.low_stock || 0
                ];
                inventoryChart.update();
            }

            // Update page views chart
            if (data.performance && data.performance.page_views && pageViewsChart) {
                const pageViews = data.performance.page_views;
                pageViewsChart.data.labels = Object.keys(pageViews);
                pageViewsChart.data.datasets[0].data = Object.values(pageViews);
                pageViewsChart.update();
            }

            // Update traffic sources chart
            if (data.performance && data.performance.traffic_sources && trafficSourcesChart) {
                const traffic = data.performance.traffic_sources;
                trafficSourcesChart.data.labels = Object.keys(traffic);
                trafficSourcesChart.data.datasets[0].data = Object.values(traffic);
                trafficSourcesChart.update();
            }
        } catch (error) {
            console.error('Error updating charts:', error);
        }
    }

    // Update tables with data
    function updateTables(data) {
        // Update top products table
        if (data.sales && data.sales.top_selling_products) {
            const tbody = document.querySelector('#topProductsTable tbody');
            if (tbody) {
                tbody.innerHTML = '';
                data.sales.top_selling_products.forEach(product => {
                    const row = tbody.insertRow();
                    row.insertCell(0).textContent = product.title || 'N/A';
                    row.insertCell(1).textContent = product.total_sold || 0;
                    row.insertCell(2).textContent = '$' + formatNumber(product.total_revenue || 0);
                });
            }
        }

        // Update product performance table
        if (data.products && data.products.product_performance) {
            const tbody = document.querySelector('#productPerformanceTable tbody');
            if (tbody) {
                tbody.innerHTML = '';
                data.products.product_performance.forEach(product => {
                    const row = tbody.insertRow();
                    row.insertCell(0).textContent = product.title || 'N/A';
                    row.insertCell(1).textContent = product.clicks || 0;
                    row.insertCell(2).textContent = product.impressions || 0;
                    row.insertCell(3).textContent = (product.ctr || 0) + '%';
                });
            }
        }

        // Update marketing metrics
        if (data.marketing && data.marketing.email_campaigns) {
            const emailOpenRate = document.getElementById('emailOpenRate');
            const emailClickRate = document.getElementById('emailClickRate');
            if (emailOpenRate) emailOpenRate.textContent = (data.marketing.email_campaigns.open_rate || 0) + '%';
            if (emailClickRate) emailClickRate.textContent = (data.marketing.email_campaigns.click_rate || 0) + '%';
        }

        if (data.marketing && data.marketing.newsletter_stats) {
            const totalSubscribers = document.getElementById('totalSubscribers');
            const newSubscribers = document.getElementById('newSubscribers');
            if (totalSubscribers) totalSubscribers.textContent = formatNumber(data.marketing.newsletter_stats.total_subscribers || 0);
            if (newSubscribers) newSubscribers.textContent = formatNumber(data.marketing.newsletter_stats.new_this_month || 0);
        }
    }

    // Start real-time updates
    function startRealTimeUpdates() {
        realTimeInterval = setInterval(async () => {
            try {
                const response = await fetch('/api/admin/analytics/real-time');
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('onlineUsers').textContent = data.data.online_users;
                    document.getElementById('currentOrders').textContent = data.data.current_orders.pending + data.data.current_orders.processing;
                    document.getElementById('newUsersToday').textContent = data.data.recent_activity.new_users;
                    document.getElementById('systemStatus').textContent = data.data.system_status.database;
                }
            } catch (error) {
                console.error('Error updating real-time data:', error);
                document.getElementById('connectionStatus').textContent = 'Disconnected';
                document.getElementById('connectionStatus').className = 'badge badge-danger';
            }
        }, 30000); // Update every 30 seconds
    }

    // Refresh analytics
    function refreshAnalytics() {
        loadAnalytics();
    }

    // Export analytics
    async function exportAnalytics() {
        console.log('Export function called - using POST method');
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const type = document.getElementById('analyticsType').value;
        
        console.log('Export parameters:', { type, format: 'xlsx', startDate, endDate });
        
        try {
            const response = await fetch('/api/admin/analytics/export?_t=' + Date.now(), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Cache-Control': 'no-cache'
                },
                body: JSON.stringify({
                    type: type,
                    format: 'xlsx',
                    start_date: startDate,
                    end_date: endDate
                })
            });
            
            console.log('Response status:', response.status);
            
            if (response.ok) {
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `analytics-${type}-${startDate}-to-${endDate}.xlsx`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
                console.log('Export completed successfully');
            } else {
                const errorText = await response.text();
                console.error('Export failed:', response.status, errorText);
                alert('Export failed. Please try again.');
            }
        } catch (error) {
            console.error('Export error:', error);
            alert('Export failed. Please try again.');
        }
    }

    // Update date range
    function updateDateRange() {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const type = document.getElementById('analyticsType').value;
        
        // Load analytics for specific date range
        loadDateRangeAnalytics(type, startDate, endDate);
    }

    // Load date range analytics
    async function loadDateRangeAnalytics(type, startDate, endDate) {
        try {
            const response = await fetch(`/api/admin/analytics/date-range?type=${type}&start_date=${startDate}&end_date=${endDate}`);
            const data = await response.json();
            
            if (data.success) {
                // Update charts and tables with filtered data
                // Create a proper data structure for updateCharts
                const chartData = {};
                chartData[type] = data.data;
                updateCharts(chartData);
                
                // Show success message
                showNotification('Analytics updated successfully!', 'success');
            }
        } catch (error) {
            console.error('Error loading date range analytics:', error);
            showNotification('Error loading analytics data', 'error');
        }
    }

    // Export chart
    function exportChart(chartType) {
        // Implement chart export functionality
        console.log('Exporting chart:', chartType);
    }

    // Print chart
    function printChart(chartType) {
        // Implement chart print functionality
        console.log('Printing chart:', chartType);
    }

    // Format number
    function formatNumber(num) {
        return new Intl.NumberFormat().format(num);
    }

    // Show notification
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 3000);
    }

    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        if (realTimeInterval) {
            clearInterval(realTimeInterval);
        }
    });
</script>
@endpush
