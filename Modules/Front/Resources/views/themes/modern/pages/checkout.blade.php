@php 
use Modules\Core\Helpers\Helper;
$user = Auth::user();
$defaultAddress = $user?->defaultShippingAddress();
$cartItems = Helper::getAllProductFromCart();
$subtotal = Helper::totalCartPrice();
@endphp
@extends($themePath . '.layouts.master')
@section('title','Checkout')
@section('content')
<!-- breadcrumb start -->
<div class="breadcrumb-container">
    <div class="container">
        <ol class="breadcrumb">
            <li><i class="fa fa-home pr-10"></i><a href="{{ route('front.index') }}">Home</a></li>
            <li class="active">Checkout</li>
        </ol>
    </div>
</div>
<!-- breadcrumb end -->

<!-- main-container start -->
<section class="main-container">
    <div class="container">
        <div class="row">
            <div class="main col-md-12">
                <h1 class="page-title">Checkout</h1>
                <div class="separator-2"></div>
                
                @if($cartItems->isEmpty())
                    <div class="alert alert-warning">
                        Your cart is empty. <a href="{{ route('front.product-grids') }}">Continue shopping</a>
                    </div>
                @else
                <form method="POST" action="{{ route('front.cart.order') }}">
                    @csrf
                    
                    <!-- Cart Summary -->
                    <table class="table cart">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th class="amount">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cartItems as $item)
                            <tr>
                                <td class="product">
                                    <a href="{{ route('front.product-detail', $item->product->slug) }}">{{ $item->product->title }}</a>
                                    <small>{{ Str::limit($item->product->summary, 50) }}</small>
                                </td>
                                <td class="price">${{ number_format($item->price, 2) }}</td>
                                <td class="quantity">
                                    <div class="form-group">
                                        <input type="text" class="form-control" value="{{ $item->quantity }}" readonly>
                                    </div>
                                </td>
                                <td class="amount">${{ number_format($item->amount, 2) }}</td>
                            </tr>
                            @endforeach
                            <tr>
                                <td class="total-quantity" colspan="3">Subtotal</td>
                                <td class="amount">${{ number_format($subtotal, 2) }}</td>
                            </tr>
                            @if(session('coupon'))
                            <tr>
                                <td class="total-quantity" colspan="3">Discount</td>
                                <td class="amount">-${{ number_format(session('coupon.value'), 2) }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td class="total-quantity" colspan="3">Total</td>
                                <td class="total-amount">${{ number_format($subtotal - (session('coupon.value') ?? 0), 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div class="space-bottom"></div>
                    
                    <!-- Billing Information -->
                    <fieldset>
                        <legend>Billing Information</legend>
                        <div class="form-horizontal">
                            <div class="row">
                                <div class="col-lg-3">
                                    <h3 class="title">Personal Info</h3>
                                </div>
                                <div class="col-lg-8 col-lg-offset-1">
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">First Name<small class="text-default">*</small></label>
                                        <div class="col-md-10">
                                            <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $defaultAddress?->first_name) }}" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Last Name<small class="text-default">*</small></label>
                                        <div class="col-md-10">
                                            <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $defaultAddress?->last_name) }}" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Phone<small class="text-default">*</small></label>
                                        <div class="col-md-10">
                                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $defaultAddress?->phone) }}" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Email<small class="text-default">*</small></label>
                                        <div class="col-md-10">
                                            <input type="email" name="email" class="form-control" value="{{ old('email', $defaultAddress?->email ?? $user?->email) }}" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="space"></div>
                            <div class="row">
                                <div class="col-lg-3">
                                    <h3 class="title">Your Address</h3>
                                </div>
                                <div class="col-lg-8 col-lg-offset-1">
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Address<small class="text-default">*</small></label>
                                        <div class="col-md-10">
                                            <input type="text" name="address1" class="form-control" value="{{ old('address1', $defaultAddress?->address1) }}" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">City<small class="text-default">*</small></label>
                                        <div class="col-md-10">
                                            <input type="text" name="city" class="form-control" value="{{ old('city', $defaultAddress?->city) }}" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Country<small class="text-default">*</small></label>
                                        <div class="col-md-10">
                                            <select name="country" class="form-control">
                                                <option value="US" {{ old('country', $defaultAddress?->country) == 'US' ? 'selected' : '' }}>United States</option>
                                                <option value="MK" {{ old('country', $defaultAddress?->country) == 'MK' ? 'selected' : '' }}>North Macedonia</option>
                                                <option value="GB" {{ old('country', $defaultAddress?->country) == 'GB' ? 'selected' : '' }}>United Kingdom</option>
                                                <option value="DE" {{ old('country', $defaultAddress?->country) == 'DE' ? 'selected' : '' }}>Germany</option>
                                                <option value="FR" {{ old('country', $defaultAddress?->country) == 'FR' ? 'selected' : '' }}>France</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Zip Code<small class="text-default">*</small></label>
                                        <div class="col-md-10">
                                            <input type="text" name="post_code" class="form-control" value="{{ old('post_code', $defaultAddress?->post_code) }}" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    
                    <!-- Payment Method -->
                    <fieldset>
                        <legend>Payment Method</legend>
                        <div class="form-group">
                            <div class="col-md-12">
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="payment_method" value="cod" checked>
                                        <i class="fa fa-money pr-10"></i> Cash on Delivery
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="payment_method" value="stripe">
                                        <i class="fa fa-credit-card pr-10"></i> Credit Card (Stripe)
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="payment_method" value="paypal">
                                        <i class="fa fa-paypal pr-10"></i> PayPal
                                    </label>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    
                    <div class="text-right">
                        <a href="{{ route('cart-list') }}" class="btn btn-group btn-default">
                            <i class="icon-left-open-big"></i> Back to Cart
                        </a>
                        <button type="submit" class="btn btn-group btn-default">
                            Place Order <i class="icon-right-open-big"></i>
                        </button>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
