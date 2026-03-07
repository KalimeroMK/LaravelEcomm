@extends('front::themes.modern.layouts.master')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="my-4">{{ __('Categories') }}</h1>
            
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('front.index') }}">Home</a></li>
                    <li class="breadcrumb-item active">Categories</li>
                </ol>
            </nav>
        </div>
    </div>
    
    <div class="row">
        @foreach($categories as $category)
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                @if($category->photo)
                <img src="{{ $category->photo }}" class="card-img-top" alt="{{ $category->title }}" style="height: 200px; object-fit: cover;">
                @else
                <img src="{{ route('front.placeholder.image', ['type' => 'category', 'text' => $category->title, 'index' => $loop->index]) }}" class="card-img-top" alt="{{ $category->title }}" style="height: 200px; object-fit: cover;">
                @endif
                <div class="card-body">
                    <h5 class="card-title">{{ $category->title }}</h5>
                    <p class="card-text">{{ Str::limit($category->summary, 100) }}</p>
                    <a href="{{ route('front.category.detail', ['slug' => $category->slug]) }}" class="btn btn-primary">
                        {{ __('View Products') }}
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
