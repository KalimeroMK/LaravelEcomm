<nav class="navbar navbar-expand-lg">
    <div class="navbar-collapse">
        <div class="nav-inner">
            <ul class="nav main-menu menu navbar-nav">
                <li class="{{Request::path()=='home' ? 'active' : ''}}"><a
                            href="{{route('front.index')}}">@lang('frontend.home')</a></li>
                <li class="{{Request::path()=='about-us' ? 'active' : ''}}"><a
                            href="{{route('front.about-us')}}">@lang('frontend.about_us')</a></li>
                <li class="@if(Request::path()=='product-grids'||Request::path()=='product-lists')  active  @endif">
                    <a href="{{route('front.product-grids')}}">@lang('frontend.products')</a><span
                            class="new">@lang('frontend.new')</span></li>
                <li class="{{Request::path()=='bundles' ? 'active' : ''}}"><a
                            href="{{route('front.bundles')}}">@lang('frontend.bundles')</a><span
                            class="new">@lang('frontend.hot')</span>
                </li>
                <li class="{{Request::path()=='blog' ? 'active' : ''}}"><a
                            href="{{route('front.blog')}}">@lang('frontend.blog')</a></li>
                <li class="{{Request::path()=='contact' ? 'active' : ''}}"><a
                            href="{{route('front.contact')}}">@lang('frontend.contact_us')</a></li>
            </ul>
        </div>
    </div>
</nav>
