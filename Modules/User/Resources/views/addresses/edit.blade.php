@extends('admin::layouts.master')

@section('title','Edit Address')

@section('content')

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h4 class="font-weight-bold m-0">Edit Address</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('user.addresses.update', $address) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="first_name">First Name <span class="text-danger">*</span></label>
                        <input type="text" name="first_name" id="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', $address->first_name) }}" required>
                        @error('first_name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="last_name">Last Name <span class="text-danger">*</span></label>
                        <input type="text" name="last_name" id="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name', $address->last_name) }}" required>
                        @error('last_name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $address->email) }}">
                        @error('email')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $address->phone) }}">
                        @error('phone')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="company">Company (optional)</label>
                <input type="text" name="company" id="company" class="form-control @error('company') is-invalid @enderror" value="{{ old('company', $address->company) }}">
                @error('company')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="country">Country <span class="text-danger">*</span></label>
                        <select name="country" id="country" class="form-control @error('country') is-invalid @enderror" required>
                            <option value="">Select Country</option>
                            <option value="MK" {{ old('country', $address->country) == 'MK' ? 'selected' : '' }}>North Macedonia</option>
                            <option value="AL" {{ old('country', $address->country) == 'AL' ? 'selected' : '' }}>Albania</option>
                            <option value="BG" {{ old('country', $address->country) == 'BG' ? 'selected' : '' }}>Bulgaria</option>
                            <option value="GR" {{ old('country', $address->country) == 'GR' ? 'selected' : '' }}>Greece</option>
                            <option value="RS" {{ old('country', $address->country) == 'RS' ? 'selected' : '' }}>Serbia</option>
                            <option value="HR" {{ old('country', $address->country) == 'HR' ? 'selected' : '' }}>Croatia</option>
                            <option value="SI" {{ old('country', $address->country) == 'SI' ? 'selected' : '' }}>Slovenia</option>
                            <option value="BA" {{ old('country', $address->country) == 'BA' ? 'selected' : '' }}>Bosnia and Herzegovina</option>
                            <option value="ME" {{ old('country', $address->country) == 'ME' ? 'selected' : '' }}>Montenegro</option>
                            <option value="XK" {{ old('country', $address->country) == 'XK' ? 'selected' : '' }}>Kosovo</option>
                            <option value="RO" {{ old('country', $address->country) == 'RO' ? 'selected' : '' }}>Romania</option>
                            <option value="DE" {{ old('country', $address->country) == 'DE' ? 'selected' : '' }}>Germany</option>
                            <option value="IT" {{ old('country', $address->country) == 'IT' ? 'selected' : '' }}>Italy</option>
                            <option value="FR" {{ old('country', $address->country) == 'FR' ? 'selected' : '' }}>France</option>
                            <option value="GB" {{ old('country', $address->country) == 'GB' ? 'selected' : '' }}>United Kingdom</option>
                            <option value="US" {{ old('country', $address->country) == 'US' ? 'selected' : '' }}>United States</option>
                            <option value="CA" {{ old('country', $address->country) == 'CA' ? 'selected' : '' }}>Canada</option>
                            <option value="AU" {{ old('country', $address->country) == 'AU' ? 'selected' : '' }}>Australia</option>
                        </select>
                        @error('country')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="city">City <span class="text-danger">*</span></label>
                        <input type="text" name="city" id="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', $address->city) }}" required>
                        @error('city')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="state">State/Province</label>
                        <input type="text" name="state" id="state" class="form-control @error('state') is-invalid @enderror" value="{{ old('state', $address->state) }}">
                        @error('state')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="post_code">Postal Code <span class="text-danger">*</span></label>
                        <input type="text" name="post_code" id="post_code" class="form-control @error('post_code') is-invalid @enderror" value="{{ old('post_code', $address->post_code) }}" required>
                        @error('post_code')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="address1">Address Line 1 <span class="text-danger">*</span></label>
                <input type="text" name="address1" id="address1" class="form-control @error('address1') is-invalid @enderror" value="{{ old('address1', $address->address1) }}" required placeholder="Street address, P.O. box, company name">
                @error('address1')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="address2">Address Line 2</label>
                <input type="text" name="address2" id="address2" class="form-control @error('address2') is-invalid @enderror" value="{{ old('address2', $address->address2) }}" placeholder="Apartment, suite, unit, building, floor, etc.">
                @error('address2')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="type">Address Type <span class="text-danger">*</span></label>
                        <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                            <option value="shipping" {{ old('type', $address->type) == 'shipping' ? 'selected' : '' }}>Shipping</option>
                            <option value="billing" {{ old('type', $address->type) == 'billing' ? 'selected' : '' }}>Billing</option>
                            <option value="both" {{ old('type', $address->type) == 'both' ? 'selected' : '' }}>Both</option>
                        </select>
                        @error('type')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" name="is_default" id="is_default" class="custom-control-input" value="1" {{ old('is_default', $address->is_default) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="is_default">Set as default address</label>
                </div>
            </div>

            <div class="form-group">
                <label for="notes">Delivery Notes (optional)</label>
                <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="2" placeholder="Any special instructions for delivery">{{ old('notes', $address->notes) }}</textarea>
                @error('notes')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Update Address</button>
                <a href="{{ route('user.addresses.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@endsection
