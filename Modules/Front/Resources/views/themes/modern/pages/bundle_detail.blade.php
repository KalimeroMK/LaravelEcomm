@extends($themePath . '.layouts.master')
@section('SOE')
    <title>{{$bundle->name ?? ''}} || PRODUCT DETAIL</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name='copyright' content=''>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="keywords" content="online shop, purchase, cart, ecommerce site, best online shopping">
    <meta name="description" content="{{$bundle->description ??''}}">
    <meta property="og:url" content="{{route('front.bundle-detail',$bundle->slug ??'')}}">
    <meta property="og:type" content="article">
    <meta property="og:title" content="{{$bundle->name ?? ''}}">
    <meta property="og:image" content="{{$bundle->image_url ?? ''}}">
    <meta property="og:description" content="{{$bundle->description ?? ''}}">
@endsection
@section('content')
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="{{route('front.index')}}">Home<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="">Shop Details</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <!-- Shop Single -->
    <section class="shop single section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="row">
                        <div class="col-lg-6 col-12">
                            <!-- Product Slider -->
                            <div class="product-gallery">
                                <!-- Images slider -->
                                <div class="flexslider-thumbnails">
                                    <ul class="slides">
                                        <li data-thumb="" rel="adjustX:10, adjustY:">
                                            <img src="{{$bundle->image_url}}"
                                                 alt="{{$bundle->name}}">
                                        </li>
                                    </ul>
                                </div>
                                <!-- End Images slider -->
                            </div>
                            <!-- End Product slider -->
                        </div>
                        <div class="col-lg-6 col-12">
                            <div class="product-info">
                                <h2 class="product-title">{{$bundle->name}}</h2>
                                <div class="product-price">
                                    <span class="price">{{ currency($bundle->price) }}</span>
                                </div>
                                <div class="product-description">
                                    <p>{!! ($bundle->description) !!}</p>
                                </div>
                                
                                @if($bundle->products && $bundle->products->count() > 0)
                                    <div class="bundle-products mt-4">
                                        <h4>Products in this Bundle ({{$bundle->products->count()}})</h4>
                                        <div class="row">
                                            @foreach($bundle->products as $product)
                                                <div class="col-md-6 mb-3">
                                                    <div class="bundle-product-item">
                                                        <a href="{{route('front.product-detail', $product->slug)}}">
                                                            <img src="{{$product->imageUrl}}" alt="{{$product->title}}" class="img-fluid" style="max-width: 100px;">
                                                            <h5>{{$product->title}}</h5>
                                                            <p class="price">{{ currency($product->price) }}</p>
                                                        </a>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="add-to-cart mt-4">
                                    <a href="#" class="btn btn-primary">Add Bundle to Cart</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="product-info">
                                <div class="nav-main">
                                    <!-- Tab Nav -->
                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                        <li class="nav-item"><a class="nav-link active" data-toggle="tab"
                                                                href="#description" role="tab">Description</a></li>
                                        <li class="nav-item"><a class="nav-link" data-toggle="tab"
                                                                href="#products" role="tab">Products</a></li>
                                    </ul>
                                    <!--/ End Tab Nav -->
                                </div>
                                <div class="tab-content" id="myTabContent">
                                    <!-- Description Tab -->
                                    <div class="tab-pane fade show active" id="description" role="tabpanel">
                                        <div class="tab-single">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="single-des">
                                                        <p>{!! ($bundle->description) !!}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Products Tab -->
                                    <div class="tab-pane fade" id="products" role="tabpanel">
                                        <div class="tab-single">
                                            <div class="row">
                                                @if($bundle->products && $bundle->products->count() > 0)
                                                    @foreach($bundle->products as $product)
                                                        <div class="col-md-4 mb-4">
                                                            <div class="product-item">
                                                                <a href="{{route('front.product-detail', $product->slug)}}">
                                                                    <img src="{{$product->imageUrl}}" alt="{{$product->title}}" class="img-fluid">
                                                                    <h5>{{$product->title}}</h5>
                                                                    <p class="price">{{ currency($product->price) }}</p>
                                                                    @if($product->summary)
                                                                        <p class="summary">{{Str::limit($product->summary, 100)}}</p>
                                                                    @endif
                                                                </a>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="col-12">
                                                        <p>No products in this bundle.</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/ End Shop Single -->

    <!-- Start Most Popular -->
    <div class="product-area most-popular related-product section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section-title">
                        <h2>Related Products</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                {{-- {{$bundle->rel_prods}} --}}
                <div class="col-12">
                    <div class="owl-carousel popular-slider">
                        @foreach($related as $data)
                            @if($data->id !==$bundle->id)
                                <!-- Start Single Product -->
                                <div class="single-product">
                                    <div class="product-img">
                                        <a href="{{route('front.product-detail',$data->slug)}}">
                                            <img class="default-img" src="{{$data->imageUrl}}"
                                                 alt="{{$data->imageUrl}}">
                                            <img class="hover-img" src="{{$data->imageUrl}}" alt="{{$data->imageUrl}}">
                                            <span class="price-dec">{{$data->discount}} % Off</span>
                                            {{-- <span class="out-of-stock">Hot</span> --}}
                                        </a>
                                        <div class="button-head">
                                            <div class="product-action">
                                                <a title="Wishlist" href="#"><i class=" ti-heart "></i><span>Add to Wishlist</span></a>
                                                <a title="Compare" href="#"><i class="ti-bar-chart-alt"></i><span>Add to Compare</span></a>
                                            </div>
                                            <div class="product-action-2">
                                                <a title="Add to cart" href="#">Add to cart</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="product-content">
                                        <h3><a href="{{route('front.product-detail',$data->slug)
                                        }}">{{$data->title}}</a></h3>
                                        <div class="product-price">
                                            @php
                                                $after_discount=($data->price-(($data->discount*$data->price)/100));
                                            @endphp
                                            <span class="old">{{ currency($data->price) }}</span>
                                            <span>{{ currency($after_discount) }}</span>
                                        </div>

                                    </div>
                                </div>
                                <!-- End Single Product -->

                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Most Popular Area -->



    <!-- Modal end -->

@endsection
@push('styles')
    <style>
        /* Rating */
        .rating_box {
            display: inline-flex;
        }

        .star-rating {
            font-size: 0;
            padding-left: 10px;
            padding-right: 10px;
        }

        .star-rating__wrap {
            display: inline-block;
            font-size: 1rem;
        }

        .star-rating__wrap:after {
            content: "";
            display: table;
            clear: both;
        }

        .star-rating__ico {
            float: right;
            padding-left: 2px;
            cursor: pointer;
            color: #F7941D;
            font-size: 16px;
            margin-top: 5px;
        }

        .star-rating__ico:last-child {
            padding-left: 0;
        }

        .star-rating__input {
            display: none;
        }

        .star-rating__ico:hover:before,
        .star-rating__ico:hover ~ .star-rating__ico:before,
        .star-rating__input:checked ~ .star-rating__ico:before {
            content: "\F005";
        }

    </style>
@endpush
@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
@endpush
