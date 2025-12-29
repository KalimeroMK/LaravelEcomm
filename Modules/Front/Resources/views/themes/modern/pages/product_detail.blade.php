@extends($themePath . '.layouts.master')

@section('title', $product_detail->title ?? 'Product Detail')

@section('content')
{{-- Breadcrumb --}}
<section class="page-header page-header-dark bg-secondary">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>{{ $product_detail->title }}</h1>
                <ol class="breadcrumb">
                    <li><a href="{{ route('front.index') }}">Home</a></li>
                    <li class="active">Product Details</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="main-container">
    <div class="container">
        <div class="row">
            {{-- Product Gallery --}}
            <div class="col-md-6">
                <div class="product-gallery-container">
                    @php
                        $mediaItems = $product_detail->getMedia('product');
                    @endphp
                    @if($mediaItems->count())
                        <div id="product-carousel" class="carousel slide" data-ride="carousel">
                            <div class="carousel-inner">
                                @foreach($mediaItems as $key => $media)
                                <div class="item {{ $key == 0 ? 'active' : '' }}">
                                    <img src="{{ $media->getUrl() }}" alt="{{ $product_detail->title }}" class="img-responsive">
                                </div>
                                @endforeach
                            </div>
                            @if($mediaItems->count() > 1)
                            <a class="left carousel-control" href="#product-carousel" data-slide="prev">
                                <span class="fa fa-chevron-left"></span>
                            </a>
                            <a class="right carousel-control" href="#product-carousel" data-slide="next">
                                <span class="fa fa-chevron-right"></span>
                            </a>
                            @endif
                        </div>
                    @else
                        <img src="https://placehold.co/600x600/eee/999?text=No+Image" class="img-responsive" alt="{{ $product_detail->title }}">
                    @endif
                </div>
            </div>

            {{-- Product Info --}}
            <div class="col-md-6">
                <div class="product-details">
                    <h2>{{ $product_detail->title }}</h2>
                    
                    {{-- Rating --}}
                    @php
                        $rate = ceil($product_detail->getReview->avg('rate'));
                        $reviewCount = $product_detail->getReview->count();
                    @endphp
                    <div class="rating mb-20">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fa fa-star{{ $rate >= $i ? '' : '-o' }} text-default"></i>
                        @endfor
                        <span class="ml-10">({{ $reviewCount }} reviews)</span>
                    </div>

                    {{-- Price --}}
                    @php
                        $price = $product_detail->price;
                        $discount = $product_detail->discount ?? 0;
                        $specialPrice = $product_detail->special_price ?? null;
                        $finalPrice = $specialPrice ? $specialPrice : ($price - (($price * $discount) / 100));
                    @endphp
                    <h3 class="price">
                        <span class="text-default">${{ number_format($finalPrice, 2) }}</span>
                        @if($finalPrice < $price)
                            <del class="text-muted ml-10">${{ number_format($price, 2) }}</del>
                            <span class="label label-danger">-{{ $discount }}%</span>
                        @endif
                    </h3>

                    {{-- Summary --}}
                    <p class="lead">{!! $product_detail->summary !!}</p>

                    {{-- Stock --}}
                    <p class="mb-20">
                        <strong>Availability:</strong>
                        @if($product_detail->stock > 0)
                            <span class="text-success"><i class="fa fa-check"></i> In Stock ({{ $product_detail->stock }})</span>
                        @else
                            <span class="text-danger"><i class="fa fa-times"></i> Out of Stock</span>
                        @endif
                    </p>

                    {{-- Categories --}}
                    <p class="mb-20">
                        <strong>Category:</strong>
                        @foreach($product_detail->categories as $category)
                            <a href="{{ route('front.product-cat', $category->slug) }}" class="label label-default">{{ $category->title }}</a>
                        @endforeach
                    </p>

                    {{-- Add to Cart Form --}}
                    <form action="{{ route('single-add-to-cart') }}" method="POST" class="mb-30">
                        @csrf
                        <input type="hidden" name="slug" value="{{ $product_detail->slug }}">
                        
                        <div class="form-group" style="display: flex; align-items: center;">
                            <label style="margin-right: 15px; margin-bottom: 0;">Quantity:</label>
                            <div class="input-group" style="width: 140px;">
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default btn-number" data-type="minus" data-field="quantity">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </span>
                                <input type="text" name="quantity" class="form-control input-number text-center" value="1" min="1" max="100" style="width: 60px; padding: 0; color: #555; background-color: #fff;">
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default btn-number" data-type="plus" data-field="quantity">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-default btn-lg btn-animated">
                            <i class="fa fa-shopping-cart"></i> Add to Cart
                        </button>
                        <a href="{{ route('add-to-wishlist', $product_detail->slug) }}" class="btn btn-default-transparent btn-lg btn-animated">
                            <i class="fa fa-heart-o"></i> Add to Wishlist
                        </a>
                    </form>

                    {{-- Share --}}
                    <div class="sharethis-inline-share-buttons"></div>
                </div>
            </div>
        </div>

        {{-- Product Tabs --}}
        <div class="row mt-50">
            <div class="col-md-12">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#description">Description</a></li>
                    <li><a data-toggle="tab" href="#reviews">Reviews ({{ $reviewCount }})</a></li>
                </ul>

                <div class="tab-content">
                    {{-- Description Tab --}}
                    <div id="description" class="tab-pane fade in active">
                        <div class="pv-30">
                            {!! $product_detail->description !!}
                        </div>
                    </div>

                    {{-- Reviews Tab --}}
                    <div id="reviews" class="tab-pane fade">
                        <div class="pv-30">
                            {{-- Add Review Form --}}
                            @auth
                            <h4>Add Your Review</h4>
                            <form method="POST" action="{{ route('product.review.store', $product_detail->slug) }}" class="mb-40">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product_detail->id }}">
                                
                                <div class="form-group">
                                    <label>Rating</label>
                                    <div class="rating-input">
                                        @for($i = 5; $i >= 1; $i--)
                                        <input type="radio" id="star{{ $i }}" name="rate" value="{{ $i }}" required>
                                        <label for="star{{ $i }}" class="fa fa-star"></label>
                                        @endfor
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Your Review</label>
                                    <textarea name="review" class="form-control" rows="5" required></textarea>
                                </div>

                                <button type="submit" class="btn btn-default">Submit Review</button>
                            </form>
                            @else
                            <p class="alert alert-info">
                                Please <a href="{{ route('login') }}">login</a> to add a review.
                            </p>
                            @endauth

                            {{-- Reviews List --}}
                            <h4>Customer Reviews</h4>
                            @forelse($product_detail->getReview as $review)
                            <div class="review-item mb-30 p-20 border-radius-3" style="background:#f9f9f9;">
                                <div class="row">
                                    <div class="col-md-2 text-center">
                                        <img src="{{ $review->user?->getFirstMediaUrl('photo') ?: asset('backend/img/avatar.png') }}" 
                                             alt="{{ $review->user['name'] }}" 
                                             class="img-circle" 
                                             style="width:80px;height:80px;">
                                        <p class="mt-10"><strong>{{ $review->user['name'] }}</strong></p>
                                    </div>
                                    <div class="col-md-10">
                                        <div class="rating mb-10">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fa fa-star{{ $review->rate >= $i ? ' text-default' : '-o' }}"></i>
                                            @endfor
                                            <span class="text-muted ml-10">{{ $review->created_at->format('M d, Y') }}</span>
                                        </div>
                                        <p>{{ $review->review }}</p>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <p class="text-muted">No reviews yet. Be the first to review this product!</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Related Products --}}
        @if(isset($related) && $related->count() > 0)
        <div class="row mt-50">
            <div class="col-md-12">
                <h3 class="mb-30">Related Products</h3>
            </div>
        </div>
        <div class="row">
            @foreach($related->take(4) as $product)
            <div class="col-md-3 col-sm-6">
                <div class="product-item mb-30">
                    <div class="product-item-img">
                        <a href="{{ route('front.product-detail', $product->slug) }}">
                            <img src="{{ $product->imageUrl }}" alt="{{ $product->title }}" class="img-responsive">
                        </a>
                        @if($product->discount)
                        <span class="badge badge-danger">-{{ $product->discount }}%</span>
                        @endif
                    </div>
                    <div class="product-item-title">
                        <a href="{{ route('front.product-detail', $product->slug) }}">{{ $product->title }}</a>
                    </div>
                    <div class="product-item-price">
                        @php
                            $relatedPrice = $product->price - ($product->price * ($product->discount ?? 0) / 100);
                        @endphp
                        @if($product->discount)
                            <del class="text-muted">${{ number_format($product->price, 2) }}</del>
                        @endif
                        <span class="text-default">${{ number_format($relatedPrice, 2) }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>
@endsection

@push('styles')
<style>
.product-gallery-container { margin-bottom: 30px; }
.product-details h2 { margin-top: 0; }
.rating { font-size: 18px; }
.rating .fa { margin-right: 2px; }
.price { font-size: 32px; font-weight: 600; margin: 20px 0; }
.rating-input { display: flex; flex-direction: row-reverse; justify-content: flex-end; }
.rating-input input { display: none; }
.rating-input label { cursor: pointer; font-size: 24px; color: #ddd; margin-right: 5px; }
.rating-input label:hover, .rating-input label:hover ~ label, 
.rating-input input:checked ~ label { color: #FFD700; }
.product-item { transition: all 0.3s; }
.product-item:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
/* Quantity Input Alignment Fixes */
.btn-number { margin-top: 0 !important; height: 36px; line-height: 22px; }
.input-number { height: 36px !important; margin-top: 0 !important; vertical-align: top; }

</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Quantity buttons
    $('.btn-number').click(function(e) {
        e.preventDefault();
        const type = $(this).attr('data-type');
        const input = $(this).closest('.input-group').find('.input-number');
        let currentVal = parseInt(input.val()) || 1;
        
        if(type == 'minus') {
            if(currentVal > 1) input.val(currentVal - 1);
        } else {
            if(currentVal < 100) input.val(currentVal + 1);
        }
    });
});
</script>
@endpush
