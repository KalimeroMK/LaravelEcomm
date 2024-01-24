@extends('admin::layouts.master')

@section('content')
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="row">
            <div class="col-md-12">
                @include('notification::notification')

            </div>
        </div>
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary float-left">Shipping List</h6>
            <a href="{{route('shipping.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip"
               data-placement="bottom" title="Add User"><i class="fas fa-plus"></i> Add Shipping</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                @if(count($shippings)>0)
                    <table class="table table-bordered" id="data-table">
                        <thead>
                        <tr>
                            <th>S.N.</th>
                            <th>Title</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>S.N.</th>
                            <th>Title</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        @foreach($shippings as $shipping)
                            <tr>
                                <td>{{$shipping->id}}</td>
                                <td>{{$shipping->type}}</td>
                                <td>${{$shipping->price}}</td>
                                <td>
                                    @if($shipping->status=='active')
                                        <span class="badge badge-success">{{$shipping->status}}</span>
                                    @else
                                        <span class="badge badge-warning">{{$shipping->status}}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{route('shipping.edit',$shipping->id)}}"
                                       class="btn btn-primary btn-sm float-left mr-1"
                                       style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip"
                                       title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>
                                    <form method="POST" action="{{route('shipping.destroy',[$shipping->id])}}">
                                        @csrf
                                        @method('delete')
                                        <button class="btn btn-danger btn-sm dltBtn"
                                                data-id="{{$shipping->id}}" style="height:30px;
                                                width:30px;border-radius:50%
                                        " data-toggle="tooltip" data-placement="bottom" title="Delete"><i
                                                    class="fas fa-trash-alt"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <h6 class="text-center">No shippings found!!! Please create shipping</h6>
                @endif
            </div>
        </div>
    </div>
@endsection
