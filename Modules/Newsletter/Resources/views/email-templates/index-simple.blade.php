@extends('admin::layouts.master')
@section('title','E-SHOP || Email Templates')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Email Templates</h1>
            <a href="{{ route('admin.email-templates.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Template
            </a>
        </div>

        @if(isset($templates) && $templates->count() > 0)
            <div class="row">
                @foreach($templates as $template)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">{{ $template->name }}</h6>
                                <div>
                                    @if($template->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                    @if($template->is_default)
                                        <span class="badge bg-warning">Default</span>
                                    @endif
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="card-text">
                                    <strong>Type:</strong> 
                                    <span class="badge bg-info">
                                        {{ \Modules\Newsletter\Models\EmailTemplate::getTemplateTypes()[$template->template_type] ?? $template->template_type }}
                                    </span>
                                </p>
                                <p class="card-text">
                                    <strong>Subject:</strong> {{ $template->subject }}
                                </p>
                                <p class="card-text">
                                    <small class="text-muted">
                                        Sent: {{ $template->emailAnalytics()->count() }} | 
                                        Open: {{ $template->emailAnalytics()->whereNotNull('opened_at')->count() }} | 
                                        Click: {{ $template->emailAnalytics()->whereNotNull('clicked_at')->count() }}
                                    </small>
                                </p>
                            </div>
                            <div class="card-footer">
                                <div class="btn-group w-100" role="group">
                                    <a href="{{ route('admin.email-templates.show', $template->id) }}" class="btn btn-outline-info btn-sm" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.email-templates.usage', $template->id) }}" class="btn btn-outline-success btn-sm" title="View Usage">
                                        <i class="fas fa-chart-bar"></i>
                                    </a>
                                    <a href="{{ route('admin.email-templates.edit', $template->id) }}" class="btn btn-outline-primary btn-sm" title="Edit Template">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('admin.email-templates.preview', $template->id) }}" class="btn btn-outline-secondary btn-sm" target="_blank" title="Preview">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                    @if(!$template->is_default)
                                        <form action="{{ route('admin.email-templates.set-default', $template->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-warning btn-sm" title="Set as Default" onclick="return confirm('Set this template as default?')">
                                                <i class="fas fa-star"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.email-templates.toggle-active', $template->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-{{ $template->is_active ? 'danger' : 'success' }} btn-sm" 
                                                title="{{ $template->is_active ? 'Deactivate' : 'Activate' }}"
                                                onclick="return confirm('{{ $template->is_active ? 'Deactivate' : 'Activate' }} this template?')">
                                            <i class="fas fa-{{ $template->is_active ? 'pause' : 'play' }}"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $templates->links('pagination::admin-bootstrap-5') }}
            </div>
        @else
            <div class="text-center py-5">
                <h5>No email templates found</h5>
                <p>Create your first email template to get started.</p>
                <a href="{{ route('admin.email-templates.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Template
                </a>
            </div>
        @endif
    </div>
@endsection
