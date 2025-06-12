<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link  rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Topbar Search -->
    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
        <div class="input-group">
            <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                   aria-label="Search" aria-describedby="basic-addon2">
            <div class="input-group-append">
                <button class="btn btn-primary" type="button">
                    <i class="fas fa-search fa-sm"></i>
                </button>
            </div>
        </div>
    </form>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">

        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
        <li class="nav-item dropdown no-arrow d-sm-none">
            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown"
               aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-search fa-fw"></i> ~
            </a>
            <!-- Dropdown - Messages -->
            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                 aria-labelledby="searchDropdown">
                <form class="form-inline mr-auto w-100 navbar-search">
                    <div class="input-group">
                        <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                               aria-label="Search" aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </li>

        {{-- Home page --}}
        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="{{route('admin')}}" target="_blank" data-toggle="tooltip"
               data-placement="bottom" title="home" role="button">
                <i class="fas fa-home fa-fw"></i>
            </a>
        </li>

        <!-- Nav Item - Messages -->
        <li class="nav-item dropdown no-arrow mx-1" id="messageT" data-url="{{route('messages.five')}}">
            @include('message::message')
        </li>

        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
               aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{Auth()->user()->name}}</span>
                @php $user = Auth()->user(); @endphp
                @if($user && $user->getFirstMediaUrl('photo'))
                    <img class="img-profile rounded-circle" src="{{$user->getFirstMediaUrl('photo')}}">
                @else
                    <img class="img-profile rounded-circle" src="{{asset('backend/img/avatar.png')}}">
                @endif
            </a>
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="{{route('user-profile')}}">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    Profile
                </a>
                <a class="dropdown-item" href="{{route('front.index')}}">
                    <i class="fas fa-shopify fa-sm fa-fw mr-2 text-gray-400"></i>
                    Web shop
                </a>
                @auth
                    @if(session('impersonated_by'))
                        <a class="dropdown-item" href="{{ route('users.leave-impersonate') }}">
                            <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                            Leave
                            Impersonation
                        </a>
                    @endif
                @endauth
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{ route('logout') }}"
                   onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i> {{ __('Logout') }}
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </li>

    </ul>
    <div class="dropdown">
        <button type="button" class="btn header-item waves-effect" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
            @switch(Session::get('locale', 'en'))
                @case('mk')
                    <img src="{{ asset('images/north-macedonia.png') }}" alt="Macedonian Language" height="32">
                    @break
                @case('de')
                    <img src="{{ asset('images/germany.png') }}" alt="German Language" height="32">
                    @break
                @default
                    <img src="{{ asset('images/united-kingdom.png') }}" alt="English Language" height="32">
            @endswitch
        </button>
        <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="{{ route('lang.switch', 'en') }}">English</a>
            <a class="dropdown-item" href="{{ route('lang.switch', 'mk') }}">Macedonian</a>
            <a class="dropdown-item" href="{{ route('lang.switch', 'de') }}">German</a>
        </div>
    </div>
</nav>
