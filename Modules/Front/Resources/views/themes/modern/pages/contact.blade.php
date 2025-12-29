@extends($themePath . '.layouts.master')
@section('content')
<section class="page-header page-header-dark bg-secondary">
    <div class="container"><div class="row"><div class="col-md-12">
        <h1>Contact Us</h1>
        <ol class="breadcrumb">
            <li><a href="{{ route('front.index') }}">Home</a></li>
            <li class="active">Contact</li>
        </ol>
    </div></div></div>
</section>

<section class="main-container">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <h2>Get in Touch</h2>
                @auth
                <p>Write us a message</p>
                @else
                <p>Write us a message <span class="text-danger">(You need to login first)</span></p>
                @endauth
                
                <form method="POST" action="{{ route('front.store-message') }}" class="form">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Your Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Subject <span class="text-danger">*</span></label>
                                <input type="text" name="subject" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Phone <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Message <span class="text-danger">*</span></label>
                                <textarea name="message" class="form-control" rows="6" required></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-default btn-animated">Send Message</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-4">
                @php $settings = DB::table('settings')->first(); @endphp
                <div class="sidebar">
                    <div class="block">
                        <h4><i class="fa fa-phone"></i> Call Us</h4>
                        <p>{{ $settings->phone ?? '+00 1234567890' }}</p>
                    </div>
                    <div class="block">
                        <h4><i class="fa fa-envelope"></i> Email</h4>
                        <p>{{ $settings->email ?? 'info@example.com' }}</p>
                    </div>
                    <div class="block">
                        <h4><i class="fa fa-map-marker"></i> Address</h4>
                        <p>{{ $settings->address ?? 'Your Address' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div id="google-map" style="height:400px;">
    <iframe src="https://maps.google.com/maps?q={{ $settings->latitude ?? '50.85' }},{{ $settings->longitude ?? '4.35' }}&t=&z=13&ie=UTF8&iwloc=&output=embed"
        width="100%" height="100%" frameborder="0" style="border:0;" allowfullscreen=""></iframe>
</div>

@include($themePath . '.layouts.newsletter')
@endsection

@push('scripts')
<script src="{{ asset('frontend/js/jquery.form.js') }}"></script>
<script src="{{ asset('frontend/js/jquery.validate.min.js') }}"></script>
@endpush
