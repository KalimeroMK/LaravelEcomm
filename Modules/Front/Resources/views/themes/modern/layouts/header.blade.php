<!-- header start -->
<!-- classes:  -->
<!-- "fixed": enables fixed navigation mode (sticky menu) e.g. class="header fixed clearfix" -->
<!-- "dark": dark version of header e.g. class="header dark clearfix" -->
<!-- "full-width": mandatory class for the full-width menu layout -->
<!-- "centered": mandatory class for the centered logo layout -->
<!-- ================ --> 
<header class="header fixed full-width clearfix">
    
    <!-- header-second start -->
    <!-- ================ -->
    <div class="header-second clearfix">
        
        <!-- main-navigation start -->
        <!-- classes: -->
        <!-- "onclick": Makes the dropdowns open on click, this the default bootstrap behavior e.g. class="main-navigation onclick" -->
        <!-- "animated": Enables animations on dropdowns opening e.g. class="main-navigation animated" -->
        <!-- "with-dropdown-buttons": Mandatory class that adds extra space, to the main navigation, for the search and cart dropdowns -->
        <!-- ================ -->
        <div class="main-navigation animated with-dropdown-buttons">

            <!-- navbar start -->
            <!-- ================ -->
            <nav class="navbar navbar-default" role="navigation">
                <div class="container-fluid">

                    <!-- Toggle get grouped for better mobile display -->
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-1">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        
                        <!-- header-first start -->
                        <!-- ================ -->
                        <div class="header-first clearfix">

                            <!-- logo -->
                            <div id="logo-mobile" class="logo">
                                <a href="{{ route('front.index') }}">
                                    <img id="logo-img-mobile" src="{{ theme_asset('img/logo_light_blue.png') }}" alt="{{ $settings['site-name'] ?? 'E-commerce' }}">
                                </a>
                            </div>

                            <!-- name-and-slogan -->
                            <div class="site-slogan hidden-xs">
                                {{ $settings['short_des'] ?? 'Modern E-commerce Website' }}
                            </div>

                        </div>
                        <!-- header-first end -->
                        
                    </div>

                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse" id="navbar-collapse-1">
                        <!-- main-menu -->
                        <ul class="nav navbar-nav navbar-right">

                            <!-- Home -->
                            <li class="{{ request()->routeIs('front.index') ? 'active' : '' }}">
                                <a href="{{ route('front.index') }}">Home</a>
                            </li>

                            <!-- Products -->
                            <li class="dropdown {{ request()->routeIs('front.product*') ? 'active' : '' }}">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Products</a>
                                <ul class="dropdown-menu">
                                    <li><a href="{{ route('front.product-grids') }}"><i class="icon-basket-1 pr-10"></i>All Products</a></li>
                                    <li><a href="{{ route('front.product-lists') }}"><i class="icon-list pr-10"></i>Product Lists</a></li>
                                </ul>
                            </li>

                            <!-- Categories -->
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Categories</a>
                                <ul class="dropdown-menu">
                                    @if(isset($categories) && $categories->count() > 0)
                                        @foreach($categories->take(10) as $category)
                                            <li><a href="{{ route('front.product-cat', $category->slug) }}">{{ $category->title }}</a></li>
                                        @endforeach
                                    @else
                                        <li><a href="#">No categories available</a></li>
                                    @endif
                                </ul>
                            </li>

                            <!-- Brands -->
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Brands</a>
                                <ul class="dropdown-menu">
                                    @if(isset($brands) && $brands->count() > 0)
                                        @foreach($brands->take(10) as $brand)
                                            <li><a href="{{ route('front.product-brand', $brand->slug) }}">{{ $brand->title }}</a></li>
                                        @endforeach
                                    @else
                                        <li><a href="#">No brands available</a></li>
                                    @endif
                                </ul>
                            </li>

                            <!-- Blog -->
                            <li class="{{ request()->routeIs('front.blog*') ? 'active' : '' }}">
                                <a href="{{ route('front.blog') }}">Blog</a>
                            </li>

                            <!-- About -->
                            <li class="{{ request()->routeIs('front.about') ? 'active' : '' }}">
                                <a href="{{ route('front.about-us') }}">About</a>
                            </li>

                            <!-- Contact -->
                            <li class="{{ request()->routeIs('front.contact') ? 'active' : '' }}">
                                <a href="{{ route('front.contact') }}">Contact</a>
                            </li>

                            <!-- Search dropdown -->
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa fa-search"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <form class="navbar-form" role="search" action="{{ route('front.product-search') }}" method="GET">
                                            <div class="form-group">
                                                <input type="text" class="form-control" placeholder="Search products..." name="search" value="{{ request('search') }}">
                                            </div>
                                            <button type="submit" class="btn btn-default">Search</button>
                                        </form>
                                    </li>
                                </ul>
                            </li>

                            <!-- Cart dropdown -->
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa fa-shopping-cart"></i>
                                    <span class="badge" id="cart-count">0</span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <div class="cart-preview">
                                            <div id="cart-items">
                                                <p class="text-center">Your cart is empty</p>
                                            </div>
                                            <div class="cart-actions">
                                                <a href="{{ route('cart-list') }}" class="btn btn-primary btn-sm">View Cart</a>
                                                <a href="{{ route('front.checkout') }}" class="btn btn-success btn-sm">Checkout</a>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </li>

                            <!-- User Account -->
                            @auth
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <i class="fa fa-user"></i> {{ Auth::user()->name }}
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a href="{{ route('user.dashboard') }}"><i class="fa fa-dashboard pr-10"></i>Dashboard</a></li>
                                        <li><a href="{{ route('user.profile') }}"><i class="fa fa-user pr-10"></i>Profile</a></li>
                                        <li><a href="{{ route('user.orders') }}"><i class="fa fa-shopping-bag pr-10"></i>Orders</a></li>
                                        <li><a href="{{ route('user.wishlist') }}"><i class="fa fa-heart pr-10"></i>Wishlist</a></li>
                                        <li class="divider"></li>
                                        <li>
                                            <a href="{{ route('logout') }}" 
                                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                <i class="fa fa-sign-out pr-10"></i>Logout
                                            </a>
                                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                                @csrf
                                            </form>
                                        </li>
                                    </ul>
                                </li>
                            @else
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <i class="fa fa-user"></i> Account
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a href="{{ route('login') }}"><i class="fa fa-sign-in pr-10"></i>Login</a></li>
                                        <li><a href="{{ route('register') }}"><i class="fa fa-user-plus pr-10"></i>Register</a></li>
                                    </ul>
                                </li>
                            @endauth

                        </ul>
                    </div>
                </div>
            </nav>
            <!-- navbar end -->

        </div>
        <!-- main-navigation end -->

    </div>
    <!-- header-second end -->

</header>
<!-- header end -->
