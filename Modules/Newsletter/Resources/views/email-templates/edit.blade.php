@extends('admin::layouts.master')
@section('title','E-SHOP || Edit Email Template')
@section('content')
    <div class="card">
        <h5 class="card-header">Edit Email Template: {{ $emailTemplate->name }}</h5>
        <div class="card-body">
            <form action="{{route('admin.email-templates.update', $emailTemplate->id)}}" method="POST" id="email-template-form">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Template Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" 
                                   value="{{ old('name', $emailTemplate->name) }}" required>
                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="template_type">Template Type <span class="text-danger">*</span></label>
                            <select name="template_type" id="template_type" class="form-control" required>
                                <option value="">Select Type</option>
                                @foreach($templateTypes as $key => $label)
                                    <option value="{{ $key }}" {{ old('template_type', $emailTemplate->template_type) == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('template_type')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="subject">Email Subject <span class="text-danger">*</span></label>
                    <input type="text" name="subject" id="subject" class="form-control" 
                           value="{{ old('subject', $emailTemplate->subject) }}" required>
                    @error('subject')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="html_content">HTML Content <span class="text-danger">*</span></label>
                    <div class="email-editor-container">
                        <div class="editor-toolbar">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="insertText('{{ $emailTemplate->name }}')">
                                <i class="fas fa-user"></i> Name
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="insertText('{{ $emailTemplate->email ?? 'user@example.com' }}')">
                                <i class="fas fa-envelope"></i> Email
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="insertText('{{ config('app.name') }}')">
                                <i class="fas fa-building"></i> Company
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-success" onclick="insertText('<br>')">
                                <i class="fas fa-level-down-alt"></i> Line Break
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-info" onclick="insertText('<p></p>')">
                                <i class="fas fa-paragraph"></i> Paragraph
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-warning" onclick="insertText('<h2></h2>')">
                                <i class="fas fa-heading"></i> Heading
                            </button>
                        </div>
                        <textarea name="html_content" id="html_content" class="form-control" rows="15" required>{{ old('html_content', $emailTemplate->html_content) }}</textarea>
                    </div>
                    @error('html_content')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="text_content">Text Content (Optional)</label>
                    <textarea name="text_content" id="text_content" class="form-control" rows="8">{{ old('text_content', $emailTemplate->text_content) }}</textarea>
                    @error('text_content')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" 
                                       value="1" {{ old('is_active', $emailTemplate->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active Template
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" name="is_default" id="is_default" class="form-check-input" 
                                       value="1" {{ old('is_default', $emailTemplate->is_default) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_default">
                                    Set as Default
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="preview_data">Preview Data (JSON)</label>
                    <textarea name="preview_data" id="preview_data" class="form-control" rows="4" 
                              placeholder='{"name": "John Doe", "email": "john@example.com", "company": "Example Corp"}'>{{ old('preview_data', $emailTemplate->preview_data ? json_encode($emailTemplate->preview_data, JSON_PRETTY_PRINT) : '') }}</textarea>
                    @error('preview_data')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="button-container">
                    <button type="button" class="btn btn-warning" onclick="previewTemplate()">
                        <i class="fas fa-eye"></i> Preview
                    </button>
                    <button type="reset" class="btn btn-secondary">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Update Template
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
                    <h5 class="modal-title">Email Template Preview</h5>
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
        .email-editor-container {
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .editor-toolbar {
            background: #f8f9fa;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }
        .editor-toolbar .btn {
            font-size: 12px;
        }
        #html_content {
            border: none;
            border-radius: 0;
            font-family: 'Courier New', monospace;
        }
        .button-container {
            margin-top: 20px;
            text-align: right;
        }
        .button-container .btn {
            margin-left: 10px;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        // Initialize TinyMCE
        tinymce.init({
            selector: '#html_content',
            height: 400,
            plugins: 'advlist autolink lists link image charmap print preview anchor searchreplace visualblocks code fullscreen insertdatetime media table paste code help wordcount',
            toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
            content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }',
            setup: function (editor) {
                editor.on('change', function () {
                    editor.save();
                });
            }
        });

        function insertText(text) {
            if (tinymce.get('html_content')) {
                tinymce.get('html_content').insertContent(text);
            } else {
                const textarea = document.getElementById('html_content');
                const start = textarea.selectionStart;
                const end = textarea.selectionEnd;
                const value = textarea.value;
                textarea.value = value.substring(0, start) + text + value.substring(end);
                textarea.selectionStart = textarea.selectionEnd = start + text.length;
            }
        }

        function previewTemplate() {
            const form = document.getElementById('email-template-form');
            const formData = new FormData(form);
            
            // Create preview endpoint
            fetch('{{ route("admin.email-templates.preview") }}', {
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

        // Auto-save draft
        let autoSaveTimeout;
        document.getElementById('html_content').addEventListener('input', function() {
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = setTimeout(function() {
                // Auto-save logic here
                console.log('Auto-saving draft...');
            }, 2000);
        });
    </script>
@endpush

