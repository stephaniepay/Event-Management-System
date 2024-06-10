@extends('layout')

@section('title', 'Seat Details')

@section('content')
<div class="container mt-3">
    <h1>Seat Details for Seat ID: {{ $seat->id }}</h1>

    @php
        $eventSessionId = explode('_', $seat->id)[0];
    @endphp

    <div class="table-responsive seat-details mt-4">
        <table class="table table-bordered ">
            <tbody>
                <tr>
                    <th>User ID</th>
                    <td>{{ optional($seat->order->user)->id ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Username</th>
                    <td>{{ optional($seat->order->user)->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Event Name</th>
                    <td>{{ optional($seat->eventSession->event)->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Session ID</th>
                    <td>{{ $eventSessionId }}</td>
                </tr>
                <tr>
                    <th>Seat Section</th>
                    <td>{{ $seat->section ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Row</th>
                    <td>{{ $seat->row ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Number</th>
                    <td>{{ $seat->number ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Order ID</th>
                    <td>{{ $seat->order_id ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Order Created At</th>
                    <td>{{ optional($seat->order)->created_at->format('d-m-Y H:i') ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Payment ID</th>
                    <td>{{ optional($seat->order->payment)->id ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Payment Type</th>
                    <td>{{ optional($seat->order->payment)->payment_type ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Payment Amount</th>
                    <td>{{ optional($seat->order->payment)->amount ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Payment Status</th>
                    <td>{{ optional($seat->order->payment)->status ?? 'N/A' }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="mt-5">
        <a href="{{ route('session.select-seat', ['session' => $eventSessionId]) }}" class="btn btn-secondary mb-3">
            <i class="fas fa-arrow-left"></i> Back to Seat Selection
        </a>
    </div>
</div>
@endsection
