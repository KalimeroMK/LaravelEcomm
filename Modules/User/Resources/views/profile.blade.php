@extends('admin::layouts.master')

@section('title','Admin Profile')

@section('content')

    <div class="card shadow mb-4">
        <div class="row">
            <div class="col-md-12">
                @include('notification::notification')

            </div>
        </div>
        <div class="card-header py-3">
            <h4 class=" font-weight-bold">Profile</h4>
            <ul class="breadcrumbs">
                <li><a href="{{route('admin')}}" style="color:#999">Dashboard</a></li>
                <li><a href="" class="active text-primary">Profile Page</a></li>
            </ul>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="image">
                            @if($profile['photo'] ?? '')
                                <img class="card-img-top img-fluid roundend-circle mt-4"
                                     style="border-radius:50%;height:80px;width:80px;margin:auto;"
                                     src="{{$profile['photo'] ?? ''}}" alt="profile picture">
                            @else
                                <img class="card-img-top img-fluid roundend-circle mt-4"
                                     style="border-radius:50%;height:80px;width:80px;margin:auto;"
                                     src="{{asset('backend/img/avatar.png')}}" alt="profile picture">
                            @endif
                        </div>
                        <div class="card-body mt-4 ml-2">
                            <h5 class="card-title text-left"><small><i class="fas fa-user"></i> {{$profile['name'] ?? ''}}
                                </small></h5>
                            <p class="card-text text-left"><small><i class="fas fa-envelope"></i> {{$profile['email'] ?? ''}}
                                </small></p>
                            <p class="card-text text-left"><small class="text-muted"><i
                                            class="fas fa-hammer"></i> {{$profile['role'] ?? ''}}</small></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <form class="border px-4 pt-2 pb-3" method="POST" action="{{route('profile-update',$profile['id'] ?? '')}}">
                        @csrf
                        <div class="form-group">
                            <label for="inputTitle" class="col-form-label">Name</label>
                            <input id="inputTitle" type="text" name="name" placeholder="@lang('partials.name')"
                                   value="{{$profile['name'] ?? ''}}" class="form-control">
                            @error('name')

                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="inputEmail" class="col-form-label">Email</label>
                            <input id="inputEmail" disabled type="email" name="email" placeholder="Enter email"
                                   value="{{$profile['email'] ?? ''}}" class="form-control">
                            @error('email')

                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="inputPhoto" class="col-form-label">Photo</label>
                            <div class="input-group">
                          <span class="input-group-btn">
                              <a id="lfm" data-input="thumbnail" data-preview="holder" class="btn btn-primary">
                              <i class="fa fa-picture-o"></i> Choose
                              </a>
                          </span>
                                <input id="thumbnail" class="form-control" type="text" name="photo"
                                       value="{{$profile['photo'] ?? ''}}">
                            </div>
                            @error('photo')

                            @enderror
                        </div>
                        <div class="col-md-12 mt-3">
                            @if(isset($profile['loginSecurity']) && $profile['loginSecurity']['google2fa_enable'] ?? false)
                                <a class="btn btn-warning"
                                   href="{{ route('disable2fa') }}">@lang('translation.users.2fa_disable')
                                </a>
                            @else
                                <a class="btn btn-secondary"
                                   href="{{ route('2fa') }}">@lang('translation.users.2fa_enable')</a>
                            @endif
                        </div>
                        <button type="submit" class="btn btn-success btn-sm float-right">Update</button>
                    </form>

                </div>
            </div>
        </div>
    </div>

@endsection

<style>
    .breadcrumbs {
        list-style: none;
    }

    .breadcrumbs li {
        float: left;
        margin-right: 10px;
    }

    .breadcrumbs li a:hover {
        text-decoration: none;
    }

    .breadcrumbs li .active {
        color: red;
    }

    .breadcrumbs li + li:before {
        content: "/\00a0";
    }

    .image {
        background: url('{{asset('backend/img/background.jpg')}}');
        height: 150px;
        background-position: center;
        background-attachment: cover;
        position: relative;
    }

    .image img {
        position: absolute;
        top: 55%;
        left: 35%;
        margin-top: 30%;
    }

    i {
        font-size: 14px;
        padding-right: 8px;
    }
</style>

@push('scripts')
    <script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
    <script>
        $('#lfm').filemanager('image');
    </script>
@endpush