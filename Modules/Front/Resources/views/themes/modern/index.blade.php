@extends($themePath . '.layouts.master')

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
        <div id="main-slider" class="carousel slide" data-ride="carousel">
            <!-- Indicators -->
            <ol class="carousel-indicators">
                @if(isset($banners) && $banners->count() > 0)
                    @foreach($banners as $key => $banner)
                        <li data-target="#main-slider" data-slide-to="{{ $key }}" class="{{ $loop->first ? 'active' : '' }}"></li>
                    @endforeach
                @else
                    <li data-target="#main-slider" data-slide-to="0" class="active"></li>
                @endif
            </ol>

            <!-- Wrapper for slides -->
            <div class="carousel-inner" role="listbox">
                @if(isset($banners) && $banners->count() > 0)
                    @foreach($banners as $banner)
                        <div class="item {{ $loop->first ? 'active' : '' }}">
                            <img src="{{ $banner->imageUrl }}" alt="{{ $banner->title }}" style="width:100%; height: 500px; object-fit: cover;">
                            <div class="carousel-caption">
                                <h2 class="title text-white" style="color: #fff; font-size: 48px; font-weight: 700; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">{{ $banner->title }}</h2>
                                <div class="separator-2 light"></div>
                                <p style="color: #fff; font-size: 18px; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">{{ Str::limit($banner->description, 150) }}</p>
                                <a href="{{ route('front.product-grids') }}" class="btn btn-default btn-animated">
                                    Shop Now <i class="fa fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="item active">
                        <img src="{{ theme_asset('img/shop-slide-1.jpg') }}" alt="Default Banner" style="width:100%; height: 500px; object-fit: cover;">
                        <div class="carousel-caption">
                            <h2 class="title text-white" style="color: #fff; font-size: 48px; font-weight: 700; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">Welcome to {{ $settings['site-name'] ?? 'E-commerce' }}</h2>
                            <div class="separator-2 light"></div>
                            <p style="color: #fff; font-size: 18px; text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">{{ $settings['description'] ?? 'Discover amazing products with the best prices and quality.' }}</p>
                            <a href="{{ route('front.product-grids') }}" class="btn btn-default btn-animated">
                                Shop Now <i class="fa fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Controls -->
            <a class="left carousel-control" href="#main-slider" role="button" data-slide="prev">
                <span class="icon-prev" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="right carousel-control" href="#main-slider" role="button" data-slide="next">
                <span class="icon-next" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
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
                            @if(isset($latest_products) && $latest_products->count() > 0)
                                @foreach($latest_products->take(8) as $product)
                                    <div class="col-md-3 col-sm-6 masonry-grid-item">
                                        <div class="listing-item white-bg bordered mb-20">
                                            <div class="overlay-container">
                                                <img src="{{ $product->imageUrl }}" alt="{{ $product->title }}">
                                                <a class="overlay-link popup-img-single" href="{{ $product->imageUrl }}">
                                                    <i class="fa fa-search-plus"></i>
                                                </a>
                                                @if($product->discount > 0)
                                                    <span class="badge">{{ $product->discount }}% OFF</span>
                                                @endif
                                                <div class="overlay-to-top links">
                                                    <span class="small">
                                                        <a href="{{ route('add-to-wishlist', $product->slug) }}" class="btn-sm-link"><i class="fa fa-heart-o pr-10"></i>Add to Wishlist</a>
                                                        <a href="{{ route('front.product-detail', $product->slug) }}" class="btn-sm-link"><i class="icon-link pr-5"></i>View Details</a>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="body">
                                                <h3><a href="{{ route('front.product-detail', $product->slug) }}">{{ $product->title }}</a></h3>
                                                <p class="small">{{ Str::limit($product->description, 100) }}</p>
                                                <div class="elements-list clearfix">
                                                    <span class="price">{{ number_format($product->price, 2) }} {{ config('app.currency', '$') }}</span>
                                                    <a href="{{ route('add-to-cart', $product->slug) }}" class="pull-right margin-clear btn btn-sm btn-default-transparent btn-animated">
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
                        <div class="row masonry-grid-fitrows grid-space- 10">
                            @if(isset($featured_products) && $featured_products->count() > 0)
                                @foreach($featured_products->take(8) as $product)
                                    <div class="col-md-3 col-sm-6 masonry-grid-item">
                                        <div class="listing-item white-bg bordered mb-20">
                                            <div class="overlay-container">
                                                <img src="{{ $product->imageUrl }}" alt="{{ $product->title }}">
                                                <a class="overlay-link popup-img-single" href="{{ $product->imageUrl }}">
                                                    <i class="fa fa-search-plus"></i>
                                                </a>
                                                @if($product->discount > 0)
                                                    <span class="badge">{{ $product->discount }}% OFF</span>
                                                @endif
                                                <div class="overlay-to-top links">
                                                    <span class="small">
                                                        <a href="{{ route('add-to-wishlist', $product->slug) }}" class="btn-sm-link"><i class="fa fa-heart-o pr-10"></i>Add to Wishlist</a>
                                                        <a href="{{ route('front.product-detail', $product->slug) }}" class="btn-sm-link"><i class="icon-link pr-5"></i>View Details</a>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="body">
                                                <h3><a href="{{ route('front.product-detail', $product->slug) }}">{{ $product->title }}</a></h3>
                                                <p class="small">{{ Str::limit($product->description, 100) }}</p>
                                                <div class="elements-list clearfix">
                                                    <span class="price">{{ number_format($product->price, 2) }} {{ config('app.currency', '$') }}</span>
                                                    <a href="{{ route('add-to-cart', $product->slug) }}" class="pull-right margin-clear btn btn-sm btn-default-transparent btn-animated">
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
                                        <h3>Featured Products</h3>
                                        <p>No featured products available</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="tab-pane" id="pill-3">
                        <div class="row masonry-grid-fitrows grid-space-10">
                            @if(isset($hot_products) && $hot_products->count() > 0)
                                @foreach($hot_products->take(8) as $product)
                                    <div class="col-md-3 col-sm-6 masonry-grid-item">
                                        <div class="listing-item white-bg bordered mb-20">
                                            <div class="overlay-container">
                                                <img src="{{ $product->imageUrl }}" alt="{{ $product->title }}">
                                                <a class="overlay-link popup-img-single" href="{{ $product->imageUrl }}">
                                                    <i class="fa fa-search-plus"></i>
                                                </a>
                                                @if($product->discount > 0)
                                                    <span class="badge">{{ $product->discount }}% OFF</span>
                                                @endif
                                                <div class="overlay-to-top links">
                                                    <span class="small">
                                                        <a href="{{ route('add-to-wishlist', $product->slug) }}" class="btn-sm-link"><i class="fa fa-heart-o pr-10"></i>Add to Wishlist</a>
                                                        <a href="{{ route('front.product-detail', $product->slug) }}" class="btn-sm-link"><i class="icon-link pr-5"></i>View Details</a>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="body">
                                                <h3><a href="{{ route('front.product-detail', $product->slug) }}">{{ $product->title }}</a></h3>
                                                <p class="small">{{ Str::limit($product->description, 100) }}</p>
                                                <div class="elements-list clearfix">
                                                    <span class="price">{{ number_format($product->price, 2) }} {{ config('app.currency', '$') }}</span>
                                                    <a href="{{ route('add-to-cart', $product->slug) }}" class="pull-right margin-clear btn btn-sm btn-default-transparent btn-animated">
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
                                        <h3>Best Sellers</h3>
                                        <p>No best sellers available</p>
                                    </div>
                                </div>
                            @endif
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
@endpush
