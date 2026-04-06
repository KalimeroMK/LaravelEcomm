@php
use Modules\Core\Helpers\Helper;
@endphp
@extends($themePath . '.layouts.master')
@section('title', $category->title)
@section('content')
    <style>
        .pagination { justify-content: center; display: flex; list-style: none; padding: 0; margin: 20px 0; }
        .pagination .page-item { display: inline-block; margin: 0 3px; }
        .pagination .page-item.active .page-link { background-color: #f7941d; border-color: #f7941d; color: #fff; }
        .pagination .page-link { 
            color: #333; 
            display: block; 
            padding: 8px 16px; 
            border: 1px solid #ddd; 
            text-decoration: none; 
            border-radius: 4px;
            min-width: 40px;
            text-align: center;
        }
        .pagination .page-link:hover { background-color: #f5f5f5; border-color: #f7941d; color: #f7941d; }
    </style>
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="{{ route('front.index') }}">Home<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="{{ route('front.product-cat', $category->slug) }}">{{ $category->title }}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <!-- Product Style 1 -->
    <section class="product-area shop-sidebar shop-list shop section">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                    <div class="shop-sidebar">
                        <!-- Single Widget -->
                        <div class="single-widget category">
                            <h3 class="title">{{ $category->title }}</h3>
                            @if($category->summary)
                                <p>{{ $category->summary }}</p>
                            @endif
                        </div>
                        <!--/ End Single Widget -->

                        <!-- Single Widget -->
                        @if($childCategories->isNotEmpty())
                        <div class="single-widget category">
                            <h3 class="title">Subcategories</h3>
                            <ul class="categor-list">
                                @foreach($childCategories as $childCat)
                                <li>
                                    <a href="{{ route('front.product-cat', $childCat->slug) }}">
                                        {{ $childCat->title }}
                                        <span>({{ $childCat->products_count ?? 0 }})</span>
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        <!--/ End Single Widget -->
                    </div>
                </div>

                <div class="col-lg-9 col-md-8 col-12">
                    <div class="row">
                        <div class="col-12">
                            <!-- Shop Top -->
                            <div class="shop-top">
                                <div class="shop-shorter">
                                    <div class="single-shorter">
                                        <label>Show :</label>
                                        <select class="nice-select">
                                            <option>09</option>
                                            <option>15</option>
                                            <option>25</option>
                                            <option>30</option>
                                        </select>
                                    </div>
                                    <div class="single-shorter">
                                        <label>Sort By :</label>
                                        <select class="nice-select">
                                            <option>Name</option>
                                            <option>Price</option>
                                            <option>Size</option>
                                        </select>
                                    </div>
                                </div>
                                <ul class="view-mode">
                                    <li><a href="{{ route('front.product-grids') }}"><i class="fa fa-th-large"></i></a></li>
                                    <li class="active"><a href="{{ route('front.product-lists') }}"><i class="fa fa-th-list"></i></a></li>
                                </ul>
                            </div>
                            <!--/ End Shop Top -->
                        </div>
                    </div>

                    @if($childCategories->isNotEmpty())
                        <!-- Subcategories Grid -->
                        <div class="row">
                            @foreach($childCategories as $childCat)
                            <div class="col-lg-4 col-md-6 col-12">
                                <div class="single-product">
                                    <div class="product-img">
                                        <a href="{{ route('front.product-cat', $childCat->slug) }}">
                                            @if($childCat->photo)
                                                <img class="default-img" src="{{ $childCat->photo }}" alt="{{ $childCat->title }}" style="height: 200px; object-fit: cover;">
                                            @else
                                                <img class="default-img" src="{{ asset('frontend/img/placeholder.jpg') }}" alt="{{ $childCat->title }}" style="height: 200px; object-fit: cover;">
                                            @endif
                                        </a>
                                    </div>
                                    <div class="product-content">
                                        <h3><a href="{{ route('front.product-cat', $childCat->slug) }}">{{ $childCat->title }}</a></h3>
                                        <p>{{ Str::limit($childCat->summary, 60) }}</p>
                                        <div class="product-price">
                                            <span>{{ $childCat->products_count ?? 0 }} Products</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @elseif($products->isNotEmpty())
                        <!-- Products List -->
                        <div class="row">
                            @foreach($products as $product)
                            <div class="col-lg-12 col-md-12 col-12" style="margin-bottom: 25px;">
                                <!-- Start Single List -->
                                <div class="single-list">
                                    <div class="row">
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <div class="list-image overlay" style="position: relative;">
                                                <img src="{{ $product->image_url ?? asset('frontend/img/placeholder.jpg') }}" alt="{{ $product->title }}">
                                                <!-- Overlay Actions -->
                                                <div class="image-actions" style="position: absolute; bottom: 15px; left: 50%; transform: translateX(-50%); z-index: 10;">
                                                    <a href="{{ route('add-to-cart', $product->slug) }}" class="action-btn" style="width: 40px; height: 40px; background: #fff; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; color: #333; text-decoration: none; box-shadow: 0 2px 5px rgba(0,0,0,0.2); margin-right: 10px;">
                                                        <i class="fa fa-shopping-bag"></i>
                                                    </a>
                                                    <a href="#" class="action-btn" style="width: 40px; height: 40px; background: #fff; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; color: #333; text-decoration: none; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                                                        <i class="fa fa-heart"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-8 col-md-6 col-12 no-padding">
                                            <div class="content">
                                                <h4 class="title"><a href="{{ route('front.product-detail', $product->slug) }}">{{ $product->title }}</a></h4>
                                                <p class="price with-discount">
                                                    @if($product->discount > 0)
                                                        <span class="old">${{ number_format($product->price, 2) }}</span>
                                                        <span>${{ number_format($product->price - ($product->price * $product->discount / 100), 2) }}</span>
                                                    @else
                                                        <span>${{ number_format($product->price, 2) }}</span>
                                                    @endif
                                                </p>
                                                <p>{{ Str::limit($product->summary, 150) }}</p>
                                                <!-- Product Actions -->
                                                <div class="product-actions" style="display: flex; align-items: center; justify-content: space-between; margin-top: 20px; padding-top: 15px; border-top: 1px solid #eee;">
                                                    <div class="action-links" style="display: flex; gap: 15px;">
                                                        <a data-toggle="modal" data-target="#exampleModal" title="Quick View" href="#" style="display: inline-flex; align-items: center; color: #333; text-decoration: none;">
                                                            <i class="ti-eye" style="margin-right: 5px;"></i> Quick Shop
                                                        </a>
                                                        <a title="Wishlist" href="#" style="display: inline-flex; align-items: center; color: #333; text-decoration: none;">
                                                            <i class="ti-heart" style="margin-right: 5px;"></i> Add to Wishlist
                                                        </a>
                                                    </div>
                                                    <a class="btn btn-primary" title="Add to cart" href="{{ route('add-to-cart', $product->slug) }}" style="background: #f7941d; color: #fff; padding: 10px 20px; border-radius: 4px; text-decoration: none; font-weight: 600;">
                                                        Add to cart
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Single List -->
                            </div>
                            @endforeach
                        </div>
                        <!-- Pagination -->
                        <div class="row" style="margin-top: 40px;">
                            <div class="col-12 text-center">
                                {{ $products->links('vendor.pagination.bootstrap-4') }}
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">
                            No products or subcategories found in this category.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
    <!--/ End Product Style 1  -->
@endsection
