@extends('admin::layouts.master')

@section('title','Order Detail')

@section('content')
    <div class="card">
        <h5 class="card-header">@lang('sidebar.orders') <a href="{{route('order.pdf',$order->id)}}"
                                                           class=" btn btn-sm btn-primary shadow-sm float-right"><i
                    class="fas fa-download fa-sm text-white-50"></i> @lang('partials.pdf')</a>
        </h5>
        <div class="card-body">
            @if($order)
                <table class="table table-striped table-hover">

                    <thead>
                    <tr>
                        <th>@lang('partials.s_n')</th>
                        <th>@lang('partials.order_no')</th>
                        <th>@lang('partials.name')</th>
                        <th>@lang('partials.edit')</th>
                        <th>@lang('partials.quantity')</th>
                        <th>@lang('partials.charge')</th>
                        <th>@lang('partials.total_amount')</th>
                        <th>@lang('partials.status')</th>
                        <th>@lang('partials.action')</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>{{$order->id}}</td>
                        <td>{{$order->order_number}}</td>
                        <td>{{$order->first_name}} {{$order->last_name}}</td>
                        <td>{{$order->email}}</td>
                        <td>{{$order->quantity}}</td>
                        <td>@foreach($order->shipping as $data)
                                $ {{number_format($data,2)}}
                            @endforeach</td>
                        <td>${{number_format($order->total_amount,2)}}</td>
                        <td>
                            @if($order->status=='new')
                                <span class="badge badge-primary">{{$order->status}}</span>
                            @elseif($order->status=='process')
                                <span class="badge badge-warning">{{$order->status}}</span>
                            @elseif($order->status=='delivered')
                                <span class="badge badge-success">{{$order->status}}</span>
                            @else
                                <span class="badge badge-danger">{{$order->status}}</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{route('orders.edit',$order->id)}}" class="btn btn-primary btn-sm float-left mr-1"
                               style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" title="edit"
                               data-placement="bottom"><i class="fas fa-edit"></i></a>
                            <form method="POST" action="{{route('orders.destroy',[$order->id])}}">
                                @csrf
                                @method('delete')
                                <button class="btn btn-danger btn-sm dltBtn" data-id={{$order->id}} style="height:30px;
                                        width:30px;border-radius:50%
                                " data-toggle="tooltip" data-placement="bottom" title="Delete"><i
                                    class="fas fa-trash-alt"></i></button>
                            </form>
                        </td>

                    </tr>
                    </tbody>
                </table>

                <section class="confirmation_part section_padding">
                    <div class="order_boxes">
                        <div class="row">
                            <div class="col-lg-6 col-lx-4">
                                <div class="order-info">
                                    <h4 class="text-center pb-4">@lang('partials.order_info')</h4>
                                    <table class="table">
                                        <tr class="">
                                            <td>Order Number</td>
                                            <td> : {{$order->order_number}}</td>
                                        </tr>
                                        <tr>
                                            <td>Order Date</td>
                                            <td> : {{$order->created_at->format('D d M, Y')}}
                                                at {{$order->created_at->format('g : i a')}} </td>
                                        </tr>
                                        <tr>
                                            <td>Quantity</td>
                                            <td> : {{$order->quantity}}</td>
                                        </tr>
                                        <tr>
                                            <td>Order Status</td>
                                            <td> : {{$order->status}}</td>
                                        </tr>
                                        <tr>

                                            <td>Shipping Charge</td>
                                            <td>@foreach($order->shipping as $data)
                                                    $ {{number_format($data,2)}}
                                                @endforeach</td>
                                        </tr>
                                        <tr>
                                            <td>Coupon</td>
                                            <td> : $ {{number_format($order->coupon,2)}}</td>
                                        </tr>
                                        <tr>
                                            <td>Total Amount</td>
                                            <td> : $ {{number_format($order->total_amount,2)}}</td>
                                        </tr>
                                        <tr>
                                            <td>Payment Method</td>
                                            <td> : @if($order->payment_method=='cod')
                                                    Cash on Delivery
                                                @else
                                                    Paypal
                                                @endif</td>
                                        </tr>
                                        <tr>
                                            <td>Payment Status</td>
                                            <td> : {{$order->payment_status}}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="col-lg-6 col-lx-4">
                                <div class="shipping-info">
                                    <h4 class="text-center pb-4">@lang('partials.shipping_info')/h4>
                                        <table class="table">
                                            <tr class="">
                                                <td>Full Name</td>
                                                <td> : {{$order->first_name}} {{$order->last_name}}</td>
                                            </tr>
                                            <tr>
                                                <td>@lang('partials.email')</td>
                                                <td> : {{$order->email}}</td>
                                            </tr>
                                            <tr>
                                                <td>@lang('partials.Phone')</td>
                                                <td> : {{$order->phone}}</td>
                                            </tr>
                                            <tr>
                                                <td>@lang('messages.address')</td>
                                                <td> : {{$order->address1}}, {{$order->address2}}</td>
                                            </tr>
                                            <tr>
                                                <td>@lang('messages.country')</td>
                                                <td> : {{$order->country}}</td>
                                            </tr>
                                            <tr>
                                                <td>@lang('partials.code')</td>
                                                <td> : {{$order->post_code}}</td>
                                            </tr>
                                        </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            @endif

        </div>
    </div>
@endsection

@push('styles')
    <style>
        .order-info, .shipping-info {
            background: #ECECEC;
            padding: 20px;
        }

        .order-info h4, .shipping-info h4 {
            text-decoration: underline;
        }

    </style>
@endpush
