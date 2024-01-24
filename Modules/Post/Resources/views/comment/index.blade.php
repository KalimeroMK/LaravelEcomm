@extends('admin::layouts.master')
@section('title','E-SHOP || Comment Page')
@section('content')
    <!-- DataTales Example -->
    <div class="card shadow mb-4">

        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary float-left">Comment Lists</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                @if(count($comments)>0)
                    <table class="table table-bordered" id="data-table">
                        <thead>
                        <tr>
                            <th>S.N.</th>
                            <th>Author</th>
                            <th>Post Title</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>S.N.</th>
                            <th>Author</th>
                            <th>Post Title</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        @foreach($comments as $comment)

                            <tr>
                                <td>{{$comment->id}}</td>
                                <td>{{$comment->user_info['name']}}</td>
                                <td>{{$comment->post['title'] ?? ''}}</td>
                                <td>{{$comment->comment ??''}}</td>
                                <td>{{$comment->created_at->format('M d D, Y g: i a')}}</td>
                                <td>
                                    @if($comment->status=='active')
                                        <span class="badge badge-success">{{$comment->status}}</span>
                                    @else
                                        <span class="badge badge-warning">{{$comment->status}}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{route('comment.edit',$comment->id)}}"
                                       class="btn btn-primary btn-sm float-left mr-1"
                                       style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip"
                                       title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>
                                    <form method="POST" action="{{route('comment.destroy',$comment->id)}}">
                                        @csrf
                                        @method('delete')
                                        <button class="btn btn-danger btn-sm dltBtn"
                                                data-id="{{$comment->id}}" style="height:30px; width:30px;border-radius:50%
                                        " data-toggle="tooltip" data-placement="bottom" title="Delete"><i
                                                    class="fas fa-trash-alt"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <h6 class="text-center">No post comments found!!!</h6>
                @endif
            </div>
        </div>
    </div>
@endsection

