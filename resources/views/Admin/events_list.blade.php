{{-- ADMIN --}}

@extends('layout')

@section('title', 'Event List')

@section('content')
    <div class="container mt-3">

        <div class="event-list-title mb-4">
            <h1>Event List</h1>
        </div>

        @if(session('info'))
            <div class="alert alert-info">{{ session('info') }}</div>
        @endif

        <div class="row mb-5 filter-form">
            <form action="{{ route('events.index') }}" method="GET" class="w-100">
                <div class="row">

                    {{-- Category Filter --}}
                    <div class="col-md-4 mb-3 mb-md-0">
                        <select name="category" class="form-control">
                            <option value="all">All Categories</option>
                            <option value="teamSports">Team Sports</option>
                            <option value="individualSports">Individual Sports</option>
                            <option value="racquetSports">Racquet Sports</option>
                            <option value="waterSports">Water Sports</option>
                            <option value="outdoorAdventure">Outdoor & Adventure</option>
                            <option value="fitnessHealth">Fitness & Health</option>
                        </select>
                    </div>

                    {{-- Event Starting In 3 Days and 7 Days Filter --}}
                    <div class="col-md-4 mb-3 mb-md-0">
                        <select name="starting_time" class="form-control">
                            <option value="">All Time</option>
                            <option value="3">Starting in 3 Days</option>
                            <option value="7">Starting in 7 Days</option>
                        </select>
                    </div>

                    {{-- State Filter --}}
                    <div class="col-md-3 mb-3 mb-md-0">
                        <select name="state" class="form-control">
                            <option value="all">All States</option>
                            @foreach ($states as $state)
                                <option value="{{ $state }}">{{ $state }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </div>
            </form>
        </div>

        <a href="{{ route('events.create') }}" class="btn btn-success mb-2 add-event-btn">
            <i class="fas fa-plus-circle me-1"></i> Add New Event
        </a>

        @if(isset($noEvents) && $noEvents)
            <div class="alert alert-info">No events found for this organizer.</div>
        @else
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Created at</th>
                            <th>Status</th>
                            <th>Published by</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($events as $event)
                            <tr>
                                <td>{{ $event->id }}</td>
                                <td>{{ $event->name }}</td>
                                <td>{{ $event->created_at->format('Y-m-d H:i') }}</td>
                                <td>{{ $event->status }}</td>
                                <td>
                                    @if($event->organizer_id === null)
                                        Admin
                                    @elseif($event->organizer)
                                        {{ $event->organizer->user->name }}
                                        {{-- ({{ $event->organizer->name }}) Organization Name --}}
                                    @else
                                        Unknown
                                    @endif
                                </td>

                                <td>
                                    <div>
                                        @if(auth()->user()->isAdmin() || auth()->user()->id === $event->organizer_id)
                                            <a href="{{ route('events.edit', $event->id) }}" class="btn btn-primary">Edit</a>

                                            <form action="{{ route('events.destroy', $event->id) }}" method="POST" style="display: inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger delete-event-btn">Delete</button>
                                            </form>
                                        @else
                                            <button class="not-allowed btn-disabled" disabled data-toggle="tooltip" title="You cannot edit events hosted by other organizers.">Edit</button>
                                            <button class="not-allowed btn-disabled" disabled data-toggle="tooltip" title="You cannot delete events hosted by other organizers.">Delete</button>
                                        @endif
                                        <a href="{{ route('events.show', $event->id) }}" class="btn btn-info mt-auto more-details-btn">Details</a>
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <div class="col">
                                <div class="alert alert-info">No events found matching your filters.</div>
                            </div>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
