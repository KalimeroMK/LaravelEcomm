<!-- Meta Tag -->
@yield('meta')
<!-- Favicon -->
<link rel="icon" type="image/png" href="images/favicon.png">
<!-- Web Font -->
<link href="https://fonts.googleapis.com/css?family=Poppins:200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&display=swap"
      rel="stylesheet">
@include('feed::links')

<!-- StyleSheet -->
<link rel="stylesheet" href="{{asset('frontend/css/all_front.min.css')}}">
<!-- Font Awesome -->
<link rel="stylesheet" href="{{asset('frontend/css/font-awesome.css')}}">
<!-- Themify Icons -->
<link rel="stylesheet" href="{{asset('frontend/css/themify-icons.css')}}">

<script type='text/javascript'
        src='https://platform-api.sharethis.com/js/sharethis.js#property=5f2e5abf393162001291e431&product=inline-share-buttons'
        async='async'></script>
<style>
    /* Multilevel dropdown */
    .dropdown-submenu {
        position: relative;
    }

    .dropdown-submenu > a:after {
        content: "\f0da";
        float: right;
        border: none;
        font-family: 'FontAwesome';
    }

    .dropdown-submenu > .dropdown-menu {
        top: 0;
        left: 100%;
        margin-top: 0px;
        margin-left: 0px;
    }

    /*

    /* Language Switcher Styles */
    .language-switcher-item {
        display: inline-block;
        margin-left: 15px;
    }

    .language-switcher-item .language-switcher {
        display: inline-block;
    }

    .language-switcher-item .language-switcher .dropdown-toggle {
        display: flex;
        align-items: center;
        gap: 5px;
        padding: 5px 10px;
        background: transparent;
        border: 1px solid #ddd;
        border-radius: 4px;
        color: #333;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .language-switcher-item .language-switcher .dropdown-toggle:hover {
        background: #f5f5f5;
        border-color: #999;
    }

    .language-switcher-item .language-switcher .flag {
        font-size: 16px;
    }

    .language-switcher-item .language-switcher .language-name {
        font-weight: 500;
    }

    .language-switcher-item .dropdown-menu {
        position: absolute;
        top: 100%;
        right: 0;
        z-index: 1000;
        min-width: 150px;
        padding: 5px 0;
        margin: 2px 0 0;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        list-style: none;
    }

    .language-switcher-item .dropdown-menu li {
        display: block;
        width: 100%;
    }

    .language-switcher-item .dropdown-menu .dropdown-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 15px;
        color: #333;
        text-decoration: none;
        transition: background 0.2s;
    }

    .language-switcher-item .dropdown-menu .dropdown-item:hover {
        background: #f5f5f5;
    }

    /* Dark header variant */
    .header.shop .topbar .language-switcher-item .language-switcher .dropdown-toggle {
        color: #fff;
        border-color: rgba(255,255,255,0.3);
    }

    .header.shop .topbar .language-switcher-item .language-switcher .dropdown-toggle:hover {
        background: rgba(255,255,255,0.1);
    }
</style>
@stack('styles')
