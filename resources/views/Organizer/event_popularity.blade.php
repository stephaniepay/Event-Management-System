@extends('layout')

@section('title', 'Event Popularity')

@section('content')
<div class="container mt-4">

    <div class="event-popularity-container">
        <h1>Event Popularity</h1>
        <div class="table-responsive">
            <table class="table table-bordered mt-4">
                <thead>
                    <tr>
                        <th>Event Name</th>
                        <th>Total Registrations</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($events as $event)
                        <tr>
                            <td>{{ $event['event_name'] }}</td>
                            <td>{{ $event['total_registrations'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($mostPopularEvent)
            <div class="alert alert-success mt-4">
                <strong>Most Popular Event: </strong> {{ $mostPopularEvent['event_name'] }}
                ({{ $mostPopularEvent['total_registrations'] }} registrations)
            </div>
        @endif
    </div>

    <div class="mt-5">
        <a href="{{ route('profile.organizer') }}" class="btn btn-secondary mb-3">
            <i class="fas fa-arrow-left"></i> Back to Profile
        </a>
    </div>
</div>
@endsection
