{{-- Return/refund request block for the customer's order detail page.
     Expects $order (with the orderReturn relation available). --}}
@php($return = $order->orderReturn)

@if($return)
    <div class="alert alert-{{ ['requested' => 'warning', 'approved' => 'info', 'rejected' => 'secondary', 'refunded' => 'success'][$return->status] ?? 'light' }} mt-4">
        <strong>@lang('Return request'):</strong> {{ ucfirst($return->status) }}
        @if($return->status === 'refunded' && $return->refund_amount)
            — @lang('refunded') {{ number_format((float) $return->refund_amount, 2) }}
        @endif
        @if($return->admin_note)
            <br><small>{{ $return->admin_note }}</small>
        @endif
    </div>
@elseif($order->canRequestReturn())
    <div class="card mt-4">
        <div class="card-body">
            <h5 class="mb-3">@lang('Need to return this order?')</h5>
            <form action="{{ route('user.orders.return.request', $order) }}" method="POST">
                @csrf
                <div class="form-group">
                    <textarea name="reason" class="form-control" rows="3" required minlength="10"
                              placeholder="@lang('Tell us why you want to return this order (at least 10 characters)')">{{ old('reason') }}</textarea>
                    @error('reason')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <button type="submit" class="btn btn-outline-danger">@lang('Request return / refund')</button>
            </form>
        </div>
    </div>
@endif
