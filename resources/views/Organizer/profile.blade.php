{{-- ORGANIZER --}}
@extends('layout')

@section('title', 'My Profile')

@section('content')
<div class="container mt-3">
    <div class="profile-header mb-4">
        <h1>My Profile</h1>
        <p>You're signed in as Organizer {{ $organizer->name }}</span>
    </div>

    @if(session()->has('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if(session()->has('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            Profile Information
        </div>
        <div class="card-body d-flex justify-content-between align-items-center">
            <div class="user-info">
                <h4 class="user-name">{{ $user->name }}</h4>
                <p class="user-email">{{ $user->email }}</p>
            </div>
            <div>
                <a href="{{ route('profile.organizer.edit') }}" class="btn btn-edit-profile">Edit Profile</a>
            </div>
        </div>
    </div>

    <div class="organizer-dashboard-container mt-5">
        <h3>Dashboard</h3>
        <div class="organizer-metrics">

        {{-- Events Managed --}}
        <div class="metrics-card">
            <a href="{{ route('profile.events') }}" class="metrics-card-link">
                <i class="fas fa-calendar-alt metrics-icon"></i>
                <div class="metrics-info">
                    <strong>{{ count($events) }}</strong>
                    Events Hosted
                </div>
            </a>
        </div>

        {{-- Sessions Managed --}}
        <div class="metrics-card">
            <a href="{{ route('profile.sessions') }}" class="metrics-card-link">
                <i class="fas fa-calendar-check metrics-icon"></i>
                <div class="metrics-info">
                    <strong>{{ $sessionsManaged }}</strong>
                    Sessions Managed
                </div>
            </a>
        </div>

        {{-- Total Registrations --}}
        <div class="metrics-card">
            <a href="{{ route('profile.total-registrations') }}" class="metrics-card-link">
                <i class="fas fa-users metrics-icon"></i>
                <div class="metrics-info">
                    <strong>{{ $totalRegistrations }}</strong>
                    Total Registrations
                </div>
            </a>
        </div>

        {{-- Reviews Ratings Received --}}
        <div class="metrics-card">
            <a href="{{ route('profile.reviews-ratings') }}" class="metrics-card-link">
                <i class="fas fa-star-half-alt metrics-icon"></i>
                <div class="metrics-info">
                    <strong>{{ $averageRating }}/5</strong>
                    Average Rating & Feedback
                </div>
            </a>
        </div>

        {{-- Most Popular Event --}}
        <div class="metrics-card">
            <a href="{{ route('profile.events.popularity') }}" class="metrics-card-link">
                <i class="fas fa-trophy metrics-icon"></i>
                <div class="metrics-info">
                    <strong>{{ $mostPopularEvent }}</strong>
                    Most Popular Event
                </div>
            </a>
        </div>

        {{-- Total Revenues --}}
        <div class="metrics-card">
            <a href="{{ route('profile.revenues') }}" class="metrics-card-link">
                <i class="fas fa-chart-bar metrics-icon"></i>
                <div class="metrics-info">
                    <strong>{{ $totalRevenues }}</strong>
                    Total Revenues
                </div>
            </a>
        </div>

    </div>
</div>
@endsection
