@php
use Modules\Core\Helpers\Helper;
@endphp
@extends($themePath . '.layouts.master')
@section('title', $category->title)
@section('content')
<!-- breadcrumb start -->
<div class="breadcrumb-container">
    <div class="container">
        <ol class="breadcrumb">
            <li><i class="fa fa-home pr-10"></i><a href="{{ route('front.index') }}">Home</a></li>
            <li class="active">{{ $category->title }}</li>
        </ol>
    </div>
</div>
<!-- breadcrumb end -->

<!-- main-container start -->
<section class="main-container">
    <div class="container">
        <div class="row">
            <!-- main content start -->
            <div class="main col-md-12">
                <h1 class="page-title">{{ $category->title }}</h1>
                <div class="separator-2"></div>
                
                @if($category->summary)
                    <p class="lead">{{ $category->summary }}</p>
                @endif

                <!-- Child Categories Section -->
                @if($childCategories->isNotEmpty())
                    <h3 class="mt-4">Subcategories</h3>
                    <div class="separator"></div>
                    <div class="row">
                        @foreach($childCategories as $childCat)
                        <div class="col-md-4 col-sm-6">
                            <div class="image-box style-2 mb-20 bordered light-gray-bg">
                                <div class="overlay-container">
                                    @if($childCat->photo)
                                        <img src="{{ $childCat->photo }}" alt="{{ $childCat->title }}" style="height: 200px; object-fit: cover; width: 100%;">
                                    @else
                                        <img src="{{ asset('frontend/themes/modern/images/category-placeholder.jpg') }}" alt="{{ $childCat->title }}" style="height: 200px; object-fit: cover; width: 100%;">
                                    @endif
                                    <div class="overlay-to-top">
                                        <p class="small margin-clear"><em>{{ Str::limit($childCat->summary, 60) }}</em></p>
                                    </div>
                                </div>
                                <div class="body padding-horizontal-clear">
                                    <h4 class="title margin-clear">
                                        <a href="{{ route('front.product-cat', $childCat->slug) }}">{{ $childCat->title }}</a>
                                    </h4>
                                    <p class="small mb-10 text-muted">
                                        <i class="fa fa-folder-o pr-1"></i> 
                                        {{ $childCat->children_count ?? 0 }} subcategories
                                        <span class="pl-1 pr-1">|</span>
                                        {{ $childCat->products_count ?? 0 }} products
                                    </p>
                                    <a href="{{ route('front.product-cat', $childCat->slug) }}" class="btn btn-default btn-sm margin-clear">
                                        View <i class="fa fa-arrow-right pl-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <!-- Products Section (if no subcategories) -->
                    @if($products->isNotEmpty())
                        <h3 class="mt-4">Products</h3>
                        <div class="separator"></div>
                        <div class="row">
                            @foreach($products as $product)
                            <div class="col-md-4 col-sm-6">
                                <div class="listing-item bordered light-gray-bg mb-20">
                                    <div class="overlay-container">
                                        <img src="{{ $product->image_url ?? asset('frontend/themes/modern/images/product-placeholder.jpg') }}" alt="{{ $product->title }}" style="height: 250px; object-fit: cover; width: 100%;">
                                        <a href="{{ route('front.product-detail', $product->slug) }}" class="overlay-link"></a>
                                        @if($product->discount > 0)
                                            <span class="badge badge-danger">-{{ $product->discount }}%</span>
                                        @endif
                                    </div>
                                    <div class="body">
                                        <h4 class="title"><a href="{{ route('front.product-detail', $product->slug) }}">{{ $product->title }}</a></h4>
                                        <p class="small text-muted">{{ Str::limit($product->summary, 80) }}</p>
                                        <div class="elements-list clearfix">
                                            <span class="price">
                                                @if($product->discount > 0)
                                                    <del>${{ number_format($product->price, 2) }}</del>
                                                    <span class="text-default">${{ number_format($product->price - ($product->price * $product->discount / 100), 2) }}</span>
                                                @else
                                                    <span class="text-default">${{ number_format($product->price, 2) }}</span>
                                                @endif
                                            </span>
                                            <a href="{{ route('front.product-detail', $product->slug) }}" class="pull-right btn btn-sm btn-default btn-animated">
                                                View <i class="fa fa-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <!-- Pagination -->
                        <div class="row">
                            <div class="col-md-12 text-center">
                                {{ $products->links() }}
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">
                            No products found in this category.
                        </div>
                    @endif
                @endif
            </div>
            <!-- main content end -->
        </div>
    </div>
</section>

<!-- section start -->
@if($recentProducts->isNotEmpty())
<section class="pv-30 clearfix">
    <div class="container">
        <h3 class="title">Recent Products</h3>
        <div class="separator-2"></div>
        <div class="row">
            @foreach($recentProducts as $recentProduct)
            <div class="col-md-3 col-sm-6">
                <div class="listing-item bordered light-gray-bg mb-20">
                    <div class="overlay-container">
                        <img src="{{ $recentProduct->image_url ?? asset('frontend/themes/modern/images/product-placeholder.jpg') }}" alt="{{ $recentProduct->title }}" style="height: 180px; object-fit: cover; width: 100%;">
                        <a href="{{ route('front.product-detail', $recentProduct->slug) }}" class="overlay-link"></a>
                    </div>
                    <div class="body">
                        <h5 class="title"><a href="{{ route('front.product-detail', $recentProduct->slug) }}">{{ $recentProduct->title }}</a></h5>
                        <span class="price text-default">${{ number_format($recentProduct->price, 2) }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
<!-- section end -->
@endsection
