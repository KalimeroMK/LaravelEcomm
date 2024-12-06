<nav class="navbar navbar-expand-lg">
    <div class="navbar-collapse">
        <div class="nav-inner">
            <ul class="nav main-menu menu navbar-nav">
                <li class="{{Request::path()=='home' ? 'active' : ''}}"><a
                            href="{{route('front.index')}}">Home</a></li>
                <li class="{{Request::path()=='about-us' ? 'active' : ''}}"><a
                            href="{{route('front.about-us')}}">About Us</a></li>
                <li class="@if(Request::path()=='product-grids'||Request::path()=='product-lists')  active  @endif">
                    <a href="{{route('front.product-grids')}}">Products</a><span
                            class="new">New</span></li>
                <li class="{{Request::path()=='bundles' ? 'active' : ''}}"><a
                            href="{{route('front.bundles')}}">Bundles</a><span
                            class="new">Hot</span>
                </li>
                <li class="{{Request::path()=='blog' ? 'active' : ''}}"><a
                            href="{{route('front.blog')}}">Blog</a></li>
                <li class="{{Request::path()=='contact' ? 'active' : ''}}"><a
                            href="{{route('front.contact')}}">Contact Us</a></li>
            </ul>
        </div>
    </div>
</nav>