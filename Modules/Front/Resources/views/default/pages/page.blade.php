@extends('front::layouts.master')

@section('content')
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="{{route('front.index')}}">Home<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="">Page Details</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <!-- Shop Single -->
    <section class="about-us section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="about-content">
                        <h3>{!! $page->title !!}</h3>
                        <p>{!! $page->content !!}</p>
                        <div class="button">
                            <a href="{{route('front.blog')}}" class="btn">Our Blog</a>
                            <a href="{{route('front.contact')}}" class="btn primary">Contact Us</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection
