<form method="POST"
      action="{{ route($user->exists ? 'users.update' : 'users.store', $user->exists ? $user->id : null) }}"
      enctype="multipart/form-data">
    @csrf
    @if($user->exists)
        @method('put')
    @endif

    <div class="form-group">
        <label for="name">Name</label>
        <input id="name" class="form-control" name="name" type="text" value="{{ old('name', $user->name ?? null) }}">
    </div>

    <div class="form-group">
        <label for="email">Email</label>
        <input id="email" class="form-control" name="email" type="email"
               value="{{ old('email', $user->email ?? null) }}">
    </div>

    <div class="form-group">
        <label for="password">Password</label>
        <input id="password" class="form-control" name="password" type="password">
    </div>

    <div class="form-group">
        <label for="confirm-password">Confirm Password</label>
        <input id="confirm-password" class="form-control" name="confirm-password" type="password">
    </div>

    <div class="form-group">
        <label for="roles">Role</label>
        <select class="form-control" name="roles[]" multiple>
            @foreach ($roles as $role)
                <option
                    value="{{ $role->id }}" {{ $user->roles->contains($role->id) ? 'selected' : '' }}>{{ $role->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
</form>

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet"/>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            $('select').select2();
        });
    </script>
@endpush
