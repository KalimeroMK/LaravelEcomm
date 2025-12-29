@extends('admin::layouts.master')

@section('title', 'Recovery Codes')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Two-Factor Authentication Recovery Codes</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <strong>Important:</strong> Store these recovery codes in a safe place. You can use them to access your account if you lose access to your authenticator device.
                        </div>

                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="recovery-codes-list">
                            <h5>Your Recovery Codes:</h5>
                            <div class="list-group">
                                @foreach($recovery_codes as $code)
                                    <div class="list-group-item">
                                        <code>{{ $code }}</code>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-4">
                            <form action="{{ route('admin.2fa.recovery-codes.regenerate') }}" method="POST" onsubmit="return confirm('Are you sure you want to regenerate recovery codes? Your old codes will no longer work.');">
                                @csrf
                                <button type="submit" class="btn btn-warning">Regenerate Recovery Codes</button>
                            </form>
                        </div>

                        <div class="mt-3">
                            <a href="{{ route('admin.2fa') }}" class="btn btn-secondary">Back to 2FA Settings</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

