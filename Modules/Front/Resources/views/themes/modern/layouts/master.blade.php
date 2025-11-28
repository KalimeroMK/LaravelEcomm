<!DOCTYPE html>
<!--[if IE 9]> <html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="ie9"> <![endif]-->
<!--[if gt IE 9]> <html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="ie"> <![endif]-->
<!--[if !IE]><!-->
<html dir="ltr" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!--<![endif]-->

<head>
    <meta charset="utf-8">
    <title>@yield('title', 'E-commerce Website')</title>
    <meta name="description" content="@yield('description', 'Modern E-commerce Website')">
    <meta name="author" content="Laravel E-commerce">

    <!-- Mobile Meta -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ theme_asset('img/favicon.ico') }}">

    <!-- Web Fonts -->
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,300,300italic,400italic,500,500italic,700,700italic' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Raleway:700,400,300' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Pacifico' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=PT+Serif' rel='stylesheet' type='text/css'>

    <!-- Bootstrap core CSS -->
    <link href="{{ theme_asset('bootstrap/css/bootstrap.css') }}" rel="stylesheet">

    <!-- Font Awesome CSS -->
    <link href="{{ theme_asset('fonts/font-awesome/css/font-awesome.css') }}" rel="stylesheet">

    <!-- Fontello CSS -->
    <link href="{{ theme_asset('fonts/fontello/css/fontello.css') }}" rel="stylesheet">

    <!-- Plugins -->
    <link href="{{ theme_asset('plugins/magnific-popup/magnific-popup.css') }}" rel="stylesheet">
    <link href="{{ theme_asset('plugins/rs-plugin-5/css/settings.css') }}" rel="stylesheet">
    <link href="{{ theme_asset('plugins/rs-plugin-5/css/layers.css') }}" rel="stylesheet">
    <link href="{{ theme_asset('plugins/rs-plugin-5/css/navigation.css') }}" rel="stylesheet">
    <link href="{{ theme_asset('css/animations.css') }}" rel="stylesheet">
    <link href="{{ theme_asset('plugins/owlcarousel2/assets/owl.carousel.min.css') }}" rel="stylesheet">
    <link href="{{ theme_asset('plugins/owlcarousel2/assets/owl.theme.default.min.css') }}" rel="stylesheet">
    <link href="{{ theme_asset('plugins/hover/hover-min.css') }}" rel="stylesheet">
    
    <!-- The Project's core CSS file -->
    <link href="{{ theme_asset('css/style.css') }}" rel="stylesheet">
    <!-- The Project's Typography CSS file -->
    <link href="{{ theme_asset('css/typography-default.css') }}" rel="stylesheet">
    <!-- Color Scheme -->
    <link href="{{ theme_asset('css/skins/light_blue.css') }}" rel="stylesheet">
    
    <!-- Custom css --> 
    <link href="{{ theme_asset('css/custom.css') }}" rel="stylesheet">

    @stack('styles')
</head>

<!-- body classes:  -->
<!-- "boxed": boxed layout mode e.g. <body class="boxed"> -->
<!-- "pattern-1 ... pattern-9": background patterns for boxed layout mode e.g. <body class="boxed pattern-1"> -->
<!-- "transparent-header": makes the header transparent and pulls the banner to top -->
<!-- "gradient-background-header": applies gradient background to header -->
<!-- "page-loader-1 ... page-loader-6": add a page loader to the page (more info @components-page-loaders.html) -->
<body class="no-trans transparent-header">

    <!-- scrollToTop -->
    <!-- ================ -->
    <div class="scrollToTop circle"><i class="icon-up-open-big"></i></div>
    
    <!-- page wrapper start -->
    <!-- ================ -->
    <div class="page-wrapper">
    
        <!-- header-container start -->
        <div class="header-container">
            @include($themePath . '.layouts.header')
        </div>
        <!-- header-container end -->

        <!-- main-container start -->
        <!-- ================ -->
        <div class="main-container">
            @yield('content')
        </div>
        <!-- main-container end -->

        <!-- footer start -->
        <!-- ================ -->
        @include($themePath . '.layouts.footer')
        <!-- footer end -->
        
    </div>
    <!-- page-wrapper end -->

    <!-- JavaScript files placed at the end of the document so the pages load faster -->
    <!-- ================================================== -->
    <!-- Jquery and Bootstap core js files -->
    <script type="text/javascript" src="{{ theme_asset('plugins/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ theme_asset('bootstrap/js/bootstrap.min.js') }}"></script>
    <!-- Modernizr javascript -->
    <script type="text/javascript" src="{{ theme_asset('plugins/modernizr.js') }}"></script>
    <script type="text/javascript" src="{{ theme_asset('plugins/rs-plugin-5/js/jquery.themepunch.tools.min.js') }}"></script>
    <script type="text/javascript" src="{{ theme_asset('plugins/rs-plugin-5/js/jquery.themepunch.revolution.min.js') }}"></script>
    <!-- Isotope javascript -->
    <script type="text/javascript" src="{{ theme_asset('plugins/isotope/isotope.pkgd.min.js') }}"></script>
    <!-- Magnific Popup javascript -->
    <script type="text/javascript" src="{{ theme_asset('plugins/magnific-popup/jquery.magnific-popup.min.js') }}"></script>
    <!-- Appear javascript -->
    <script type="text/javascript" src="{{ theme_asset('plugins/waypoints/jquery.waypoints.min.js') }}"></script>
    <!-- Count To javascript -->
    <script type="text/javascript" src="{{ theme_asset('plugins/jquery.countTo.js') }}"></script>
    <!-- Parallax javascript -->
    <script src="{{ theme_asset('plugins/jquery.parallax-1.1.3.js') }}"></script>
    <!-- Contact form -->
    <script src="{{ theme_asset('plugins/jquery.validate.js') }}"></script>
    <!-- Background Video -->
    <script src="{{ theme_asset('plugins/vide/jquery.vide.js') }}"></script>
    <!-- Owl carousel javascript -->
    <script type="text/javascript" src="{{ theme_asset('plugins/owlcarousel2/owl.carousel.min.js') }}"></script>
    <!-- SmoothScroll javascript -->
    <script src="{{ theme_asset('plugins/jquery.browser.js') }}"></script>
    <script type="text/javascript" src="{{ theme_asset('plugins/SmoothScroll.js') }}"></script>
    <!-- Initialization of Plugins -->
    <script type="text/javascript" src="{{ theme_asset('js/template.js') }}"></script>
    <!-- Custom Scripts -->
    <script type="text/javascript" src="{{ theme_asset('js/custom.js') }}"></script>

    @stack('scripts')
</body>
</html>
