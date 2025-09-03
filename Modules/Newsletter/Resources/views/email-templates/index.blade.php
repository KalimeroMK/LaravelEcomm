@extends('admin::layouts.master')
@section('title','E-SHOP || Email Templates')
@section('content')
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary float-left">Email Templates</h6>
            <a href="{{route('admin.email-templates.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip"
               data-placement="bottom" title="Add Email Template">
                <i class="fas fa-plus"></i> Add Template
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                @if(isset($templates) && $templates->count() > 0)
                    <table class="table table-bordered" id="data-table">
                        <thead>
                        <tr>
                            <th>@lang('partials.s_n')</th>
                            <th>Name</th>
                            <th>Subject</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Default</th>
                            <th>Analytics</th>
                            <th>@lang('partials.action')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($templates as $index => $template)
                            <tr>
                                <td>{{$index + 1}}</td>
                                <td>{{$template->name}}</td>
                                <td>{{$template->subject}}</td>
                                <td>
                                    <span class="badge badge-info">
                                        {{ \Modules\Newsletter\Models\EmailTemplate::getTemplateTypes()[$template->template_type] ?? $template->template_type }}
                                    </span>
                                </td>
                                <td>
                                    @if($template->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    @if($template->is_default)
                                        <span class="badge badge-warning">Default</span>
                                    @else
                                        <span class="badge badge-light">-</span>
                                    @endif
                                </td>
                                <td>
                                    <small>
                                        Sent: {{ $template->emailAnalytics()->count() }}<br>
                                        Open: {{ $template->emailAnalytics()->whereNotNull('opened_at')->count() }}<br>
                                        Click: {{ $template->emailAnalytics()->whereNotNull('clicked_at')->count() }}
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{route('admin.email-templates.show', $template->id)}}" 
                                           class="btn btn-info btn-sm" data-toggle="tooltip" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{route('admin.email-templates.edit', $template->id)}}" 
                                           class="btn btn-primary btn-sm" data-toggle="tooltip" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{route('admin.email-templates.preview', $template->id)}}" 
                                           class="btn btn-secondary btn-sm" data-toggle="tooltip" title="Preview" target="_blank">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                        <form action="{{route('admin.email-templates.duplicate', $template->id)}}" 
                                              method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-sm" 
                                                    data-toggle="tooltip" title="Duplicate">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </form>
                                        @if(!$template->is_default)
                                            <form action="{{route('admin.email-templates.set-default', $template->id)}}" 
                                                  method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm" 
                                                        data-toggle="tooltip" title="Set as Default">
                                                    <i class="fas fa-star"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{route('admin.email-templates.toggle-active', $template->id)}}" 
                                              method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-{{$template->is_active ? 'warning' : 'success'}} btn-sm" 
                                                    data-toggle="tooltip" title="{{$template->is_active ? 'Deactivate' : 'Activate'}}">
                                                <i class="fas fa-{{$template->is_active ? 'pause' : 'play'}}"></i>
                                            </button>
                                        </form>
                                        @if(!$template->is_default)
                                            <form action="{{route('admin.email-templates.destroy', $template->id)}}" 
                                                  method="POST" style="display: inline;" 
                                                  onsubmit="return confirm('Are you sure you want to delete this template?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" 
                                                        data-toggle="tooltip" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $templates->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <h5>No email templates found</h5>
                        <p>Create your first email template to get started.</p>
                        <a href="{{route('admin.email-templates.create')}}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Template
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .btn-group .btn {
            margin-right: 2px;
        }
        .badge {
            font-size: 0.75em;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // DataTables is already initialized in admin footer
        // No need to initialize again here
    </script>
@endpush

