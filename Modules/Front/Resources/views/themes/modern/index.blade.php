@extends('front::layouts.master')

@section('title', 'Home - ' . ($settings['site-name'] ?? 'E-commerce Website'))
@section('description', $settings['short_des'] ?? 'Modern e-commerce website with advanced features')

@section('content')

<!-- banner start -->
<!-- ================ -->
<div class="banner clearfix">

    <!-- slideshow start -->
    <!-- ================ -->
    <div class="slideshow">

        <!-- slider revolution start -->
        <!-- ================ -->
        <div class="slider-revolution-5-container">
            <div id="slider-banner-fullwidth-big-height" class="slider-banner-fullwidth-big-height rev_slider" data-version="5.0">
                <ul class="slides">
                    <!-- slide 1 start -->
                    <!-- ================ -->
                    <li data-transition="random" data-slotamount="default" data-masterspeed="default" data-title="Welcome to {{ $settings['site-name'] ?? 'E-commerce' }}">

                    <!-- main image -->
                    <img src="{{ theme_asset('img/shop-slide-1.jpg') }}" alt="slidebg1" data-bgposition="center top" data-bgrepeat="no-repeat" data-bgfit="cover" class="rev-slidebg">

                    <!-- Transparent Background -->
                    <div class="tp-caption dark-translucent-bg"
                        data-x="center"
                        data-y="center"
                        data-start="0"
                        data-transform_idle="o:1;"
                        data-transform_in="o:0;s:600;e:Power2.easeInOut;"
                        data-transform_out="o:0;s:600;"
                        data-width="5000"
                        data-height="5000">
                    </div>

                    <!-- LAYER NR. 1 -->
                    <div class="tp-caption large_white"
                        data-x="left"
                        data-y="155"
                        data-start="500"
                        data-transform_idle="o:1;"
                        data-transform_in="y:[100%];sX:1;sY:1;o:0;s:1150;e:Power4.easeInOut;"
                        data-transform_out="y:[100%];s:1000;e:Power2.easeInOut;"
                        data-mask_in="x:0px;y:[100%];s:inherit;e:inherit;"
                        data-mask_out="x:inherit;y:inherit;s:inherit;e:inherit;">
                        Welcome to <span class="text-default">{{ $settings['site-name'] ?? 'E-commerce' }}</span><br>
                        Modern Shopping Experience
                    </div>

                    <!-- LAYER NR. 2 -->
                    <div class="tp-caption large_white tp-resizeme"
                        data-x="left"
                        data-y="270"
                        data-start="750"
                        data-transform_idle="o:1;"
                        data-transform_in="o:0;s:2000;e:Power4.easeInOut;">
                            <div class="separator-2 light"></div>
                    </div>

                    <!-- LAYER NR. 3 -->
                    <div class="tp-caption medium_white"
                        data-x="left"
                        data-y="290"
                        data-start="750"
                        data-transform_idle="o:1;"
                        data-transform_in="y:[100%];sX:1;sY:1;s:850;e:Power4.easeInOut;"
                        data-transform_out="y:[100%];s:1000;e:Power2.easeInOut;"
                        data-mask_in="x:0px;y:0px;s:inherit;e:inherit;"
                        data-mask_out="x:inherit;y:inherit;s:inherit;e:inherit;">
                        {{ $settings['description'] ?? 'Discover amazing products with the best prices and quality.' }}<br>
                        Shop with confidence and enjoy fast delivery.<br>
                        Modern e-commerce experience awaits you.
                    </div>

                    <!-- LAYER NR. 4 -->
                    <div class="tp-caption small_white"
                        data-x="left"
                        data-y="410"
                        data-start="1000"
                        data-transform_idle="o:1;"
                        data-transform_in="y:[100%];sX:1;sY:1;o:0;s:600;e:Power4.easeInOut;"
                        data-transform_out="y:[100%];s:1000;e:Power2.easeInOut;"
                        data-mask_in="x:0px;y:[100%];s:inherit;e:inherit;"
                        data-mask_out="x:inherit;y:inherit;s:inherit;e:inherit;">
                        <a href="{{ route('front.product-grids') }}" class="btn btn-default btn-animated">
                            Shop Now <i class="fa fa-arrow-right"></i>
                        </a>
                    </div>

                    </li>
                    <!-- slide 1 end -->

                    <!-- slide 2 start -->
                    <!-- ================ -->
                    <li class="text-right" data-transition="random" data-slotamount="default" data-masterspeed="default" data-title="New Arrivals">

                    <!-- main image -->
                    <img src="{{ theme_asset('img/shop-slide-2.jpg') }}" alt="slidebg2" data-bgposition="center top" data-bgrepeat="no-repeat" data-bgfit="cover" class="rev-slidebg">

                    <!-- Transparent Background -->
                    <div class="tp-caption dark-translucent-bg"
                        data-x="center"
                        data-y="center"
                        data-start="0"
                        data-transform_idle="o:1;"
                        data-transform_in="o:0;s:600;e:Power2.easeInOut;"
                        data-transform_out="o:0;s:600;"
                        data-width="5000"
                        data-height="5000">
                    </div>

                    <!-- LAYER NR. 1 -->
                    <div class="tp-caption large_white"
                        data-x="right"
                        data-y="155"
                        data-start="500"
                        data-transform_idle="o:1;"
                        data-transform_in="y:[100%];sX:1;sY:1;o:0;s:1150;e:Power4.easeInOut;"
                        data-transform_out="y:[100%];s:1000;e:Power2.easeInOut;"
                        data-mask_in="x:0px;y:[100%];s:inherit;e:inherit;"
                        data-mask_out="x:inherit;y:inherit;s:inherit;e:inherit;">
                        <span class="text-default">New</span> Arrivals<br>
                        Latest Products & Trends
                    </div>

                    <!-- LAYER NR. 2 -->
                    <div class="tp-caption large_white tp-resizeme"
                        data-x="right"
                        data-y="270"
                        data-start="750"
                        data-transform_idle="o:1;"
                        data-transform_in="o:0;s:2000;e:Power4.easeInOut;">
                            <div class="separator-3 light"></div>
                    </div>

                    <!-- LAYER NR. 3 -->
                    <div class="tp-caption medium_white"
                        data-x="right"
                        data-y="290"
                        data-start="750"
                        data-transform_idle="o:1;"
                        data-transform_in="y:[100%];sX:1;sY:1;s:850;e:Power4.easeInOut;"
                        data-transform_out="y:[100%];s:1000;e:Power2.easeInOut;"
                        data-mask_in="x:0px;y:0px;s:inherit;e:inherit;"
                        data-mask_out="x:inherit;y:inherit;s:inherit;e:inherit;">
                        Discover the latest products and trends.<br>
                        Stay ahead with our newest arrivals.<br>
                        Quality products at competitive prices.
                    </div>

                    <!-- LAYER NR. 4 -->
                    <div class="tp-caption small_white"
                        data-x="right"
                        data-y="410"
                        data-start="1000"
                        data-transform_idle="o:1;"
                        data-transform_in="y:[100%];sX:1;sY:1;o:0;s:600;e:Power4.easeInOut;"
                        data-transform_out="y:[100%];s:1000;e:Power2.easeInOut;"
                        data-mask_in="x:0px;y:[100%];s:inherit;e:inherit;"
                        data-mask_out="x:inherit;y:inherit;s:inherit;e:inherit;">
                        <a href="{{ route('front.product-grids') }}" class="btn btn-default btn-animated">
                            Explore Now <i class="fa fa-arrow-right"></i>
                        </a>
                    </div>

                    </li>
                    <!-- slide 2 end -->
                </ul>
                <div class="tp-bannertimer"></div>
            </div>
        </div>
        <!-- slider revolution end -->

    </div>
    <!-- slideshow end -->

