@extends('front::default.layouts.master')

@section('title','E-SHOP || BUNDLE PAGE')

@section('content')
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="/">Home<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="/">Bundles</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <!-- Product Style -->
    <section class="product-area shop-sidebar shop section">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                    <div class="shop-sidebar">
                        <!-- Single Widget -->
                        <div class="single-widget recent-post">
                            <h3 class="title">Recent post</h3>
                            {{-- {{dd($recent_products)}} --}}
                            @foreach($products as $product)
                                <div class="single-post first">
                                    <div class="image">
                                        <img src="{{$product->ImageThumbUrl}}" alt="{{$product->title}}">
                                    </div>
                                    <div class="content">
                                        <h5>
                                            <a href="{{route('front.bundle-detail',$product->slug)}}">{{$product->title}}</a>
                                        </h5>
                                        @php
                                            $org=($product->price-($product->price*$product->discount)/100);
                                        @endphp
                                        <p class="price">
                                            <del class="text-muted">${{number_format($product->price,2)}}</del>
                                            ${{number_format($org,2)}}  </p>

                                    </div>
                                </div>
                                <!-- End Single Post -->
                            @endforeach
                        </div>
                        <!--/ End Single Widget -->
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
                                        <label>Sort By :</label>
                                        <select class='sortBy' name='sortBy' id='sortFilter'>
                                            <option value="">Default</option>
                                            <option value="title"
                                                    @if(!empty($_GET['sortBy']) && $_GET['sortBy']=='title') selected @endif>
                                                Name
                                            </option>
                                            <option value="price"
                                                    @if(!empty($_GET['sortBy']) && $_GET['sortBy']=='price') selected @endif>
                                                Price
                                            </option>
                                            <option value="category"
                                                    @if(!empty($_GET['sortBy']) && $_GET['sortBy']=='category') selected @endif>
                                                Category
                                            </option>
                                            <option value="brand"
                                                    @if(!empty($_GET['sortBy']) && $_GET['sortBy']=='brand') selected @endif>
                                                Brand
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <ul class="view-mode">
                                    <li class="active"><a href="javascript:void(0)"><i
                                                    class="fa fa-th-large"></i></a></li>
                                    <li><a href="{{route('front.product-lists')}}"><i class="fa fa-th-list"></i></a>
                                    </li>
                                </ul>
                            </div>
                            <!--/ End Shop Top -->
                        </div>
                    </div>
                    <div class="row">
                        {{-- {{$products}} --}}
                        @if(count($products)>0)
                            @foreach($products as $product)
                                <div class="col-lg-4 col-md-6 col-12">
                                    <div class="single-product">
                                        <div class="product-img">
                                            <a href="{{route('front.bundle-detail',$product->slug)}}">
                                                <img class="default-img" src="{{$product->imageUrl}}"
                                                     alt="{{$product->imageUrl}}">
                                                <img class="hover-img" src="{{$product->imageUrl}}"
                                                     alt="{{$product->imageUrl}}">
                                                @if($product->discount)
                                                    <span class="price-dec">{{$product->discount}} % Off</span>
                                                @endif
                                            </a>
                                            <div class="button-head">
                                                <div class="product-action">
                                                    <a data-toggle="modal" data-target="#{{$product->id}}"
                                                       title="Quick View" href="#"><i class=" ti-eye"></i><span>Quick Shop</span></a>
                                                    <a title="Wishlist"
                                                       href="{{route('add-to-wishlist',$product->slug)}}"
                                                       class="wishlist" data-id="{{$product->id}}"><i
                                                                class=" ti-heart "></i><span>Add to Wishlist</span></a>
                                                </div>
                                                <div class="product-action-2">
                                                    <a title="Add to cart"
                                                       href="{{route('add-to-cart',$product->slug)}}">Add to
                                                        cart</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="product-content">
                                            <h3>
                                                <a href="{{route('front.product-detail',$product->slug)}}">{{$product->title}}</a>
                                            </h3>
                                            @php
                                                $after_discount=($product->price-($product->price*$product->discount)/100);
                                            @endphp
                                            <span>${{number_format($after_discount,2)}}</span>
                                            <del style="padding-left:4%;">
                                                ${{number_format($product->price,2)}}</del>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <h4 class="text-warning" style="margin:100px auto;">There are no products.</h4>
                        @endif


                    </div>
                    <div class="row">
                        <div class="col-md-12 justify-content-center d-flex">
                            {{$products->appends($_GET)->links('vendor.pagination.bootstrap-4')}}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
    <!--/ End Product Style 1  -->



    <!-- Modal -->
    @if($products)
        @foreach($products as $key=>$product)
            <div class="modal fade" id="{{$product->id}}" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                        class="ti-close" aria-hidden="true"></span></button>
                        </div>
                        <div class="modal-body">
                            <div class="row no-gutters">
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                    <!-- Product Slider -->
                                    <div class="product-gallery">
                                        <div class="quickview-slider-active">

                                            <div class="single-slider">
                                                <img class="default-img" src="{{$product->imageUrl}}"
                                                     alt="{{$product->imageUrl}}">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Product slider -->
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                    <div class="quickview-content">
                                        <h2>{{$product->title}}</h2>
                                        <div class="quickview-ratting-review">
                                            <div class="quickview-ratting-wrap">
                                                <div class="quickview-ratting">
                                                    <i class="yellow fa fa-star"></i>
                                                    <i class="yellow fa fa-star"></i>
                                                    <i class="yellow fa fa-star"></i>
                                                    <i class="yellow fa fa-star"></i>
                                                    <i class="fa fa-star"></i>
                                                    @php
                                                        $rate=DB::table('product_reviews')->where('product_id',$product->id)->avg('rate');
                                                        $rate_count=DB::table('product_reviews')->where('product_id',$product->id)->count();
                                                    @endphp
                                                    @for($i=1; $i<=5; $i++)
                                                        @if($rate>=$i)
                                                            <i class="yellow fa fa-star"></i>
                                                        @else
                                                            <i class="fa fa-star"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                                <a href="#"> ({{$rate_count}} customer review)</a>
                                            </div>
                                            <div class="quickview-stock">
                                                @if($product->stock >0)
                                                    <span><i class="fa fa-check-circle-o"></i> {{$product->stock}} in stock</span>
                                                @else
                                                    <span><i class="fa fa-times-circle-o text-danger"></i> {{$product->stock}} out stock</span>
                                                @endif
                                            </div>
                                        </div>
                                        @php
                                            $after_discount=($product->price-($product->price*$product->discount)/100);
                                        @endphp
                                        <h3><small>
                                                <del class="text-muted">${{number_format($product->price,2)}}</del>
                                            </small> ${{number_format($after_discount,2)}}  </h3>
                                        <div class="quickview-peragraph">
                                            <p>{!! html_entity_decode($product->summary) !!}</p>
                                        </div>
                                        @if($product->size)
                                            <div class="size">
                                                <h4>Size</h4>
                                                <ul>
                                                    @php
                                                        $sizes=explode(',',$product->size);
                                                    @endphp
                                                    @foreach($sizes as $size)
                                                        <li><a href="#" class="one">{{$size}}</a></li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                        <div class="size">
                                            <div class="row">
                                                <div class="col-lg-6 col-12">
                                                    <h5 class="title">Size</h5>
                                                    <select>
                                                        @php
                                                            $sizes=explode(',',$product->size);
                                                            // dd($sizes);
                                                        @endphp
                                                        @foreach($sizes as $size)
                                                            <option>{{$size}}</option>
                                                        @endforeach
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
                                        <form action="{{route('single-add-to-cart')}}" method="POST">
                                            @csrf
                                            <div class="quantity">
                                                <!-- Input Order -->
                                                <div class="input-group">
                                                    <div class="button minus">
                                                        <button type="button" class="btn btn-primary btn-number"
                                                                disabled="disabled" data-type="minus"
                                                                data-field="quantity[1]">
                                                            <i class="ti-minus"></i>
                                                        </button>
                                                    </div>
                                                    <input type="hidden" name="slug" value="{{$product->slug}}">
                                                    <input type="text" name="quantity[1]" class="input-number"
                                                           data-min="1"
                                                           data-max="1000" value="1">
                                                    <div class="button plus">
                                                        <button type="button" class="btn btn-primary btn-number"
                                                                data-type="plus" data-field="quantity[1]">
                                                            <i class="ti-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <!--/ End Input Order -->
                                            </div>
                                            <div class="add-to-cart">
                                                <button type="submit" class="btn">Add to cart</button>
                                                <a href="{{route('add-to-wishlist',$product->slug)}}" class="btn min"><i
                                                            class="ti-heart"></i></a>
                                            </div>
                                        </form>
                                        <div class="default-social">
                                            <!-- ShareThis BEGIN -->
                                            <div class="sharethis-inline-share-buttons"></div><!-- ShareThis END -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
    <!-- Modal end -->

