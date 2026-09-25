@php use Modules\Core\Helpers\Helper; @endphp
@extends($themePath . '.layouts.master')
@section('title','Wishlist Page')
@section('content')
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="{{ route('front.index') }}">Home<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="javascript:void(0);">Wishlist</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <!-- Shopping Cart -->
    <div class="shopping-cart section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <!-- Shopping Summery -->
                    <table class="table shopping-summery">
                        <thead>
                        <tr class="main-hading">
                            <th>@lang('partials.product')</th>
                            <th>@lang('partials.name')</th>
                            <th class="text-center">TOTAL</th>
                            <th class="text-center">ADD TO CART</th>
                            <th class="text-center"><i class="ti-trash remove-icon"></i></th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(Helper::getAllProductFromWishlist())
                            @foreach(Helper::getAllProductFromWishlist() as $key=>$wishlist)
                                <tr>
                                    @php
                                        $photo=explode(',',$wishlist->product['photo']);
                                    @endphp
                                    <td class="image" data-title="No"><img src="{{$photo[0]}}" alt="{{$photo[0]}}"></td>
                                    <td class="product-des" data-title="Description">
                                        <p class="product-name"><a
                                                    href="{{route('front.product-detail',$wishlist->product['slug'])
                                                    }}">{{$wishlist->product['title']}}</a>
                                        </p>
                                        <p class="product-des">{!!($wishlist['summary']) !!}</p>
                                    </td>
                                    <td class="total-amount" data-title="Total"><span>${{$wishlist['amount']}}</span>
                                    </td>
                                    <td><a href="{{route('add-to-cart',$wishlist->product['slug'])}}"
                                           class='btn text-white'>Add To Cart</a></td>
                                    <td class="action" data-title="Remove"><a
                                                href="{{route('wishlist-delete',$wishlist->id)}}"><i
                                                    class="ti-trash remove-icon"></i></a></td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td class="text-center">
                                    There are no any wishlist available. <a href="{{route('front.product-grids')}}"
                                                                            style="color:blue;">Continue shopping</a>

                                </td>
                            </tr>
                        @endif


                        </tbody>
                    </table>
                    <!--/ End Shopping Summery -->
                </div>
            </div>
        </div>
    </div>
    <!--/ End Shopping Cart -->

    <!-- Start Shop Services Area  -->
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
    <!-- End Shop Newsletter -->

    @include($themePath . '.layouts.newsletter')




    <!-- Modal end -->

@endsection
@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
@endpush
