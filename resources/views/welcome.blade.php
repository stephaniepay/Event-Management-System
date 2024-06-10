@extends('layout')

@section('title', "Home Page")

@section('content')

{{-- Advertisement Banner Section --}}
<div class="banner-container">
    <div id="bannerCarousel" class="carousel slide home-banner-carousel" data-ride="carousel">

        <ol class="carousel-indicators">
            <li data-target="#bannerCarousel" data-slide-to="0" class="active"></li>
            <li data-target="#bannerCarousel" data-slide-to="1"></li>
            <li data-target="#bannerCarousel" data-slide-to="2"></li>
        </ol>

        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="{{ asset('storage/images/home_banners/sports_event_banner_7.jpg') }}" alt="Advertisement Banner 1" class="d-block w-100">
                <div class="carousel-caption">
                    <h5>Watch the latest MLB Matches</h5>
                    <p>Experience the thrill of the game, up close and personal.</p>
                    <a href="#" class="btn btn-primary carousel-button">Learn More</a>
                </div>
            </div>
            <div class="carousel-item">
                <img src="{{ asset('storage/images/home_banners/sports_event_banner_10.jpg') }}" alt="Advertisement Banner 2" class="d-block w-100">
                <div class="carousel-caption">
                    <h5>One World, One Game, One Passion. </h5>
                    <p>Book your seats today and feel the Celebration!</p>
                    <a href="#" class="btn btn-primary carousel-button">Sign Up Now</a>
                </div>
            </div>
            <div class="carousel-item">
                <img src="{{ asset('storage/images/home_banners/sports_event_banner_11.jpg') }}" alt="Advertisement Banner 2" class="d-block w-100">
                <div class="carousel-caption">
                    <h5>Feel the Rush of the Race</h5>
                    <p>Reserve the Best viewing Today!  </p>
                    <a href="#" class="btn btn-primary carousel-button">Book Now</a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Title Description Section --}}
<div class="container mt-5">
    <div class="row justify-content-center text-center">
        <div class="col-12 col-md-8">
            <div class="section-heading">
                @auth
                    <h2>WELCOME, {{ strtoupper(auth()->user()->name) }}!</h2>
                @else
                    <h2>SPORTS EVENT MANAGEMENT SYSTEM</h2>
                @endauth
                <p class="text-muted">Our platform caters to the needs of three key actors: admin, organizers, and users. We efficiently manage diverse sports events across six categories. Whether you're a sports enthusiast seeking the thrill of competitive events, an organizer striving to streamline event management, or a user eager to engage with your favorite sports, we've got you covered.</p>
            </div>
        </div>
    </div>
</div>


<div class="container home-container mt-5">

    {{-- Categories Section --}}
    <div class="home-categories-section">
        <div class="home-categories-header text-center mb-4"></div>
        <div class="row">
            @foreach ($categories as $category)
                <div class="col-md-4 mb-4">
                    <div class="category-box">
                        <a href="{{ route('events.index', ['category' => $category]) }}" class="category-link">
                            <div class="interactive-background-image" style="background-image: url('{{ $categoryImageUrls[$category] }}');">
                                <div class="category-label">{{ $categoryLabels[$category] }}</div>
                            </div>
                            <div class="category-hover row">
                                <h4>{{ $categoryLabels[$category] }}</h4>
                                <span class="category-description-hover">{{ $categoryDescriptions[$category] }}</span>
                            </div>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Popular Events --}}
    <div class="home-popular-events-section mt-5">
        <div class="home-categories-header text-center mb-4">
            <h2>Popular Events</h2>
        </div>
        <div class="row">
            @foreach ($events->take(6) as $event)
                <div class="col-md-4 mb-4">
                    <div class="card home-event-card h-100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title home-event-title">{{ $event->name }}</h5>
                            <p class="card-text home-event-description mb-4">{{ Str::limit($event->description, 50) }}</p>
                            <p class="card-text"><strong class="semi-bold">No. of Sessions:</strong> {{ $event->sessions->count() }} </p> {{-- Number of sessions --}}
                            <p class="card-text"><strong class="semi-bold">Location:</strong> {{ $event->city }}, {{ $event->state }}</p>
                            <a href="{{ route('events.show', $event->id) }}" class="btn btn-info mt-auto">
                                <i class="fas fa-info-circle"></i> More Details
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('events.index') }}" class="btn btn-primary more-event-btn">
                More Events <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>
@endsection