@endsection
@push('styles')
    <style>
        .pagination {
            display: inline-flex;
        }

        .filter_button {
            /* height:20px; */
            text-align: center;
            background: #F7941D;
            padding: 8px 16px;
            margin-top: 10px;
            color: white;
        }
    </style>
@endpush
@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script>
        $(document).ready(function () {
            /*----------------------------------------------------*/
            /*  Jquery Ui slider js
            /*----------------------------------------------------*/
            if ($("#slider-range").length > 0) {
                const max_value = parseInt($("#slider-range").data('max')) || 500;
                const min_value = parseInt($("#slider-range").data('min')) || 0;
                const currency = $("#slider-range").data('currency') || '';
                let price_range = min_value + '-' + max_value;
                if ($("#price_range").length > 0 && $("#price_range").val()) {
                    price_range = $("#price_range").val().trim();
                }

                let price = price_range.split('-');
                $("#slider-range").slider({
                    range: true,
                    min: min_value,
                    max: max_value,
                    values: price,
                    slide: function (event, ui) {
                        $("#amount").val(currency + ui.values[0] + " -  " + currency + ui.values[1]);
                        $("#price_range").val(ui.values[0] + "-" + ui.values[1]);
                    }
                });
            }
            if ($("#amount").length > 0) {
                const m_currency = $("#slider-range").data('currency') || '';
                $("#amount").val(m_currency + $("#slider-range").slider("values", 0) +
                    "  -  " + m_currency + $("#slider-range").slider("values", 1));
            }
        })
    </script>
    <script>
        $(document).ready(function () {
            // Your existing script content

            // Sorting filter handler
            $('#sortFilter').change(function () {
                var sortByValue = $(this).val(); // Get the selected sort by value
                var currentUrl = window.location.href; // Get current URL
                var newUrl;

                // Check if URL already has query parameters
                if (currentUrl.includes('?')) {
                    // Check if URL already has sortBy parameter
                    if (currentUrl.includes('sortBy')) {
                        // Replace existing sortBy value
                        newUrl = currentUrl.replace(/(sortBy=)[^\&]+/, '$1' + sortByValue);
                    } else {
                        // Add sortBy parameter
                        newUrl = currentUrl + '&sortBy=' + sortByValue;
                    }
                } else {
                    // Add sortBy parameter
                    newUrl = currentUrl + '?sortBy=' + sortByValue;
                }

                // Redirect to the new URL
                window.location.href = newUrl;
            });
        });
    </script>
@endpush
