@extends('admin::layouts.master')

@section('content')
    <!-- DataTales Example -->
    <div class="card shadow mb-4">

        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary float-left">Review Lists</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                @if(isset($reviews))
                    <table class="table table-bordered" id="data-table">
                        <thead>
                        <tr>
                            <th>S.N.</th>
                            <th>Review By</th>
                            <th>Product Title</th>
                            <th>Review</th>
                            <th>Rate</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>S.N.</th>
                            <th>Review By</th>
                            <th>Product Title</th>
                            <th>Review</th>
                            <th>Rate</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        @foreach($reviews as $review)

                            <tr>
                                <td>{{$review->id}}</td>
                                <td>{{$review->user->name ?? ''}}</td>
                                <td>{{$review->product->title ?? ''}}</td>
                                <td>{{$review->review}}</td>
                                <td>
                                    <ul style="list-style:none">
                                        @for($i=1; $i<=5;$i++)
                                            @if($review->rate >=$i)
                                                <li style="float:left;color:#F7941D;"><i class="fa fa-star"></i></li>
                                            @else
                                                <li style="float:left;color:#F7941D;"><i class="far fa-star"></i></li>
                                            @endif
                                        @endfor
                                    </ul>
                                </td>
                                <td>{{$review->created_at->format('M d D, Y g: i a')}}</td>
                                <td>
                                    @if($review->status=='active')
                                        <span class="badge badge-success">{{$review->status}}</span>
                                    @else
                                        <span class="badge badge-warning">{{$review->status}}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{route('product_review.edit',$review->id)}}"
                                       class="btn btn-primary btn-sm float-left mr-1"
                                       style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip"
                                       title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>
                                    <form method="POST" action="{{route('product_review.destroy',$review->id)}}">
                                        @csrf
                                        @method('delete')
                                        <button class="btn btn-danger btn-sm dltBtn"
                                                data-id="{{$review->id}}" style="height:30px; width:30px;border-radius:50%
                                        " data-toggle="tooltip" data-placement="bottom" title="Delete"><i
                                                    class="fas fa-trash-alt"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <h6 class="text-center">No reviews found!!!</h6>
                @endif
            </div>
        </div>
    </div>
@endsection

