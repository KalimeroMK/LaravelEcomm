@extends('front::layouts.master')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="my-4">All Categories</h1>
            <hr>
        </div>
    </div>
    
    <div class="row">
        @foreach($categories as $category)
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                @if($category->photo)
                <img src="{{ $category->photo }}" class="card-img-top" alt="{{ $category->title }}" style="height: 200px; object-fit: cover;">
                @else
                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                    <span class="text-muted">No Image</span>
                </div>
                @endif
                <div class="card-body">
                    <h5 class="card-title">{{ $category->title }}</h5>
                    <p class="card-text">{{ Str::limit($category->summary, 100) }}</p>
                    <a href="{{ route('front.category.detail', ['slug' => $category->slug]) }}" class="btn btn-primary">
                        View Products
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
