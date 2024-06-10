@extends('layout')

@section('title', 'Event Popularity')

@section('content')
<div class="container mt-3">

    <div class="event-popularity-container">
        <h1>Total Revenues by Event</h1>
        <div class="table-responsive">
            <table class="table table-striped mt-4">
                <thead>
                    <tr>
                        <th>Event Name</th>
                        <th>Sessions Count</th>
                        <th>Total Registrations</th>
                        <th>Total Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sortedRevenuesByEvent as $revenue)
                        <tr>
                            <td>{{ $revenue['event_name'] }}</td>
                            <td>{{ $revenue['sessions_count'] }}</td>
                            <td>{{ $revenue['total_registrations'] }}</td>
                            <td>RM {{ number_format($revenue['total_revenue'], 2) }}</td>
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