</div>
<!-- banner end -->

<div id="page-start"></div>

<!-- section start -->
<!-- ================ -->
<section class="section light-gray-bg clearfix">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <!-- pills start -->
                <!-- ================ -->
                <!-- Nav tabs -->
                <ul class="nav nav-pills" role="tablist">
                    <li class="active"><a href="#pill-1" role="tab" data-toggle="tab" title="Latest Products"><i class="icon-star"></i> Latest Products</a></li>
                    <li><a href="#pill-2" role="tab" data-toggle="tab" title="Featured"><i class="icon-heart"></i> Featured</a></li>
                    <li><a href="#pill-3" role="tab" data-toggle="tab" title="Best Sellers"><i class="icon-up-1"></i> Best Sellers</a></li>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content clear-style">
                    <div class="tab-pane active" id="pill-1">
                        <div class="row masonry-grid-fitrows grid-space-10">
                            @if(isset($products) && $products->count() > 0)
                                @foreach($products->take(8) as $product)
                                    <div class="col-md-3 col-sm-6 masonry-grid-item">
                                        <div class="listing-item white-bg bordered mb-20">
                                            <div class="overlay-container">
                                                <img src="{{ $product->image ?? theme_asset('img/product-1.jpg') }}" alt="{{ $product->title }}">
                                                <a class="overlay-link popup-img-single" href="{{ $product->image ?? theme_asset('img/product-1.jpg') }}">
                                                    <i class="fa fa-search-plus"></i>
                                                </a>
                                                @if($product->discount > 0)
                                                    <span class="badge">{{ $product->discount }}% OFF</span>
                                                @endif
                                                <div class="overlay-to-top links">
                                                    <span class="small">
                                                        <a href="#" class="btn-sm-link"><i class="fa fa-heart-o pr-10"></i>Add to Wishlist</a>
                                                        <a href="{{ route('front.product-detail', $product->slug) }}" class="btn-sm-link"><i class="icon-link pr-5"></i>View Details</a>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="body">
                                                <h3><a href="{{ route('front.product-detail', $product->slug) }}">{{ $product->title }}</a></h3>
                                                <p class="small">{{ Str::limit($product->description, 100) }}</p>
                                                <div class="elements-list clearfix">
                                                    <span class="price">{{ number_format($product->price, 2) }} {{ config('app.currency', '$') }}</span>
                                                    <a href="#" class="pull-right margin-clear btn btn-sm btn-default-transparent btn-animated">
                                                        Add to Cart<i class="fa fa-shopping-cart"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-md-12">
                                    <div class="text-center">
                                        <h3>No products available</h3>
                                        <p>Check back later for new products!</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="tab-pane" id="pill-2">
                        <div class="row masonry-grid-fitrows grid-space-10">
                            <div class="col-md-12">
                                <div class="text-center">
                                    <h3>Featured Products</h3>
                                    <p>Coming soon - featured products will be displayed here!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="pill-3">
                        <div class="row masonry-grid-fitrows grid-space-10">
                            <div class="col-md-12">
                                <div class="text-center">
                                    <h3>Best Sellers</h3>
                                    <p>Coming soon - best selling products will be displayed here!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- pills end -->
            </div>
        </div>
    </div>
