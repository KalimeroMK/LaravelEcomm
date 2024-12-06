@extends('front::layouts.master')

@section('title','E-SHOP || About Us')

@section('content')

    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="/">Home<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="{{ route('front.about-us') }}">About Us</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <!-- About Us -->
    <section class="about-us section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-12">
                    <div class="about-content">
                        <h3>Welcome To <span>Eshop</span></h3>
                        <p>@foreach($settings as $data)
                                {{$data->description}}
                            @endforeach</p>
                        <div class="button">
                            <a href="{{route('front.blog')}}" class="btn">Our Blog</a>
                            <a href="{{route('front.contact')}}" class="btn primary">Contact Us</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-12">
                    <div class="about-img overlay">
                        {{-- <div class="button">
                            <a href="https://www.youtube.com/watch?v=nh2aYrGMrIE" class="video video-popup mfp-iframe"><i class="fa fa-play"></i></a>
                        </div> --}}
                        <img src="@foreach($settings as $data) {{$data->photo}} @endforeach"
                             alt="@foreach($settings as $data) {{$data->photo}} @endforeach">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End About Us -->

    <!-- Start Team -->
    <section id="team" class="team section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section-title">
                        <h2>Our Expert Team</h2>
                        <p>Business consulting excepteur sint occaecat cupidatat consulting non proident, sunt in culpa
                            qui officia deserunt laborum market. </p>
                    </div>
                </div>
            </div>
            <div class="row">
                <!-- Single Team -->
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="single-team">
                        <!-- Image -->
                        <div class="image">
                            <img src="images/team/team1.jpg" alt="#">
                        </div>
                        <!-- End Image -->
                        <div class="info-head">
                            <!-- Info Box -->
                            <div class="info-box">
                                <h4 class="name"><a href="#">Dahlia Moore</a></h4>
                                <span class="designation">Senior Manager</span>
                            </div>
                            <!-- End Info Box -->
                            <!-- Social -->
                            <div class="social-links">
                                <ul class="social">
                                    <li><a href="#"><i class="fa fa-facebook-f"></i></a></li>
                                    <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                                    <li><a href="#"><i class="fa fa-behance"></i></a></li>
                                    <li><a href="#"><i class="fa fa-instagram"></i></a></li>
                                </ul>
                            </div>
                            <!-- End Social -->
                        </div>
                    </div>
                </div>
                <!-- End Single Team -->
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="single-team">
                        <!-- Image -->
                        <div class="image">
                            <img src="images/team/team2.jpg" alt="#">
                        </div>
                        <!-- End Image -->
                        <div class="info-head">
                            <!-- Info Box -->
                            <div class="info-box">
                                <h4 class="name"><a href="#">Jhone digo</a></h4>
                                <span class="designation">Markeitng</span>
                            </div>
                            <!-- End Info Box -->
                            <!-- Social -->
                            <div class="social-links">
                                <ul class="social">
                                    <li><a href="#"><i class="fa fa-facebook-f"></i></a></li>
                                    <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                                    <li><a href="#"><i class="fa fa-behance"></i></a></li>
                                    <li><a href="#"><i class="fa fa-instagram"></i></a></li>
                                </ul>
                            </div>
                            <!-- End Social -->
                        </div>
                    </div>
                </div>
                <!-- End Single Team -->
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="single-team">
                        <!-- Image -->
                        <div class="image">
                            <img src="images/team/team3.jpg" alt="#">
                        </div>
                        <!-- End Image -->
                        <div class="info-head">
                            <!-- Info Box -->
                            <div class="info-box">
                                <h4 class="name"><a href="#">Zara tingo</a></h4>
                                <span class="designation">Web Developer</span>
                            </div>
                            <!-- End Info Box -->
                            <!-- Social -->
                            <div class="social-links">
                                <ul class="social">
                                    <li><a href="#"><i class="fa fa-facebook-f"></i></a></li>
                                    <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                                    <li><a href="#"><i class="fa fa-behance"></i></a></li>
                                    <li><a href="#"><i class="fa fa-instagram"></i></a></li>
                                </ul>
                            </div>
                            <!-- End Social -->
                        </div>
                    </div>
                </div>
                <!-- End Single Team -->
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="single-team">
                        <!-- Image -->
                        <div class="image">
                            <img src="images/team/team4.jpg" alt="#">
                        </div>
                        <!-- End Image -->
                        <div class="info-head">
                            <!-- Info Box -->
                            <div class="info-box">
                                <h4 class="name"><a href="#">David Zone</a></h4>
                                <span class="designation">SEO Expert</span>
                            </div>
                            <!-- End Info Box -->
                            <!-- Social -->
                            <div class="social-links">
                                <ul class="social">
                                    <li><a href="#"><i class="fa fa-facebook-f"></i></a></li>
                                    <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                                    <li><a href="#"><i class="fa fa-behance"></i></a></li>
                                    <li><a href="#"><i class="fa fa-instagram"></i></a></li>
                                </ul>
                            </div>
                            <!-- End Social -->
                        </div>
                    </div>
                </div>
                <!-- End Single Team -->
            </div>
        </div>
    </section>
    <!--/ End Team Area -->

    <!-- Start Shop Services Area -->
    <section class="shop-services section">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-12">
                    <!-- Start Single Service -->
                    <div class="single-service">
                        <i class="ti-rocket"></i>
                        <h4>Free shiping</h4>
                        <p>Orders over $100</p>
                    </div>
                    <!-- End Single Service -->
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <!-- Start Single Service -->
                    <div class="single-service">
                        <i class="ti-reload"></i>
                        <h4>Free Return</h4>
                        <p>Within 30 days returns</p>
                    </div>
                    <!-- End Single Service -->
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <!-- Start Single Service -->
                    <div class="single-service">
                        <i class="ti-lock"></i>
                        <h4>Sucure Payment</h4>
                        <p>100% secure payment</p>
                    </div>
                    <!-- End Single Service -->
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <!-- Start Single Service -->
                    <div class="single-service">
                        <i class="ti-tag"></i>
                        <h4>Best Peice</h4>
                        <p>Guaranteed price</p>
                    </div>
                    <!-- End Single Service -->
                </div>
            </div>
        </div>
    </section>
    <!-- End Shop Services Area -->

    @include('front::layouts.newsletter')
@endsection
