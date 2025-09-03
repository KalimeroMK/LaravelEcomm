@extends('admin::layouts.master')
@section('title', 'Email Campaign Analytics')
@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Email Campaign Analytics</h1>
        <div class="btn-group" role="group">
            <a href="{{ route('admin.email-campaigns.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Campaigns
            </a>
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
                            <label for="campaignFilter">Campaign:</label>
                            <select id="campaignFilter" class="form-control">
                                <option value="">All Campaigns</option>
                                <!-- Campaign options will be loaded dynamically -->
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
        <!-- Total Sent Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Sent</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalSent">0</div>
                            <div class="text-xs text-muted" id="sentChange">+0% from last month</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-paper-plane fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Open Rate Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Open Rate</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="openRate">0%</div>
                            <div class="text-xs text-muted" id="openRateChange">+0% from last month</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-eye fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Click Rate Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Click Rate</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="clickRate">0%</div>
                            <div class="text-xs text-muted" id="clickRateChange">+0% from last month</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-mouse-pointer fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bounce Rate Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Bounce Rate</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="bounceRate">0%</div>
                            <div class="text-xs text-muted" id="bounceRateChange">+0% from last month</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Campaign Performance Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Campaign Performance</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                            <div class="dropdown-header">Chart Options:</div>
                            <a class="dropdown-item" href="#" onclick="changeChartType('line')">Line Chart</a>
                            <a class="dropdown-item" href="#" onclick="changeChartType('bar')">Bar Chart</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="campaignChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Email Status Distribution -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Email Status</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="statusPieChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> Sent
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Opened
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-info"></i> Clicked
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-warning"></i> Bounced
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Campaign Details Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Campaign Details</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="campaignTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Campaign</th>
                                    <th>Subject</th>
                                    <th>Sent</th>
                                    <th>Opened</th>
                                    <th>Clicked</th>
                                    <th>Bounced</th>
                                    <th>Open Rate</th>
                                    <th>Click Rate</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="campaignTableBody">
                                <!-- Campaign data will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Real-time Stats -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Real-time Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="h4 mb-0 text-primary" id="onlineUsers">0</div>
                                <div class="text-xs text-muted">Online Users</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="h4 mb-0 text-success" id="emailsSentToday">0</div>
                                <div class="text-xs text-muted">Emails Sent Today</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="h4 mb-0 text-info" id="emailsOpenedToday">0</div>
                                <div class="text-xs text-muted">Emails Opened Today</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="h4 mb-0 text-warning" id="emailsClickedToday">0</div>
                                <div class="text-xs text-muted">Emails Clicked Today</div>
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
        height: 10rem;
        width: 100%;
    }
    
    .chart-pie {
        position: relative;
        height: 15rem;
        width: 100%;
    }
    
    .card {
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
    }
    
    .card .card-header {
        font-weight: 500;
    }
    
    .card-header:first-child {
        border-radius: calc(0.35rem - 1px) calc(0.35rem - 1px) 0 0;
    }
    
    .btn-group .btn {
        margin-right: 0.25rem;
    }
    
    .btn-group .btn:last-child {
        margin-right: 0;
    }
    
    .table-responsive {
        max-height: 400px;
        overflow-y: auto;
    }
    
    .badge {
        font-size: 0.8em;
    }
</style>
@endpush

