@extends('front::layouts.master')

@section('title','E-SHOP || About Us')

@section('content')

    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="/">Home<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="#">Password reset</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <!-- Start Team -->
    <section id="team" class="team section">


        <div class="card o-hidden border-0 shadow-lg 2">

            <div class="p-5">
                <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-2">Forgot Your Password?</h1>
                    <p class="mb-4">We get it, stuff happens. Just enter your email address below
                        and
                        we'll send you a link to reset your password!</p>
                </div>
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif
                <form class="user" method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="form-group">
                        <input type="email"
                               class="form-control form-control-user @error('email') is-invalid @enderror"
                               id="exampleInputEmail" aria-describedby="emailHelp"
                               placeholder="Enter Email Address..." name="email"
                               value="{{ old('email') }}" required autocomplete="email" autofocus>
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary btn-user btn-block">
                        Reset Password
                    </button>
                </form>
                <hr>
                <div class="text-center">
                    <a class="small" href="{{route('login')}}">Already have an account? Login!</a>
                </div>
            </div>
        </div>

    </section>

    <!-- End Shop Services Area -->

    @include('front::layouts.newsletter')
@endsection
