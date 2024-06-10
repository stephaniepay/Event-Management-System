<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Seat;
use App\Models\EventSession;

class PaymentController extends Controller
{


    public function index(Request $request)
    {
        $totalAmount = session('totalAmount');
        $seatIds = session('seatIds');
        $orderId = session('orderId');

        if ($orderId) {
            $order = Order::with(['carts' => function ($query) {
                $query->where('is_purchased', false);
            }])->find($orderId);
        } else {
            $order = new Order();
            $order->total_amount = $totalAmount;
            $order->id = 0; //Temporary ID
        }

        if (!$order) {
            return redirect()->route('cart')->with('error', 'Order details not found.');
        }

        return view('User.payment', compact('order'));
    }


    public function success($paymentId)
    {
        $payment = Payment::findOrFail($paymentId);

        return view('User.payment_success', compact('payment'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:credit_card,paypal,cash_on_delivery',
        ]);

        $totalAmount = session('totalAmount');
        $seatIds = session('seatIds');
        $cartIds = Cart::where('user_id', auth()->id())
                    ->where('is_purchased', false)
                    ->pluck('id');

        if (empty($seatIds)) {
            return redirect()->route('cart')->with('error', 'No seats selected.');
        }

        // --------- Process payment ---------

        //Create order
        $order = new Order();
        $order->user_id = auth()->id();
        $order->total_amount = $totalAmount;
        $order->seat_ids = json_encode($seatIds);
        $order->save();

        Cart::whereIn('id', $cartIds)->update(['order_id' => $order->id, 'is_purchased' => true]);

        Seat::whereIn('id', $seatIds)->update(['is_booked' => true, 'order_id' => $order->id]);

        //Create payment
        $payment = new Payment();
        $payment->order_id = $order->id;
        $payment->user_id = auth()->id();
        $payment->amount = $totalAmount;
        $payment->status = 'success';
        $payment->payment_type = $request->input('payment_method');
        $payment->details = json_encode(['message' => 'Payment processing successful']);
        $payment->save();

        $order->payment_id = $payment->id;
        $order->save();

        session()->forget(['totalAmount', 'seatIds', 'orderId']);

        return redirect()->route('payment.success', ['paymentId' => $payment->id]);
    }


}

