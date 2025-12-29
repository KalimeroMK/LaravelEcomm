@extends('admin::layouts.master')

@section('title', 'Edit Shipping Zone')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Edit Shipping Zone</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('shipping.zones.update', $zone) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="name">Zone Name *</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ $zone->name }}" required>
                            </div>

                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3">{{ $zone->description }}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="countries">Countries (ISO codes, comma-separated)</label>
                                <input type="text" class="form-control" id="countries" name="countries" 
                                       value="{{ $zone->countries ? implode(', ', $zone->countries) : '' }}"
                                       placeholder="US, CA, MX (leave empty for all countries)">
                            </div>

                            <div class="form-group">
                                <label for="priority">Priority</label>
                                <input type="number" class="form-control" id="priority" name="priority" value="{{ $zone->priority }}" min="0">
                            </div>

                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" 
                                           {{ $zone->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>

                            <h4 class="mt-4">Shipping Methods</h4>
                            <div id="methods-container">
                                @foreach($zone->methods as $index => $method)
                                    <div class="method-item border p-3 mb-3">
                                        <input type="hidden" name="methods[{{ $index }}][id]" value="{{ $method->id }}">
                                        <div class="form-group">
                                            <label>Shipping Method *</label>
                                            <select name="methods[{{ $index }}][shipping_id]" class="form-control" required>
                                                <option value="">Select Method</option>
                                                @foreach($shippingMethods as $shippingMethod)
                                                    <option value="{{ $shippingMethod->id }}" 
                                                            {{ $method->shipping_id == $shippingMethod->id ? 'selected' : '' }}>
                                                        {{ $shippingMethod->type }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Price *</label>
                                            <input type="number" step="0.01" name="methods[{{ $index }}][price]" 
                                                   class="form-control" value="{{ $method->price }}" required min="0">
                                        </div>
                                        <div class="form-group">
                                            <label>Free Shipping Threshold</label>
                                            <input type="number" step="0.01" name="methods[{{ $index }}][free_shipping_threshold]" 
                                                   class="form-control" value="{{ $method->free_shipping_threshold }}" min="0">
                                        </div>
                                        <div class="form-group">
                                            <label>Estimated Days</label>
                                            <input type="number" name="methods[{{ $index }}][estimated_days]" 
                                                   class="form-control" value="{{ $method->estimated_days }}" min="1">
                                        </div>
                                        <div class="form-group">
                                            <label>Priority</label>
                                            <input type="number" name="methods[{{ $index }}][priority]" 
                                                   class="form-control" value="{{ $method->priority }}" min="0">
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" name="methods[{{ $index }}][is_active]" 
                                                   value="1" {{ $method->is_active ? 'checked' : '' }}>
                                            <label class="form-check-label">Active</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-secondary" onclick="addMethod()">Add Method</button>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">Update Zone</button>
                                <a href="{{ route('shipping.zones.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let methodCount = {{ $zone->methods->count() }};
        function addMethod() {
            const container = document.getElementById('methods-container');
            const newMethod = container.firstElementChild.cloneNode(true);
            const inputs = newMethod.querySelectorAll('input, select');
            inputs.forEach(input => {
                if (input.name) {
                    input.name = input.name.replace(/\[\d+\]/, '[' + methodCount + ']');
                }
                if (input.type === 'hidden') {
                    input.remove();
                } else if (input.type !== 'checkbox') {
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

