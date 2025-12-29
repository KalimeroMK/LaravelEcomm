@extends('admin::layouts.master')

@section('title', 'Create Shipping Zone')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Create Shipping Zone</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('shipping.zones.store') }}" method="POST">
                            @csrf

                            <div class="form-group">
                                <label for="name">Zone Name *</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>

                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="countries">Countries (ISO codes, comma-separated)</label>
                                <input type="text" class="form-control" id="countries" name="countries" 
                                       placeholder="US, CA, MX (leave empty for all countries)">
                                <small class="form-text text-muted">Enter 2-letter country codes separated by commas</small>
                            </div>

                            <div class="form-group">
                                <label for="priority">Priority</label>
                                <input type="number" class="form-control" id="priority" name="priority" value="0" min="0">
                                <small class="form-text text-muted">Higher priority zones are checked first</small>
                            </div>

                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" checked>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>

                            <h4 class="mt-4">Shipping Methods</h4>
                            <div id="methods-container">
                                <div class="method-item border p-3 mb-3">
                                    <div class="form-group">
                                        <label>Shipping Method *</label>
                                        <select name="methods[0][shipping_id]" class="form-control" required>
                                            <option value="">Select Method</option>
                                            @foreach($shippingMethods as $method)
                                                <option value="{{ $method->id }}">{{ $method->type }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Price *</label>
                                        <input type="number" step="0.01" name="methods[0][price]" class="form-control" required min="0">
                                    </div>
                                    <div class="form-group">
                                        <label>Free Shipping Threshold</label>
                                        <input type="number" step="0.01" name="methods[0][free_shipping_threshold]" class="form-control" min="0">
                                    </div>
                                    <div class="form-group">
                                        <label>Estimated Days</label>
                                        <input type="number" name="methods[0][estimated_days]" class="form-control" min="1">
                                    </div>
                                    <div class="form-group">
                                        <label>Priority</label>
                                        <input type="number" name="methods[0][priority]" class="form-control" value="0" min="0">
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="methods[0][is_active]" value="1" checked>
                                        <label class="form-check-label">Active</label>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-secondary" onclick="addMethod()">Add Method</button>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">Create Zone</button>
                                <a href="{{ route('shipping.zones.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let methodCount = 1;
        function addMethod() {
            const container = document.getElementById('methods-container');
            const newMethod = container.firstElementChild.cloneNode(true);
            const inputs = newMethod.querySelectorAll('input, select');
            inputs.forEach(input => {
                if (input.name) {
                    input.name = input.name.replace(/\[0\]/, '[' + methodCount + ']');
                }
                if (input.type !== 'checkbox') {
                    input.value = '';
                } else {
                    input.checked = true;
                }
            });
            container.appendChild(newMethod);
            methodCount++;
        }
    </script>
@endsection

