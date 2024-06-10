{{-- ADMIN --}}

<script src="{{ asset('js/event_details.js') }}"></script>

@extends('layout')


@section('title', 'Event Details')

@section('content')

<div class="container event-details-container">

    <div class="event-title-container mt-3">
        <div class="event-title">
            <h1>{{ $event->name }}</h1>
            <p class="text-muted">Published by: {{ $organizerName }}</p>
        </div>
        @if(auth()->user()->isAdmin() || auth()->user()->id === $event->organizer_id)
            <a href="{{ route('events.edit', $event->id) }}" class="btn btn-primary mb-2">Edit Event</a>
        @endif
    </div>

    @if (session('success'))
        <div class="container mt-3">
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        </div>
    @endif


    <div class="row mb-4">

        {{-- Event Pictures  --}}
        <div class="event-pictures-container mt-3">
            <h3>Event Pictures</h3>
            @if($event->images->count() > 0)
                <div id="carouselEventImages" class="carousel slide event-picture-carousel" data-bs-ride="carousel">
                    <div class="carousel-inner">
                    @foreach($event->images as $index => $image)
                        <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                            <div class="event-image-container">
                                <img src="{{ asset('storage/images/event/' . $image->filename) }}" class="d-block event-image" alt="Event Image">
                            </div>
                        </div>
                    @endforeach
                    </div>
                    @if($event->images->count() > 1)
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselEventImages" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselEventImages" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    @endif
                </div>
            @else
                <p>No images available for this event.</p>
            @endif
        </div>



        {{-- Event Information --}}
        <div class="event-details-section mt-3">
            <div class="event-details">
                <h3>Details</h3>
                <p><strong>Category:</strong> {{ $categoryLabels[$event->category] }}</p>
                <p><strong>Location:</strong> {{ $event->address_line }} {{ $event->city }}, {{ $event->zip_code }}, {{ $event->state }}</p>
                <p><strong>Maximum capacity per session:</strong> {{ $event->max_capacity_per_session }}</p>
                <p><strong>Price per seat:</strong> RM {{ $event->price_per_seat }}</p>
                <p><strong>Description:</strong> {{ $event->description }}</p>
            </div>


            {{-- Weather API Integration --}}
            <div class="weather-forecast-container">
                <h3>Weather Forecast</h3>
                <div id="weather" class="weather-forecast-grid"></div>
            </div>
        </div>

        {{-- Sessions List --}}
        <div class="container mt-3">
            <h3>Event Sessions</h3>
            <div class="card mb-5">
                <div class="card-body">
                    <div class="sessions-container">
                        <div class="d-flex flex-row justify-content-start align-items-center">
                            @foreach ($event->sessions as $session)
                                <div class="session-card">
                                    <h4><span class="session-number nbr1">{{ $loop->iteration }}</span></h4>
                                    <div class="date">{{ $session->start_time->format('l') }}</div>
                                    <div class="month">{{ $session->start_time->format('F') }}</div>
                                    <div class="day">{{ $session->start_time->format('j') }}</div>
                                    <div class="time">
                                        {{ $session->start_time->format('g:ia') }}
                                        <span> - </span>
                                        {{ $session->end_time->format('g:ia') }}
                                    </div>
                                    <a href="{{ route('session.select-seat', $session->id) }}" class="btn btn-primary btn-sm">Select</a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Player Details --}}
        <div class="container mt-3">
            <h3>Session Player Details</h3>


            <div class="player-details-container">
                @foreach ($event->sessions as $session)
                    <div class="session-player mb-3">

                        <div class="session-header">
                            <h4>
                                Session <span class="session-number nbr2">{{ $loop->iteration }}</span>:

                                @if($session->start_time->format('Y-m-d') === $session->end_time->format('Y-m-d'))
                                    {{ $session->start_time->format('j F Y, g:ia') }} - {{ $session->end_time->format('g:ia') }}
                                @else
                                    {{ $session->start_time->format('j F Y, g:ia') }} - {{ $session->end_time->format('j F Y, g:ia') }}
                                @endif
                            </h4>

                            <div>
                                @if(auth()->user()->isAdmin() || auth()->user()->id === $event->organizer_id)
                                    <a href="{{ route('team-players.create', ['sessionId' => $session->id]) }}" class="btn btn-success">Manage Players</a>
                                    <a href="{{ route('sessions.showSelectWinner', ['sessionId' => $session->id]) }}" class="btn btn-primary">Select Winner</a>
                                @endif
                            </div>
                        </div>

                        <div class="player-card-container">

                            <div class="team-players-name">

                                @if ($session->teamPlayers->isNotEmpty())
                                    <h3>
                                        {{ $session->teamPlayers->pluck('name')->join(' VS. ') }}
                                    </h3>
                                @endif

                            </div>


                            <div class="d-flex justify-content-between flex-wrap">

                                @if ($session->teamPlayers->isNotEmpty())

                                    <div class="player-card-scroll-container">

                                        @foreach ($session->teamPlayers as $player)
                                            <div class="col-custom">
                                                <div class="card player-card {{ $session->winner_player_id == $player->id ? 'winner-card' : '' }}">
                                                    <img src="{{ asset('storage/images/team_players/' . $player->picture_filename) }}" class="card-img-top" alt="{{ $player->name }}">
                                                    <div class="card-body player-card">

                                                        <div class="title-winner-container">
                                                            <h5 class="card-title">{{ $player->name }}</h5>
                                                        </div>

                                                        <p class="card-text">Number of Votes: {{ $player->votes }}</p>

                                                        @if ($session->winner_player_id == $player->id)
                                                            <span class="winner-label">Won</span>
                                                        @endif

                                                        <a href="{{ $player->wikipedia_link }}" class="btn btn-primary wikipedia-btn" target="_blank">Wikipedia</a>

                                                        @if (auth()->check())
                                                            <button class="not-allowed admin-vote-btn" disabled data-toggle="tooltip" title="Admin & Organizers cannot vote">Vote</button>
                                                        @endif

                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach

                                    </div>
                                @else
                                    <p>No team players added for this session.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>

    {{-- Ratings and Reviews --}}
    <div class="reviews-ratings-container mt-3">
        <h3>Reviews and Ratings</h3>
        <div class="reviews-container">
            @forelse ($event->reviews as $review)
                <div class="review">
                    <div class="review-header">
                        <strong>{{ $review->user->name }}</strong>
                        <span>Rating: {{ $review->rating }}/5</span>
                    </div>
                    <p class="review-body mt-2">{{ $review->review }}</p>
                </div>
            @empty
                <p>No reviews yet.</p>
            @endforelse
        </div>

        {{-- Social Media --}}
        <div class="social-media mt-4">
            <h4>Share this event</h4>
            <div class="social-buttons">
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener noreferrer" class="btn btn-facebook">
                <img src="{{ asset('Storage/images/fbshare.png') }}" class="small-btn">
                </a>

                <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}" target="_blank" rel="noopener noreferrer" class="btn btn-twitter">
                <img src="{{ asset('Storage/images/xshare.png') }}" class="small-btn">
                </a>
            </div>
        </div>
    </div>

</div>
@endsection


@section('scripts')
<script>
    var eventDetails = {
        latitude: {{ $event->latitude }},
        longitude: {{ $event->longitude }},
        sessions: @json($event->sessions),
        basePath: "{{ asset('Storage/images/weather_icon/') }}/"
    };
</script>
@endsection

