<script src="{{ asset('js/add_event.js') }}"></script>

@extends('layout')

@section('title', 'Add Event')

@section('content')
    <div class="container mt-3">
        <h1 class="mb-4">Add New Event</h1>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3" id="event-name">
                <label for="name" class="form-label"><h4>Event Name</h4></label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>

            <div class="mb-3" id="event-category">
                <label for="category" class="form-label"><h4>Event Category</h4></label>
                <select class="form-control" id="category" name="category" required onchange="updateCategoryDescription()">
                    <option value="">Choose a category</option>
                    <option value="teamSports">Team Sports</option>
                    <option value="individualSports">Individual Sports</option>
                    <option value="racquetSports">Racquet Sports</option>
                    <option value="waterSports">Water Sports</option>
                    <option value="outdoorAdventure">Outdoor & Adventure</option>
                    <option value="fitnessHealth">Fitness & Health</option>
                </select>
                <small id="category-description" class="form-text text-muted mt-2"></small>
            </div>


            <div class="mb-3" id="event-description">
                <label for="description" class="form-label"><h4>Description</h4></label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>

            <div class="mb-3" id="event-address">
                <label for="address" class="form-label"><h4>Location Address</h4></label>
                <div class="address-input-container" style="background: rgba(255, 255, 255, 0.5); padding: 0px; border-radius: 5px;">
                    <input type="text" class="form-control mb-3" style="background: rgba(255, 255, 255, 0.5);" id="address_line" name="address_line" placeholder="Address Line" required>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" style="background: rgba(255, 255, 255, 0.5);" id="city" name="city" placeholder="City" required>
                        </div>

                        <div class="col-md-4">
                            <input type="text" class="form-control" style="background: rgba(255, 255, 255, 0.5);" id="state" name="state" placeholder="State" required>
                        </div>

                        <div class="col-md-4">
                            <input type="text" class="form-control" style="background: rgba(255, 255, 255, 0.5);" id="zip_code" name="zip_code" placeholder="Zip Code" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-3" id="event-location">
                <label for="latitude" class="form-label"><h4>Location Coordinates</h4></label>
                <div class="row g-3">
                    <div class="col-md-6">
                        <input type="text" class="form-control" id="latitude" name="latitude" placeholder="Latitude" readonly>
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control" id="longitude" name="longitude" placeholder="Longitude" readonly>
                    </div>
                </div>
            </div>

            <div class="mb-3" id="event-price-per-seat">
                <label for="price_per_seat" class="form-label"><h4>Price Per Seat</h4></label>
                <input type="number" class="form-control" id="price_per_seat" name="price_per_seat" placeholder="Min: 10, Max: 500" required>
            </div>


            <div class="mb-3" id="event-capacity">
                <label for="max_capacity_per_session" class="form-label"><h4>Max Capacity Per Session</h4></label>
                <input type="number" class="form-control" id="max_capacity_per_session" name="max_capacity_per_session" placeholder="Min: 1, Max: 1000" required>
            </div>


            <div class="mb-3" id="event-sessions">
                <label for="event_sessions" class="form-label"><h4>Event Sessions</h4></label>
                <div>
                    <button type="button" id="add-session-btn" class="btn btn-success btn-sm add-session-btn">
                        <i class="fas fa-plus-circle me-1"></i> Add Session
                    </button>
                    <div id="sessions-list"></div>
                </div>
            </div>


            <div class="mb-3" id="event-images">
                <label for="event_images" class="form-label"><h4>Event Images</h4></label>
                <input type="file" class="form-control" id="event_images" name="event_images[]" multiple required>
            </div>


            <div class="mt-5 text-end">
                <button type="submit" class="btn btn-primary">Add Event</button>
            </div>
        </form>
    </div>
@endsection

