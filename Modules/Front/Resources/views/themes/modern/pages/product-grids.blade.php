@extends($themePath . '.layouts.master')

@section('title','E-SHOP || PRODUCT PAGE')

@section('content')
    {{-- Breadcrumb --}}
    <section class="page-header page-header-dark bg-secondary">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1>Shop</h1>
                    <ol class="breadcrumb">
                        <li><a href="{{ route('front.index') }}">Home</a></li>
                        <li class="active">Shop Grid</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    {{-- Product Grid Section --}}
    <form action="{{ route('front.product-filter') }}" method="POST">
        @csrf
        <section class="main-container">
            <div class="container">
                <div class="row">
                    {{-- Sidebar --}}
                    <div class="col-md-3 col-sm-4">
                        <aside class="sidebar">
                            {{-- Categories Widget --}}
                            <div class="block clearfix">
                                <h3 class="title">Categories</h3>
                                <ul class="list-unstyled">
                                    @foreach ($categories as $category)
                                        <li>
                                            <a href="{{ route('front.product-cat', $category->slug) }}">
                                                {{ $category->title }}
                                            </a>
                                            @if($category->childrenCategories->count() > 0)
                                                <ul class="list-unstyled">
                                                    @foreach ($category->childrenCategories as $childCategory)
                                                        @include($themePath . '.layouts.child_category', ['child_category' => $childCategory])
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            {{-- Price Filter --}}
                            <div class="block clearfix">
                                <h3 class="title">Shop by Price</h3>
                                <div id="slider-range" data-min="0" data-max="{{ $max }}"></div>
                                <div class="form-group mt-20">
                                    <label>Range:</label>
                                    <input type="text" id="amount" class="form-control" readonly/>
                                    <input type="hidden" name="price_range" id="price_range" value="@if(!empty($_GET['price'])){{ $_GET['price'] }}@endif"/>
                                </div>
                                <button type="submit" class="btn btn-default btn-block">Filter</button>
                            </div>

                            {{-- Recent Products --}}
                            <div class="block clearfix">
                                <h3 class="title">Recent Products</h3>
                                @foreach($products->take(3) as $product)
                                    <div class="media">
                                        <a class="pull-left" href="{{ route('front.product-detail', $product->slug) }}">
                                            <img class="media-object" src="{{ $product->imageThumbUrl }}" alt="{{ $product->title }}" style="width:80px;">
                                        </a>
                                        <div class="media-body">
                                            <h5 class="media-heading"><a href="{{ route('front.product-detail', $product->slug) }}">{{ $product->title }}</a></h5>
                                            @php
                                                $org = ($product->price - ($product->price * $product->discount) / 100);
                                            @endphp
                                            <p class="price">
                                                <del class="text-muted">${{ number_format($product->price, 2) }}</del>
                                                ${{ number_format($org, 2) }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Brands --}}
                            <div class="block clearfix">
                                <h3 class="title">Brands</h3>
                                <ul class="list-unstyled">
                                    @foreach($brands as $brand)
                                        <li><a href="{{ route('front.product-brand', $brand->slug) }}">{{ $brand->title }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </aside>
                    </div>

                    {{-- Product Grid --}}
                    <div class="col-md-9 col-sm-8">
                        {{-- Toolbar --}}
                        <div class="row mb-20">
                            <div class="col-md-6">
                                <div class="form-inline">
                                    <label>Show:</label>
                                    <select name="show" class="form-control input-sm" onchange="this.form.submit();">
                                        <option value="">Default</option>
                                        <option value="9" @if(!empty($_GET['show']) && $_GET['show']=='9') selected @endif>09</option>
                                        <option value="15" @if(!empty($_GET['show']) && $_GET['show']=='15') selected @endif>15</option>
                                        <option value="21" @if(!empty($_GET['show']) && $_GET['show']=='21') selected @endif>21</option>
                                        <option value="30" @if(!empty($_GET['show']) && $_GET['show']=='30') selected @endif>30</option>
                                    </select>

                                    <label class="ml-20">Sort By:</label>
                                    <select name="sortBy" class="form-control input-sm" onchange="this.form.submit();">
                                        <option value="">Default</option>
                                        <option value="title" @if(!empty($_GET['sortBy']) && $_GET['sortBy']=='title') selected @endif>Name</option>
                                        <option value="price" @if(!empty($_GET['sortBy']) && $_GET['sortBy']=='price') selected @endif>Price</option>
                                        <option value="category" @if(!empty($_GET['sortBy']) && $_GET['sortBy']=='category') selected @endif>Category</option>
                                        <option value="brand" @if(!empty($_GET['sortBy']) && $_GET['sortBy']=='brand') selected @endif>Brand</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 text-right">
                                <div class="btn-group">
                                    <a href="javascript:void(0)" class="btn btn-sm btn-default active"><i class="fa fa-th"></i></a>
                                    <a href="{{ route('front.product-lists') }}" class="btn btn-sm btn-default"><i class="fa fa-list"></i></a>
                                </div>
                            </div>
                        </div>

                        {{-- Products --}}
                        <div class="row isotope-container">
                            @if(count($products) > 0)
                                @foreach($products as $product)
                                    <div class="col-md-4 col-sm-6 isotope-item product-list-item" data-product-id="{{ $product->id }}">
                                        <div class="product-item">
                                            <div class="product-item-img">
                                                <a href="{{ route('front.product-detail', $product->slug) }}">
                                                    <img src="{{ $product->imageUrl }}" alt="{{ $product->title }}" class="img-responsive">
                                                </a>
                                                @if($product->discount)
                                                    <span class="badge badge-danger">-{{ $product->discount }}%</span>
                                                @endif
                                                <div class="product-item-overlay">
                                                    <a href="#" data-toggle="modal" data-target="#modal-{{ $product->id }}" class="btn btn-sm btn-default-transparent"><i class="fa fa-eye"></i></a>
                                                    <a href="{{ route('add-to-wishlist', $product->slug) }}" class="btn btn-sm btn-default-transparent"><i class="fa fa-heart-o"></i></a>
                                                    <a href="{{ route('add-to-cart', $product->slug) }}" class="btn btn-sm btn-default-transparent"><i class="fa fa-shopping-cart"></i></a>
                                                </div>
                                            </div>
                                            <div class="product-item-title">
                                                <a href="{{ route('front.product-detail', $product->slug) }}">{{ $product->title }}</a>
                                            </div>
                                            <div class="product-item-price">
                                                @php
                                                    $after_discount = ($product->price - ($product->price * $product->discount) / 100);
                                                @endphp
                                                @if($product->discount)
                                                    <del class="text-muted">${{ number_format($product->price, 2) }}</del>
                                                @endif
                                                <span class="text-default">${{ number_format($after_discount, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-md-12">
                                    <div class="alert alert-warning">
                                        <h4>There are no products.</h4>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Pagination --}}
                        <div class="row">
                            <div class="col-md-12 text-center">
                                {{ $products->appends($_GET)->links('vendor.pagination.bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </form>

    {{-- Quick View Modals --}}
    @if($products)
        @foreach($products as $product)
            <div class="modal fade" id="modal-{{ $product->id }}" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                            <h4 class="modal-title">{{ $product->title }}</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <img src="{{ $product->imageUrl }}" alt="{{ $product->title }}" class="img-responsive">
                                </div>
                                <div class="col-md-6">
                                    <h3>{{ $product->title }}</h3>
                                    @php
                                        $after_discount = ($product->price - ($product->price * $product->discount) / 100);
                                        $rate = DB::table('product_reviews')->where('product_id', $product->id)->avg('rate');
                                        $rate_count = DB::table('product_reviews')->where('product_id', $product->id)->count();
                                    @endphp
                                    <div class="rating">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fa fa-star{{ $rate >= $i ? ' text-default' : '-o' }}"></i>
                                        @endfor
                                        <span>({{ $rate_count }} reviews)</span>
                                    </div>
                                    <h4 class="price">
                                        @if($product->discount)
                                            <del class="text-muted">${{ number_format($product->price, 2) }}</del>
                                        @endif
                                        ${{ number_format($after_discount, 2) }}
                                    </h4>
                                    <p>{!! html_entity_decode($product->summary) !!}</p>
                                    <p>
                                        @if($product->stock > 0)
                                            <span class="text-success"><i class="fa fa-check"></i> {{ $product->stock }} in stock</span>
                                        @else
                                            <span class="text-danger"><i class="fa fa-times"></i> Out of stock</span>
                                        @endif
                                    </p>
                                    <form action="{{ route('single-add-to-cart') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="slug" value="{{ $product->slug }}">
                                        <div class="form-group">
                                            <div class="input-group" style="max-width:150px;">
                                                <span class="input-group-btn">
                                                    <button type="button" class="btn btn-default btn-number" data-type="minus" data-field="quantity[1]">
                                                        <i class="fa fa-minus"></i>
                                                    </button>
                                                </span>
                                                <input type="text" name="quantity[1]" class="form-control input-number text-center" value="1" min="1" max="100">
                                                <span class="input-group-btn">
                                                    <button type="button" class="btn btn-default btn-number" data-type="plus" data-field="quantity[1]">
                                                        <i class="fa fa-plus"></i>
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-default btn-animated">Add to Cart</button>
                                        <a href="{{ route('add-to-wishlist', $product->slug) }}" class="btn btn-default-transparent btn-animated"><i class="fa fa-heart-o"></i></a>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
@endsection

@push('styles')
<link href="{{ theme_asset('plugins/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet">
<style>
.product-item { margin-bottom: 30px; }
.product-item-img { position: relative; overflow: hidden; }
.product-item-img img { width: 100%; transition: all 0.3s; }
.product-item:hover .product-item-img img { transform: scale(1.1); }
.product-item-overlay { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); opacity: 0; transition: all 0.3s; }
.product-item:hover .product-item-overlay { opacity: 1; }
.product-item-title { padding: 15px 0 5px; }
.product-item-title a { color: #333; font-weight: 500; }
.product-item-title a:hover { color: #4eb3dd; }
.product-item-price { font-size: 18px; font-weight: 600; }
</style>
@endpush

@push('scripts')
<script src="{{ theme_asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>
<script>
$(document).ready(function() {
    // Price slider
    if ($("#slider-range").length > 0) {
        const max_value = parseInt($("#slider-range").data('max')) || 500;
        const min_value = parseInt($("#slider-range").data('min')) || 0;
        let price_range = min_value + '-' + max_value;
        if ($("#price_range").val()) {
            price_range = $("#price_range").val().trim();
        }
        let price = price_range.split('-');
        $("#slider-range").slider({
            range: true,
            min: min_value,
            max: max_value,
            values: price,
            slide: function(event, ui) {
                $("#amount").val("$" + ui.values[0] + " - $" + ui.values[1]);
                $("#price_range").val(ui.values[0] + "-" + ui.values[1]);
            }
        });
        $("#amount").val("$" + $("#slider-range").slider("values", 0) + " - $" + $("#slider-range").slider("values", 1));
    }

    // Track product impressions
    const productIds = $('.product-list-item').map(function() { return $(this).data('product-id'); }).get();
    if (productIds.length > 0) {
        $.post('/api/v1/tracking/product-impressions', { product_ids: productIds, _token: '{{ csrf_token() }}' });
    }

    // Track product clicks
    $('.product-list-item a').click(function() {
        const productId = $(this).closest('.product-list-item').data('product-id');
        $.post('/api/v1/tracking/product-click', { product_id: productId, _token: '{{ csrf_token() }}' });
    });

    // Quantity buttons
    $('.btn-number').click(function(e) {
        e.preventDefault();
        const type = $(this).attr('data-type');
        const input = $('input[name="' + $(this).attr('data-field') + '"]');
        const currentVal = parseInt(input.val());
        if (!isNaN(currentVal)) {
            if (type == 'minus') {
                if (currentVal > input.attr('min')) {
                    input.val(currentVal - 1).change();
                }
            } else if (type == 'plus') {
                if (currentVal < input.attr('max')) {
                    input.val(currentVal + 1).change();
                }
            }
        } else {
            input.val(0);
        }
    });
});
</script>
@endpush
