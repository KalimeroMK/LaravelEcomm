@extends('admin::layouts.master')

@section('title', 'Email Settings')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Email Settings</h3>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('settings.email.update', $settings) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="mail_driver">Mail Driver</label>
                                <select class="form-control" id="mail_driver" name="mail_driver">
                                    <option value="smtp" {{ ($emailSettings['mail_driver'] ?? 'smtp') == 'smtp' ? 'selected' : '' }}>SMTP</option>
                                    <option value="sendmail" {{ ($emailSettings['mail_driver'] ?? '') == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                                    <option value="mailgun" {{ ($emailSettings['mail_driver'] ?? '') == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                    <option value="ses" {{ ($emailSettings['mail_driver'] ?? '') == 'ses' ? 'selected' : '' }}>Amazon SES</option>
                                    <option value="postmark" {{ ($emailSettings['mail_driver'] ?? '') == 'postmark' ? 'selected' : '' }}>Postmark</option>
                                    <option value="log" {{ ($emailSettings['mail_driver'] ?? '') == 'log' ? 'selected' : '' }}>Log</option>
                                </select>
                            </div>

                            <h4 class="mt-4">SMTP Settings</h4>
                            <div class="form-group">
                                <label for="mail_host">SMTP Host</label>
                                <input type="text" class="form-control" id="mail_host" 
                                       name="mail_host" 
                                       value="{{ $emailSettings['mail_host'] ?? '' }}">
                            </div>
                            <div class="form-group">
                                <label for="mail_port">SMTP Port</label>
                                <input type="number" class="form-control" id="mail_port" 
                                       name="mail_port" 
                                       value="{{ $emailSettings['mail_port'] ?? '587' }}">
                            </div>
                            <div class="form-group">
                                <label for="mail_username">SMTP Username</label>
                                <input type="text" class="form-control" id="mail_username" 
                                       name="mail_username" 
                                       value="{{ $emailSettings['mail_username'] ?? '' }}">
                            </div>
                            <div class="form-group">
                                <label for="mail_password">SMTP Password</label>
                                <input type="password" class="form-control" id="mail_password" 
                                       name="mail_password" 
                                       value="{{ $emailSettings['mail_password'] ?? '' }}">
                            </div>
                            <div class="form-group">
                                <label for="mail_encryption">Encryption</label>
                                <select class="form-control" id="mail_encryption" name="mail_encryption">
                                    <option value="tls" {{ ($emailSettings['mail_encryption'] ?? 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ ($emailSettings['mail_encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                </select>
                            </div>

                            <h4 class="mt-4">Email From Settings</h4>
                            <div class="form-group">
                                <label for="mail_from_address">From Address</label>
                                <input type="email" class="form-control" id="mail_from_address" 
                                       name="mail_from_address" 
                                       value="{{ $emailSettings['mail_from_address'] ?? '' }}">
                            </div>
                            <div class="form-group">
                                <label for="mail_from_name">From Name</label>
                                <input type="text" class="form-control" id="mail_from_name" 
                                       name="mail_from_name" 
                                       value="{{ $emailSettings['mail_from_name'] ?? '' }}">
                            </div>
                            <div class="form-group">
                                <label for="mail_reply_to">Reply To Address</label>
                                <input type="email" class="form-control" id="mail_reply_to" 
                                       name="mail_reply_to" 
                                       value="{{ $emailSettings['mail_reply_to'] ?? '' }}">
                            </div>

                            <button type="submit" class="btn btn-primary">Save Email Settings</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

