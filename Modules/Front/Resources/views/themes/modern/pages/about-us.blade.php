@extends($themePath . '.layouts.master')

@section('title', 'About Us - ' . ($settings->first()?->site_name ?? 'E-SHOP'))

@section('content')
<section class="page-header page-header-dark bg-secondary">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>About Us</h1>
                <ol class="breadcrumb">
                    <li><a href="{{ route('front.index') }}">Home</a></li>
                    <li class="active">About Us</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<!-- About Us -->
<section class="about-us section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 col-12">
                <div class="about-content">
                    <h3>Welcome To <span>{{ $settings->first()?->site_name ?? 'Eshop' }}</span></h3>
                    <p>{{ $settings->first()?->description ?? $settings->first()?->short_des }}</p>
                    <div class="button">
                        <a href="{{ route('front.blog') }}" class="btn">Our Blog</a>
                        <a href="{{ route('front.contact') }}" class="btn primary">Contact Us</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-12">
                <div class="about-img"
                     style="background:#f6f7fb; border-radius:8px; display:flex; align-items:center; justify-content:center; min-height:320px; padding:40px;">
                    <img src="{{ $settings->first()?->getFirstMediaUrl('settings') ?: asset(ltrim($settings->first()?->logo ?? 'frontend/img/logo.png', '/')) }}"
                         alt="{{ $settings->first()?->site_name ?? 'logo' }}"
                         style="max-width:70%; max-height:240px; object-fit:contain;">
                </div>
            </div>
        </div>
    </div>
</section>
<!-- End About Us -->

<!-- Shop Services -->
<section class="shop-services section home">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6 col-12">
                <div class="single-service">
                    <i class="ti-rocket"></i>
                    <h4>Fast Shipping</h4>
                    <p>Quick and safe delivery to your doorstep</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <div class="single-service">
                    <i class="ti-reload"></i>
                    <h4>Free Returns</h4>
                    <p>Within 30 days of purchase</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <div class="single-service">
                    <i class="ti-lock"></i>
                    <h4>Secure Payment</h4>
                    <p>Industry-standard encryption</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <div class="single-service">
                    <i class="ti-headphone-alt"></i>
                    <h4>24/7 Support</h4>
                    <p>Our team is always here to help</p>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- End Shop Services -->

@include($themePath . '.layouts.newsletter')
@endsection
