@extends('layout')

@section('title', 'Select Seat')

@section('content')
<div class="container mt-3">
    <h2>Session ID: {{ $session->id }}</h2>

    <div class="seat-map-legend mt-4">
        <div class="legend-item"><span class="legend-color available"></span> Available</div>
        <div class="legend-item"><span class="legend-color unavailable"></span> Booked</div>
        <div class="legend-item"><span class="legend-color selected-cart"></span> Selected</div>
    </div>


    <div class="seat-map">
        <div class="seat-section upper">
            @foreach(['upper'] as $section)
                <div class="seat-section {{ $section }}">
                    @foreach(array_reverse($seatMap[$section]) as $row => $seats)
                        <div class="row-container">
                            <span class="row-label">{{ $row }}</span>
                            <div class="row">
                                @foreach($seats as $seat)
                                    <button class="seat {{ $seat->is_booked ? 'unavailable' : 'available' }}"
                                        data-seat-id="{{ $seat->id }}"
                                        style="cursor: {{ $seat->is_booked ? 'pointer' : 'not-allowed' }}"
                                        @if($seat->is_booked) onclick="showSeatDetails('{{ $seat->id }}')" @endif>
                                    {{ $seat->label }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>


        <div class="{{ $stadiumClass }}">
            <span class="stadium-label">{{ $stadiumLabel }}</span>
        </div>

        <div class="seat-section lower">
            @foreach(['lower'] as $section)
                <div class="seat-section {{ $section }}">
                    @foreach($seatMap[$section] as $row => $seats)
                        <div class="row-container">
                            <span class="row-label">{{ $row }}</span>
                            <div class="row">
                                @foreach($seats as $seat)
                                    <button class="seat {{ $seat->is_booked ? 'unavailable' : 'available' }}"
                                        data-seat-id="{{ $seat->id }}"
                                        style="cursor: {{ $seat->is_booked ? 'pointer' : 'not-allowed' }}"
                                        @if($seat->is_booked) onclick="showSeatDetails('{{ $seat->id }}')" @endif>
                                    {{ $seat->label }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function showSeatDetails(seatId) {
        window.location.href = `/seat-details/${seatId}`
    }
</script>
@endsection
