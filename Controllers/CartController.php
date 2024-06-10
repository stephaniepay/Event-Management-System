<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;

class CartController extends Controller
{

    public function index()
    {
        $carts = Cart::with(['user', 'eventSession.event'])
                ->where('user_id', auth()->id())
                ->where('is_purchased', false)
                ->get();
        return view('User.cart', compact('carts'));
    }


    public function remove($id)
    {
        $cart = Cart::with('order')->findOrFail($id);

        $eventSessionId = optional($cart->eventSession)->id;

        $cart->delete();

        if ($cart->order_id) {
            $order = Order::findOrFail($cart->order_id);
            $order->load('carts');
        }

        if (Cart::where('user_id', auth()->id())->where('is_purchased', false)->doesntExist()) {
            if (isset($eventSessionId)) {
                session(['lastEventSessionId' => $eventSessionId]);
            }
        }

        return redirect()->route('cart')->with('success', 'Item removed successfully.');
    }


    public function confirmCart(Request $request)
    {
        $existingOrderId = session('orderId');
        if ($existingOrderId) {
            return redirect()->route('payment');
        }

        $carts = Cart::with(['eventSession.event'])
                    ->where('user_id', auth()->id())
                    ->where('is_purchased', false)
                    ->get();

        $cartItems = $carts->map(function ($cart) {
            return [
                'event_name' => $cart->eventSession->event->name ?? 'N/A',
                'session_time' => $cart->eventSession->start_time->format('d-m-Y H:i') . ' to ' . $cart->eventSession->end_time->format('d-m-Y H:i'),
                'seat_ids' => $cart->seat_ids,
                'total_amount' => $cart->total_amount,
            ];
        })->toArray();

        $seatIds = $carts->pluck('seat_ids')->flatten();

        session([
            'cartItems' => $cartItems,
            'totalAmount' => $carts->sum('total_amount'),
            'seatIds' => $seatIds,
            'orderId' => null  //To check if an order has been created
        ]);

        return redirect()->route('payment');
    }







}
