<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<!-- Title Tag  -->
<head>
    <meta charset="utf-8">
    @yield('SOE')
    @if (Request::path() == '/')
        <title></title>
        <meta charset="utf-8">
        <title>@lang('frontend.meta_title')</title>
        <meta name="robots" content="index, follow">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link rel="profile" href="http://gmpg.org/xfn/11">
        <link type="text/plain" rel="author" href="http://yourwebsite.com/humans.txt"/>
        <meta property="og:title" content="@lang('frontend.meta_title')"/>
        <meta property="article:author" content="https://www.facebook.com/YourEcommercePage"/>
        <meta property="og:site_name" content="E-commerce Website"/>
        <meta property="fb:app_id" content="YourFacebookAppID"/>
        <meta name="google-site-verification" content="YourGoogleVerificationCode"/>
        <meta property="og:type" content="website"/>
        <meta property="og:image" content="http://yourwebsite.com/assets/img/logo/logo.jpg"/>
        <meta property="article:tag"
              content="@lang('frontend.meta_keywords')"/>
        <meta property="og:description"
              content="@lang('frontend.meta_description')"/>
        <meta name="description"
              content="@lang('frontend.meta_description')"/>
        <meta name="keywords"
              content="@lang('frontend.meta_keywords')"/>
        <meta name="author" content="Zoran Shefot Bogoevski">
    @endif
    <!-- Mobile Meta -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="57x57" href="/assets/img/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/assets/img/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/assets/img/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/assets/img/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/assets/img/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/assets/img/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/assets/img/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/img/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/img/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/assets/img/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/img/favicon/favicon-16x16.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">

    <!-- Favicon -->
    <link rel="shortcut icon" href="/assets/img/favicon.ico">
    @include('front::layouts.head')
    {!! $schema ?? '' !!}
    
    <!-- RTL Support for Arabic -->
    @if(app()->getLocale() == 'ar')
        <style>
            body {
                direction: rtl;
                text-align: right;
            }
            .col-lg-1, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6,
            .col-lg-7, .col-lg-8, .col-lg-9, .col-lg-10, .col-lg-11, .col-lg-12,
            .col-md-1, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6,
            .col-md-7, .col-md-8, .col-md-9, .col-md-10, .col-md-11, .col-md-12,
            .col-sm-1, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6,
            .col-sm-7, .col-sm-8, .col-sm-9, .col-sm-10, .col-sm-11, .col-sm-12 {
                float: right;
            }
        </style>
    @endif
</head>
<body class="js">
<!-- Preloader -->
<div class="preloader">
    <div class="preloader-inner">
        <div class="preloader-icon">
            <span></span>
            <span></span>
        </div>
    </div>
</div>
<!-- End Preloader -->
@include('front::layouts.notification')
<!-- Header -->
@include('front::layouts.header')
<!--/ End Header -->
@yield('content')

@include('front::layouts.footer')

</body>
</html>
