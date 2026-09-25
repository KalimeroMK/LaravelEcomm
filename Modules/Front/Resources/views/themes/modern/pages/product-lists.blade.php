@extends($themePath . '.layouts.master')
@section('title','E-SHOP || PRODUCT LIST')
@section('content')
@include($themePath . '.layouts.breadcrumbs', ['title' => 'Products'])

<section class="product-area shop-sidebar shop section">
    <div class="container">
        <div class="row">
            {{-- Sidebar --}}
            <div class="col-lg-3 col-md-4 col-12">
                <div class="shop-sidebar">
                    {{-- Categories --}}
                    <div class="single-widget category">
                        <h3 class="title">Categories</h3>
                        <ul class="categor-list">
                            @foreach (($categories ?? collect()) as $category)
                                <li><a href="{{ route('front.product-cat', $category->slug) }}">{{ $category->title }}</a></li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Recent Products --}}
                    @php $sidebarRecent = $recent_products ?? $recentProducts ?? collect(); @endphp
                    @if($sidebarRecent->isNotEmpty())
                        <div class="single-widget recent-post">
                            <h3 class="title">Recent Products</h3>
                            @foreach($sidebarRecent as $recent)
                                <div class="single-post first">
                                    <div class="image">
                                        <img src="{{ $recent->image_thumb_url }}" alt="{{ $recent->title }}">
                                    </div>
                                    <div class="content">
                                        <h5><a href="{{ route('front.product-detail', $recent->slug) }}">{{ $recent->title }}</a></h5>
                                        @php $org = ($recent->price - ($recent->price * $recent->discount) / 100); @endphp
                                        <p class="price">
                                            <del class="text-muted">${{ number_format($recent->price, 2) }}</del>
                                            ${{ number_format($org, 2) }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Brands --}}
                    @if(($brands ?? collect())->isNotEmpty())
                        <div class="single-widget category">
                            <h3 class="title">Brands</h3>
                            <ul class="categor-list">
                                @foreach($brands as $brand)
                                    <li><a href="{{ route('front.product-brand', $brand->slug) }}">{{ $brand->title }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Product List --}}
            <div class="col-lg-9 col-md-8 col-12">
                <div class="text-right mb-20">
                    <a href="{{ route('front.product-grids') }}" class="btn btn-sm btn-default"><i class="fa fa-th"></i></a>
                    <a href="javascript:void(0)" class="btn btn-sm btn-default active"><i class="fa fa-list"></i></a>
                </div>
                @forelse($products as $product)
                    <div class="row product-list-item mb-20" data-product-id="{{ $product->id }}">
                        <div class="col-md-4">
                            <a href="{{ route('front.product-detail', $product->slug) }}">
                                <img src="{{ $product->image_thumb_url }}" alt="{{ $product->title }}" class="img-responsive" style="width:100%;">
                            </a>
                        </div>
                        <div class="col-md-8">
                            <h3><a href="{{ route('front.product-detail', $product->slug) }}">{{ $product->title }}</a></h3>
                            @php $after_discount = ($product->price - ($product->price * $product->discount) / 100); @endphp
                            <h4 class="price">
                                @if($product->discount)<del class="text-muted">${{ number_format($product->price, 2) }}</del>@endif
                                ${{ number_format($after_discount, 2) }}
                            </h4>
                            <p>{!! html_entity_decode($product->summary) !!}</p>
                            <a href="{{ route('add-to-cart', $product->slug) }}" class="btn btn-default">Add to Cart</a>
                        </div>
                    </div>
                @empty
                    <div class="text-center" style="padding:80px 20px;">
                        <i class="fa fa-search" style="font-size:42px; color:#ccc;"></i>
                        <h4 class="text-warning" style="margin-top:20px;">@lang('frontend.there_are_no_products')</h4>
                        <a href="{{ route('front.product-grids') }}" class="btn mt-3">Browse all products</a>
                    </div>
                @endforelse
                @if($products->isNotEmpty())
                    <div class="text-center">
                        {{ $products->appends(request()->query())->links('vendor.pagination.bootstrap-4') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
