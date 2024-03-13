<!DOCTYPE html>
<html lang="en">

<head>
    <title>E-SHOP || Magic Link</title>
    @include('admin::layouts.head')

</head>

<body class="bg-gradient-primary">

<div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">

        <div class="col-xl-10 col-lg-12 col-md-9 mt-5">

            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <!-- Nested Row within Card Body -->
                    <div class="row">
                        <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                        <div class="col-lg-6">
                            <div class="p-5">

                                @if(session('magic_link_sent'))
                                    <p>{{ session('magic_link_sent') }}</p>
                                @endif
                                <div class="form-group">

                                    <form method="POST" action="{{ route('magic.login') }}">
                                        @csrf
                                        <div class="form-group">
                                            <input type="email"
                                                   class="form-control form-control-user @error('email') is-invalid @enderror"
                                                   name="email" value="{{ old('email') }}" id="exampleInputEmail"
                                                   aria-describedby="emailHelp" placeholder="Enter Email Address..."
                                                   required autocomplete="email" autofocus>

                                        </div>
                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            Login
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
</body>

</html>
