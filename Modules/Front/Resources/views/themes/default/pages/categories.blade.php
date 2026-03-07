@extends('front::themes.default.layouts.master')

@section('content')
<section class="product-area shop-sidebar shop section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2>All Categories</h2>
                <hr>
            </div>
        </div>
        
        <div class="row">
            @foreach($categories as $category)
            <div class="col-lg-4 col-md-6 col-12">
                <div class="single-category mb-4">
                    <div class="category-img">
                        <a href="{{ route('front.category.detail', ['slug' => $category->slug]) }}">
                            @if($category->photo)
                            <img src="{{ $category->photo }}" alt="{{ $category->title }}" style="width: 100%; height: 250px; object-fit: cover;">
                            @else
                            <img src="{{ route('front.placeholder.image', ['type' => 'category', 'text' => $category->title, 'index' => $loop->index]) }}" alt="{{ $category->title }}" style="width: 100%; height: 250px; object-fit: cover;">
                            @endif
                        </a>
                    </div>
                    <div class="category-content mt-3">
                        <h3><a href="{{ route('front.category.detail', ['slug' => $category->slug]) }}">{{ $category->title }}</a></h3>
                        <p>{{ Str::limit($category->summary, 100) }}</p>
                        <a href="{{ route('front.category.detail', ['slug' => $category->slug]) }}" class="btn">
                            View Products <i class="ti-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
