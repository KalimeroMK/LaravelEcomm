@extends('admin::layouts.master')
@section('title','E-SHOP || Brand Page')
@section('content')
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary float-left">Brand List</h6>
            <a href="{{route('bundles.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip"
               data-placement="bottom" title="Add User"><i class="fas fa-plus"></i> Add bundle</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                @if(count($bundles)>0)
                    <table class="table table-bordered" id="data-table">
                        <thead>
                        <tr>
                            <th>S.N.</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Product</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>S.N.</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Product</th>
                            <th>Action</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        @foreach($bundles as $bundle)
                            <tr>
                                <td>{{$bundle->id}}</td>
                                <td>{{$bundle->name}}</td>
                                <td>{{$bundle->description}}</td>
                                <td>{{$bundle->price}}</td>
                                <td>@foreach($bundle->products as $product)
                                        {{ $product->title }},
                                    @endforeach</td>
                                <td>
                                    <a href="{{route('bundles.edit',$bundle->id)}}"
                                       class="btn btn-primary btn-sm float-left mr-1"
                                       style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip"
                                       title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>
                                    <form method="POST" action="{{route('bundles.destroy',$bundle->id)}}">
                                        @csrf
                                        @method('delete')
                                        <button class="btn btn-danger btn-sm dltBtn"
                                                data-id="{{$bundle->id}}" style="height:30px; width:30px;border-radius:50%
                                        " data-toggle="tooltip" data-placement="bottom" title="Delete"><i
                                                    class="fas fa-trash-alt"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <h6 class="text-center">No bundle found!!! Please create bundle</h6>
                @endif
            </div>
        </div>
    </div>
@endsection


