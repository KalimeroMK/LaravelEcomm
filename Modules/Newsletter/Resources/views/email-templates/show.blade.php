@extends('admin::layouts.master')
@section('title','E-SHOP || Email Template Details')
@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Email Template: {{ $emailTemplate->name }}</h5>
            <div class="btn-group">
                <a href="{{ route('admin.email-templates.edit', $emailTemplate->id) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('admin.email-templates.preview', $emailTemplate->id) }}" class="btn btn-secondary btn-sm" target="_blank">
                    <i class="fas fa-external-link-alt"></i> Preview
                </a>
                <a href="{{ route('admin.email-templates.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <!-- Template Details -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Template Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Name:</strong> {{ $emailTemplate->name }}<br>
                                    <strong>Type:</strong> 
                                    <span class="badge badge-info">
                                        {{ \Modules\Newsletter\Models\EmailTemplate::getTemplateTypes()[$emailTemplate->template_type] ?? $emailTemplate->template_type }}
                                    </span><br>
                                    <strong>Subject:</strong> {{ $emailTemplate->subject }}<br>
                                </div>
                                <div class="col-md-6">
                                    <strong>Status:</strong> 
                                    @if($emailTemplate->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif<br>
                                    <strong>Default:</strong> 
                                    @if($emailTemplate->is_default)
                                        <span class="badge badge-warning">Yes</span>
                                    @else
                                        <span class="badge badge-light">No</span>
                                    @endif<br>
                                    <strong>Created:</strong> {{ $emailTemplate->created_at->format('M d, Y H:i') }}<br>
                                    <strong>Updated:</strong> {{ $emailTemplate->updated_at->format('M d, Y H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- HTML Content Preview -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">HTML Content Preview</h6>
                        </div>
                        <div class="card-body">
                            <div class="email-preview" style="border: 1px solid #ddd; padding: 20px; background: #f8f9fa; max-height: 400px; overflow-y: auto;">
                                {!! $emailTemplate->html_content !!}
                            </div>
                        </div>
                    </div>

                    <!-- Text Content -->
                    @if($emailTemplate->text_content)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Text Content</h6>
                        </div>
                        <div class="card-body">
                            <pre style="background: #f8f9fa; padding: 15px; border-radius: 4px; white-space: pre-wrap;">{{ $emailTemplate->text_content }}</pre>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="col-md-4">
                    <!-- Analytics -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Analytics</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="border-right">
                                        <h4 class="text-primary">{{ $emailTemplate->emailAnalytics()->count() }}</h4>
                                        <small>Sent</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border-right">
                                        <h4 class="text-success">{{ $emailTemplate->emailAnalytics()->whereNotNull('opened_at')->count() }}</h4>
                                        <small>Opened</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <h4 class="text-info">{{ $emailTemplate->emailAnalytics()->whereNotNull('clicked_at')->count() }}</h4>
                                    <small>Clicked</small>
                                </div>
                            </div>
                            <hr>
                            <div class="row text-center">
                                <div class="col-6">
                                    <strong>Open Rate:</strong><br>
                                    @php
                                        $sent = $emailTemplate->emailAnalytics()->count();
                                        $opened = $emailTemplate->emailAnalytics()->whereNotNull('opened_at')->count();
                                        $openRate = $sent > 0 ? round(($opened / $sent) * 100, 1) : 0;
                                    @endphp
                                    <span class="badge badge-success">{{ $openRate }}%</span>
                                </div>
                                <div class="col-6">
                                    <strong>Click Rate:</strong><br>
                                    @php
                                        $clicked = $emailTemplate->emailAnalytics()->whereNotNull('clicked_at')->count();
                                        $clickRate = $sent > 0 ? round(($clicked / $sent) * 100, 1) : 0;
                                    @endphp
                                    <span class="badge badge-info">{{ $clickRate }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Settings -->
                    @if($emailTemplate->settings)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Settings</h6>
                        </div>
                        <div class="card-body">
                            <pre style="background: #f8f9fa; padding: 15px; border-radius: 4px; font-size: 12px;">{{ json_encode($emailTemplate->settings, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </div>
                    @endif

                    <!-- Preview Data -->
                    @if($emailTemplate->preview_data)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Preview Data</h6>
                        </div>
                        <div class="card-body">
                            <pre style="background: #f8f9fa; padding: 15px; border-radius: 4px; font-size: 12px;">{{ json_encode($emailTemplate->preview_data, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </div>
                    @endif

                    <!-- Actions -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Actions</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.email-templates.edit', $emailTemplate->id) }}" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Edit Template
                                </a>
                                <a href="{{ route('admin.email-templates.preview', $emailTemplate->id) }}" class="btn btn-secondary" target="_blank">
                                    <i class="fas fa-external-link-alt"></i> Preview Template
                                </a>
                                @if(!$emailTemplate->is_default)
                                    <form action="{{ route('admin.email-templates.set-default', $emailTemplate->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-warning w-100">
                                            <i class="fas fa-star"></i> Set as Default
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('admin.email-templates.duplicate', $emailTemplate->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-info w-100">
                                        <i class="fas fa-copy"></i> Duplicate Template
                                    </button>
                                </form>
                                @if(!$emailTemplate->is_default)
                                    <form action="{{ route('admin.email-templates.destroy', $emailTemplate->id) }}" method="POST" style="display: inline;" 
                                          onsubmit="return confirm('Are you sure you want to delete this template?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger w-100">
                                            <i class="fas fa-trash"></i> Delete Template
                                        </button>
                                    </form>
                                @endif
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
    .border-right {
        border-right: 1px solid #dee2e6;
    }
    .email-preview {
        font-family: Arial, sans-serif;
    }
    .email-preview img {
        max-width: 100%;
        height: auto;
    }
    .badge {
        font-size: 0.8em;
    }
</style>
@endpush
