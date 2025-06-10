@extends('admin::layouts.master')
@section('title','Product Stats')
@section('content')
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary float-left">Product Stats</h6>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <form method="GET" action="">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <label for="from">From</label>
                            <input type="date" id="from" name="from" class="form-control" value="{{ $from ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <label for="to">To</label>
                            <input type="date" id="to" name="to" class="form-control" value="{{ $to ?? '' }}">
                        </div>
                        <div class="col-md-4">
                            <label for="category_id">Category</label>
                            <select id="category_id" name="category_id" class="form-control">
                                <option value="">-- All Categories --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" @if(isset($categoryId) && $categoryId == $cat->id) selected @endif>{{ $cat->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                @if(count($statsListDto) > 0)
                    <table class="table table-bordered" id="data-table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product</th>
                            <th>Impressions</th>
                            <th>Clicks</th>
                            <th>CTR (%)</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>ID</th>
                            <th>Product</th>
                            <th>Impressions</th>
                            <th>Clicks</th>
                            <th>CTR (%)</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        @foreach($statsListDto as $stat)
                            <tr>
                                <td>{{ $stat->product->id }}</td>
                                <td>
                                    <a href="{{ route('product-stats.detail', $stat->product->id) }}">{{ $stat->product->title }}</a>
                                </td>
                                <td>{{ $stat->impressions }}</td>
                                <td>{{ $stat->clicks }}</td>
                                <td>{{ $stat->ctr * 100 }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <h6 class="text-center">No records found</h6>
                @endif
            </div>
        </div>
    </div>
@endsection