@push('scripts')
<script>
    // Global variables
    let campaignChart, statusPieChart;
    let realTimeInterval;

    // Initialize dashboard
    document.addEventListener('DOMContentLoaded', function() {
        loadAnalytics();
        initializeCharts();
        startRealTimeUpdates();
    });

    // Load analytics data
    async function loadAnalytics() {
        try {
            const response = await fetch('/admin/email-campaigns/analytics/api');
            const data = await response.json();
            
            if (data.success && data.data) {
                updateOverviewCards(data.data);
                updateCharts(data.data);
                updateTable(data.data);
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
    function updateOverviewCards(data) {
        if (!data) return;
        
        document.getElementById('totalSent').textContent = formatNumber(data.total_sent || 0);
        document.getElementById('openRate').textContent = (data.open_rate || 0) + '%';
        document.getElementById('clickRate').textContent = (data.click_rate || 0) + '%';
        document.getElementById('bounceRate').textContent = (data.bounce_rate || 0) + '%';
    }

    // Initialize charts
    function initializeCharts() {
        // Campaign Chart
        const campaignCtx = document.getElementById('campaignChart').getContext('2d');
        campaignChart = new Chart(campaignCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Emails Sent',
                    data: [],
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                }, {
                    label: 'Emails Opened',
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

        // Status Pie Chart
        const statusPieCtx = document.getElementById('statusPieChart').getContext('2d');
        statusPieChart = new Chart(statusPieCtx, {
            type: 'doughnut',
            data: {
                labels: ['Sent', 'Opened', 'Clicked', 'Bounced'],
                datasets: [{
                    data: [0, 0, 0, 0],
                    backgroundColor: [
                        '#4e73df',
                        '#1cc88a',
                        '#36b9cc',
                        '#f6c23e'
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
        if (!data) return;

        // Update campaign chart
        if (campaignChart && data.campaign_performance) {
            campaignChart.data.labels = data.campaign_performance.map(item => item.date);
            campaignChart.data.datasets[0].data = data.campaign_performance.map(item => item.sent);
            campaignChart.data.datasets[1].data = data.campaign_performance.map(item => item.opened);
            campaignChart.update();
        }

        // Update status pie chart
        if (statusPieChart) {
            statusPieChart.data.datasets[0].data = [
                data.total_sent || 0,
                data.total_opened || 0,
                data.total_clicked || 0,
                data.total_bounced || 0
            ];
            statusPieChart.update();
        }
    }

    // Update table with data
    function updateTable(data) {
        if (!data || !data.campaigns) return;

        const tbody = document.getElementById('campaignTableBody');
        tbody.innerHTML = '';

        data.campaigns.forEach(campaign => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${campaign.name || 'N/A'}</td>
                <td>${campaign.subject || 'N/A'}</td>
                <td>${formatNumber(campaign.sent || 0)}</td>
                <td>${formatNumber(campaign.opened || 0)}</td>
                <td>${formatNumber(campaign.clicked || 0)}</td>
                <td>${formatNumber(campaign.bounced || 0)}</td>
                <td>${(campaign.open_rate || 0).toFixed(1)}%</td>
                <td>${(campaign.click_rate || 0).toFixed(1)}%</td>
                <td>${campaign.date || 'N/A'}</td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="viewCampaign(${campaign.id})">
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    // Start real-time updates
    function startRealTimeUpdates() {
        realTimeInterval = setInterval(async () => {
            try {
                const response = await fetch('/admin/email-campaigns/analytics/real-time');
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('onlineUsers').textContent = data.data.online_users || 0;
                    document.getElementById('emailsSentToday').textContent = data.data.emails_sent_today || 0;
                    document.getElementById('emailsOpenedToday').textContent = data.data.emails_opened_today || 0;
                    document.getElementById('emailsClickedToday').textContent = data.data.emails_clicked_today || 0;
                }
            } catch (error) {
                console.error('Error updating real-time data:', error);
            }
        }, 30000); // Update every 30 seconds
    }

    // Utility functions
    function formatNumber(num) {
        return new Intl.NumberFormat().format(num);
    }

    function showNotification(message, type = 'info') {
        // Simple notification - you can replace with your preferred notification system
        console.log(`${type.toUpperCase()}: ${message}`);
    }

    function refreshAnalytics() {
        loadAnalytics();
        showNotification('Analytics refreshed', 'success');
    }

    function exportAnalytics() {
        showNotification('Export functionality will be implemented', 'info');
    }

    function updateDateRange() {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const campaign = document.getElementById('campaignFilter').value;
        
        // Reload analytics with new filters
        loadAnalytics();
        showNotification('Filters applied', 'success');
    }

    function changeChartType(type) {
        if (campaignChart) {
            campaignChart.config.type = type;
            campaignChart.update();
        }
    }

    function viewCampaign(campaignId) {
        showNotification(`Viewing campaign ${campaignId}`, 'info');
    }

    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        if (realTimeInterval) {
            clearInterval(realTimeInterval);
        }
    });
</script>
@endpush
