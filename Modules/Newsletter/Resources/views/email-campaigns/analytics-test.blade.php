<!DOCTYPE html>
<html>
<head>
    <title>Email Campaign Analytics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid mt-4">
        <h1>Email Campaign Analytics</h1>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Analytics Data</h5>
                    </div>
                    <div class="card-body">
                        <pre>{{ json_encode($analytics, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
