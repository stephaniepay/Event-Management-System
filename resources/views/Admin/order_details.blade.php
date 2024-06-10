@extends('layout')

@section('title', 'Order Details')

@section('content')
<div class="container mt-3">
    <h1>Order Details</h1>

    <div class="order-details-container mt-3">
        <div class="order-payment-details">
            <p><strong>User ID:</strong> #{{ $order->user->id ?? 'N/A' }}</p>
            <p><strong>Order ID:</strong> #{{ $order->id }}</p>
            <p><strong>Order Date:</strong> {{ $order->created_at->format('d-m-Y') }}</p>
            <p><strong>Payment ID:</strong> #{{ $order->payment->id }}</p>
            <p><strong>Payment Status:</strong> {{ optional($order->payment)->status ?? 'Not Available' }}</p>
            <p><strong>Payment Method:</strong> {{ optional($order->payment)->payment_type ?? 'Not Available' }}</p>
        </div>

        @if($order->carts && $order->carts->count())
            @php $seatCounter = 1; @endphp
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Event Name</th>
                            <th>Session ID</th>
                            <th>Event Session</th>
                            <th>Seat Row</th>
                            <th>Seat Number</th>
                            <th>Section</th>
                            <th>Price (RM)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->carts as $cart)
                            @php $seats = $cart->getSeats(); @endphp
                            @foreach($seats as $seat)
                                <tr>
                                    <td>{{ $seatCounter++ }}</td>
                                    <td>{{ optional($cart->eventSession)->event->name ?? 'N/A' }}</td>
                                    <td>{{ $cart->eventSession->id ?? 'N/A' }}</td>
                                    <td>{{ optional($cart->eventSession)->start_time->format('d-m-Y') ?? 'N/A' }},
                                        {{ optional($cart->eventSession)->start_time->format('H:i') ?? 'N/A' }}
                                        to {{ optional($cart->eventSession)->end_time->format('H:i') ?? 'N/A' }}</td>
                                    <td>{{ $seat->row }}</td>
                                    <td>{{ $seat->number }}</td>
                                    <td>{{ $seat->section }}</td>
                                    <td>{{ $seat->price }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p>No carts available for this order.</p>
        @endif

        <div class="total-price text-end">
            <h4>Total Amount: RM{{ $order->total_amount }}</h4>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('profile.recentOrders') }}" class="btn btn-secondary mb-3">
            <i class="fas fa-arrow-left"></i> Back to Order List
        </a>
    </div>
</div>
@endsection
