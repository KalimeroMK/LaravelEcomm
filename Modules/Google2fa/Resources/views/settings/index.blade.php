@extends('admin::layouts.master')

@section('title', '2FA Settings')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Two-Factor Authentication Settings</h3>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('admin.2fa.settings.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="enforce_for_admins" 
                                           name="enforce_for_admins" value="1" 
                                           {{ $settings->enforce_for_admins ? 'checked' : '' }}>
                                    <label class="form-check-label" for="enforce_for_admins">
                                        Enforce 2FA for Administrators
                                    </label>
                                    <small class="form-text text-muted">
                                        Require all admin and super-admin users to enable 2FA
                                    </small>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="enforce_for_users" 
                                           name="enforce_for_users" value="1" 
                                           {{ $settings->enforce_for_users ? 'checked' : '' }}>
                                    <label class="form-check-label" for="enforce_for_users">
                                        Enforce 2FA for All Users
                                    </label>
                                    <small class="form-text text-muted">
                                        Require all users to enable 2FA
                                    </small>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="enforced_roles">Enforce 2FA for Specific Roles</label>
                                <select multiple class="form-control" id="enforced_roles" name="enforced_roles[]">
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" 
                                                {{ in_array($role->name, $settings->enforced_roles ?? []) ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">
                                    Hold Ctrl (Windows) or Cmd (Mac) to select multiple roles
                                </small>
                            </div>

                            <div class="form-group">
                                <label for="recovery_codes_count">Number of Recovery Codes</label>
                                <input type="number" class="form-control" id="recovery_codes_count" 
                                       name="recovery_codes_count" 
                                       value="{{ $settings->recovery_codes_count }}" 
                                       min="5" max="20" required>
                                <small class="form-text text-muted">
                                    Number of recovery codes to generate (5-20)
                                </small>
                            </div>

                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="require_backup_codes" 
                                           name="require_backup_codes" value="1" 
                                           {{ $settings->require_backup_codes ? 'checked' : '' }}>
                                    <label class="form-check-label" for="require_backup_codes">
                                        Require Backup Codes
                                    </label>
                                    <small class="form-text text-muted">
                                        Require users to save backup codes before enabling 2FA
                                    </small>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Save Settings</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

