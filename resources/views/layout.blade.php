<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">


        <title>@yield('title', 'Sports Event Management System')</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
        <link href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" rel="stylesheet">



        {{-- CSS --}}
        <link href="{{ asset('css/main.css') }}" rel="stylesheet">
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <link href="{{ asset('css/event_details.css') }}" rel="stylesheet">
        <link href="{{ asset('css/seat_map.css') }}" rel="stylesheet">
        <link href="{{ asset('css/seat_selection.css') }}" rel="stylesheet">
        <link href="{{ asset('css/home.css') }}" rel="stylesheet">
        {{-- <link href="{{ asset('css/theme.css') }}" rel="stylesheet"> --}}
        <link href="{{ asset('css/profile.css') }}" rel="stylesheet">
        <link href="{{ asset('css/notifications.css') }}" rel="stylesheet">
        <link href="{{ asset('css/footer.css') }}" rel="stylesheet">
        <link href="{{ asset('css/add_edit_event.css') }}" rel="stylesheet">
        <link href="{{ asset('css/manage_organizer_requests.css') }}" rel="stylesheet">
        <link href="{{ asset('css/event_list.css') }}" rel="stylesheet">
        <link href="{{ asset('css/cart.css') }}" rel="stylesheet">
        <link href="{{ asset('css/navbar.css') }}" rel="stylesheet">
        <link href="{{ asset('css/login_register.css') }}" rel="stylesheet">
        <link href="{{ asset('css/add_edit_player.css') }}" rel="stylesheet">
        <link href="{{ asset('css/payment.css') }}" rel="stylesheet">
        <link href="{{ asset('css/login_register.css') }}" rel="stylesheet">
        <link href="{{ asset('css/upcoming_sessions.css') }}" rel="stylesheet">
        <link href="{{ asset('css/organizer_list.css') }}" rel="stylesheet">
        <link href="{{ asset('css/teamplayer_statistics.css') }}" rel="stylesheet">
        <link href="{{ asset('css/order_list.css') }}" rel="stylesheet">





    </head>

    <body>
        @include('include.header')

        <div class="content-wrap">
            <main class="main-content" role="main">
                @yield('content')
            </main>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="http://cdnjs.cloudflare.com/ajax/libs/gsap/1.18.0/TweenMax.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/animejs@3.0.1/lib/anime.min.js"></script>




        {{-- JS --}}
        <script src="{{ asset('js/notifications.js') }}"></script>
        <script src="{{ asset('js/sports_animation.js') }}"></script>
        <script src="{{ asset('js/add_teamplayer.js') }}"></script>
        <script src="{{ asset('js/event_statistics.js') }}"></script>
        <script src="{{ asset('js/event_list.js') }}"></script>
        <script src="{{ asset('js/manage_organizer_request.js') }}"></script>
        <script src="{{ asset('js/teamplayer_list.js') }}"></script>
        <script src="{{ asset('js/organizer_list.js') }}"></script>
        <script src="{{ asset('js/session_list.js') }}"></script>
        <script src="{{ asset('js/profile_edit.js') }}"></script>
        <script src="{{ asset('js/cart.js') }}"></script>
        <script src="{{ asset('js/payment.js') }}"></script>
        <script src="{{ asset('js/select_seat.js') }}"></script>
        <script src="{{ asset('js/welcome.js') }}"></script>
        <script src="{{ asset('js/edit_teamplayer.js') }}"></script>




        @yield('scripts')
        @include('include.footer')
    </body>
</html>
