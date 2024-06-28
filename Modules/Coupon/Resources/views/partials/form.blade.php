<form method="POST"
      action="{{ route($coupon->exists ? 'coupons.update' : 'coupons.store', $coupon->exists ? $coupon->id : null) }}"
      enctype="multipart/form-data">
    @csrf
    @if($coupon->exists)
        @method('put')
    @endif

    <div class="form-group">
        <label for="inputCode">Coupon Code <span class="text-danger">*</span></label>
        <input id="inputCode" type="text" name="code" placeholder="Enter Coupon Code" value="{{$coupon->code}}"
               class="form-control">
    </div>

    <div class="form-group">
        <label for="type">Type <span class="text-danger">*</span></label>
        <select name="type" class="form-control">
            <option value="fixed" {{($coupon->type=='fixed') ? 'selected' : ''}}>Fixed</option>
            <option value="percent" {{($coupon->type=='percent') ? 'selected' : ''}}>Percent</option>
        </select>
    </div>

    <div class="form-group">
        <label for="inputValue">Value <span class="text-danger">*</span></label>
        <input id="inputValue" type="number" name="value" placeholder="Enter Coupon value" value="{{$coupon->value}}"
               class="form-control">
    </div>

    <div class="form-group">
        <label for="status">Status <span class="text-danger">*</span></label>
        <select name="status" class="form-control">
            <option value="active" {{($coupon->status=='active') ? 'selected' : ''}}>Active</option>
            <option value="inactive" {{($coupon->status=='inactive') ? 'selected' : ''}}>Inactive</option>
        </select>
    </div>

    <div class="form-group mb-3">
        <button type="submit" class="btn btn-success">Update</button>
    </div>
</form>
