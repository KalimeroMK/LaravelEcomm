@extends('admin::layouts.master')
@section('title','E-SHOP || Newsletter Page')
@section('content')
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="row">
            <div class="col-md-12">
                @include('notification::notification')
            </div>
        </div>
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary float-left">Newsletter List</h6>
            <a href="{{route('newsletters.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip"
               data-placement="bottom" title="Add newsletter"><i class="fas fa-plus"></i> Add newsletter</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                @if(isset($newsletters))
                    <table class="table table-bordered" id="data-table" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>S.N.</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>S.N.</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        @foreach($newsletters as $newsletter)
                            <tr>
                                <td>{{$newsletter->id}}</td>
                                <td>{{$newsletter->email}}</td>

                                <td>
                                    @if($newsletter->is_validated ==1)
                                        <span class="badge badge-success">Validated</span>
                                    @else
                                        <span class="badge badge-warning">Not validated</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{route('newsletters.edit',$newsletter->id)}}"
                                       class="btn btn-primary btn-sm float-left mr-1"
                                       style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip"
                                       title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>
                                    <form method="POST" action="{{route('newsletters.destroy',$newsletter->id)}}">
                                        @csrf
                                        @method('delete')
                                        <button class="btn btn-danger btn-sm dltBtn"
                                                data-id={{$newsletter->id}} style="height:30px;
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
                    <h6 class="text-center">No brands found!!! Please create brand</h6>
                @endif
            </div>
        </div>
    </div>
@endsection


