@if ($permission->exists)
    <form class="form-horizontal" method="POST" action="{{ route('permissions.update', $permission) }}"
          enctype="multipart/form-data">
        @method('put')
        @csrf
        @else
            <form class="form-horizontal" method="POST" action="{{ route('permissions.store') }}"
                  enctype="multipart/form-data">
                @csrf
                @endif
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Name:</strong>
                            <input type="text" id="name" value="{{ $permission->name ?? null }}" name="name"
                                   class="form-control">
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                        <button type="submit" class="btn purple">{{ trans('messages.save') }}</button>
                    </div>
                </div>
            </form>