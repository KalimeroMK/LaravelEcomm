@extends('admin::layouts.master')
@section('title', 'Complaint Detail')
@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Complaint Details</h6>
        </div>
        <div class="card-body">
            {{-- Complaint Detail Form --}}
            <form class="form-horizontal" method="POST"
                  action="{{ route($complaint->exists ? 'complaints.update' : 'complaints.store', $complaint->exists ?
                  $complaint->id :
                  null) }}"
                  enctype="multipart/form-data">
                @csrf
                @if($complaint->exists)
                    @method('put')
                @endif
                <!-- Form fields here -->

                <div class="form-group">
                    <label for="inputOrderID">Order ID</label>
                    @if($complaint->exists)
                        <input id="inputOrderID" type="text" name="order_id"
                               value="{{ $complaint->order_id }}"
                               class="form-control" readonly>
                    @else
                        <input id="inputOrderID" type="text" name="order_id"
                               value="{{ request()->route('order_id') }}"
                               class="form-control" placeholder="Enter Order ID" required>
                    @endif
                </div>

                <div class="form-group">
                    <label for="inputDescription" class="col-form-label">Description</label>
                    <textarea id="inputDescription" name="description" placeholder="Enter Description"
                              class="form-control" {{ $complaint->exists ? 'readonly' : '' }}>{{ $complaint->description ?? null }}</textarea>
                </div>

                <div class="form-group">
                    <label for="status" class="col-form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="open" {{ $complaint->status == 'open' ? 'selected' : '' }}>Open</option>
                        <option value="closed" {{ $complaint->status == 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>

                {{-- Replies Timeline --}}
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Complaint Details</h6>
                    </div>
                    <div class="card-body">
                        <!-- Complaint Detail Form (same as before) -->

                        <!-- Replies Timeline -->
                        <div class="timeline mt-4">
                            @foreach($complaint->complaint_replies as $reply)
                                <div class="timeline-item">
                                    <div class="timeline-date">{{ $reply->created_at->format('d/m/Y') }}</div>
                                    <div class="timeline-content">
                                        <div class="reply-card">
                                            <h6 class="mb-1 font-weight-bold {{ $reply->user->role == 'super-admin' ? 'text-primary' : 'text-secondary' }}">
                                                {{ $reply->user->name }}
                                            </h6>
                                            <p class="mb-0">{{ $reply->reply_content }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- New Reply Form (same as before) -->
                    </div>
                </div>

                {{-- New Reply Form --}}
                <hr>
                <h5 class="font-weight-bold">Add Reply</h5>
                @csrf
                <div class="form-group">
                    <label for="newReplyContent" class="col-form-label">Reply Content</label>
                    <textarea id="newReplyContent" name="reply_content" placeholder="Enter reply content..."
                              class="form-control" required>{{ old('reply_content') }}</textarea>
                </div>
                <div class="d-flex justify-content-between">
                    <button type="reset" class="btn btn-warning">Reset</button>
                    <button class="btn btn-success" type="submit">Submit</button>
                </div>
            </form>
        </div>
    </div>
@endsection
