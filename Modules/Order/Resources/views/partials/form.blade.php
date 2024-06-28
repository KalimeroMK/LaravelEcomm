<form method="POST"
      action="{{ route($order->exists ? 'orders.update' : 'orders.store', $order->exists ? $order->id : null) }}"
      enctype="multipart/form-data">
    @csrf
    @if($order->exists)
        @method('put')
    @endif

    <div class="form-group">
        <label for="status">@lang('partials.status')</label>
        <select name="status" class="form-control">
            <option value="new" {{ $order->status == 'new' ? 'selected' : '' }}>New</option>
            <option value="process" {{ $order->status == 'process' ? 'selected' : '' }}>Process</option>
            <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
            <option value="cancel" {{ $order->status == 'cancel' ? 'selected' : '' }}>Cancel</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">@lang('partials.update')</button>
</form>
