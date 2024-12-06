@extends('front::layouts.master')
@section('SOE')
    <title>{{$product_detail->title ?? ''}} || PRODUCT DETAIL</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name='copyright' content=''>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="keywords" content="online shop, purchase, cart, ecommerce site, best online shopping">
    <meta name="description" content="{{$product_detail->summary ??''}}">
    <meta property="og:url" content="{{route('front.product-detail',$product_detail->slug ??'')}}">
    <meta property="og:type" content="article">
    <meta property="og:title" content="{{$product_detail->title ?? ''}}">
    <meta property="og:image" content="{{$product_detail->imageUrl ?? ''}}">
    <meta property="og:description" content="{{$product_detail->description ?? ''}}">
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
                                            <img src="{{$product_detail->imageUrl}}"
                                                 alt="{{$product_detail->title}}">
                                        </li>
                                    </ul>
                                </div>
                                <!-- End Images slider -->
                            </div>
                            <!-- End Product slider -->
                        </div>
                        <div class="col-lg-6 col-12">
                            <div class="product-des">
                                <!-- Description -->
                                <div class="short">
                                    <h4>{{$product_detail->title}}</h4>
                                    <div class="rating-main">
                                        <ul class="rating">
                                            @php
                                                $rate=ceil($product_detail->getReview->avg('rate'))
                                            @endphp
                                            @for($i=1; $i<=5; $i++)
                                                @if($rate>=$i)
                                                    <li><i class="fa fa-star"></i></li>
                                                @else
                                                    <li><i class="fa fa-star-o"></i></li>
                                                @endif
                                            @endfor
                                        </ul>
                                        <a href="#" class="total-review">({{$product_detail['getReview']->count()}})
                                            Review</a>
                                    </div>
                                    @php
                                        $after_discount=($product_detail->price-(($product_detail->price*$product_detail->discount)/100));
                                    @endphp
                                    <p class="price"><span class="discount">${{number_format($after_discount,2)}}</span><s>${{number_format($product_detail->price,2)}}</s>
                                    </p>
                                    <p class="description">{!!($product_detail->summary)!!}</p>
                                </div>
                                <!--/ End Description -->
                                <!-- Color -->
                                <div class="color">
                                    <h4>Available Options <span>Color</span></h4>
                                    <ul>
                                        @php
                                            $colors = explode(',', $product_detail->color);
                                        @endphp

                                        @foreach($colors as $color)
                                            <li>
                                                <a href="#" class="{{ strtolower(trim($color)) }}">
                                                    <i class="ti-check"></i>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <!--/ End Color -->
                                <!-- Size -->
                                @if($product_detail->size)
                                    <div class="size mt-4">
                                        <h4>Size</h4>
                                        <ul>
                                            @php
                                                $sizes=explode(',',$product_detail->size);
                                            @endphp
                                            @foreach($sizes as $size)
                                                <li><a href="#" class="one">{{$size}}</a></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <!--/ End Size -->
                                <!-- Product Buy -->
                                <div class="product-buy">
                                    <form action="{{route('single-add-to-cart')}}" method="POST">
                                        @csrf
                                        <div class="quantity">
                                            <h6>Quantity :</h6>
                                            <!-- Input Order -->
                                            <div class="input-group">
                                                <div class="button minus">
                                                    <button type="button" class="btn btn-primary btn-number"
                                                            disabled="disabled" data-type="minus"
                                                            data-field="quantity[1]">
                                                        <i class="ti-minus"></i>
                                                    </button>
                                                </div>
                                                <input type="hidden" name="slug" value="{{$product_detail->slug}}">
                                                <input type="text" name="quantity[1]" class="input-number" data-min="1"
                                                       data-max="1000" value="1" id="quantity">
                                                <div class="button plus">
                                                    <button type="button" class="btn btn-primary btn-number"
                                                            data-type="plus" data-field="quantity[1]">
                                                        <i class="ti-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <!--/ End Input Order -->
                                        </div>
                                        <div class="add-to-cart mt-4">
                                            <button type="submit" class="btn">Add to cart</button>
                                            <a href="{{route('add-to-wishlist',$product_detail->slug)}}"
                                               class="btn min"><i class="ti-heart"></i></a>
                                        </div>
                                    </form>

                                    <p class="cat">Category :
                                        @foreach($product_detail->categories as $category)
                                            <a href="{{route('front.product-cat',$category->slug)}}">{{$category->title}}</a>
                                        @endforeach
                                    </p>

                                    <p class="availability">Stock : @if($product_detail->stock>0)
                                            <span
                                                    class="badge badge-success">{{$product_detail->stock}}</span>
                                        @else
                                            <span
                                                    class="badge badge-danger">{{$product_detail->stock}}</span>
                                        @endif</p>
                                </div>
                                <!--/ End Product Buy -->
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
                                        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#reviews"
                                                                role="tab">Reviews</a></li>
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
                                                        <p>{!! ($product_detail->description) !!}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--/ End Description Tab -->
                                    <!-- Reviews Tab -->
                                    <div class="tab-pane fade" id="reviews" role="tabpanel">
                                        <div class="tab-single review-panel">
                                            <div class="row">
                                                <div class="col-12">

                                                    <!-- Review -->
                                                    <div class="comment-review">
                                                        <div class="add-review">
                                                            <h5>Add A Review</h5>
                                                            <p>Your email address will not be published. Required fields
                                                                are marked</p>
                                                        </div>
                                                        <h4>Your Rating <span class="text-danger">*</span></h4>
                                                        <div class="review-inner">
                                                            <!-- Form -->
                                                            @auth
                                                                <form class="form" method="post"
                                                                      action="{{route('product.review.store',$product_detail->slug)}}">
                                                                    @csrf
                                                                    <div class="row">
                                                                        <div class="col-lg-12 col-12">
                                                                            <div class="rating_box">
                                                                                <div class="star-rating">
                                                                                    <div class="star-rating__wrap">
                                                                                        <input
                                                                                                class="star-rating__input"
                                                                                                id="star-rating-5"
                                                                                                type="radio" name="rate"
                                                                                                value="5">
                                                                                        <label
                                                                                                class="star-rating__ico fa fa-star-o"
                                                                                                for="star-rating-5"
                                                                                                title="5 out of 5 stars"></label>
                                                                                        <input
                                                                                                class="star-rating__input"
                                                                                                id="star-rating-4"
                                                                                                type="radio" name="rate"
                                                                                                value="4">
                                                                                        <label
                                                                                                class="star-rating__ico fa fa-star-o"
                                                                                                for="star-rating-4"
                                                                                                title="4 out of 5 stars"></label>
                                                                                        <input
                                                                                                class="star-rating__input"
                                                                                                id="star-rating-3"
                                                                                                type="radio" name="rate"
                                                                                                value="3">
                                                                                        <label
                                                                                                class="star-rating__ico fa fa-star-o"
                                                                                                for="star-rating-3"
                                                                                                title="3 out of 5 stars"></label>
                                                                                        <input
                                                                                                class="star-rating__input"
                                                                                                id="star-rating-2"
                                                                                                type="radio" name="rate"
                                                                                                value="2">
                                                                                        <label
                                                                                                class="star-rating__ico fa fa-star-o"
                                                                                                for="star-rating-2"
                                                                                                title="2 out of 5 stars"></label>
                                                                                        <input
                                                                                                class="star-rating__input"
                                                                                                id="star-rating-1"
                                                                                                type="radio" name="rate"
                                                                                                value="1">
                                                                                        <label
                                                                                                class="star-rating__ico fa fa-star-o"
                                                                                                for="star-rating-1"
                                                                                                title="1 out of 5 stars"></label>
                                                                                        @error('rate')
                                                                                        <span
                                                                                                class="text-danger">{{$message}}</span>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-12 col-12">
                                                                            <div class="form-group">
                                                                                <label>Write a review</label>
                                                                                <textarea name="review" rows="6"
                                                                                          placeholder=""></textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-12 col-12">
                                                                            <div class="form-group button5">
                                                                                <button type="submit" class="btn">
                                                                                    Submit
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            @else
                                                                <p class="text-center p-5">
                                                                    You need to <a href="{{route('login')}}"
                                                                                   style="color:rgb(54, 54, 204)">Login</a>
                                                                    OR <a style="color:blue"
                                                                          href="{{route('register')}}">Register</a>

                                                                </p>
                                                                <!--/ End Form -->
                                                            @endauth
                                                        </div>
                                                    </div>

                                                    <div class="ratting-main">
                                                        <div class="avg-ratting">
                                                            {{-- @php
                                                                $rate=0;
                                                                foreach($product_detail->rate as $key=>$rate){
                                                                    $rate +=$rate
                                                                }
                                                            @endphp --}}
                                                            <h4>{{ceil($product_detail->getReview->avg('rate'))}} <span>(Overall)</span>
                                                            </h4>
                                                            <span>Based on {{$product_detail->getReview->count()}} Comments</span>
                                                        </div>
                                                        @foreach($product_detail['getReview'] as $data)
                                                            <!-- Single Rating -->
                                                            <div class="single-rating">
                                                                <div class="rating-author">
                                                                    @if($data->user['photo'])
                                                                        <img src="{{$data->user['photo']}}"
                                                                             alt="{{$data->user['photo']}}">
                                                                    @else
                                                                        <img src="{{asset('backend/img/avatar.png')}}"
                                                                             alt="Profile.jpg">
                                                                    @endif
                                                                </div>
                                                                <div class="rating-des">
                                                                    <h6>{{$data->user['name']}}</h6>
                                                                    <div class="ratings">

                                                                        <ul class="rating">
                                                                            @for($i=1; $i<=5; $i++)
                                                                                @if($data->rate>=$i)
                                                                                    <li><i class="fa fa-star"></i></li>
                                                                                @else
                                                                                    <li><i class="fa fa-star-o"></i>
                                                                                    </li>
                                                                                @endif
                                                                            @endfor
                                                                        </ul>
                                                                        <div class="rate-count">
                                                                            (<span>{{$data->rate}}</span>)
                                                                        </div>
                                                                    </div>
                                                                    <p>{{$data->review}}</p>
                                                                </div>
                                                            </div>
                                                            <!--/ End Single Rating -->
                                                        @endforeach
                                                    </div>

                                                    <!--/ End Review -->

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--/ End Reviews Tab -->
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
                {{-- {{$product_detail->rel_prods}} --}}
                <div class="col-12">
                    <div class="owl-carousel popular-slider">
                        @foreach($related as $data)
                            @if($data->id !==$product_detail->id)
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
                                                <a data-toggle="modal" data-target="#modelExample" title="Quick View"
                                                   href="#"><i class=" ti-eye"></i><span>Quick Shop</span></a>
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
                                            <span class="old">${{number_format($data->price,2)}}</span>
                                            <span>${{number_format($after_discount,2)}}</span>
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


    <!-- Modal -->
    <div class="modal fade" id="modelExample" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span class="ti-close"
                                                                                                      aria-hidden="true"></span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row no-gutters">
                        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                            <!-- Product Slider -->
                            <div class="product-gallery">
                                <div class="quickview-slider-active">
                                    <div class="single-slider">
                                        <img src="images/modal1.png" alt="#">
                                    </div>
                                    <div class="single-slider">
                                        <img src="images/modal2.png" alt="#">
                                    </div>
                                    <div class="single-slider">
                                        <img src="images/modal3.png" alt="#">
                                    </div>
                                    <div class="single-slider">
                                        <img src="images/modal4.png" alt="#">
                                    </div>
                                </div>
                            </div>
                            <!-- End Product slider -->
                        </div>
                        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                            <div class="quickview-content">
                                <h2>Flared Shift Dress</h2>
                                <div class="quickview-ratting-review">
                                    <div class="quickview-ratting-wrap">
                                        <div class="quickview-ratting">
                                            <i class="yellow fa fa-star"></i>
                                            <i class="yellow fa fa-star"></i>
                                            <i class="yellow fa fa-star"></i>
                                            <i class="yellow fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <a href="#"> (1 customer review)</a>
                                    </div>
                                    <div class="quickview-stock">
                                        <span><i class="fa fa-check-circle-o"></i> in stock</span>
                                    </div>
                                </div>
                                <h3>$29.00</h3>
                                <div class="quickview-peragraph">
                                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Mollitia iste laborum
                                        ad impedit pariatur esse optio tempora sint ullam autem deleniti nam in quos qui
                                        nemo ipsum numquam.</p>
                                </div>
                                <div class="size">
                                    <div class="row">
                                        <div class="col-lg-6 col-12">
                                            <h5 class="title">Size</h5>
                                            <select>
                                                <option selected="selected">s</option>
                                                <option>m</option>
                                                <option>l</option>
                                                <option>xl</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-6 col-12">
                                            <h5 class="title">Color</h5>
                                            <select>
                                                <option selected="selected">orange</option>
                                                <option>purple</option>
                                                <option>black</option>
                                                <option>pink</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="quantity">
                                    <!-- Input Order -->
                                    <div class="input-group">
                                        <div class="button minus">
                                            <button type="button" class="btn btn-primary btn-number" disabled="disabled"
                                                    data-type="minus" data-field="quantity[1]">
                                                <i class="ti-minus"></i>
                                            </button>
                                        </div>
                                        <input type="text" name="qty" class="input-number" data-min="1" data-max="1000"
                                               value="1">
                                        <div class="button plus">
                                            <button type="button" class="btn btn-primary btn-number" data-type="plus"
                                                    data-field="quantity[1]">
                                                <i class="ti-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <!--/ End Input Order -->
                                </div>
                                <div class="add-to-cart">
                                    <a href="#" class="btn">Add to cart</a>
                                    <a href="#" class="btn min"><i class="ti-heart"></i></a>
                                    <a href="#" class="btn min"><i class="fa fa-compress"></i></a>
                                </div>
                                <div class="default-social">
                                    <h4 class="share-now">Share:</h4>
                                    <ul>
                                        <li><a class="facebook" href="#"><i class="fa fa-facebook"></i></a></li>
                                        <li><a class="twitter" href="#"><i class="fa fa-twitter"></i></a></li>
                                        <li><a class="youtube" href="#"><i class="fa fa-pinterest-p"></i></a></li>
                                        <li><a class="dribbble" href="#"><i class="fa fa-google-plus"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
