@extends('admin::layouts.master')
@section('title', 'E-SHOP || Complaint Detail')
@section('content')

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">@lang('partials.complaint_detail')</h6>
        </div>
        <div class="card-body">
            {{-- Complaint Detail Form --}}
            <form class="form-horizontal" method="POST"
                  action="{{ route($complaint->exists ? 'complaints.update' : 'complaints.store', $complaint->exists ? $complaint->id : null) }}"
                  enctype="multipart/form-data">
                @csrf
                @if($complaint->exists)
                    @method('put')
                @endif

                {{-- Order ID --}}
                <div class="form-group">
                    <label for="inputOrderID" class="col-form-label">@lang('partials.order_id') <span
                                class="text-danger">*</span></label>
                    <input id="inputOrderID" type="text" name="order_id" placeholder="@lang('partials.order_id')"
                           value="{{ $complaint->order_id ?? null }}" class="form-control">
                </div>

                {{-- Description --}}
                <div class="form-group">
                    <label for="inputDescription" class="col-form-label">@lang('partials.description') <span
                                class="text-danger">*</span></label>
                    <textarea id="inputDescription" name="description" placeholder="@lang('partials.description')"
                              class="form-control">{{ $complaint->description ?? null }}</textarea>
                </div>

                {{-- Status (Open/Closed) --}}
                <div class="form-group">
                    <label for="status" class="col-form-label">@lang('partials.status') <span
                                class="text-danger">*</span></label>
                    <select name="status" class="form-control">
                        <option @if($complaint->status =="open") selected
                                @endif value="open">@lang('partials.open')</option>
                        <option @if($complaint->status =="closed") selected
                                @endif value="closed">@lang('partials.closed')</option>
                    </select>
                </div>

                {{-- Reply Section (Visible on Edit) --}}
                @if($complaint->exists)
                    <div class="form-group">
                        <label for="reply" class="col-form-label">@lang('partials.reply')</label>
                        <textarea id="reply" name="reply_content" placeholder="@lang('partials.reply_placeholder')"
                                  class="form-control">{{ old('reply_content') }}</textarea>
                    </div>
                @endif

                {{-- Buttons --}}
                <div class="button-container">
                    <button type="reset" class="btn btn-warning">@lang('partials.reset')</button>
                    <button class="btn btn-success" type="submit">@lang('partials.submit')</button>
                </div>
            </form>

            {{-- Replies List --}}
            @if($complaint->complaint_replies->count() > 0)
                <hr>
                <h5 class="font-weight-bold">@lang('partials.replies')</h5>
                <ul class="list-group">
                    @foreach($complaint->complaint_replies as $reply)
                        <li class="list-group-item">
                            <strong>@lang('partials.user'):</strong> {{ $reply->user->name }}<br>
                            <strong>@lang('partials.date'):</strong> {{ $reply->created_at->format('d M, Y H:i') }}<br>
                            <strong>@lang('partials.reply_content'):</strong> {{ $reply->reply_content }}
                        </li>
                    @endforeach
                </ul>
            @else
                <p>@lang('partials.no_replies')</p>
            @endif

            {{-- Add New Reply Form --}}
            <hr>
            <h5 class="font-weight-bold">@lang('partials.add_reply')</h5>
            <form method="POST" action="{{ route('complaints.replies.store', $complaint->id) }}">
                @csrf
                <div class="form-group">
                    <label for="newReplyContent" class="col-form-label">@lang('partials.reply_content')</label>
                    <textarea id="newReplyContent" name="reply_content"
                              placeholder="@lang('partials.reply_placeholder')"
                              class="form-control" required>{{ old('reply_content') }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary">@lang('partials.submit_reply')</button>
            </form>
        </div>
    </div>
@endsection
