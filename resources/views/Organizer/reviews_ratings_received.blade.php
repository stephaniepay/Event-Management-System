@extends('layout')

@section('title', 'Reviews & Ratings Received')

@section('content')
<div class="container mt-3">

    <div class="reviews-ratings-container">
        <h1>Reviews & Ratings Received</h1>
        <div class="table-responsive">
            <table class="table table-striped mt-4">
                <thead>
                    <tr>
                        <th>Event Name</th>
                        <th>User</th>
                        <th>Rating</th>
                        <th>Review</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reviews as $review)
                        <tr>
                            <td>{{ $review->event->name }}</td>
                            <td>{{ $review->user->name }}</td>
                            <td>{{ $review->rating }}/5</td>
                            <td>{{ $review->review }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">No reviews received yet.</td>
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
