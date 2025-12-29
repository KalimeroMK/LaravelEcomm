@extends('admin::layouts.master')

@section('title', 'Payment Settings')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Payment Settings</h3>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('settings.payment.update', $settings) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <h4>Stripe Settings</h4>
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="stripe_enabled" 
                                           name="stripe_enabled" value="1" 
                                           {{ ($paymentSettings['stripe_enabled'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="stripe_enabled">
                                        Enable Stripe
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="stripe_public_key">Stripe Public Key</label>
                                <input type="text" class="form-control" id="stripe_public_key" 
                                       name="stripe_public_key" 
                                       value="{{ $paymentSettings['stripe_public_key'] ?? '' }}">
                            </div>
                            <div class="form-group">
                                <label for="stripe_secret_key">Stripe Secret Key</label>
                                <input type="password" class="form-control" id="stripe_secret_key" 
                                       name="stripe_secret_key" 
                                       value="{{ $paymentSettings['stripe_secret_key'] ?? '' }}">
                            </div>

                            <h4 class="mt-4">PayPal Settings</h4>
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="paypal_enabled" 
                                           name="paypal_enabled" value="1" 
                                           {{ ($paymentSettings['paypal_enabled'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="paypal_enabled">
                                        Enable PayPal
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="paypal_client_id">PayPal Client ID</label>
                                <input type="text" class="form-control" id="paypal_client_id" 
                                       name="paypal_client_id" 
                                       value="{{ $paymentSettings['paypal_client_id'] ?? '' }}">
                            </div>
                            <div class="form-group">
                                <label for="paypal_client_secret">PayPal Client Secret</label>
                                <input type="password" class="form-control" id="paypal_client_secret" 
                                       name="paypal_client_secret" 
                                       value="{{ $paymentSettings['paypal_client_secret'] ?? '' }}">
                            </div>
                            <div class="form-group">
                                <label for="paypal_mode">PayPal Mode</label>
                                <select class="form-control" id="paypal_mode" name="paypal_mode">
                                    <option value="sandbox" {{ ($paymentSettings['paypal_mode'] ?? 'sandbox') == 'sandbox' ? 'selected' : '' }}>Sandbox</option>
                                    <option value="live" {{ ($paymentSettings['paypal_mode'] ?? '') == 'live' ? 'selected' : '' }}>Live</option>
                                </select>
                            </div>

                            <h4 class="mt-4">Other Payment Methods</h4>
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="cod_enabled" 
                                           name="cod_enabled" value="1" 
                                           {{ ($paymentSettings['cod_enabled'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="cod_enabled">
                                        Enable Cash on Delivery
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="bank_transfer_enabled" 
                                           name="bank_transfer_enabled" value="1" 
                                           {{ ($paymentSettings['bank_transfer_enabled'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="bank_transfer_enabled">
                                        Enable Bank Transfer
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="bank_account_details">Bank Account Details</label>
                                <textarea class="form-control" id="bank_account_details" 
                                          name="bank_account_details" rows="3">{{ $paymentSettings['bank_account_details'] ?? '' }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">Save Payment Settings</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