</section>
<!-- section end -->

<!-- section start -->
<!-- ================ -->
<section class="section clearfix">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1 class="text-center title">Why Choose Us?</h1>
                <div class="separator"></div>
                <p class="text-center">{{ $settings['short_des'] ?? 'We provide the best shopping experience with quality products and excellent service.' }}</p>
                <div class="row grid-space-20">
                    <div class="col-md-3 col-sm-6">
                        <div class="box-style-1 white-bg object-non-visible" data-animation-effect="fadeInUpSmall" data-effect-delay="0">
                            <i class="fa fa-truck text-default"></i>
                            <h2>Free Shipping</h2>
                            <p>Free shipping on orders over $50. Fast and reliable delivery to your doorstep.</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="box-style-1 white-bg object-non-visible" data-animation-effect="fadeInUpSmall" data-effect-delay="200">
                            <i class="fa fa-shield text-default"></i>
                            <h2>Secure Payment</h2>
                            <p>Your payment information is safe and secure with our encrypted payment system.</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="box-style-1 white-bg object-non-visible" data-animation-effect="fadeInUpSmall" data-effect-delay="400">
                            <i class="fa fa-refresh text-default"></i>
                            <h2>Easy Returns</h2>
                            <p>30-day return policy. Easy returns and exchanges for your peace of mind.</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="box-style-1 white-bg object-non-visible" data-animation-effect="fadeInUpSmall" data-effect-delay="600">
                            <i class="fa fa-headphones text-default"></i>
                            <h2>24/7 Support</h2>
                            <p>Round-the-clock customer support to help you with any questions or concerns.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- section end -->

@endsection

@push('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        // Initialize Revolution Slider
        if ($('#slider-banner-fullwidth-big-height').length) {
            $('#slider-banner-fullwidth-big-height').revolution({
                delay: 9000,
                startwidth: 1170,
                startheight: 500,
                hideThumbs: 10,
                fullWidth: "on",
                forceFullWidth: "on"
            });
        }
    });
</script>
@endpush
