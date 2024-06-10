@extends('layout')

@section('title', 'Session List')

@section('content')
<div class="container mt-3">

    <div class="session-list-container">

        <div class="session-list-title mb-4">
            <h1>Session List</h1>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Session ID</th>
                        <th>Event Name</th>
                        <th>Date</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sessions as $session)
                        <tr>
                            <td>{{ $session->id }}</td>
                            <td>{{ $session->event->name }}</td>
                            <td>{{ $session->start_time->format('d-m-Y') }}</td>
                            <td>{{ $session->start_time->format('g:ia') }}</td>
                            <td>{{ $session->end_time->format('g:ia') }}</td>
                            <td>
                                <a href="{{ route('team-players.create', ['sessionId' => $session->id]) }}" class="btn btn-primary">Manage Players</a>
                                @if( $session->status == 'Past')
                                    <a href="{{ route('sessions.showSelectWinner', $session->id) }}" class="btn btn-primary">Select Winner</a>
                                @else
                                    <button class="not-allowed btn-disabled" disabled data-toggle="tooltip" title="Winner can only be selected for past sessions.">Select Winner</button>
                                @endif
                                <a href="{{ route('session.select-seat', $session->id) }}" class="btn btn-info">View Seats</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">
        <a href="{{ route('profile.organizer') }}" class="btn btn-secondary mb-3">
            <i class="fas fa-arrow-left"></i> Back to Profile
        </a>
    </div>
</div>
@endsection

