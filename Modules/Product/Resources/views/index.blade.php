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
            <h6 class="m-0 font-weight-bold text-primary float-left">Product Lists</h6>
            <a href="{{route('products.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip"
               data-placement="bottom" title="Add User"><i class="fas fa-plus"></i> Add Product</a><br><br>
            <form action="{{ route('product.import') }}" method="POST" enctype="multipart/form-data"
                  class="col-3 float-right">
                @csrf
                <input type="file" name="file" class="form-control">
                <br>
                <button class="btn btn-primary btn-sm">Import product data</button>
                <a href="{{route('product.export')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip"
                   data-placement="bottom" title="Export posts"><i class="fas fa-plus"></i> Export posts</a>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                @if(isset($products))
                    <table class="table table-bordered" id="product-dataTable" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>S.N.</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Is Featured</th>
                            <th>Price</th>
                            <th>Discount</th>
                            <th>Size</th>
                            <th>Condition</th>
                            <th>Brand</th>
                            <th>Stock</th>
                            <th>Photo</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>S.N.</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Is Featured</th>
                            <th>Price</th>
                            <th>Discount</th>
                            <th>Size</th>
                            <th>Condition</th>
                            <th>Brand</th>
                            <th>Stock</th>
                            <th>Photo</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </tfoot>
                        <tbody>

                        @foreach($products as $product)
                            <tr>
                                <td>{{$product->id}}</td>
                                <td>{{$product->title}}</td>
                                <td>
                                    @foreach($product->categories as $category)
                                        {{$category->title}}
                                    @endforeach
                                </td>
                                <td>{{(($product->is_featured==1)? 'Yes': 'No')}}</td>
                                <td>Rs. {{$product->price}} /-</td>
                                <td>  {{$product->discount}}% OFF</td>
                                <td> @foreach($product->sizes as $size)
                                        {{$size->name}},
                                    @endforeach</td>
                                <td>{{$product->condition->status}}</td>
                                <td>{{ $product->brand->title ?? ''}}</td>
                                <td>
                                    @if($product->stock>0)
                                        <span class="badge badge-primary">{{$product->stock}}</span>
                                    @else
                                        <span class="badge badge-danger">{{$product->stock}}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($product->photo)
                                        @php
                                            $photo=explode(',',$product->photo);
                                        @endphp
                                        <img src="{{$photo[0]}}" class="img-fluid zoom" style="max-width:80px"
                                             alt="{{$product->photo}}">
                                    @else
                                        <img src="{{asset('backend/img/thumbnail-default.jpg')}}" class="img-fluid"
                                             style="max-width:80px" alt="avatar.png">
                                    @endif
                                </td>
                                <td>
                                    @if($product->status=='active')
                                        <span class="badge badge-success">{{$product->status}}</span>
                                    @else
                                        <span class="badge badge-warning">{{$product->status}}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{route('products.edit',$product->id)}}"
                                       class="btn btn-primary btn-sm float-left mr-1"
                                       style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip"
                                       title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>
                                    <form method="POST" action="{{route('products.destroy',[$product->id])}}">
                                        @csrf
                                        @method('delete')
                                        <button class="btn btn-danger btn-sm dltBtn"
                                                data-id={{$product->id}} style="height:30px;
                                                width:30px;border-radius:50%
                                        " data-toggle="tooltip" data-placement="bottom" title="Delete"><i
                                                class="fas fa-trash-alt"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <span style="float:right">{{$products->links('vendor.pagination.bootstrap-4')}}</span>
                @else
                    <h6 class="text-center">No Products found!!! Please create Product</h6>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css"/>
    <style>
        div.dataTables_wrapper div.dataTables_paginate {
            display: none;
        }

        .zoom {
            transition: transform .2s; /* Animation */
        }

        .zoom:hover {
            transform: scale(5);
        }
    </style>
@endpush

@push('scripts')

    <!-- Page level plugins -->


    <!-- Page level custom scripts -->
    <script>

        $('#product-dataTable').DataTable({
            "scrollX": false,
            "columnDefs": [
                {
                    "orderable": false,
                    "targets": [10, 11, 12]
                }
            ]
        });

        // Sweet alert

        function deleteData(id) {

        }
    </script>
    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('.dltBtn').click(function (e) {
                const form = $(this).closest('form');
                const dataID = $(this).data('id');
                // alert(dataID);
                e.preventDefault();
                swal({
                    title: "Are you sure?",
                    text: "Once deleted, you will not be able to recover this data!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                    .then((willDelete) => {
                        if (willDelete) {
                            form.submit();
                        } else {
                            swal("Your data is safe!");
                        }
                    });
            })
        })
    </script>
@endpush
