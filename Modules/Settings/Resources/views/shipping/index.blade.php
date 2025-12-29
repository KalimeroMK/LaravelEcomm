@extends('admin::layouts.master')

@section('title', 'Shipping Settings')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Shipping Settings</h3>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('settings.shipping.update', $settings) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="default_shipping_method">Default Shipping Method</label>
                                <select class="form-control" id="default_shipping_method" name="default_shipping_method">
                                    <option value="">Select Method</option>
                                    <option value="flat_rate" {{ ($shippingSettings['default_shipping_method'] ?? '') == 'flat_rate' ? 'selected' : '' }}>Flat Rate</option>
                                    <option value="free_shipping" {{ ($shippingSettings['default_shipping_method'] ?? '') == 'free_shipping' ? 'selected' : '' }}>Free Shipping</option>
                                    <option value="weight_based" {{ ($shippingSettings['default_shipping_method'] ?? '') == 'weight_based' ? 'selected' : '' }}>Weight Based</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="free_shipping_threshold">Free Shipping Threshold (Amount)</label>
                                <input type="number" step="0.01" class="form-control" id="free_shipping_threshold" 
                                       name="free_shipping_threshold" 
                                       value="{{ $shippingSettings['free_shipping_threshold'] ?? '' }}">
                                <small class="form-text text-muted">Orders above this amount will qualify for free shipping</small>
                            </div>

                            <div class="form-group">
                                <label for="flat_rate_shipping">Flat Rate Shipping Price</label>
                                <input type="number" step="0.01" class="form-control" id="flat_rate_shipping" 
                                       name="flat_rate_shipping" 
                                       value="{{ $shippingSettings['flat_rate_shipping'] ?? '' }}">
                            </div>

                            <div class="form-group">
                                <label for="estimated_delivery_days">Estimated Delivery Days</label>
                                <input type="number" class="form-control" id="estimated_delivery_days" 
                                       name="estimated_delivery_days" 
                                       value="{{ $shippingSettings['estimated_delivery_days'] ?? '' }}">
                                <small class="form-text text-muted">Average number of days for delivery</small>
                            </div>

                            <button type="submit" class="btn btn-primary">Save Shipping Settings</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

