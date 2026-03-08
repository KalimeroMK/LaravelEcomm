<!-- Start Footer Area -->
<footer class="footer">
    <!-- Footer Top -->
    <div class="footer-top section">
        <div class="container">
            <div class="row">
                <div class="col-lg-5 col-md-6 col-12">
                    <!-- Single Widget -->
                    <div class="single-footer about">
                        <div class="logo">
                            <a href="/"><img src="{{asset('backend/img/logo2.png')}}" alt="#"></a>
                        </div>
                        <p class="text">@foreach($settings as $data)
                                {{$data->short_des}}
                            @endforeach</p>
                        <p class="call">@lang('frontend.call_us_24/7')<span><a
                                        href="tel:123456789">@foreach($settings as $data)
                                        {{$data->phone}}
                                    @endforeach</a></span>
                        </p>
                    </div>
                    <!-- End Single Widget -->
                </div>
                <div class="col-lg-2 col-md-6 col-12">
                    <!-- Single Widget -->
                    <div class="single-footer links">
                        <h4>@lang('frontend.information')</h4>
                        <ul>
                            <li><a href="{{route('front.about-us')}}">@lang('frontend.about_us')</a></li>
                            <li><a href="{{route('front.contact')}}">@lang('frontend.contact_us')</a></li>
                            @foreach($pageList as $page)
                                <li><a href="{{ route('front.pages', $page['slug']) }}">{{ $page['title'] }}</a></li>
                            @endforeach                        </ul>
                    </div>
                    <!-- End Single Widget -->
                </div>
                <div class="col-lg-2 col-md-6 col-12">
                    <!-- Single Widget -->
                    <div class="single-footer links">
                        <h4>@lang('frontend.customer_service')</h4>
                        <ul>
                            <li><a href="#">@lang('frontend.payment_methods')</a></li>
                            <li><a href="#">@lang('frontend.money_back')</a></li>
                            <li><a href="#">@lang('frontend.returns')</a></li>
                            <li><a href="#">@lang('frontend.shipping')</a></li>
                            <li><a href="#">@lang('frontend.privacy_policy')</a></li>
                        </ul>
                    </div>
                    <!-- End Single Widget -->
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <!-- Single Widget -->
                    <div class="single-footer social">
                        <h4>@lang('frontend.get_in_touch')</h4>
                        <!-- Single Widget -->
                        <div class="contact">
                            <ul>
                                @foreach($settings as $data)
                                    <li>{{$data->address}}</li>
                                    <li>{{$data->email}}</li>
                                    <li>{{$data->phone}} </li>
                                @endforeach
                            </ul>
                        </div>
                        <!-- End Single Widget -->
                        <div class="sharethis-inline-follow-buttons"></div>
                    </div>
                    <!-- End Single Widget -->
                </div>
            </div>
        </div>
    </div>
    <!-- End Footer Top -->
    <div class="copyright">
        <div class="container">
            <div class="inner">
                <div class="row">
                    <div class="col-lg-6 col-12">
                        <div class="left">
                            <p>@lang('frontend.copyright', ['year' => date('Y'), 'name' => 'KalimeroMK'])</p>
                        </div>
                    </div>
                    <div class="col-lg-6 col-12">
                        <div class="right">
                            <img src="{{asset('backend/img/payments.png')}}" alt="#">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- /End Footer Area -->

<!-- Jquery -->
<script src="{{asset('frontend/js/all_front.min.js')}}"></script>


@stack('scripts')
<script>
    setTimeout(function () {
        $('.alert').slideUp();
    }, 5000);
    $(function () {
        // ------------------------------------------------------- //
        // Multi Level dropdowns
        // ------------------------------------------------------ //
        $("ul.dropdown-menu [data-toggle='dropdown']").on("click", function (event) {
            event.preventDefault();
            event.stopPropagation();

            $(this).siblings().toggleClass("show");


            if (!$(this).next().hasClass('show')) {
                $(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
            }
            $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function (e) {
                $('.dropdown-submenu .show').removeClass("show");
            });

        });
    });
</script>
