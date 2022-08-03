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
            <h6 class="m-0 font-weight-bold text-primary float-left">User Lists</h6>
            <a href="{{route('users.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip"
               data-placement="bottom" title="Add User"><i class="fas fa-plus"></i> Add user</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                @if(isset($users))
                    <table class="table table-bordered" id="product-dataTable" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>S.N.</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Photo</th>
                            <th>Join Date</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Impersonate</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>S.N.</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Photo</th>
                            <th>Join Date</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Impersonate</th>
                            <th>Action</th>
                        </tr>
                        </tfoot>
                        <tbody>

                        @foreach($users as $user)
                            <tr>
                                <td>{{$user->id}}</td>
                                <td>{{$user->name}}</td>
                                <td>{{$user->email}}</td>
                                <td>
                                    @if($user->photo)
                                        <img src="{{$user->photo}}" class="img-fluid rounded-circle"
                                             style="max-width:50px"
                                             alt="{{$user->photo}}">
                                    @else
                                        <img src="{{asset('backend/img/avatar.png')}}" class="img-fluid rounded-circle"
                                             style="max-width:50px" alt="avatar.png">
                                    @endif
                                </td>
                                <td>{{(($user->created_at)? $user->created_at->diffForHumans() : '')}}</td>
                                <td>
                                    @if(!empty($user->getRoleNames()))
                                        @foreach($user->getRoleNames() as $v)
                                            <label class="badge badge-success">{{ $v }}</label>
                                        @endforeach
                                    @endif
                                </td>
                                <td>
                                    @if($user->status=='active')
                                        <span class="badge badge-success">{{$user->status}}</span>
                                    @else
                                        <span class="badge badge-warning">{{$user->status}}</span>
                                    @endif
                                </td>
                                @role('super-admin')
                                <td>
                                    @if($user->id != auth()->id())
                                        <a href="{{ route('users.impersonate', $user->id) }}"
                                           class="btn btn-warning btn-sm">Impersonate</a>
                                    @endif
                                </td>
                                @endrole
                                <td>
                                    <a href="{{route('users.edit',$user->id)}}"
                                       class="btn btn-primary btn-sm float-left mr-1"
                                       style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip"
                                       title="edit"
                                       data-placement="bottom"><i class="fas fa-edit"></i></a>
                                    <form method="POST" action="{{route('users.destroy',[$user->id])}}">
                                        @csrf
                                        @method('delete')
                                        <button class="btn btn-danger btn-sm dltBtn"
                                                data-id={{$user->id}} style="height:30px; data-toggle="tooltip"
                                                data-placement="bottom" title="Delete"><i
                                                    class="fas fa-trash-alt"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach                        </tbody>
                    </table>
                    <span style="float:right">{{$users->links('vendor.pagination.bootstrap-4')}}</span>
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
