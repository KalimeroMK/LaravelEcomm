<!DOCTYPE html>
<html lang="en">
<!-- Title Tag -->
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
        <meta property="og:image" content="{{ asset("$themeAssetsPath/img/logo/logo.jpg") }}"/>
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
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset("$themeAssetsPath/img/favicon/apple-icon-57x57.png") }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset("$themeAssetsPath/img/favicon/apple-icon-60x60.png") }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset("$themeAssetsPath/img/favicon/apple-icon-72x72.png") }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset("$themeAssetsPath/img/favicon/apple-icon-76x76.png") }}">
    <link rel="apple-touch-icon" sizes="114x114"
          href="{{ asset("$themeAssetsPath/img/favicon/apple-icon-114x114.png") }}">
    <link rel="apple-touch-icon" sizes="120x120"
          href="{{ asset("$themeAssetsPath/img/favicon/apple-icon-120x120.png") }}">
    <link rel="apple-touch-icon" sizes="144x144"
          href="{{ asset("$themeAssetsPath/img/favicon/apple-icon-144x144.png") }}">
    <link rel="apple-touch-icon" sizes="152x152"
          href="{{ asset("$themeAssetsPath/img/favicon/apple-icon-152x152.png") }}">
    <link rel="apple-touch-icon" sizes="180x180"
          href="{{ asset("$themeAssetsPath/img/favicon/apple-icon-180x180.png") }}">
    <link rel="icon" type="image/png" sizes="192x192"
          href="{{ asset("$themeAssetsPath/img/favicon/android-icon-192x192.png") }}">
    <link rel="icon" type="image/png" sizes="32x32"
          href="{{ asset("$themeAssetsPath/img/favicon/favicon-32x32.png") }}">
    <link rel="icon" type="image/png" sizes="96x96"
          href="{{ asset("$themeAssetsPath/img/favicon/favicon-96x96.png") }}">
    <link rel="icon" type="image/png" sizes="16x16"
          href="{{ asset("$themeAssetsPath/img/favicon/favicon-16x16.png") }}">
    <link rel="manifest" href="{{ asset("$themeAssetsPath/manifest.json") }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ asset("$themeAssetsPath/img/favicon/ms-icon-144x144.png") }}">
    <meta name="theme-color" content="#ffffff">

    <!-- Favicon -->
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Cookie&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap"
          rel="stylesheet">

    <!-- Css Styles -->
    <link rel="stylesheet" href="{{ asset("$themeAssetsPath/css/bootstrap.min.css") }}" type="text/css">
    <link rel="stylesheet" href="{{ asset("$themeAssetsPath/css/font-awesome.min.css") }}" type="text/css">
    <link rel="stylesheet" href="{{ asset("$themeAssetsPath/css/elegant-icons.css") }}" type="text/css">
    <link rel="stylesheet" href="{{ asset("$themeAssetsPath/css/jquery-ui.min.css") }}" type="text/css">
    <link rel="stylesheet" href="{{ asset("$themeAssetsPath/css/magnific-popup.css") }}" type="text/css">
    <link rel="stylesheet" href="{{ asset("$themeAssetsPath/css/owl.carousel.min.css") }}" type="text/css">
    <link rel="stylesheet" href="{{ asset("$themeAssetsPath/css/slicknav.min.css") }}" type="text/css">
    {{--    <link rel="stylesheet" href="{{ asset("$themeAssetsPath/css/main.css") }}" type="text/css">--}}


</head>
<body>
<!-- Preloader -->
<!-- End Preloader -->
@include('front::ashion.layouts.notification')
<!-- Header -->
@include('front::ashion.layouts.header')
<!--/ End Header -->
@yield('content')

@include('front::ashion.layouts.footer')

<!-- Scripts -->
<!-- Js Plugins -->
<script src="{{ asset("$themeAssetsPath/js/jquery-3.3.1.min.js") }}"></script>
<script src="{{ asset("$themeAssetsPath/js/bootstrap.min.js") }}"></script>
<script src="{{ asset("$themeAssetsPath/js/jquery.magnific-popup.min.js") }}"></script>
<script src="{{ asset("$themeAssetsPath/js/jquery-ui.min.js") }}"></script>
<script src="{{ asset("$themeAssetsPath/js/mixitup.min.js") }}"></script>
<script src="{{ asset("$themeAssetsPath/js/jquery.countdown.min.js") }}"></script>
<script src="{{ asset("$themeAssetsPath/js/jquery.slicknav.js") }}"></script>
<script src="{{ asset("$themeAssetsPath/js/owl.carousel.min.js") }}"></script>
<script src="{{ asset("$themeAssetsPath/js/jquery.nicescroll.min.js") }}"></script>
<script src="{{ asset("$themeAssetsPath/js/main.js") }}"></script>
</body>
</html>
