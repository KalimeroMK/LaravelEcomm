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
            <h6 class="m-0 font-weight-bold text-primary float-left">@lang('partials.list')</h6>
            <a href="{{route('users.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip"
               data-placement="bottom" title="Add User"><i class="fas fa-plus"></i>@lang('partials.create')</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                @if(isset($users))
                    <table class="table table-bordered" id="data-table" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>@lang('partials.s_n')</th>
                            <th>@lang('partials.name')</th>
                            <th>@lang('partials.email')</th>
                            <th>@lang('partials.status')</th>
                            <th>@lang('partials.date')</th>
                            <th>@lang('partials.action')</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>@lang('partials.s_n')</th>
                            <th>@lang('partials.name')</th>
                            <th>@lang('partials.email')</th>
                            <th>@lang('partials.status')</th>
                            <th>@lang('partials.date')</th>
                            <th>@lang('partials.action')</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $user['id'] ?? '' }}</td>
                                <td>{{ $user['name'] ?? '' }}</td>
                                <td>{{ $user['email'] ?? '' }}</td>
                                <td>{{ $user['status'] ?? '' }}</td>
                                <td>{{ $user['created_at'] ?? '' }}</td>
                                <td>
                                    <a href="{{ route('users.edit', $user['id']) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('users.destroy', $user['id']) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">@lang('partials.no_records_found')</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                @else
                    <h6 class="text-center">@lang('partials.no_records_found')</h6>
                @endif
            </div>
        </div>
    </div>
@endsection
