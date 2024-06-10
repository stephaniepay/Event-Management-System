{{-- ADMIN --}}

@extends('layout')

@section('title', 'Organizer List')

@section('content')
<div class="container mt-3">
    <h1 class="mb-4">Organizers</h1>
    <div class="row">
        @forelse ($organizers as $organizer)
            <div class="col-md-4 d-flex align-items-stretch">
                <div class="card mb-3 text-center">
                    <div class="card-avatar-container mx-auto">
                        <img src="{{ Storage::url('images/organizer_profiles/' . $organizer->organizer->picture_filename) }}"
                        alt="Profile Picture" class="card-avatar rounded-circle">
                    </div>
                    <div class="card-body">
                        <h3 class="card-title organizer-name">{{ $organizer->organizer->name }}</h3>
                        <p class="text-muted contact-label">Contact Person:</p>
                        <p class="card-text contact-name">{{ $organizer->name }}</p>
                        <p class="card-text">{{ $organizer->email }}</p>

                        <div class="organizer-cards-btn">
                            <a href="{{ route('organizer.events', $organizer->id) }}" class="btn btn-primary mt-auto view-event-btn">View Events</a>
                            <form action="{{ route('organizer.delete', $organizer->id) }}" method="POST" class="d-inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-delete-organizer">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <p>There are no organizer requests at the moment.</p>
        @endforelse
    </div>
</div>
@endsection


