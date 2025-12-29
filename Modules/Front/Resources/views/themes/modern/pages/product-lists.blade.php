@extends($themePath . '.layouts.master')
@section('title','E-SHOP || PRODUCT LIST')
@section('content')
{{-- Same structure as product-grids but with list layout --}}
<section class="page-header page-header-dark bg-secondary">
    <div class="container"><div class="row"><div class="col-md-12">
        <h1>Products - List View</h1>
        <ol class="breadcrumb">
            <li><a href="{{ route('front.index') }}">Home</a></li>
            <li class="active">Products</li>
        </ol>
    </div></div></div>
</section>

<form action="{{ route('front.product-filter') }}" method="POST">
@csrf
<section class="main-container">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <aside class="sidebar">
                    <div class="block clearfix">
                        <h3 class="title">Categories</h3>
                        <ul class="list-unstyled">
                            @foreach ($categories as $category)
                            <li><a href="{{ route('front.product-cat', $category->slug) }}">{{ $category->title }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </aside>
            </div>
            <div class="col-md-9">
                <div class="text-right mb-20">
                    <a href="{{ route('front.product-grids') }}" class="btn btn-sm btn-default"><i class="fa fa-th"></i></a>
                    <a href="javascript:void(0)" class="btn btn-sm btn-default active"><i class="fa fa-list"></i></a>
                </div>
                @foreach($products as $product)
                <div class="row product-list-item mb-20" data-product-id="{{ $product->id }}">
                    <div class="col-md-4">
                        <img src="{{ $product->imageUrl }}" alt="{{ $product->title }}" class="img-responsive">
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
                @endforeach
                <div class="text-center">
                    {{ $products->appends($_GET)->links('vendor.pagination.bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</section>
</form>
@endsection
