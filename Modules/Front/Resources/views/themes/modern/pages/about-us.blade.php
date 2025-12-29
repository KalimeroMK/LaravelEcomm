@extends($themePath . '.layouts.master')
@section('content')
<section class="page-header page-header-dark bg-secondary">
    <div class="container"><div class="row"><div class="col-md-12">
        <h1>About Us</h1>
        <ol class="breadcrumb">
            <li><a href="{{ route('front.index') }}">Home</a></li>
            <li class="active">About Us</li>
        </ol>
    </div></div></div>
</section>

<section class="main-container">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>Welcome to Our E-commerce Store</h2>
                <p class="lead">We are dedicated to providing you with the best products and excellent customer service.</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                
                <div class="row mt-40">
                    <div class="col-md-4 text-center">
                        <i class="fa fa-rocket fa-3x text-default"></i>
                        <h4>Fast Shipping</h4>
                        <p>We deliver your products quickly and safely to your doorstep.</p>
                    </div>
                    <div class="col-md-4 text-center">
                        <i class="fa fa-shield fa-3x text-default"></i>
                        <h4>Secure Payment</h4>
                        <p>Your transactions are protected with industry-standard encryption.</p>
                    </div>
                    <div class="col-md-4 text-center">
                        <i class="fa fa-headphones fa-3x text-default"></i>
                        <h4>24/7 Support</h4>
                        <p>Our customer service team is always here to help you.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@include($themePath . '.layouts.newsletter')
@endsection
