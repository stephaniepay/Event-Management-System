{{-- ADMIN --}}

@extends('layout')

@section('title', 'Event List')

@section('content')
    <div class="container mt-3">

        <div class="event-list-container">
            <div class="event-list-title mb-4">
                <h1>Event List</h1>
            </div>

            @if(session('info'))
                <div class="alert alert-info">{{ session('info') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Created at</th>
                            <th>Updated at</th>
                            <th>Published by</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($events as $event)
                            <tr>
                                <td>{{ $event->id }}</td>
                                <td>{{ $event->name }}</td>
                                <td>{{ $event->created_at->format('Y-m-d H:i') }}</td>
                                <td>{{ $event->updated_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    @if($event->organizer_id === null)
                                        Admin
                                    @elseif($event->organizer)
                                        {{ $event->organizer->user->name }}
                                    @else
                                        Unknown
                                    @endif
                                </td>

                                <td>
                                    <a href="{{ route('events.edit', $event->id) }}" class="btn btn-primary">Edit</a>
                                    <form action="{{ route('events.destroy', $event->id) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger delete-event-btn">Delete</button>
                                    </form>
                                    <a href="{{ route('events.show', $event->id) }}" class="btn btn-info mt-auto more-details-btn">Details</a>
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
