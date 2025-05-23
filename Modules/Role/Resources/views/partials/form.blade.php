@if (isset($role['id']))
    <form class="form-horizontal" method="POST" action="{{ route('roles.update', $role['id']) }}" enctype="multipart/form-data">
        @method('put')
        @csrf
@else
    <form class="form-horizontal" method="POST" action="{{ route('roles.store') }}" enctype="multipart/form-data">
        @csrf
@endif
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>@lang('partials.name'):</strong>
                <input type="text" id="name" value="{{ $role['name'] ?? '' }}" name="name" class="form-control">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>@lang('sidebar.permissions'):</strong>
                <br/>
                @foreach ($permissions as $permission)
                    <label for="{{ $permission['id'] }}">
                        <input id="{{ $permission['id'] }}" name="permissions[]" type="checkbox"
                               value="{{ $permission['id'] }}"
                            {{ (isset($role['permissions']) && in_array($permission['id'], array_column($role['permissions'], 'id'))) ? 'checked' : '' }}>
                        {{ $permission['name'] }}
                    </label><br>
                @endforeach
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
            <button type="submit" class="btn purple">{{ trans('messages.save') }}</button>
        </div>
    </div>
</form>