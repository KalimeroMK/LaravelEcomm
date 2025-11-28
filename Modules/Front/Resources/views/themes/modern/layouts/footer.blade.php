<!-- footer top start -->
<!-- ================ -->
<div class="dark-bg default-hovered footer-top animated-text">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="call-to-action text-center">
                    <div class="row">
                        <div class="col-sm-8">
                            <h2>Modern E-commerce Website</h2>
                            <h2>Shop with confidence</h2>
                        </div>
                        <div class="col-sm-4">
                            <p class="mt-10">
                                <a href="{{ route('front.product-grids') }}" class="btn btn-animated btn-lg btn-gray-transparent">
                                    Shop Now<i class="fa fa-cart-arrow-down pl-20"></i>
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- footer top end -->

<!-- footer start (Add "dark" class to #footer in order to enable dark footer) -->
<!-- ================ -->
<footer id="footer" class="clearfix">

    <!-- .footer start -->
    <!-- ================ -->
    <div class="footer">
        <div class="container">
            <div class="footer-inner">
                <div class="row">
                    <div class="col-md-3">
                        <div class="footer-content">
                            <div class="logo-footer">
                                <img id="logo-footer" src="{{ theme_asset('img/logo_light_blue.png') }}" alt="{{ $settings['site-name'] ?? 'E-commerce' }}">
                            </div>
                            <p>{{ $settings['description'] ?? 'Modern e-commerce website with advanced features and beautiful design.' }}</p>
                            <p>Shop with confidence and enjoy the best shopping experience.</p>
                            <div class="icons-block mt-10 mb-10">
                                <i class="fa fa-cc-paypal"></i>
                                <i class="fa fa-cc-amex"></i>
                                <i class="fa fa-cc-discover"></i>
                                <i class="fa fa-cc-mastercard"></i>
                                <i class="fa fa-cc-visa"></i>
                                <i class="fa fa-cc-stripe"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="footer-content">
                            <h2 class="title">My Account</h2>
                            <div class="separator-2"></div>
                            <nav class="mb-20">
                                <ul class="nav nav-pills nav-stacked list-style-icons">
                                    @auth
                                        <li><a href="{{ route('user.dashboard') }}"><i class="icon-tools"></i> Dashboard</a></li>
                                        <li><a href="{{ route('user.orders') }}"><i class="icon-search"></i> My Orders</a></li>
                                        <li><a href="{{ route('cart-list') }}"><i class="icon-basket-1"></i> Cart</a></li>
                                        <li><a href="{{ route('user.wishlist') }}"><i class="icon-heart"></i> Wish List</a></li>
                                        <li><a href="{{ route('user.profile') }}"><i class="icon-chat"></i> Profile</a></li>
                                    @else
                                        <li><a href="{{ route('login') }}"><i class="icon-tools"></i> Login</a></li>
                                        <li><a href="{{ route('register') }}"><i class="icon-user"></i> Register</a></li>
                                        <li><a href="{{ route('cart-list') }}"><i class="icon-basket-1"></i> Cart</a></li>
                                    @endauth
                                    <li><a href="{{ route('front.contact') }}"><i class="icon-thumbs-up"></i> Support</a></li>
                                    <li><a href="#"><i class="icon-lock"></i> Privacy</a></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="footer-content">
                            <h2 class="title">Quick Links</h2>
                            <div class="separator-2"></div>
                            <nav class="mb-20">
                                <ul class="nav nav-pills nav-stacked list-style-icons">
                                    <li><a href="{{ route('front.index') }}"><i class="icon-home"></i> Home</a></li>
                                    <li><a href="{{ route('front.product-grids') }}"><i class="icon-basket-1"></i> All Products</a></li>
                                    <li><a href="{{ route('front.blog') }}"><i class="icon-pencil"></i> Blog</a></li>
                                    <li><a href="{{ route('front.about-us') }}"><i class="icon-info"></i> About Us</a></li>
                                    <li><a href="{{ route('front.contact') }}"><i class="icon-mail"></i> Contact</a></li>
                                    <li><a href="#"><i class="icon-help"></i> FAQ</a></li>
                                    <li><a href="#"><i class="icon-truck"></i> Shipping</a></li>
                                    <li><a href="#"><i class="icon-undo"></i> Returns</a></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="footer-content">
                            <h2 class="title">Contact Info</h2>
                            <div class="separator-2"></div>
                            <p>{{ $settings['short_des'] ?? 'Modern e-commerce website with advanced features.' }}</p>
                            <ul class="social-links circle animated-effect-1">
                                <li class="facebook"><a target="_blank" href="#"><i class="fa fa-facebook"></i></a></li>
                                <li class="twitter"><a target="_blank" href="#"><i class="fa fa-twitter"></i></a></li>
                                <li class="googleplus"><a target="_blank" href="#"><i class="fa fa-google-plus"></i></a></li>
                                <li class="linkedin"><a target="_blank" href="#"><i class="fa fa-linkedin"></i></a></li>
                                <li class="instagram"><a target="_blank" href="#"><i class="fa fa-instagram"></i></a></li>
                            </ul>
                            <div class="separator-2"></div>
                            <ul class="list-icons">
                                <li><i class="fa fa-map-marker pr-10 text-default"></i> {{ $settings['address'] ?? 'Your Address' }}</li>
                                <li><i class="fa fa-phone pr-10 text-default"></i> {{ $settings['phone'] ?? '+00 1234567890' }}</li>
                                <li><a href="mailto:{{ $settings['email'] ?? 'info@example.com' }}"><i class="fa fa-envelope-o pr-10"></i>{{ $settings['email'] ?? 'info@example.com' }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- .footer end -->

    <!-- .subfooter start -->
    <!-- ================ -->
    <div class="subfooter">
        <div class="container">
            <div class="subfooter-inner">
                <div class="row">
                    <div class="col-md-12">
                        <p class="text-center">
                            Copyright © {{ date('Y') }} {{ $settings['site-name'] ?? 'E-commerce Website' }}. All Rights Reserved
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- .subfooter end -->

</footer>
<!-- footer end -->
