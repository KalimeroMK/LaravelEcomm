@php use Modules\Core\Helpers\Helper; @endphp
<header class="header shop">
    <!-- Topbar -->
    <div class="topbar">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-12 col-12">
                    <!-- Top Left -->
                    <div class="top-left">
                        <ul class="list-main">
                            @foreach($settings as $data)
                                <li><i class="ti-headphone-alt"></i>{{$data->phone}}</li>
                                <li><i class="ti-email"></i> {{$data->email}} </li>
                            @endforeach
                        </ul>
                    </div>
                    <!--/ End Top Left -->
                </div>
                <div class="col-lg-6 col-md-12 col-12">
                    <!-- Top Right -->
                    <div class="right-content">
                        <ul class="list-main">
                            <li><i class="ti-alarm-clock"></i> <a href="{{ route('front.product-deal') }}">Daily
                                    deal</a></li>
                            @auth
                                @if(Auth::user()->hasRole('super-admin'))
                                    <li><i class="ti-user"></i> <a href="{{route('admin')}}"
                                                                   target="_blank">Dashboard</a></li>
                                @else
                                    <li><i class="ti-user"></i> <a href="{{route('user')}}"
                                                                   target="_blank">Dashboard</a></li>
                                @endif
                                <li><i class="ti-power-off"></i> <a href="{{route('logout')}}">Logout</a></li>

                            @else
                                <li><i class="ti-power-off"></i><a href="{{route('login')}}">Login /</a> <a
                                            href="{{route('register')}}">Register</a></li>
                            @endauth
                        </ul>
                    </div>
                    <!-- End Top Right -->
                </div>
            </div>
        </div>
    </div>
    <!-- End Topbar -->
    <div class="middle-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-2 col-md-2 col-12">
                    <!-- Logo -->
                    <div class="logo">
                        <a href="{{route('front.index')}}"><img
                                    src="@foreach($settings as $data) {{$data->logo}} @endforeach"
                                    alt="logo"></a>
                    </div>
                    <!--/ End Logo -->
                    <!-- Search Form -->
                    <div class="search-top">
                        <div class="top-search"><a href="#0"><i class="ti-search"></i></a></div>
                        <!-- Search Form -->
                        <div class="search-top">
                            <form class="search-form">
                                <input type="text" placeholder="Search here..." name="search">
                                <button value="search" type="submit"><i class="ti-search"></i></button>
                            </form>
                        </div>
                        <!--/ End Search Form -->
                    </div>
                    <!--/ End Search Form -->
                    <div class="mobile-nav"></div>
                </div>
                <div class="col-lg-8 col-md-7 col-12">
                    <div class="search-bar-top">
                        <div class="search-bar">
                            <form method="POST" action="{{route('front.product-search')}}">
                                @csrf
                                <input name="search" placeholder="Search Products Here....." type="search">
                                <button class="btnn" type="submit"><i class="ti-search"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-12">
                    <div class="right-bar float-right">
                        <!-- Search Form -->
                        <div class="sinlge-bar shopping">
                            @php
                                $total_prod=0;
                                $total_amount=0;
                            @endphp
                            @if(session('wishlist'))
                                @foreach(session('wishlist') as $wishlist_items)
                                    @php
                                        $total_prod+=$wishlist_items['quantity'];
                                        $total_amount+=$wishlist_items['amount'];
                                    @endphp
                                @endforeach
                            @endif
                            <a href="{{route('wishlist')}}" class="single-icon"><i class="fa fa-heart-o"></i> <span
                                        class="total-count">{{Modules\Core\Helpers\Helper::wishlistCount()}}</span></a>
                            <!-- Shopping Item -->
                            @auth
                                <div class="shopping-item">
                                    <div class="dropdown-cart-header">
                                        <span>{{count(Helper::getAllProductFromWishlist())}} Items</span>
                                        <a href="{{route('wishlist')}}">View Wishlist</a>
                                    </div>
                                    <ul class="shopping-list">
                                        @foreach(Helper::getAllProductFromWishlist() as $data)
                                            @php
                                                $photo=explode(',',$data->product['photo']);
                                            @endphp
                                            <li>
                                                <a href="{{route('wishlist-delete',$data->id)}}" class="remove"
                                                   title="Remove this item"><i class="fa fa-remove"></i></a>
                                                <a class="cart-img" href="#"><img src="{{$photo[0]}}"
                                                                                  alt="{{$photo[0]}}"></a>
                                                <h4><a href="{{route('front.product-detail',$data->product['slug'])}}"
                                                       target="_blank">{{$data->product['title']}}</a></h4>
                                                <p class="quantity">{{$data->quantity}} x - <span
                                                            class="amount">${{number_format($data->price,2)}}</span></p>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <div class="bottom">
                                        <div class="total">
                                            <span>Total</span>
                                            <span
                                                    class="total-amount">${{number_format(Helper::totalWishlistPrice(),2)}}</span>
                                        </div>
                                        <a href="{{route('cart-list')}}" class="btn animate">Cart</a>
                                    </div>
                                </div>
                            @endauth
                            <!--/ End Shopping Item -->
                        </div>
                        <div class="sinlge-bar shopping">
                            <a href="{{route('cart-list')}}" class="single-icon"><i class="ti-bag"></i> <span
                                        class="total-count">{{Helper::cartCount()}}</span></a>
                            <!-- Shopping Item -->
                            @auth
                                <div class="shopping-item">
                                    <div class="dropdown-cart-header">
                                        <span>{{count(Helper::getAllProductFromCart())}} Items</span>
                                        <a href="{{route('cart-list')}}">View Cart</a>
                                    </div>
                                    <ul class="shopping-list">
                                        @foreach(Helper::getAllProductFromCart() as $data)
                                            @php
                                                $photo=explode(',',$data->product['photo']);
                                            @endphp
                                            <li>
                                                <a href="{{route('cart-delete',$data->id)}}" class="remove"
                                                   title="Remove this item"><i class="fa fa-remove"></i></a>
                                                <a class="cart-img" href="#"><img src="{{$photo[0]}}"
                                                                                  alt="{{$photo[0]}}"></a>
                                                <h4><a href="{{route('front.product-detail',$data->product['slug'])}}"
                                                       target="_blank">{{$data->product['title']}}</a></h4>
                                                <p class="quantity">{{$data->quantity}} x - <span
                                                            class="amount">${{number_format($data->price,2)}}</span></p>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <div class="bottom">
                                        <div class="total">
                                            <span>Total</span>
                                            <span
                                                    class="total-amount">${{number_format(Helper::totalCartPrice(),2)}}</span>
                                        </div>
                                        <a href="{{route('checkout')}}" class="btn animate">Checkout</a>
                                    </div>
                                </div>
                            @endauth
                            <!--/ End Shopping Item -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Header Inner -->
    <div class="header-inner">
        <div class="container">
            <div class="cat-nav-head">
                <div class="row">
                    @if (Request::path() == '/')
                        <div class="all-category">
                            <h3 class="cat-heading"><i class="fa fa-bars" aria-hidden="true"></i>CATEGORIES</h3>
                            <ul class="main-category">
                                @foreach ($categories as $category)
                                    <li>
                                        <a href="{{ route('front.product-cat', $category->slug) }}">{{ $category->title }}
                                            @if($category->childrenCategories->isNotEmpty())
                                                <i class="fa fa-angle-right" aria-hidden="true"></i>
                                            @endif
                                        </a>
                                        @if($category->childrenCategories->isNotEmpty())
                                            <ul class="sub-category">
                                                @foreach ($category->childrenCategories as $childCategory)
                                                    @include('front::layouts.child_category', ['child_category' => $childCategory])
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>


                        <div class="col-lg-9 col-12">
                            <div class="menu-area">
                                <!-- Main Menu -->
                                @include('front::layouts.menu')
                                <!--/ End Main Menu -->
                            </div>
                        </div>
                    @else
                        <div class="col-12">
                            <div class="menu-area">
                                <!-- Main Menu -->
                                @include('front::layouts.menu')
                                <!--/ End Main Menu -->
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!--/ End Header Inner -->
</header>