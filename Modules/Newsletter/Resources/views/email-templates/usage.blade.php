@extends('admin::layouts.master')
@section('title','E-SHOP || Email Template Usage')
@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Template Usage: {{ $emailTemplate->name }}</h5>
            <a href="{{ route('admin.email-templates.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to Templates
            </a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <!-- Usage Statistics -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Usage Statistics</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-3">
                                    <div class="border-right">
                                        <h4 class="text-primary">{{ $usageStats['total_campaigns'] ?? 0 }}</h4>
                                        <small>Total Campaigns</small>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="border-right">
                                        <h4 class="text-success">{{ $usageStats['total_sent'] ?? 0 }}</h4>
                                        <small>Emails Sent</small>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="border-right">
                                        <h4 class="text-info">{{ $usageStats['total_opened'] ?? 0 }}</h4>
                                        <small>Emails Opened</small>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <h4 class="text-warning">{{ $usageStats['total_clicked'] ?? 0 }}</h4>
                                    <small>Emails Clicked</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Campaigns -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Recent Campaigns Using This Template</h6>
                        </div>
                        <div class="card-body">
                            @if(isset($recentCampaigns) && $recentCampaigns->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Campaign Name</th>
                                                <th>Subject</th>
                                                <th>Sent Date</th>
                                                <th>Recipients</th>
                                                <th>Open Rate</th>
                                                <th>Click Rate</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentCampaigns as $campaign)
                                                <tr>
                                                    <td>{{ $campaign->name ?? 'Unnamed Campaign' }}</td>
                                                    <td>{{ $campaign->subject ?? $emailTemplate->subject }}</td>
                                                    <td>{{ $campaign->created_at->format('M d, Y H:i') }}</td>
                                                    <td>{{ $campaign->recipients_count ?? 0 }}</td>
                                                    <td>
                                                        @php
                                                            $openRate = $campaign->recipients_count > 0 ? 
                                                                round(($campaign->opened_count ?? 0) / $campaign->recipients_count * 100, 1) : 0;
                                                        @endphp
                                                        <span class="badge badge-{{ $openRate > 20 ? 'success' : ($openRate > 10 ? 'warning' : 'danger') }}">
                                                            {{ $openRate }}%
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $clickRate = $campaign->recipients_count > 0 ? 
                                                                round(($campaign->clicked_count ?? 0) / $campaign->recipients_count * 100, 1) : 0;
                                                        @endphp
                                                        <span class="badge badge-{{ $clickRate > 5 ? 'success' : ($clickRate > 2 ? 'warning' : 'danger') }}">
                                                            {{ $clickRate }}%
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <h6>No campaigns found using this template</h6>
                                    <p class="text-muted">This template hasn't been used in any campaigns yet.</p>
                                    <a href="{{ route('admin.email-campaigns.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Create Campaign
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Template Info -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Template Information</h6>
                        </div>
                        <div class="card-body">
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
                                    <span class="badge badge-light">No</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Quick Actions</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.email-templates.edit', $emailTemplate->id) }}" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Edit Template
                                </a>
                                <a href="{{ route('admin.email-templates.preview', $emailTemplate->id) }}" class="btn btn-secondary" target="_blank">
                                    <i class="fas fa-external-link-alt"></i> Preview Template
                                </a>
                                <a href="{{ route('admin.email-campaigns.create') }}?template_id={{ $emailTemplate->id }}" class="btn btn-success">
                                    <i class="fas fa-paper-plane"></i> Create Campaign
                                </a>
                                @if(!$emailTemplate->is_default)
                                    <form action="{{ route('admin.email-templates.set-default', $emailTemplate->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-warning w-100">
                                            <i class="fas fa-star"></i> Set as Default
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
    .badge {
        font-size: 0.8em;
    }
</style>
@endpush
