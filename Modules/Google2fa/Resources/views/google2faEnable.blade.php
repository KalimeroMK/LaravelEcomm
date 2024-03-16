@extends('admin::layouts.master')

@section('content')
    <div class="container mt-5">
        <h1>Enable 2FA</h1>
        <p class="mb-4">Scan the QR code below with your 2FA application:</p>

        <div class="row">
            <!-- QR Code column -->
            <div class="col-md-6">
                <img src="{{ $qrImage }}" alt="2FA QR Code" class="img-fluid">
            </div>

            <!-- OTP column -->
            <div class="col-md-6 text-right">
                <form action="{{ route('verify-2fa') }}" method="POST" class="form-inline justify-content-end">
                    @csrf
                    <div class="form-group">
                        <label for="otp" class="mr-2">Enter OTP code</label>
                        <input type="text" name="otp" id="otp" class="form-control">
                    </div>
                </form>

                @if (session('message'))
                    <div class="alert alert-success mt-3">
                        {{ session('message') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="col-md-12 mt-3">
            <a class="btn btn-secondary" href="{{ route('users.index') }}">@lang('translation.users.back')</a>
        </div>
    </div>
@endsection