<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- SEO Meta Tags -->
    <title>{{ $seo['title'] ?? config('app.name') }}</title>
    <meta name="description" content="{{ $seo['description'] ?? 'Online shopping store with quality products and fast delivery.' }}">
    <meta name="keywords" content="{{ $seo['keywords'] ?? 'online shopping, ecommerce, products, deals, discounts' }}">
    <meta name="author" content="{{ config('app.name') }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ $seo['canonical'] ?? request()->url() }}">

    <!-- Open Graph Meta Tags -->
    @if(isset($seo['og']) && !empty($seo['og']))
        @foreach($seo['og'] as $property => $content)
            <meta property="{{ $property }}" content="{{ $content }}">
        @endforeach
    @else
        <meta property="og:title" content="{{ $seo['og_settings']['og_title'] ?? $seo['title'] ?? config('app.name') }}">
        <meta property="og:description" content="{{ $seo['og_settings']['og_description'] ?? $seo['description'] ?? 'Online shopping store with quality products and fast delivery.' }}">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ request()->url() }}">
        <meta property="og:site_name" content="{{ config('app.name') }}">
        <meta property="og:image" content="{{ $seo['og_settings']['og_image'] ?? config('app.url').'/assets/img/logo/logo.png' }}">
    @endif

    <!-- Twitter Card Meta Tags -->
    @if(isset($seo['twitter']) && !empty($seo['twitter']))
        @foreach($seo['twitter'] as $name => $content)
            <meta name="{{ $name }}" content="{{ $content }}">
        @endforeach
    @else
        <meta name="twitter:card" content="{{ $seo['twitter_settings']['twitter_card'] ?? 'summary_large_image' }}">
        <meta name="twitter:title" content="{{ $seo['title'] ?? config('app.name') }}">
        <meta name="twitter:description" content="{{ $seo['description'] ?? 'Online shopping store with quality products and fast delivery.' }}">
        <meta name="twitter:image" content="{{ config('app.url') }}/assets/img/logo/logo.png">
        @if(!empty($seo['twitter_settings']['twitter_site']))
            <meta name="twitter:site" content="{{ $seo['twitter_settings']['twitter_site'] }}">
        @endif
    @endif

    <!-- Analytics & Tracking -->
    @if(!empty($seo['analytics']['google_analytics_id']))
        <!-- Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $seo['analytics']['google_analytics_id'] }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ $seo['analytics']['google_analytics_id'] }}');
        </script>
    @endif

    @if(!empty($seo['analytics']['google_tag_manager_id']))
        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','{{ $seo['analytics']['google_tag_manager_id'] }}');</script>
    @endif

    @if(!empty($seo['analytics']['facebook_pixel_id']))
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
            fbq('init', '{{ $seo['analytics']['facebook_pixel_id'] }}');
            fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none"
            src="https://www.facebook.com/tr?id={{ $seo['analytics']['facebook_pixel_id'] }}&ev=PageView&noscript=1"
        /></noscript>
    @endif
    
    <!-- Additional SEO Meta Tags -->
    <meta name="theme-color" content="#ffffff">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    
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
    <link rel="shortcut icon" href="/assets/img/favicon.ico">
    
    <!-- Preconnect to external domains for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="//www.google-analytics.com">
    
    <!-- Include head content -->
    @include($themePath . '.layouts.head')
    
    <!-- JSON-LD Structured Data -->
    @if(isset($seo['schema']))
        @if(is_array($seo['schema']) && isset($seo['schema'][0]))
            @foreach($seo['schema'] as $schema)
                <script type="application/ld+json">
                    {!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
                </script>
            @endforeach
        @else
            <script type="application/ld+json">
                {!! json_encode($seo['schema'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
            </script>
        @endif
    @endif
    
    <!-- Custom SEO content -->
    @yield('seo')
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
    
    @include($themePath . '.layouts.notification')
    
    <!-- Header -->
    @include($themePath . '.layouts.header')
    <!--/ End Header -->
    
    @yield('content')
    
    @include($themePath . '.layouts.footer')
    
    <!-- Performance optimization scripts -->
    <script>
        // Lazy loading for images
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });
            
            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
        
        // Preload critical resources
        const preloadLinks = [
            '/assets/css/critical.css',
            '/assets/js/critical.js'
        ];
        
        preloadLinks.forEach(href => {
            const link = document.createElement('link');
            link.rel = 'preload';
            link.href = href;
            link.as = href.endsWith('.css') ? 'style' : 'script';
            document.head.appendChild(link);
        });
    </script>
    
    @stack('scripts')
</body>
</html>
