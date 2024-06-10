<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('home') }}">{{ config('app.name') }}</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon">
                <i class="fas fa-bars" style="color:#000;"></i>
            </span>
        </button>

        <div class="collapse navbar-collapse" id="navbarText">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" aria-current="page" href="{{ route('home') }}">
                        <i class="fas fa-home"></i> Home
                    </a>
                </li>


                @auth
                    @if(auth()->user()->isAdmin())
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('events.index') || request()->routeIs('events.create') || request()->routeIs('sessions.list') ? 'active' : '' }}"
                                href="#" id="eventsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-calendar-alt"></i> Events
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="eventsDropdown">
                                <li><a class="dropdown-item" href="{{ route('events.index') }}">Event List</a></li>
                                <li><a class="dropdown-item" href="{{ route('events.create') }}">Add Event</a></li>
                                <li><a class="dropdown-item" href="{{ route('sessions.list') }}">Session List</a></li>
                            </ul>
                        </li>
                    @endif

                    @if(auth()->user()->isAdmin())
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('organizer.manage') || request()->routeIs('organizers.show') ? 'active' : ''  }}"
                                href="#" id="organizerDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-tie"></i> Organizers
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="organizerDropdown">
                                <li><a class="dropdown-item" href="{{ route('organizer.manage') }}">Organizer Requests</a></li>
                                <li><a class="dropdown-item" href="{{ route('organizers.show') }}">Organizer List</a></li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('user.list') ? 'active' : '' }}" href="{{ route('user.list') }}">
                                <i class="fas fa-users"></i> Users
                            </a>
                        </li>

                    @elseif(auth()->user()->is_organizer)
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('events.index') || request()->routeIs('events.create') || request()->routeIs('sessions.list') ? 'active' : '' }}"
                                href="#" id="eventsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-calendar-alt"></i> Events
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="eventsDropdown">
                                <li><a class="dropdown-item" href="{{ route('events.index') }}">Event List</a></li>
                                <li><a class="dropdown-item" href="{{ route('events.create') }}">Add Event</a></li>
                                <li><a class="dropdown-item" href="{{ route('sessions.list') }}">Session List</a></li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('organizers.show') ? 'active' : '' }}" href="{{route('organizers.show')}}">
                                <i class="fas fa-users"></i> Organizers
                            </a>
                        </li>

                    @else
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('events.index') ? 'active' : '' }}" href="{{route('events.index')}}">
                                <i class="fas fa-calendar-alt"></i> Event List
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('organizers.show') ? 'active' : '' }}" href="{{route('organizers.show')}}">
                                <i class="fas fa-users"></i> Organizers
                            </a>
                        </li>
                    @endif


                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('team-players.list') ? 'active' : '' }}" href="{{route('team-players.list')}}">
                            <i class="fas fa-futbol"></i> Players
                        </a>
                    </li>

                @else
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}" href="{{route('login')}}">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('registration') ? 'active' : '' }}" href="{{route('registration')}}">
                            <i class="fas fa-user-plus"></i> Register
                        </a>
                    </li>
                @endauth
            </ul>

            @auth
                <ul class="navbar-nav ms-auto">

                    @if(!auth()->user()->isAdmin() && !auth()->user()->is_organizer)
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('cart') ? 'active' : '' }}" href="{{route('cart')}}">
                                <i class="fas fa-shopping-cart"></i> Cart
                            </a>
                        </li>
                    @endif

                    <li class="nav-item">
                        <a class="nav-link" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-bell"></i>
                            <span class="badge bg-danger">{{ auth()->user()->unreadNotifications->count() }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown">
                            @forelse (auth()->user()->unreadNotifications as $notification)
                                <li class="d-flex justify-content-between align-items-center">
                                    <div class="dropdown-item">
                                        {{ $notification->data['message'] }}
                                    </div>
                                    <button class="mark-as-read-btn" onclick="location.href='{{ route('notification.markAsRead', $notification->id) }}'" data-bs-toggle="tooltip" data-bs-placement="left" title="Mark as read">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </li>
                            @empty
                                <li class="dropdown-item">No new notifications</li>
                            @endforelse
                        </ul>
                    </li>


                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('profile') || request()->routeIs('profile.*') ? 'active' : '' }}"
                            href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user"></i> {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="fas fa-user-circle"></i> My Profile</a></li>

                            @if(!auth()->user()->isAdmin() && !auth()->user()->is_organizer)
                                <li><a class="dropdown-item" href="{{ route('profile.favorites') }}"><i class="fas fa-heart"></i> Favorites</a></li>
                            @endif

                            <li><a class="dropdown-item" href="{{ route('logout') }}"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            @endauth
        </div>
    </div>
</nav>


