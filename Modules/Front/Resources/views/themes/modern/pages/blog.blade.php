@php use Modules\Core\Helpers\Helper; @endphp
@extends($themePath . '.layouts.master')
@section('title','E-SHOP || Blog Page')
@section('content')
@include($themePath . '.layouts.breadcrumbs', ['title' => 'Blog'])

<section class="blog-single shop-blog grid section">
    <div class="container">
        <div class="row">
            {{-- Posts --}}
            <div class="col-lg-9 col-md-8 col-12">
                <div class="row">
                    @forelse($posts as $post)
                        <div class="col-lg-6 col-md-12 col-12">
                            <div class="shop-single-blog">
                                <a href="{{ route('front.blog-detail', $post->slug) }}">
                                    <img src="{{ $post->image_preview_url }}" alt="{{ $post->title }}">
                                </a>
                                <div class="content">
                                    <p class="date">
                                        <i class="fa fa-calendar" aria-hidden="true"></i>
                                        {{ $post->created_at->format('d M, Y') }}
                                        <span class="float-right">
                                            <i class="fa fa-user" aria-hidden="true"></i>
                                            {{ $post->author->name ?? 'Anonymous' }}
                                        </span>
                                    </p>
                                    <a href="{{ route('front.blog-detail', $post->slug) }}" class="title">{{ $post->title }}</a>
                                    <p>{!! html_entity_decode($post->summary) !!}</p>
                                    <a href="{{ route('front.blog-detail', $post->slug) }}" class="more-btn">Continue Reading</a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center" style="padding:80px 20px;">
                            <h4 class="text-warning">No blog posts yet.</h4>
                        </div>
                    @endforelse

                    <div class="col-12">
                        {{ $posts->appends(request()->query())->links('vendor.pagination.bootstrap-4') }}
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-3 col-md-4 col-12">
                <div class="main-sidebar">
                    {{-- Search --}}
                    <div class="single-widget search">
                        <form class="form" method="GET" action="{{ route('front.blog-search') }}">
                            <input type="text" placeholder="Search Here..." name="search">
                            <button class="button" type="submit"><i class="fa fa-search"></i></button>
                        </form>
                    </div>

                    {{-- Categories --}}
                    <div class="single-widget side-tags">
                        <h3 class="title">Categories</h3>
                        <ul class="tag">
                            @foreach(Helper::postCategoryList() as $cat)
                                <li><a href="{{ route('front.blog-by-category', $cat->slug) }}">{{ $cat->title }}</a></li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Recent Posts --}}
                    <div class="single-widget recent-post">
                        <h3 class="title">Recent Posts</h3>
                        @foreach(($recantPosts ?? $posts->take(3)) as $recent)
                            <div class="single-post">
                                <div class="image">
                                    <img src="{{ $recent->image_preview_url }}" alt="{{ $recent->title }}">
                                </div>
                                <div class="content">
                                    <h5><a href="{{ route('front.blog-detail', $recent->slug) }}">{{ $recent->title }}</a></h5>
                                    <ul class="comment">
                                        <li><i class="fa fa-calendar" aria-hidden="true"></i>{{ $recent->created_at->format('d M, y') }}</li>
                                    </ul>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Tags --}}
                    <div class="single-widget side-tags">
                        <h3 class="title">Tags</h3>
                        <ul class="tag">
                            @foreach(Helper::postTagList() as $tag)
                                <li><a href="{{ route('front.blog-by-tag', $tag->slug) }}">{{ $tag->title }}</a></li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Newsletter --}}
                    <div class="single-widget newsletter">
                        <h3 class="title">Newsletter</h3>
                        <div class="letter-inner">
                            <h4>Subscribe & get news <br> latest updates.</h4>
                            <form action="{{ route('subscribe') }}" method="POST">
                                @csrf
                                <div class="form-inner">
                                    <input type="email" name="email" placeholder="Enter your email">
                                    <button type="submit" class="btn mt-2">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
