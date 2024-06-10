@extends('layout')

@section('title', 'Event Statistics')

@section('content')
<div class="container mt-3">
    <h1>Event Statistics</h1>


    <div class="input-group my-4">
        <input type="text" class="form-control" placeholder="Search for events..." onkeyup="filterEvents()">
        <div class="input-group-append">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped mt-5" id="events-statistics-table">
            <thead>
                <tr>
                    <th>Event Name</th>
                    <th>Category</th>
                    <th>Sessions Count</th>
                    <th>Attendees Count</th>
                    <th>Total Revenue</th>
                    <th>Price Per Seat</th>
                </tr>
            </thead>
            <tbody>
                @foreach($events as $event)
                    <tr>
                        <td>{{ $event['name'] }}</td>
                        <td>{{ $event['category'] }}</td>
                        <td>{{ $event['sessionsCount'] }}</td>
                        <td>{{ $event['attendeesCount'] }}</td>
                        <td>RM {{ number_format($event['totalRevenue'], 2) }}</td>
                        <td>RM {{ number_format($event['pricePerSeat'], 2) }}</td>
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

