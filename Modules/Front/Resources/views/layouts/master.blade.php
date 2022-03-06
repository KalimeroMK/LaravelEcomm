<!DOCTYPE html>
<html lang="en">
<head>
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