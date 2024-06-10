@extends('layout')

@section('title', 'Event Category Statistics')

@section('content')
<div class="container mt-3">
    <h1>Event Category Statistics</h1>

    <div class="table-responsive">
        <table class="table table-striped mt-5">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Event Count</th>
                    <th>Sessions Count</th>
                    <th>Attendees Count</th>
                    <th>Total Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $categoryName => $details)
                    <tr>
                        <td>{{ $details['categoryName'] }}</td>
                        <td>{{ $details['eventCount'] }}</td>
                        <td>{{ $details['sessionsCount'] }}</td>
                        <td>{{ $details['totalAttendees'] }}</td>
                        <td>RM {{ number_format($details['totalRevenue'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-5">
        <a href="{{ route('profile.admin') }}" class="btn btn-secondary mb-3">
            <i class="fas fa-arrow-left"></i> Back to Profile
        </a>
    </div>
</div>
@endsection
