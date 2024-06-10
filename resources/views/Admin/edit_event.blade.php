<script src="{{ asset('js/edit_event.js') }}"></script>

@extends('layout')

@section('title', 'Edit Event')

@section('content')
    <div class="container mt-3">
        <h1 class="mb-4">Edit Event</h1>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('events.update', $event->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-3" id="event-name">
                <label for="name" class="form-label"><h4>Event Name</h4></label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $event->name) }}">
            </div>

            <div class="mb-3" id="event-category">
                <label for="category" class="form-label"><h4>Event Category</h4></label>
                <select class="form-control" id="category" name="category" required onchange="updateCategoryDescription()">
                    <option value="">Choose a category</option>
                    <option value="teamSports" {{ (old('category', $event->category ?? '') == 'teamSports') ? 'selected' : '' }}>Team Sports</option>
                    <option value="individualSports" {{ (old('category', $event->category ?? '') == 'individualSports') ? 'selected' : '' }}>Individual Sports</option>
                    <option value="racquetSports" {{ (old('category', $event->category ?? '') == 'racquetSports') ? 'selected' : '' }}>Racquet Sports</option>
                    <option value="waterSports" {{ (old('category', $event->category ?? '') == 'waterSports') ? 'selected' : '' }}>Water Sports</option>
                    <option value="outdoorAdventure" {{ (old('category', $event->category ?? '') == 'outdoorAdventure') ? 'selected' : '' }}>Outdoor & Adventure</option>
                    <option value="fitnessHealth" {{ (old('category', $event->category ?? '') == 'fitnessHealth') ? 'selected' : '' }}>Fitness & Health</option>
                </select>
                <small id="category-description" class="form-text text-muted mt-2"></small>
            </div>


            <div class="mb-3" id="event-description">
                <label for="description" class="form-label"><h4>Description</h4></label>
                <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $event->description) }}</textarea>
            </div>

            <div class="mb-3" id="event-address">
                <label for="address" class="form-label"><h4>Location Address</h4></label>
                <div class="address-input-container" style="background: rgba(255, 255, 255, 0.5); padding: 0px; border-radius: 5px;">
                    <input type="text" class="form-control mb-3" style="background: rgba(255, 255, 255, 0.5);" id="address_line" name="address_line" placeholder="Address Line" value="{{ old('address_line', $event->address_line) }}" required>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" style="background: rgba(255, 255, 255, 0.5);" id="city" name="city" placeholder="City" value="{{ old('city', $event->city) }}" required>
                        </div>

                        <div class="col-md-4">
                            <input type="text" class="form-control" style="background: rgba(255, 255, 255, 0.5);" id="state" name="state" placeholder="State" value="{{ old('state', $event->state) }}" required>
                        </div>

                        <div class="col-md-4">
                            <input type="text" class="form-control" style="background: rgba(255, 255, 255, 0.5);" id="zip_code" name="zip_code" placeholder="Zip Code" value="{{ old('zip_code', $event->zip_code) }}" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-3" id="event-location">
                <label for="latitude" class="form-label"><h4>Location Coordinates</h4></label>
                <div class="row g-3">
                    <div class="col-md-6">
                        <input type="text" class="form-control" id="latitude" name="latitude" placeholder="Latitude" value="{{ old('latitude', $event->latitude) }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control" id="longitude" name="longitude" placeholder="Longitude" value="{{ old('longitude', $event->longitude) }}" readonly>
                    </div>
                </div>
            </div>

            <div class="mb-3" id="event-price-per-seat">
                <label for="price_per_seat" class="form-label"><h4>Price Per Seat</h4></label>
                <input type="number" class="form-control" id="price_per_seat" name="price_per_seat" placeholder="Min: 10, Max: 500" value="{{ old('price_per_seat', $event->price_per_seat ?? '') }}" required>
            </div>

            <div class="mb-3" id="event-capacity">
                <label for="max_capacity_per_session" class="form-label"><h4>Max Capacity Per Session</h4></label>
                <input type="number" class="form-control" id="max_capacity_per_session" name="max_capacity_per_session" placeholder="Min: 1, Max: 1000" value="{{ old('max_capacity_per_session', $event->max_capacity_per_session) }}" required>
            </div>

            <div class="mb-3" id="event-sessions">
                <label for="event_sessions" class="form-label"><h4>Event Sessions</h4></label>
                <div>
                    <button type="button" id="add-session-edit-event-btn" class="btn btn-success btn-sm add-session-btn">
                        <i class="fas fa-plus-circle me-1"></i> Add Session
                    </button>
                    <div class="session-list mt-3" id="sessions-list">
                        @foreach($event->sessions as $index => $session)
                            <div class="session" data-index="{{ $index }}">
                                <strong>Session {{ $index + 1 }}:</strong>
                                <input type="hidden" name="sessions[existing][{{ $session->id }}][id]" value="{{ $session->id }}">
                                <label>Date:</label>
                                <input type="text" class="session-date-picker" name="sessions[existing][{{ $session->id }}][date]" value="{{ $session->start_time->format('Y-m-d') }}" required>
                                <label>Start Time:</label>
                                <input type="text" class="start-time-picker" name="sessions[existing][{{ $session->id }}][start_time]" value="{{ $session->start_time->format('H:i') }}" required>
                                <label>End Time:</label>
                                <input type="text" class="end-time-picker" name="sessions[existing][{{ $session->id }}][end_time]" value="{{ $session->end_time->format('H:i') }}" required>
                                <label>
                                    <input type="checkbox" name="deleted_sessions[]" value="{{ $session->id }}"> Delete
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>


            <div class="mb-3" id="event-images">
                <label for="event_images" class="form-label"><h4>Event Images</h4></label>
                <input type="file" class="form-control" id="event_images" name="event_images[]" multiple>
                <div class="event-img-container mt-2">
                    @foreach($event->images as $image)
                        <div class="image-container">
                            <img src="{{ asset('storage/images/event/' . $image->filename) }}" width="100" alt="Event Image">
                            <input type="checkbox" name="delete_images[]" value="{{ $image->id }}"> Delete
                        </div>
                        <br>
                    @endforeach
                </div>
            </div>
            <div class="mt-3 text-end">
                <button type="submit" class="btn btn-primary">Update Event</button>
            </div>
        </form>
    </div>
@endsection




@section('scripts')
<script>
    var eventEdit = {
        sessions: @json($event->sessions)
    };
</script>
@endsection
