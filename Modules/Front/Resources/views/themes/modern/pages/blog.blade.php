@php use Modules\Core\Helpers\Helper; @endphp
@extends($themePath . '.layouts.master')
@section('title','E-SHOP || Blog Page')
@section('content')
<section class="page-header page-header-dark bg-secondary">
    <div class="container"><div class="row"><div class="col-md-12">
        <h1>Blog</h1>
        <ol class="breadcrumb">
            <li><a href="{{ route('front.index') }}">Home</a></li>
            <li class="active">Blog</li>
        </ol>
    </div></div></div>
</section>

<section class="main-container">
    <div class="container">
        <div class="row">
            <div class="col-md-9">
                <div class="row grid-space-20">
                    @foreach($posts as $post)
                    <div class="col-md-6">
                        <div class="blog-post-item">
                            <div class="blog-post-img">
                                <a href="{{ route('front.blog-detail', $post->slug) }}">
                                    <img src="{{ $post->imageUrl }}" alt="{{ $post->title }}" class="img-responsive">
                                </a>
                            </div>
                            <div class="blog-post-content">
                                <h3 class="blog-post-title"><a href="{{ route('front.blog-detail', $post->slug) }}">{{ $post->title }}</a></h3>
                                <p class="blog-post-meta">
                                    <span><i class="fa fa-calendar"></i> {{ $post->created_at->format('d M, Y') }}</span>
                                    <span><i class="fa fa-user"></i> {{ $post->author->name ?? 'Anonymous' }}</span>
                                </p>
                                <p>{!! html_entity_decode($post->summary) !!}</p>
                                <a href="{{ route('front.blog-detail', $post->slug) }}" class="btn btn-default btn-sm">Read More</a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="row"><div class="col-md-12 text-center">
                    {{ $posts->appends($_GET)->links('vendor.pagination.bootstrap-4') }}
                </div></div>
            </div>
            <div class="col-md-3">
                <aside class="sidebar">
                    <div class="block clearfix">
                        <h3 class="title">Search</h3>
                        <form action="{{ route('front.blog-search') }}" method="GET">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Search...">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
                                </span>
                            </div>
                        </form>
                    </div>
                    <div class="block clearfix">
                        <h3 class="title">Categories</h3>
                        <ul class="list-unstyled">
                            @foreach(Helper::postCategoryList() as $cat)
                            <li><a href="{{ route('front.blog-by-category', $cat->slug) }}">{{ $cat->title }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="block clearfix">
                        <h3 class="title">Recent Posts</h3>
                        @foreach($posts->take(3) as $post)
                        <div class="media">
                            <a class="pull-left" href="{{ route('front.blog-detail', $post->slug) }}">
                                <img class="media-object" src="{{ $post->imageUrl }}" alt="{{ $post->title }}" style="width:80px;">
                            </a>
                            <div class="media-body">
                                <h5 class="media-heading"><a href="{{ route('front.blog-detail', $post->slug) }}">{{ $post->title }}</a></h5>
                                <p class="small"><i class="fa fa-calendar"></i> {{ $post->created_at->format('d M, y') }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="block clearfix">
                        <h3 class="title">Newsletter</h3>
                        <form method="POST" action="{{ route('subscribe') }}">
                            @csrf
                            <div class="form-group">
                                <input type="email" name="email" class="form-control" placeholder="Your email">
                            </div>
                            <button type="submit" class="btn btn-default btn-block">Subscribe</button>
                        </form>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</section>
@endsection
