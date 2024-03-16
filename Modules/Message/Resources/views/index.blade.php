@extends('admin::layouts.master')
@section('content')
    <div class="card">
        <div class="row">
            <div class="col-md-12">
                @include('notification::notification')

            </div>
        </div>
        <h5 class="card-header">Messages</h5>
        <div class="card-body">
            @if(count($messages)>0)
                <table class="table message-table" id="message-dataTable">
                    <thead>
                    <tr>
                        <th scope="col">@lang('partials.s_n')</th>
                        <th scope="col">@lang('partials.name')</th>
                        <th scope="col">@lang('partials.subject')</th>
                        <th scope="col">@lang('partials.name')</th>
                        <th scope="col">@lang('partials.active')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ( $messages as $message)

                        <tr class="@if($message->read_at) border-left-success @else bg-light border-left-warning @endif">
                            <td scope="row">{{$loop->index +1}}</td>
                            <td>{{$message->name}} {{$message->read_at}}</td>
                            <td>{{$message->subject}}</td>
                            <td>{{$message->created_at->format('F d, Y h:i A')}}</td>
                            <td>
                                <a href="{{route('message.show',$message->id)}}"
                                   class="btn btn-primary btn-sm float-left mr-1"
                                   style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" title="view"
                                   data-placement="bottom"><i class="fas fa-eye"></i></a>
                                <form method="POST" action="{{route('message.destroy',[$message->id])}}">
                                    @csrf
                                    @method('delete')
                                    <button class="btn btn-danger btn-sm dltBtn"
                                            data-id="{{$message->id}}" style="height:30px; width:30px;border-radius:50%
                                    " data-toggle="tooltip" data-placement="bottom" title="Delete"><i
                                                class="fas fa-trash-alt"></i></button>
                                </form>
                            </td>
                        </tr>

                    @endforeach
                    </tbody>
                </table>
            @else
                <h2>@lang('partials.no_records_found')</h2>
            @endif
        </div>
    </div>
@endsection
