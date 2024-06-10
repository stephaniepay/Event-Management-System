@extends('layout')

@section('title', 'Payment Success')

@section('content')
<div class="container mt-3">
    <div class="payment-container">
        <div class="payment-success">
            <h1 class="mb-3">Success!</h1>
            <p>Thank you for your payment. Your transaction has been completed successfully.</p>
            <div class="order-details mt-4">
                <p>Payment ID: <strong>{{ $payment->id }}</strong></p>
                <p>Order ID: <strong>{{ $payment->order->id }}</strong></p>
                <p>Order Total: <strong>RM {{ $payment->order->total_amount }}</strong></p>
            </div>
            <div class="action-buttons mt-4">
                <a href="{{ route('events.index') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Continue Shopping
                </a>
                <a href="{{ route('order.details', $payment->order->id) }}" class="btn btn-outline-secondary">View Order</a>
            </div>
            <div class="support mt-5">
                <p>If you have any questions about your order, please <a href="{{ route('team_member_details') }}">contact us</a>.</p>
            </div>
        </div>
    </div>
</div>
@endsection
