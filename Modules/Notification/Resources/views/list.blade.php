<div id="notifications">
    <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown"
       aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-bell fa-fw"></i>
        <!-- Counter - Alerts -->
        <span class="badge badge-danger badge-counter">
           @if(auth()->check())
                @if(count(auth()->user()->unreadNotifications) > 5)
                    <span data-count="5" class="count">5+</span>
                @else
                    <span class="count"
                          data-count="{{ count(auth()->user()->unreadNotifications) }}">{{ count(auth()->user()->unreadNotifications) }}</span>
                @endif
            @endif
        </span>
    </a>
    <!-- Dropdown - Alerts -->
    <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
         aria-labelledby="alertsDropdown">
        <h6 class="dropdown-header">
            Notifications Center
        </h6>
        @foreach(Auth::user()->unreadNotifications as $notification)
            @php
                // Decode the notification data
                $notificationData = $notification->data;
                $iconClass = $notificationData['fas'] ?? 'fa-info-circle';
                $title = $notificationData['title'] ?? 'No title';
            @endphp
            <a class="dropdown-item d-flex align-items-center" target="_blank"
               href="{{route('admin.notification', $notification->id)}}">
                <div class="mr-3">
                    <div class="icon-circle bg-primary">
                        <i class="fas {{$iconClass}} text-white"></i>
                    </div>
                </div>
                <div>
                    <div class="small text-gray-500">{{$notification->created_at->format('F d, Y h:i A')}}</div>
                    <span
                        class="@if(is_null($notification->read_at)) font-weight-bold @else small text-gray-500 @endif">{{$title}}</span>
                </div>
            </a>
            @if($loop->index + 1 == 5)
                @php
                    break
                @endphp
            @endif
        @endforeach

        <a class="dropdown-item text-center small text-gray-500" href="{{route('all.notification')}}">Show All
            Notifications</a>
    </div>
</div>
