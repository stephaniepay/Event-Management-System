@extends('layout')

@section('title', 'Voting Details')

@section('content')
    <div class="container mt-3">

        <div class="page-title-container">
            <h1 class="mb-5">Voting Details</h1>
        </div>

        <div class="row">
            @forelse ($votingHistory as $vote)
                <div class="col-md-6">
                    <div class="card voted-card mb-4 @if($vote->player->is_winner()) winner-card @endif">
                        <img src="{{ asset('storage/images/team_players/' . $vote->player->picture_filename) }}" class="card-img-top" alt="{{ $vote->player->name }}">
                        <div class="card-body voted-card-body">
                            <div class="title-winner-container">
                                <h5 class="card-title voted-card-title">{{ $vote->player->name }}</h5>
                            </div>
                            <p class="card-text voted-card-text">{{ $vote->eventSession->event->name }}</p>
                            <p class="card-text voted-card-text">Player ID: {{ $vote->player->id }}</p>
                            <p class="card-text voted-card-text">Session ID: {{ $vote->event_session_id }}</p>
                            <p class="card-text voted-card-text">Vote Date: {{ $vote->created_at->format('d-m-Y') }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <p>No voting history found.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-5">
            <a href="{{ route('profile') }}" class="btn btn-secondary mb-3">
                <i class="fas fa-arrow-left"></i> Back to Profile
            </a>
        </div>
    </div>
@endsection
