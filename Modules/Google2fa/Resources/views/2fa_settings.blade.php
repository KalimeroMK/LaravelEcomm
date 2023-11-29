@extends('admin::layouts.master')

@section('content')
    <div class="container">
        <div class="row justify-content-md-center">
            <div class="col-md-8">
                <div class="card">
                    @include('front::layouts.notification')
                    <div class="card-header"><strong>Two Factor Authentication</strong></div>
                    <div class="card-body">
                        <p>Two factor authentication (2FA) strengthens access security by requiring two methods (also
                            referred to as factors) to verify your identity. Two factor authentication protects against
                            phishing, social engineering and password brute force attacks and secures your logins from
                            attackers exploiting weak or stolen credentials.</p>
                        @if($user->loginSecurity == null)
                            {{-- Generate 2FA Secret --}}
                            <form method="POST" action="{{ route('generate2faSecret') }}">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        Generate Secret Key to Enable 2FA
                                    </button>
                                </div>
                            </form>
                        @elseif($user->loginSecurity->google2fa_enable == 0)
                            {{-- Enable 2FA --}}
                            <p>1. Scan this QR code with your Google Authenticator App. Alternatively, you can use the
                                code: <code>{{ $secret_key }}</code></p>
                            <img src="{{ $google2fa_url }}" alt="QR Code">

                            <p>2. Enter the pin from Google Authenticator app:</p>
                            <form method="POST" action="{{ route('enable2fa') }}">
                                {{ csrf_field() }}
                                <div class="form-group{{ $errors->has('verify-code') ? ' has-error' : '' }}">
                                    <label for="secret" class="control-label">Authenticator Code</label>
                                    <input id="secret" type="password" class="form-control col-md-4" name="secret"
                                           required>
                                    @if ($errors->has('verify-code'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('verify-code') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    Enable 2FA
                                </button>
                            </form>
                        @elseif($user->loginSecurity->google2fa_enable == 1)
                            {{-- 2FA is enabled --}}
                            <div class="alert alert-success">
                                2FA is currently <strong>enabled</strong> on your account.
                            </div>
                            <p>If you are looking to disable Two Factor Authentication. Please confirm your password and
                                Click Disable 2FA Button.</p>
                            <form method="POST" action="{{ route('disable2fa') }}">
                                {{ csrf_field() }}
                                <div class="form-group{{ $errors->has('current-password') ? ' has-error' : '' }}">
                                    <label for="current-password" class="control-label">Current Password</label>
                                    <input id="current-password" type="password" class="form-control col-md-4"
                                           name="current-password" required>
                                    @if ($errors->has('current-password'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('current-password') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    Disable 2FA
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
