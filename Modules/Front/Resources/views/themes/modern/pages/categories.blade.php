@extends($themePath . '.layouts.master')
@section('title', 'Categories')
@section('content')
<!-- breadcrumb start -->
<div class="breadcrumb-container">
    <div class="container">
        <ol class="breadcrumb">
            <li><i class="fa fa-home pr-10"></i><a href="{{ route('front.index') }}">Home</a></li>
            <li class="active">Categories</li>
        </ol>
    </div>
</div>
<!-- breadcrumb end -->

<!-- main-container start -->
<section class="main-container">
    <div class="container">
        <div class="row">
            <div class="main col-md-12">
                <h1 class="page-title">{{ __('All Categories') }}</h1>
                <div class="separator-2"></div>
                
                @if(isset($categories) && $categories->isNotEmpty())
                    <div class="row">
                        @foreach($categories as $category)
                        <div class="col-md-4 col-sm-6">
                            <div class="image-box style-2 mb-20 bordered light-gray-bg">
                                <div class="overlay-container">
                                    @if($category->photo)
                                        <img src="{{ $category->photo }}" alt="{{ $category->title }}" style="height: 200px; object-fit: cover; width: 100%;">
                                    @else
                                        <img src="{{ asset('frontend/themes/modern/images/category-placeholder.jpg') }}" alt="{{ $category->title }}" style="height: 200px; object-fit: cover; width: 100%;">
                                    @endif
                                    <div class="overlay-to-top">
                                        <p class="small margin-clear"><em>{{ Str::limit($category->summary, 60) }}</em></p>
                                    </div>
                                </div>
                                <div class="body padding-horizontal-clear">
                                    <h4 class="title margin-clear">
                                        <a href="{{ route('front.product-cat', $category->slug) }}">{{ $category->title }}</a>
                                    </h4>
                                    <p class="small mb-10 text-muted">
                                        <i class="fa fa-folder-o pr-1"></i> 
                                        {{ $category->children_count ?? 0 }} subcategories
                                    </p>
                                    <a href="{{ route('front.product-cat', $category->slug) }}" class="btn btn-default btn-sm margin-clear">
                                        View <i class="fa fa-arrow-right pl-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info">
                        No categories found.
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
