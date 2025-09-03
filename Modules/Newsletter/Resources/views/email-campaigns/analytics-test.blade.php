<!DOCTYPE html>
<html>
<head>
    <title>Email Campaign Analytics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .border-left-primary { border-left: 0.25rem solid #4e73df !important; }
        .border-left-success { border-left: 0.25rem solid #1cc88a !important; }
        .border-left-info { border-left: 0.25rem solid #36b9cc !important; }
        .border-left-warning { border-left: 0.25rem solid #f6c23e !important; }
        .text-gray-800 { color: #5a5c69 !important; }
        .text-gray-300 { color: #dddfeb !important; }
        .shadow { box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important; }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid mt-4">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Email Campaign Analytics</h1>
            <div class="btn-group" role="group">
                <a href="/admin/email-campaigns" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Campaigns
                </a>
                <button type="button" class="btn btn-primary" onclick="refreshAnalytics()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
        </div>

    <!-- Overview Cards -->
    <div class="row mb-4">
        <!-- Total Sent Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Sent</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $analytics['total_sent'] ?? 0 }}</div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $analytics['open_rate'] ?? 0 }}%</div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $analytics['click_rate'] ?? 0 }}%</div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $analytics['bounce_rate'] ?? 0 }}%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Data -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Detailed Analytics Data</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Metric</th>
                                    <th>Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Total Sent</td>
                                    <td>{{ $analytics['total_sent'] ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <td>Total Opened</td>
                                    <td>{{ $analytics['total_opened'] ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <td>Total Clicked</td>
                                    <td>{{ $analytics['total_clicked'] ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <td>Total Bounced</td>
                                    <td>{{ $analytics['total_bounced'] ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <td>Open Rate</td>
                                    <td>{{ $analytics['open_rate'] ?? 0 }}%</td>
                                </tr>
                                <tr>
                                    <td>Click Rate</td>
                                    <td>{{ $analytics['click_rate'] ?? 0 }}%</td>
                                </tr>
                                <tr>
                                    <td>Bounce Rate</td>
                                    <td>{{ $analytics['bounce_rate'] ?? 0 }}%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Raw Data (for debugging) -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Raw Analytics Data</h6>
                </div>
                <div class="card-body">
                    <pre class="bg-light p-3">{{ json_encode($analytics, JSON_PRETTY_PRINT) }}</pre>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function refreshAnalytics() {
        location.reload();
    }
</script>
</body>
</html>
