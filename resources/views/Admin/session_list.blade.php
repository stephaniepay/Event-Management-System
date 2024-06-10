@extends('layout')

@section('title', 'Session List')

@section('content')
    <div class="container mt-3">

        <div class="session-header mb-3">
            <h1>Session List</h1>
        </div>

        <div class="input-group mb-4">
            <input type="text" name="eventName" class="form-control" placeholder="Search for sessions..." id="session-search" onkeyup="filterSessions()">
            <div class="input-group-append">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped" id="session-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Event Name</th>
                        <th>Datetime</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sessions as $session)
                        <tr>
                            <td>{{ $session->id }}</td>
                            <td>{{ $session->event->name }}</td>
                            <td>
                                {{ $session->start_time->format('d-m-Y') }},
                                {{ $session->start_time->format('H:i') }} -
                                {{ $session->end_time->format('H:i') }}
                            </td>

                            <td>{{ $session->status }}</td>
                            <td>
                                @if(auth()->user()->isAdmin() || auth()->user()->id === $session->event->organizer_id)
                                    <a href="{{ route('team-players.create', ['sessionId' => $session->id]) }}" class="btn btn-primary">Manage Players</a>

                                    @if( $session->status == 'Past')
                                        <a href="{{ route('sessions.showSelectWinner', $session->id) }}" class="btn btn-primary">Select Winner</a>
                                    @else
                                        <button class="not-allowed btn-disabled" disabled data-toggle="tooltip" title="Winner can only be selected for past sessions.">Select Winner</button>
                                    @endif

                                @else
                                    <button class="not-allowed btn-disabled" disabled data-toggle="tooltip" title="You cannot manage players for sessions not hosted by you.">Manage Players</button>
                                    <button class="not-allowed btn-disabled" disabled data-toggle="tooltip" title="You cannot select a winner for sessions not hosted by you.">Select Winner</button>
                                @endif

                                <a href="{{ route('session.select-seat', $session->id) }}" class="btn btn-info">View Seats</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if(request()->routeIs('profile.sessions.list'))
            <div class="mt-5">
                <a href="{{ route('profile.admin') }}" class="btn btn-secondary mb-3">
                    <i class="fas fa-arrow-left"></i> Back to Profile
                </a>
            </div>
        @endif
    </div>
@endsection

