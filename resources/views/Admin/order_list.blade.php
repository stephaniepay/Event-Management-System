@extends('layout')

@section('title', 'Order List')

@section('content')

<div class="container mt-3">

    <div class="recent-orders">
        <h1>Processed Transactions</h1>

        <div class="orders-by-month mt-4">
            @foreach($groupedOrders as $month => $orders)
                <h4>{{ $month }}</h4>
                <ul>
                    @foreach($orders as $order)
                        <li class="order-detail">
                            <a href="{{ route('profile.order.details', $order->id) }}">
                            <span><strong class="semi-bold">User ID:</strong> {{ $order->user_id }}, </span>
                            <span><strong class="semi-bold">Order ID:</strong> {{ $order->id }}, </span>
                            <span><strong class="semi-bold">Payment ID:</strong> {{ $order->payment_id ?? 'N/A' }}, </span>
                            <span><strong class="semi-bold">Amount:</strong> RM {{ number_format($order->total_amount, 2) }}, </span>
                            <span><strong class="semi-bold">Order Date:</strong> {{ $order->created_at->format('M d, Y') }}</span>
                        </li>
                    @endforeach
                </ul>
            @endforeach
        </div>
    </div>

    <div class="mt-5">
        <a href="{{ route('profile.admin') }}" class="btn btn-secondary mb-3">
            <i class="fas fa-arrow-left"></i> Back to Profile
        </a>
    </div>
</div>
@endsection
