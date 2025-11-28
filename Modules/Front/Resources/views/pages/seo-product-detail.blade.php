@extends('front::layouts.seo-master')

@section('seo')
    <!-- Additional product-specific SEO -->
    @if(isset($product_detail))
        <!-- Product-specific meta tags -->
        <meta name="product:price:amount" content="{{ $product_detail->price }}">
        <meta name="product:price:currency" content="USD">
        <meta name="product:availability" content="{{ $product_detail->stock > 0 ? 'in stock' : 'out of stock' }}">
        <meta name="product:condition" content="new">
        <meta name="product:brand" content="{{ $product_detail->brand?->title ?? 'Unknown' }}">
        
        <!-- Additional Open Graph for products -->
        <meta property="product:price:amount" content="{{ $product_detail->price }}">
        <meta property="product:price:currency" content="USD">
        <meta property="product:availability" content="{{ $product_detail->stock > 0 ? 'in stock' : 'out of stock' }}">
        <meta property="product:condition" content="new">
        <meta property="product:brand" content="{{ $product_detail->brand?->title ?? 'Unknown' }}">
        
        <!-- Breadcrumb Schema -->
        @php
            $breadcrumbs = [
                ['name' => 'Products', 'url' => route('front.product-cat', $product_detail->categories->first()->slug ?? '')],
                ['name' => $product_detail->title, 'url' => null]
            ];
        @endphp
        <script type="application/ld+json">
            {!! json_encode(app(\Modules\Front\Services\SeoService::class)->generateBreadcrumbs($breadcrumbs), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
        </script>
    @endif
@endsection

@section('content')
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="{{ route('front.index') }}">Home<i class="ti-arrow-right"></i></a></li>
                            @if($product_detail->categories->isNotEmpty())
                                <li><a href="{{ route('front.product-cat', $product_detail->categories->first()->slug) }}">{{ $product_detail->categories->first()->title }}<i class="ti-arrow-right"></i></a></li>
                            @endif
                            <li class="active"><a href="javascript:void(0);">{{ $product_detail->title }}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <!-- Product Detail Section -->
    <section class="product-detail section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-12">
                    <div class="product-detail-image">
                        @if($product_detail->imageUrl)
                            <img src="{{ $product_detail->imageUrl }}" 
                                 alt="{{ $product_detail->title }} - {{ $product_detail->brand?->title ?? 'Product' }}" 
                                 class="img-fluid"
                                 loading="lazy">
                        @endif
                    </div>
                </div>
                <div class="col-lg-6 col-12">
                    <div class="product-detail-content">
                        <h1 class="product-title">{{ $product_detail->title }}</h1>
                        
                        @if($product_detail->brand)
                            <p class="product-brand">Brand: <strong>{{ $product_detail->brand->title }}</strong></p>
                        @endif
                        
                        <div class="product-price">
                            <span class="current-price">${{ number_format($product_detail->price, 2) }}</span>
                            @if($product_detail->discount && $product_detail->discount > 0)
                                <span class="original-price">${{ number_format($product_detail->price + $product_detail->discount, 2) }}</span>
                                <span class="discount-badge">Save ${{ number_format($product_detail->discount, 2) }}</span>
                            @endif
                        </div>
                        
                        <div class="product-description">
                            <h3>Description</h3>
                            <p>{!! $product_detail->description !!}</p>
                        </div>
                        
                        <div class="product-stock">
                            @if($product_detail->stock > 0)
                                <span class="stock-status in-stock">In Stock ({{ $product_detail->stock }} available)</span>
                            @else
                                <span class="stock-status out-of-stock">Out of Stock</span>
                            @endif
                        </div>
                        
                        @if($product_detail->stock > 0)
                            <div class="product-actions">
                                <form action="{{ route('add-to-cart', $product_detail->slug) }}" method="GET">
                                    @csrf
                                    <div class="quantity-selector">
                                        <label for="quantity">Quantity:</label>
                                        <input type="number" name="quantity" id="quantity" value="1" min="1" max="{{ $product_detail->stock }}">
                                    </div>
                                    <button type="submit" class="btn btn-primary add-to-cart">
                                        <i class="ti-shopping-cart"></i> Add to Cart
                                    </button>
                                </form>
                            </div>
                        @endif
                        
                        <div class="product-meta">
                            <p><strong>SKU:</strong> {{ $product_detail->sku }}</p>
                            @if($product_detail->categories->isNotEmpty())
                                <p><strong>Category:</strong> 
                                    @foreach($product_detail->categories as $category)
                                        <a href="{{ route('front.product-cat', $category->slug) }}">{{ $category->title }}</a>
                                        @if(!$loop->last), @endif
                                    @endforeach
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Product Tabs -->
            <div class="row">
                <div class="col-12">
                    <div class="product-tabs">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#description" role="tab">Description</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#specifications" role="tab">Specifications</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#reviews" role="tab">Reviews</a>
                            </li>
                        </ul>
                        
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="description" role="tabpanel">
                                <div class="product-description-content">
                                    {!! $product_detail->description !!}
                                </div>
                            </div>
                            
                            <div class="tab-pane fade" id="specifications" role="tabpanel">
                                <div class="product-specifications">
                                    <table class="table table-striped">
                                        <tbody>
                                            <tr>
                                                <td><strong>SKU</strong></td>
                                                <td>{{ $product_detail->sku }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Brand</strong></td>
                                                <td>{{ $product_detail->brand?->title ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Stock</strong></td>
                                                <td>{{ $product_detail->stock }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Status</strong></td>
                                                <td>{{ ucfirst($product_detail->status) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="tab-pane fade" id="reviews" role="tabpanel">
                                <div class="product-reviews">
                                    <h4>Customer Reviews</h4>
                                    <p>No reviews yet. Be the first to review this product!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End Product Detail Section -->
@endsection

@push('styles')
<style>
    .product-detail-image img {
        width: 100%;
        height: auto;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .product-title {
        font-size: 2.5rem;
        font-weight: bold;
        color: #2c3e50;
        margin-bottom: 1rem;
    }
    
    .product-price {
        margin: 1.5rem 0;
    }
    
    .current-price {
        font-size: 2rem;
        font-weight: bold;
        color: #e74c3c;
    }
    
    .original-price {
        font-size: 1.2rem;
        color: #95a5a6;
        text-decoration: line-through;
        margin-left: 1rem;
    }
    
    .discount-badge {
        background: #27ae60;
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.9rem;
        margin-left: 1rem;
    }
    
    .stock-status {
        padding: 0.5rem 1rem;
        border-radius: 4px;
        font-weight: bold;
    }
    
    .in-stock {
        background: #d4edda;
        color: #155724;
    }
    
    .out-of-stock {
        background: #f8d7da;
        color: #721c24;
    }
    
    .product-actions {
        margin: 2rem 0;
    }
    
    .quantity-selector {
        margin-bottom: 1rem;
    }
    
    .quantity-selector input {
        width: 80px;
        padding: 0.5rem;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    
    .add-to-cart {
        background: #3498db;
        color: white;
        padding: 1rem 2rem;
        border: none;
        border-radius: 4px;
        font-weight: bold;
        cursor: pointer;
        transition: background 0.3s;
    }
    
    .add-to-cart:hover {
        background: #2980b9;
    }
    
    .product-meta {
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #eee;
    }
    
    .product-tabs {
        margin-top: 3rem;
    }
    
    .nav-tabs .nav-link {
        color: #2c3e50;
        border: 1px solid transparent;
        border-top-left-radius: 0.25rem;
        border-top-right-radius: 0.25rem;
    }
    
    .nav-tabs .nav-link.active {
        color: #495057;
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
    }
    
    .tab-content {
        padding: 2rem;
        border: 1px solid #dee2e6;
        border-top: none;
        background: #fff;
    }
</style>
@endpush
