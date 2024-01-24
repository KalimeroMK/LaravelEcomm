@if ($coupon->exists)
    <form method="post" action="{{route('coupon.update',$coupon->id)}}"
          enctype="multipart/form-data">
        @method('put')
        @csrf
        @else
            <form class="form-horizontal" method="POST" action="{{ route('coupon.store') }}"
                  enctype="multipart/form-data">
                @csrf
                @endif
                <div class="form-group">
                    <label for="inputTitle" class="col-form-label">Coupon Code <span
                                class="text-danger">*</span></label>
                    <input id="inputTitle" type="text" name="code" placeholder="Enter Coupon Code"
                           value="{{$coupon->code}}" class="form-control">
                </div>

                <div class="form-group">
                    <label for="type" class="col-form-label">Type <span class="text-danger">*</span></label>
                    <select name="type" class="form-control">
                        <option value="fixed" {{(($coupon->type=='fixed') ? 'selected' : '')}}>Fixed</option>
                        <option value="percent" {{(($coupon->type=='percent') ? 'selected' : '')}}>Percent</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="inputTitle" class="col-form-label">Value <span class="text-danger">*</span></label>
                    <input id="inputTitle" type="number" name="value" placeholder="Enter Coupon value"
                           value="{{$coupon->value}}" class="form-control">
                </div>

                <div class="form-group">
                    <label for="status" class="col-form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-control">
                        <option value="active" {{(($coupon->status=='active') ? 'selected' : '')}}>Active</option>
                        <option value="inactive" {{(($coupon->status=='inactive') ? 'selected' : '')}}>Inactive</option>
                    </select>

                </div>
                <div class="form-group mb-3">
                    <button class="btn btn-success" type="submit">Update</button>
                </div>
            </form>
    </form>

