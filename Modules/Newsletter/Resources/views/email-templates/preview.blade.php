<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Template Preview - {{ $emailTemplate->name }}</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            line-height: 1.6; 
            color: #333; 
            margin: 0; 
            padding: 20px; 
            background: #f8f9fa;
        }
        .preview-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .preview-header {
            background: #007bff;
            color: white;
            padding: 15px 20px;
            text-align: center;
        }
        .preview-content {
            padding: 20px;
        }
        .preview-footer {
            background: #6c757d;
            color: white;
            padding: 15px 20px;
            text-align: center;
            font-size: 12px;
        }
        .template-info {
            background: #e9ecef;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .template-info h4 {
            margin: 0 0 10px 0;
            color: #495057;
        }
        .template-info p {
            margin: 5px 0;
            font-size: 14px;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 4px;
        }
        .badge-success { background: #28a745; color: white; }
        .badge-warning { background: #ffc107; color: #000; }
        .badge-info { background: #17a2b8; color: white; }
        .badge-secondary { background: #6c757d; color: white; }
    </style>
</head>
<body>
    <div class="preview-container">
        <div class="preview-header">
            <h2>Email Template Preview</h2>
        </div>
        
        <div class="preview-content">
            <!-- Template Information -->
            <div class="template-info">
                <h4>Template Information</h4>
                <p><strong>Name:</strong> {{ $emailTemplate->name }}</p>
                <p><strong>Type:</strong> 
                    <span class="badge badge-info">
                        {{ \Modules\Newsletter\Models\EmailTemplate::getTemplateTypes()[$emailTemplate->template_type] ?? $emailTemplate->template_type }}
                    </span>
                </p>
                <p><strong>Subject:</strong> {{ $emailTemplate->subject }}</p>
                <p><strong>Status:</strong> 
                    @if($emailTemplate->is_active)
                        <span class="badge badge-success">Active</span>
                    @else
                        <span class="badge badge-secondary">Inactive</span>
                    @endif
                </p>
                <p><strong>Default:</strong> 
                    @if($emailTemplate->is_default)
                        <span class="badge badge-warning">Yes</span>
                    @else
                        <span class="badge badge-secondary">No</span>
                    @endif
                </p>
            </div>

            <!-- Email Content Preview -->
            <div class="email-content">
                {!! $emailTemplate->html_content !!}
            </div>
        </div>
        
        <div class="preview-footer">
            <p>This is a preview of the email template. Variables like {{name}}, {{email}}, {{company}} will be replaced with actual data when sent.</p>
            <p>© {{ config('app.name', 'Our Company') }} - Email Template Preview</p>
        </div>
    </div>
</body>
</html>
