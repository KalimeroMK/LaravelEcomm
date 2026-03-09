@php use Modules\Core\Helpers\Helper; @endphp
@extends($themePath . '.layouts.master')
@section('title','Cart')
@section('content')
<!-- breadcrumb start -->
<div class="breadcrumb-container">
    <div class="container">
        <ol class="breadcrumb">
            <li><i class="fa fa-home pr-10"></i><a href="{{ route('front.index') }}">Home</a></li>
            <li class="active">Cart</li>
        </ol>
    </div>
</div>
<!-- breadcrumb end -->

<!-- main-container start -->
<section class="main-container">
    <div class="container">
        <div class="row">
            <div class="main col-md-12">
                <h1 class="page-title">Shopping Cart</h1>
                <div class="separator-2"></div>
                
                @if(Helper::getAllProductFromCart()->isEmpty())
                    <div class="alert alert-warning">
                        Your cart is empty. <a href="{{ route('front.product-grids') }}">Continue shopping</a>
                    </div>
                @else
                    <form action="{{route('cart-update')}}" method="POST">
                        @csrf
                        <table class="table cart">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th class="amount">Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="cart_item_list">
                                @foreach(Helper::getAllProductFromCart() as $key=>$cart)
                                @php
                                    $photo = $cart->product->image_url ?? 'default-image.jpg';
                                @endphp
                                <tr>
                                    <td class="product">
                                        <div class="media">
                                            <div class="media-left">
                                                <img src="{{$photo}}" alt="{{$cart->product['title']}}" style="width:60px;height:60px;object-fit:cover;">
                                            </div>
                                            <div class="media-body">
                                                <h5 class="media-heading">
                                                    <a href="{{route('front.product-detail',$cart->product['slug'])}}">{{$cart->product['title']}}</a>
                                                </h5>
                                                <small>{{ Str::limit($cart->product->summary, 50) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="price">${{number_format($cart['price'],2)}}</td>
                                    <td class="quantity">
                                        <div class="form-inline">
                                            <input type="number" name="quantity[{{$loop->index}}]" class="form-control" value="{{$cart->quantity}}" min="1" max="100" style="width:70px;">
                                            <input type="hidden" name="qty_id[]" value="{{$cart->id}}">
                                        </div>
                                    </td>
                                    <td class="amount">${{number_format($cart['amount'],2)}}</td>
                                    <td>
                                        <a href="{{route('cart-delete',$cart->id)}}" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to remove this item?')">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{route('front.product-grids')}}" class="btn btn-default">
                                    <i class="icon-left-open-big"></i> Continue Shopping
                                </a>
                                <button type="submit" class="btn btn-default">
                                    <i class="fa fa-refresh"></i> Update Cart
                                </button>
                            </div>
                            <div class="col-md-6 text-right">
                                <table class="table cart-total pull-right" style="width:auto;">
                                    <tbody>
                                        <tr>
                                            <td>Cart Subtotal:</td>
                                            <td class="amount">${{number_format(Helper::totalCartPrice(),2)}}</td>
                                        </tr>
                                        @if(session()->has('coupon'))
                                        <tr>
                                            <td>Discount:</td>
                                            <td class="amount">-${{number_format(Session::get('coupon')['value'],2)}}</td>
                                        </tr>
                                        @php
                                            $total_amount = Helper::totalCartPrice() - Session::get('coupon')['value'];
                                        @endphp
                                        <tr>
                                            <td><strong>Total:</strong></td>
                                            <td class="total-amount"><strong>${{number_format($total_amount,2)}}</strong></td>
                                        </tr>
                                        @else
                                        <tr>
                                            <td><strong>Total:</strong></td>
                                            <td class="total-amount"><strong>${{number_format(Helper::totalCartPrice(),2)}}</strong></td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="coupon">
                                    <h4>Apply Coupon</h4>
                                    <form action="{{route('coupon-store')}}" method="POST">
                                        @csrf
                                        <div class="input-group">
                                            <input type="text" name="code" class="form-control" placeholder="Enter Your Coupon">
                                            <span class="input-group-btn">
                                                <button class="btn btn-default" type="submit">Apply</button>
                                            </span>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="{{route('front.checkout')}}" class="btn btn-default">
                                    Checkout <i class="icon-right-open-big"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
