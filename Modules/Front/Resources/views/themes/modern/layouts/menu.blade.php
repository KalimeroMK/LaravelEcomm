{{-- Modern Theme Menu --}}
<nav class="navbar navbar-default mega-menu" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-main-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        
        <div class="collapse navbar-collapse navbar-main-collapse">
            <ul class="nav navbar-nav">
                <li class="{{ Request::path() == 'home' ? 'active' : '' }}">
                    <a href="{{ route('front.index') }}">Home</a>
                </li>
                <li class="{{ Request::path() == 'about-us' ? 'active' : '' }}">
                    <a href="{{ route('front.about-us') }}">About Us</a>
                </li>
                <li class="@if(Request::path()=='product-grids'||Request::path()=='product-lists') active @endif">
                    <a href="{{ route('front.product-grids') }}">Products</a>
                </li>
                <li class="{{ Request::path() == 'bundles' ? 'active' : '' }}">
                    <a href="{{ route('front.bundles') }}">Bundles</a>
                </li>
                <li class="{{ Request::path() == 'blog' ? 'active' : '' }}">
                    <a href="{{ route('front.blog') }}">Blog</a>
                </li>
                <li class="{{ Request::path() == 'contact' ? 'active' : '' }}">
                    <a href="{{ route('front.contact') }}">Contact Us</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
