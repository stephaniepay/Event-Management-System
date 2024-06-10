{{-- ADMIN --}}

@extends('layout')

@section('title', 'My Profile')

@section('content')

<div class="container mt-3">

    <div class="profile-header mb-4">
        <h1>My Profile</h1>
        <p>You're signed in as Admin</span>
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
                <a href="{{ route('profile.admin.edit') }}" class="btn btn-edit-profile">Edit Profile</a>
            </div>
        </div>
    </div>

    <div class="admin-dashboard-container mt-5">
        <h3>Dashboard</h3>
        <div class="admin-metrics">

            {{-- Pending Organizer Requests --}}
            <div class="metrics-card">
                <a href="{{ route('profile.organizer.manage') }}" class="metrics-card-link">
                    <i class="fas fa-user-friends metrics-icon"></i>
                    <div class="metrics-info">
                        <strong>{{ $organizerRequests }}</strong>
                        Pending Organizer Requests
                    </div>
                </a>
            </div>

            {{-- Sessions Managed  --}}
            <div class="metrics-card">
                <a href="{{ route('profile.sessions.list', ['adminOnly' => 'true']) }}" class="metrics-card-link">
                    <i class="fas fa-calendar-check metrics-icon"></i>
                    <div class="metrics-info">
                        <strong>{{ $sessionsManaged }}</strong>
                        Sessions Managed
                    </div>
                </a>
            </div>

            {{-- Total Transactions --}}
            <div class="metrics-card">
                <a href="{{ route('profile.recentOrders') }}" class="metrics-card-link">
                    <i class="fas fa-dollar-sign metrics-icon"></i>
                    <div class="metrics-info">
                        <strong>{{ number_format($totalTransactions, 2) }}</strong>
                        Total Transactions
                    </div>
                </a>
            </div>

            {{-- New User Sign Ups --}}
            <div class="metrics-card">
                <a href="{{ route('profile.user.list') }}" class="metrics-card-link">
                    <i class="fas fa-user-plus metrics-icon"></i>
                    <div class="metrics-info">
                        <strong>{{ $newUserSignUps }}</strong>
                        New User Sign Ups
                    </div>
                </a>
            </div>

            {{-- Top Purchased User --}}
            <div class="metrics-card">
                <a href="{{ route('profile.user.list') }}" class="metrics-card-link">
                    <i class="fas fa-shopping-cart metrics-icon"></i>
                    <div class="metrics-info">
                        <strong>{{ $topPurchasedUsers['name'] ?? 'N/A' }}</strong>
                        Top Purchaser
                    </div>
                </a>
            </div>

            {{-- Most Popular Team Player --}}
            <div class="metrics-card">
                <a href="{{ route('profile.team-players.list') }}" class="metrics-card-link">
                    <i class="fas fa-trophy metrics-icon"></i>
                    <div class="metrics-info">
                        <strong>{{ $mostPopularTeamPlayer->name ?? 'N/A' }}</strong>
                        Most Popular Team Player
                    </div>
                </a>
            </div>

            {{-- Most Popular Event --}}
            <div class="metrics-card">
                <a href="{{ route('profile.event.statistics') }}" class="metrics-card-link">
                    <i class="fas fa-star metrics-icon"></i>
                    <div class="metrics-info">
                        <strong>{{ $mostPopularEventName }}</strong>
                        Most Popular Event
                    </div>
                </a>
            </div>

            {{-- Most Popular Event Category --}}
            <div class="metrics-card">
                <a href="{{ route('profile.eventCategory.statistics') }}" class="metrics-card-link">
                    <i class="fas fa-chart-bar metrics-icon"></i>
                    <div class="metrics-info">
                        <strong>{{ $mostPopularEventCategoryName }}</strong>
                        Most Popular Event Category
                    </div>
                </a>
            </div>
        </div>
    </div>

@endsection
