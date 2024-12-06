@php use Modules\Core\Helpers\Helper; @endphp
@extends('front::layouts.master')

@section('title','E-TECH || Blog Detail page')

@section('content')
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="{{route('front.index')}}">Home<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="javascript:void(0);">Blog Single Sidebar</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <!-- Start Blog Single -->
    <section class="blog-single section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-12">
                    <div class="blog-single-main">
                        <div class="row">
                            <div class="col-12">
                                <div class="image">
                                    <img src="{{$post->ImageUrl}}" alt="{{$post->title}}">
                                </div>
                                <div class="blog-detail">
                                    <h2 class="blog-title">{{$post->title}}</h2>
                                    <div class="blog-meta">
                                        <span class="author"><a href="javascript:void(0);"><i class="fa
                                        fa-user"></i>By {{$post->author->name}}</a><a
                                                href="javascript:void(0);"><i class="fa fa-calendar"></i>{{$post->created_at->format('M d, Y')}}</a><a
                                                href="javascript:void(0);"><i class="fa fa-comments"></i>Comment ({{$post->allComments->count()}})</a></span>
                                    </div>
                                    <div class="sharethis-inline-reaction-buttons"></div>
                                    <div class="content">
                                        @if($post->quote)
                                            <blockquote><i class="fa fa-quote-left"></i> {!! ($post->quote) !!}
                                            </blockquote>
                                        @endif
                                        <p>{!! ($post->description) !!}</p>
                                    </div>
                                </div>
                                <div class="share-social">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="content-tags">
                                                <h4>Tags:</h4>
                                                <ul class="tag-inner">

                                                    @foreach($tags as $tag)
                                                        <li>{{$tag->title}}</a></li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @auth
                                <div class="col-12 mt-4">
                                    <div class="reply">
                                        <div class="reply-head comment-form" id="commentFormContainer">
                                            <h2 class="reply-title">Leave a Comment</h2>
                                            <!-- Comment Form -->
                                            <form class="form comment_form" id="commentForm"
                                                  action="{{route('post-comment.store',$post->slug)}}" method="POST">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-lg-6 col-md-6 col-12">
                                                        <div class="form-group">
                                                            <label>Your Name<span>*</span></label>
                                                            <input type="text" name="name" placeholder=""
                                                                   required="required">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-12">
                                                        <div class="form-group">
                                                            <label>Your Email<span>*</span></label>
                                                            <input type="email" name="email" placeholder=""
                                                                   required="required">
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group  comment_form_body">
                                                            <label>Your Message<span>*</span></label>
                                                            <textarea name="comment" id="comment" rows="10"
                                                                      placeholder=""></textarea>
                                                            <input type="hidden" name="post_id"
                                                                   value="{{ $post->id }}"/>
                                                            <input type="hidden" name="parent_id" id="parent_id"
                                                                   value=""/>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group button">
                                                            <button type="submit" class="btn"><span
                                                                    class="comment_btn comment">Post Comment</span><span
                                                                    class="comment_btn reply"
                                                                    style="display: none;">Reply Comment</span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                            <!-- End Comment Form -->
                                        </div>
                                    </div>
                                </div>

                            @else
                                <p class="text-center p-5">
                                    You need to <a href="{{route('login')}}" style="color:rgb(54, 54, 204)">Login</a> OR
                                    {{--                                    <a style="color:blue" href="{{route('register.form')}}">Register</a> for comment.--}}

                                </p>


                                <!--/ End Form -->
                            @endauth
                            <div class="col-12">
                                <div class="comments">
                                    <h3 class="comment-title">Comments ({{$post->allComments->count()}})</h3>
                                    <!-- Single Comment -->

                                    @include('front::pages.comment', ['comments' => $post->comments, 'post_id' =>
                                    $post->id, 'depth' => 3])
                                    <!-- End Single Comment -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-12">
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
                        <div class="single-widget side-tags">
                            <h3 class="title">Categories</h3>
                            <ul class="tag">
                                @foreach(Helper::postCategoryList() as $cat)
                                    <li><a href="{{ route('front.blog-by-category', $cat->slug) }}">{{$cat->title}}
                                        </a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <!--/ End Single Widget -->
                    <!-- Single Widget -->
                    <div class="single-widget recent-post">
                        <h3 class="title">Recent post</h3>
                        @foreach($recantPosts as $post)
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
                                            {{ $post->author->name ?? 'Anonymous' }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <!-- End Single Post -->
                        @endforeach
                    </div>
                    <!--/ End Single Widget -->
                    <!-- Single Widget -->
                    <!--/ End Single Widget -->
                    <!-- Single Widget -->
                    <div class="single-widget side-tags">
                        <h3 class="title">Tags</h3>
                        <ul class="tag">
                            @foreach(Helper::postTagList() as $tag)
                                <li><a href="{{ route('front.blog-by-tag', $tag->slug) }}">{{$tag->title}}</a></li>
                            @endforeach
                        </ul>
                    </div>
                    <!--/ End Single Widget -->
                    <!-- Single Widget -->
                    <div class="single-widget newsletter">
                        <h3 class="title">Newslatter</h3>
                        <div class="letter-inner">
                            <h4>Subscribe & get news <br> latest updates.</h4>
                            <form action="{{route('subscribe')}}" method="POST">
                                @csrf
                                <div class="form-inner">
                                    <input type="email" name="email" placeholder="Enter your email">
                                    <button type="submit" class="btn mt-2">Submit</button>
                                </div>
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
    <script type='text/javascript'
            src='https://platform-api.sharethis.com/js/sharethis.js#property=5f2e5abf393162001291e431&product=inline-share-buttons'
            async='async'></script>
@endpush
@push('scripts')
    <script>
        $(document).ready(function () {

            (function ($) {
                "use strict";

                $('.btn-reply.reply').click(function (e) {
                    e.preventDefault();
                    $('.btn-reply.reply').show();

                    $('.comment_btn.comment').hide();
                    $('.comment_btn.reply').show();

                    $(this).hide();
                    $('.btn-reply.cancel').hide();
                    $(this).siblings('.btn-reply.cancel').show();

                    var parent_id = $(this).data('id');
                    var html = $('#commentForm');
                    $(html).find('#parent_id').val(parent_id);
                    $('#commentFormContainer').hide();
                    $(this).parents('.comment-list').append(html).fadeIn('slow').addClass('appended');
                });

                $('.comment-list').on('click', '.btn-reply.cancel', function (e) {
                    e.preventDefault();
                    $(this).hide();
                    $('.btn-reply.reply').show();

                    $('.comment_btn.reply').hide();
                    $('.comment_btn.comment').show();

                    $('#commentFormContainer').show();
                    var html = $('#commentForm');
                    $(html).find('#parent_id').val('');

                    $('#commentFormContainer').append(html);
                });

            })(jQuery)
        })
    </script>
@endpush
