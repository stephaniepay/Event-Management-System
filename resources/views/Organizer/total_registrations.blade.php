@extends('layout')

@section('title', 'Total Registrations')

@section('content')
<div class="container mt-3">

    <div class="reviews-ratings-container">
        <h1>Total Registrations</h1>
        <div class="table-responsive">
            <table class="table table-striped mt-4">
                <thead>
                    <tr>
                        <th>User Name</th>
                        <th>User Email</th>
                        <th>Event Name</th>
                        <th>Session Datetime</th>
                        <th>Seats Booked</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($registrations as $registration)
                        <tr>
                            <td>{{ $registration['user_name'] }}</td>
                            <td>{{ $registration['user_email'] }}</td>
                            <td>{{ $registration['event_name'] }}</td>
                            <td>{{ $registration['session_date'] }}, {{ $registration['session_time'] }}</td>
                            <td>
                                {{ implode(', ', $registration['seats_booked']) }}
                                <span class="badge bg-secondary">{{ $registration['seats_booked_count'] }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">No registrations found.</td>
                        </tr>
                    @endforelse
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
