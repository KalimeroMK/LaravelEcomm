<!DOCTYPE html>
<!--[if IE 9]> <html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="ie9"> <![endif]-->
<!--[if gt IE 9]> <html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="ie"> <![endif]-->
<!--[if !IE]><!-->
<html dir="ltr" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!--<![endif]-->

<head>
    <meta charset="utf-8">
    @php
        $settings = \Modules\Settings\Models\Setting::first();
        $seoSettings = $settings?->seo_settings ?? [];
        // Use $seo from SeoViewComposer if available, otherwise use settings from database
        $seoTitle = isset($seo) ? ($seo['title'] ?? null) : ($seoSettings['meta_title'] ?? config('app.name', 'E-commerce Store'));
        $seoDescription = isset($seo) ? ($seo['description'] ?? null) : ($seoSettings['meta_description'] ?? 'Shop online for quality products with fast delivery and secure payment. Best deals and discounts available.');
        $seoKeywords = isset($seo) ? ($seo['keywords'] ?? null) : ($seoSettings['meta_keywords'] ?? 'online shopping, ecommerce, products, deals, discounts');
        $metaTitle = $seoTitle ?? config('app.name', 'E-commerce Store');
        $metaDescription = $seoDescription ?? 'Shop online for quality products with fast delivery and secure payment. Best deals and discounts available.';
        $metaKeywords = $seoKeywords ?? 'online shopping, ecommerce, products, deals, discounts';
    @endphp
    <title>@yield('title', $metaTitle)</title>
    <meta name="description" content="@yield('description', $metaDescription)">
    <meta name="keywords" content="{{ $metaKeywords }}">
    <meta name="author" content="{{ config('app.name') }}">
    
    <?php if(isset($seoSettings['og_title']) && !empty($seoSettings['og_title'])): ?>
        <meta property="og:title" content="{{ $seoSettings['og_title'] }}">
    <?php endif; ?>
    <?php if(isset($seoSettings['og_description']) && !empty($seoSettings['og_description'])): ?>
        <meta property="og:description" content="{{ $seoSettings['og_description'] }}">
    <?php endif; ?>
    <?php if(isset($seoSettings['og_image']) && !empty($seoSettings['og_image'])): ?>
        <meta property="og:image" content="{{ $seoSettings['og_image'] }}">
    <?php endif; ?>
    
    <?php if(isset($seoSettings['twitter_card']) && !empty($seoSettings['twitter_card'])): ?>
        <meta name="twitter:card" content="{{ $seoSettings['twitter_card'] }}">
    <?php endif; ?>
    <?php if(isset($seoSettings['twitter_site']) && !empty($seoSettings['twitter_site'])): ?>
        <meta name="twitter:site" content="{{ $seoSettings['twitter_site'] }}">
    <?php endif; ?>
    
    <?php if(isset($seoSettings['google_analytics_id']) && !empty($seoSettings['google_analytics_id'])): ?>
        <!-- Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $seoSettings['google_analytics_id'] }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ $seoSettings['google_analytics_id'] }}');
        </script>
    <?php endif; ?>
    
    <?php if(isset($seoSettings['facebook_pixel_id']) && !empty($seoSettings['facebook_pixel_id'])): ?>
        <!-- Facebook Pixel Code -->
        <script>
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '{{ $seoSettings['facebook_pixel_id'] }}');
            fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none"
            src="https://www.facebook.com/tr?id={{ $seoSettings['facebook_pixel_id'] }}&ev=PageView&noscript=1"
        /></noscript>
    <?php endif; ?>

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
            <div class="container">
                @include($themePath . '.layouts.notification')
            </div>
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
