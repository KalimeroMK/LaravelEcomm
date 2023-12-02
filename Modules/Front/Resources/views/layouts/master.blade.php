<!DOCTYPE html>
<html lang="en">
<!-- Title Tag  -->
<head>
    <meta charset="utf-8">
    @yield('SOE')
    @if (Request::path() == '/')
        <title></title>
        <meta charset="utf-8">
        <title>E-commerce Website - Buy and Sell Products Online</title>
        <meta name="robots" content="index, follow">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link rel="profile" href="http://gmpg.org/xfn/11">
        <link type="text/plain" rel="author" href="http://yourwebsite.com/humans.txt"/>
        <meta property="og:title" content="E-commerce Website - Buy and Sell Products Online"/>
        <meta property="article:author" content="https://www.facebook.com/YourEcommercePage"/>
        <meta property="og:site_name" content="E-commerce Website"/>
        <meta property="fb:app_id" content="YourFacebookAppID"/>
        <meta name="google-site-verification" content="YourGoogleVerificationCode"/>
        <meta property="og:type" content="website"/>
        <meta property="og:image" content="http://yourwebsite.com/assets/img/logo/logo.jpg"/>
        <meta property="article:tag"
              content="e-commerce, online shopping, products, buy and sell, shopping website, online marketplace"/>
        <meta property="og:description"
              content="Shop for a wide range of products on our e-commerce website. Explore our selection of high-quality items and enjoy secure online shopping."/>
        <meta name="description"
              content="Shop for a wide range of products on our e-commerce website. Explore our selection of high-quality items and enjoy secure online shopping."/>
        <meta name="keywords"
              content="e-commerce, online shopping, products, buy and sell, shopping website, online marketplace, deals, discounts"/>
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
    {!! $schema !!}
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