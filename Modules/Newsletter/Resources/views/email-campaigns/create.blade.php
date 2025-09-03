@extends('admin::layouts.master')
@section('title','E-SHOP || Create Email Campaign')
@section('content')
    <div class="card">
        <h5 class="card-header">Create Email Campaign</h5>
        <div class="card-body">
            <form action="{{route('admin.email-campaigns.store')}}" method="POST" id="campaign-form">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="template_id">Email Template <span class="text-danger">*</span></label>
                            <select name="template_id" id="template_id" class="form-control" required>
                                <option value="">Select Template</option>
                                @foreach($templates as $template)
                                    <option value="{{ $template->id }}" 
                                            data-subject="{{ $template->subject }}"
                                            data-type="{{ $template->template_type }}"
                                            {{ $template->is_default ? 'selected' : '' }}>
                                        {{ $template->name }} 
                                        @if($template->is_default)
                                            <span class="badge badge-warning">Default</span>
                                        @endif
                                        ({{ ucfirst($template->template_type) }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">
                                Select the email template to use for this campaign. Default templates are pre-selected.
                            </small>
                            @error('template_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="subject">Email Subject <span class="text-danger">*</span></label>
                            <input type="text" name="subject" id="subject" class="form-control" 
                                   value="{{ old('subject') }}" required>
                            @error('subject')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Content to Include</label>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input type="checkbox" name="include_posts" id="include_posts" class="form-check-input" 
                                       value="1" {{ old('include_posts', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="include_posts">
                                    Include Blog Posts
                                </label>
                            </div>
                            <div class="form-group mt-2">
                                <label for="post_limit">Number of Posts</label>
                                <select name="post_limit" id="post_limit" class="form-control">
                                    @for($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}" {{ old('post_limit', 3) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input type="checkbox" name="include_products" id="include_products" class="form-check-input" 
                                       value="1" {{ old('include_products', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="include_products">
                                    Include Featured Products
                                </label>
                            </div>
                            <div class="form-group mt-2">
                                <label for="product_limit">Number of Products</label>
                                <select name="product_limit" id="product_limit" class="form-control">
                                    @for($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}" {{ old('product_limit', 5) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Send To <span class="text-danger">*</span></label>
                    <div class="form-check">
                        <input type="radio" name="send_to" id="send_to_all" class="form-check-input" 
                               value="all" {{ old('send_to', 'all') == 'all' ? 'checked' : '' }}>
                        <label class="form-check-label" for="send_to_all">
                            All Subscribers
                        </label>
                    </div>
                    <div class="form-check">
                        <input type="radio" name="send_to" id="send_to_segment" class="form-check-input" 
                               value="segment" {{ old('send_to') == 'segment' ? 'checked' : '' }}>
                        <label class="form-check-label" for="send_to_segment">
                            Specific Segment
                        </label>
                    </div>
                </div>

                <div id="segment-options" style="display: none;">
                    <div class="form-group">
                        <label>Segment Criteria</label>
                        <div class="form-check">
                            <input type="checkbox" name="segment_criteria[new_subscribers]" id="new_subscribers" class="form-check-input" 
                                   value="1" {{ old('segment_criteria.new_subscribers') ? 'checked' : '' }}>
                            <label class="form-check-label" for="new_subscribers">
                                New Subscribers (Last 30 days)
                            </label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="segment_criteria[active_users]" id="active_users" class="form-check-input" 
                                   value="1" {{ old('segment_criteria.active_users') ? 'checked' : '' }}>
                            <label class="form-check-label" for="active_users">
                                Active Users (Made purchases)
                            </label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="segment_criteria[inactive_users]" id="inactive_users" class="form-check-input" 
                                   value="1" {{ old('segment_criteria.inactive_users') ? 'checked' : '' }}>
                            <label class="form-check-label" for="inactive_users">
                                Inactive Users (No purchases)
                            </label>
                        </div>
                    </div>
                </div>

                <div class="button-container">
                    <button type="button" class="btn btn-info" onclick="previewCampaign()">
                        <i class="fas fa-eye"></i> Preview
                    </button>
                    <button type="button" class="btn btn-warning" onclick="saveDraft()">
                        <i class="fas fa-save"></i> Save Draft
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-paper-plane"></i> Send Campaign
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Campaign Preview</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <iframe id="previewFrame" width="100%" height="500px" style="border: none;"></iframe>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .button-container {
            margin-top: 20px;
            text-align: right;
        }
        .button-container .btn {
            margin-left: 10px;
        }
        .form-check {
            margin-bottom: 10px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Auto-fill subject when template is selected
        document.getElementById('template_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const subject = selectedOption.getAttribute('data-subject');
            if (subject) {
                document.getElementById('subject').value = subject;
            }
        });

        // Show/hide segment options
        document.querySelectorAll('input[name="send_to"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                const segmentOptions = document.getElementById('segment-options');
                if (this.value === 'segment') {
                    segmentOptions.style.display = 'block';
                } else {
                    segmentOptions.style.display = 'none';
                }
            });
        });

        function previewCampaign() {
            const form = document.getElementById('campaign-form');
            const formData = new FormData(form);
            
            fetch('{{ route("admin.email-campaigns.preview") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.text())
            .then(html => {
                const previewFrame = document.getElementById('previewFrame');
                previewFrame.srcdoc = html;
                $('#previewModal').modal('show');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error generating preview');
            });
        }

        function saveDraft() {
            // Implement draft saving functionality
            alert('Draft saved successfully!');
        }

        // Form validation
        document.getElementById('campaign-form').addEventListener('submit', function(e) {
            const templateId = document.getElementById('template_id').value;
            const subject = document.getElementById('subject').value;
            
            if (!templateId || !subject) {
                e.preventDefault();
                alert('Please select a template and enter a subject.');
                return false;
            }
            
            if (confirm('Are you sure you want to send this campaign?')) {
                return true;
            } else {
                e.preventDefault();
                return false;
            }
        });
    </script>
@endpush

