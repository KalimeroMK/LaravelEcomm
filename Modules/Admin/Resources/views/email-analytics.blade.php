@extends('admin::layouts.master')
@section('title', 'Email Analytics')
@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Email Analytics</h1>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-primary" onclick="refreshEmailAnalytics()">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
            <button type="button" class="btn btn-success" onclick="exportEmailAnalytics()">
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
                            <label for="campaignType">Campaign Type:</label>
                            <select id="campaignType" class="form-control">
                                <option value="all">All Campaigns</option>
                                <option value="newsletter">Newsletters</option>
                                <option value="promotional">Promotional</option>
                                <option value="transactional">Transactional</option>
                                <option value="abandoned_cart">Abandoned Cart</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-info btn-block" onclick="updateEmailDateRange()">
                                <i class="fas fa-filter"></i> Apply Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overview Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Emails Sent</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalEmailsSent">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-envelope fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Open Rate</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="openRate">0%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-eye fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Click Rate</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="clickRate">0%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-mouse-pointer fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Unsubscribe Rate</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="unsubscribeRate">0%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-times fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Email Performance Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Email Performance Over Time</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="emailPerformanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Campaign Types Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Campaign Types</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="campaignTypesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Campaign Performance Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Campaign Performance</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="campaignTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Campaign Name</th>
                                    <th>Type</th>
                                    <th>Sent</th>
                                    <th>Opened</th>
                                    <th>Clicked</th>
                                    <th>Open Rate</th>
                                    <th>Click Rate</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody id="campaignTableBody">
                                <tr>
                                    <td colspan="8" class="text-center">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Notification Container -->
<div id="notificationContainer" class="notification-container"></div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let emailPerformanceChart, campaignTypesChart;

// Initialize charts when page loads
document.addEventListener('DOMContentLoaded', function() {
    initializeEmailCharts();
    loadEmailAnalytics();
});

function initializeEmailCharts() {
    // Email Performance Chart
    const emailCtx = document.getElementById('emailPerformanceChart');
    if (emailCtx) {
        emailPerformanceChart = new Chart(emailCtx, {
            type: 'line',
            data: {
                labels: ['No Data'],
                datasets: [{
                    label: 'Emails Sent',
                    data: [0],
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                }, {
                    label: 'Opens',
                    data: [0],
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    tension: 0.1
                }, {
                    label: 'Clicks',
                    data: [0],
                    borderColor: 'rgb(54, 162, 235)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    // Campaign Types Chart
    const campaignCtx = document.getElementById('campaignTypesChart');
    if (campaignCtx) {
        campaignTypesChart = new Chart(campaignCtx, {
            type: 'doughnut',
            data: {
                labels: ['No Data'],
                datasets: [{
                    data: [1],
                    backgroundColor: ['#e74a3b', '#1cc88a', '#36b9cc', '#f6c23e', '#858796']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
}

function loadEmailAnalytics() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    const campaignType = document.getElementById('campaignType').value;

    fetch('/api/v1/newsletter/analytics', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            start_date: startDate,
            end_date: endDate,
            campaign_type: campaignType
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateEmailOverviewCards(data.data);
            updateEmailCharts(data.data);
            updateEmailTable(data.data);
        } else {
            showNotification('Error loading email analytics: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error loading email analytics', 'error');
    });
}

function updateEmailOverviewCards(data) {
    try {
        if (data.overview) {
            document.getElementById('totalEmailsSent').textContent = data.overview.total_sent || 0;
            document.getElementById('openRate').textContent = (data.overview.open_rate || 0) + '%';
            document.getElementById('clickRate').textContent = (data.overview.click_rate || 0) + '%';
            document.getElementById('unsubscribeRate').textContent = (data.overview.unsubscribe_rate || 0) + '%';
        }
    } catch (error) {
        console.error('Error updating overview cards:', error);
    }
}

function updateEmailCharts(data) {
    try {
        // Update Email Performance Chart
        if (data.performance && emailPerformanceChart) {
            const performance = data.performance;
            emailPerformanceChart.data.labels = performance.labels || ['No Data'];
            emailPerformanceChart.data.datasets[0].data = performance.emails_sent || [0];
            emailPerformanceChart.data.datasets[1].data = performance.opens || [0];
            emailPerformanceChart.data.datasets[2].data = performance.clicks || [0];
            emailPerformanceChart.update();
        }

        // Update Campaign Types Chart
        if (data.campaign_types && campaignTypesChart) {
            const types = data.campaign_types;
            campaignTypesChart.data.labels = types.labels || ['No Data'];
            campaignTypesChart.data.datasets[0].data = types.data || [1];
            campaignTypesChart.update();
        }
    } catch (error) {
        console.error('Error updating charts:', error);
    }
}

function updateEmailTable(data) {
    try {
        const tbody = document.getElementById('campaignTableBody');
        if (data.campaigns && tbody) {
            tbody.innerHTML = '';
            if (data.campaigns.length > 0) {
                data.campaigns.forEach(campaign => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${campaign.name || 'N/A'}</td>
                        <td>${campaign.type || 'N/A'}</td>
                        <td>${campaign.sent || 0}</td>
                        <td>${campaign.opened || 0}</td>
                        <td>${campaign.clicked || 0}</td>
                        <td>${(campaign.open_rate || 0).toFixed(2)}%</td>
                        <td>${(campaign.click_rate || 0).toFixed(2)}%</td>
                        <td>${campaign.date || 'N/A'}</td>
                    `;
                    tbody.appendChild(row);
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center">No campaigns found</td></tr>';
            }
        }
    } catch (error) {
        console.error('Error updating table:', error);
    }
}

function updateEmailDateRange() {
    loadEmailAnalytics();
}

function refreshEmailAnalytics() {
    loadEmailAnalytics();
    showNotification('Email analytics refreshed', 'success');
}

function exportEmailAnalytics() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    const campaignType = document.getElementById('campaignType').value;
    const format = prompt('Select export format (json/csv/xlsx):', 'xlsx');

    if (!format || !['json', 'csv', 'xlsx'].includes(format)) {
        showNotification('Invalid format selected', 'error');
        return;
    }

    fetch('/api/v1/newsletter/analytics/export', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            start_date: startDate,
            end_date: endDate,
            campaign_type: campaignType,
            format: format
        })
    })
    .then(response => {
        if (response.ok) {
            return response.blob();
        }
        throw new Error('Export failed');
    })
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `email-analytics-${startDate}-to-${endDate}.${format}`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
        showNotification('Email analytics exported successfully', 'success');
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error exporting email analytics', 'error');
    });
}

function showNotification(message, type = 'info') {
    const container = document.getElementById('notificationContainer');
    const alertClass = type === 'error' ? 'alert-danger' : type === 'success' ? 'alert-success' : 'alert-info';
    
    const notification = document.createElement('div');
    notification.className = `alert ${alertClass} alert-dismissible fade show`;
    notification.innerHTML = `
        ${message}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    `;
    
    container.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 5000);
}

// Add event listener for campaign type change
document.addEventListener('DOMContentLoaded', function() {
    const campaignTypeSelect = document.getElementById('campaignType');
    if (campaignTypeSelect) {
        campaignTypeSelect.addEventListener('change', updateEmailDateRange);
    }
});
</script>
@endpush
