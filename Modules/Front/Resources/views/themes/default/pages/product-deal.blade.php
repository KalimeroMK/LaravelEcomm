@extends($themePath . '.layouts.master')

@section('title', __('frontend.daily_deal') . ' | E-SHOP')

@section('content')
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="{{ route('front.index') }}">@lang('frontend.home')<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="{{ route('front.product-deal') }}">@lang('frontend.daily_deal')</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--/ End Breadcrumbs -->

    <section class="product-area shop-sidebar shop section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="row">
                        @if(count($products) > 0)
                            @foreach($products as $product)
                                <div class="col-lg-3 col-md-4 col-12 product-list-item" data-product-id="{{ $product->id }}">
                                    <div class="single-product">
                                        <div class="product-img">
                                            <a href="{{ route('front.product-detail', $product->slug) }}">
                                                <img class="default-img" src="{{ $product->image_thumb_url }}" alt="{{ $product->title }}">
                                                <img class="hover-img" src="{{ $product->image_thumb_url }}" alt="{{ $product->title }}">
                                                @if($product->discount)
                                                    <span class="price-dec">{{ $product->discount }} % @lang('frontend.off')</span>
                                                @endif
                                            </a>
                                            <div class="button-head">
                                                <div class="product-action-2">
                                                    <a title="@lang('frontend.add_to_cart')" href="{{ route('add-to-cart', $product->slug) }}">@lang('frontend.add_to_cart')</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="product-content">
                                            <h3>
                                                <a href="{{ route('front.product-detail', $product->slug) }}">{{ $product->title }}</a>
                                            </h3>
                                            @php
                                                $after_discount = ($product->price - ($product->price * $product->discount) / 100);
                                            @endphp
                                            <span>{{ currency($after_discount) }}</span>
                                            <del style="padding-left:4%;">{{ currency($product->price) }}</del>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="col-12">
                                <h4 class="text-center">@lang('partials.no_records_found')</h4>
                            </div>
                        @endif
                    </div>
                    @if(method_exists($products, 'links'))
                        <div class="row">
                            <div class="col-12 d-flex justify-content-center">
                                {{ $products->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
