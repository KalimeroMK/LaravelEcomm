@php use Modules\Core\Helpers\Helper; @endphp
@extends('front::layouts.master')

@section('title','E-SHOP || Blog Page')

@section('content')
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="{{route('front.index')}}">Home<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="javascript:void(0);">Blog Grid Sidebar</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->
    <!-- Start Blog Single -->
    <section class="blog-single shop-blog grid section">
        <div class="container">
            <div class="row">
                <div class="col-9">
                    <div class="row">
                        @foreach($posts as $post)
                            <div class="col-lg-6 col-md-6 col-12">
                                <!-- Start Single Blog  -->
                                <div class="shop-single-blog">
                                    <img src="{{ $post->image_url }}" alt="{{ $post->photo }}">
                                    <div class="content">
                                        <p class="date">
                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                            {{ $post->created_at->format('d M, Y. D') }}
                                            <span class="float-right">
                        <i class="fa fa-user" aria-hidden="true"></i>
                        {{ $post->author->name ?? 'Anonymous' }}
                    </span>
                                        </p>
                                        <a href="{{ route('front.blog-detail', $post->slug) }}"
                                           class="title">{{ $post->title }}</a>
                                        <p>{!! html_entity_decode($post->summary) !!}</p>
                                        <a href="{{ route('front.blog-detail', $post->slug) }}" class="more-btn">Continue
                                            Reading</a>
                                    </div>
                                </div>
                                <!-- End Single Blog  -->
                            </div>
                        @endforeach

                        <div class="col-12">
                            <!-- Pagination -->
                            {{$posts->appends($_GET)->links('vendor.pagination.bootstrap-4')}}
                            <!--/ End Pagination -->
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="main-sidebar">
                        <!-- Single Widget -->
                        <div class="single-widget search">
                            <form class="form" method="GET" action="{{route('front.blog-search')}}">
                                <input type="text" placeholder="Search Here..." name="search">
                                <button class="button" type="submit"><i class="fa fa-search"></i></button>
                            </form>
                        </div>
                        <!--/ End Single Widget -->
                        <!-- Single Widget -->
                        <div class="single-widget category">
                            <h3 class="title">Blog Categories</h3>
                            <ul class="categor-list">
                                @foreach(Helper::postCategoryList() as $cat)
                                    <li>
                                        <a href="{{route('front.blog-by-category',$cat->slug)}}">{{$cat->title}} </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <!--/ End Single Widget -->
                        <!-- Single Widget -->
                        <div class="single-widget recent-post">
                            <h3 class="title">Recent post</h3>
                            @foreach($posts as $post)
                                <!-- Single Post -->
                                <div class="single-post">
                                    <div class="image">
                                        <img src="{{$post->ImageUrl}}" alt="{{$post->title}}">
                                    </div>
                                    <div class="content">
                                        <h5><a href="#">{{$post->title}}</a></h5>
                                        <ul class="comment">

                                            <li><i class="fa fa-calendar"
                                                   aria-hidden="true"></i>{{$post->created_at->format('d M, y')}}</li>
                                            <li><i class="fa fa-user" aria-hidden="true"></i>
                                                {{$post->author->name ?? 'Anonymous'}}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <!-- End Single Post -->
                            @endforeach
                        </div>
                        >
                        <!-- Single Widget -->
                        <div class="single-widget newsletter">
                            <h3 class="title">Newsletter</h3>
                            <div class="letter-inner">
                                <h4>Subscribe & get news <br> latest updates.</h4>
                                <form method="POST" action="{{route('subscribe')}}" class="form-inner">
                                    @csrf
                                    <input type="email" name="email" placeholder="Enter your email">
                                    <button type="submit" class="btn " style="width: 100%">Submit</button>
                                </form>
                            </div>
                        </div>
                        <!--/ End Single Widget -->
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/ End Blog Single -->
@endsection
@push('styles')
    <style>
        .pagination {
            display: inline-flex;
        }
    </style>

@endpush
