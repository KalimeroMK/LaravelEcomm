{{-- Modern Theme Head Meta/Links --}}
{{-- This file contains metadata and common links --}}
@yield('meta')

<!-- Favicon -->
<link rel="icon" type="image/png" href="{{asset('frontend/img/favicon.png')}}">
<!-- Web Font -->
<link href="https://fonts.googleapis.com/css?family=Poppins:200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&display=swap"
      rel="stylesheet">
@include('feed::links')

<!-- StyleSheet -->
<link rel="stylesheet" href="{{asset('frontend/themes/modern/css/style.css')}}">
<link rel="stylesheet" href="{{asset('frontend/css/font-awesome.css')}}">
<link rel="stylesheet" href="{{asset('frontend/css/themify-icons.css')}}">
<link rel="stylesheet" href="{{asset('frontend/themes/modern/css/animate.css')}}">

{{-- Additional Styles --}}
@stack('styles')
