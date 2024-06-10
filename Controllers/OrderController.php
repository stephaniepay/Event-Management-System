<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Models\Payment;

class OrderController extends Controller
{
    public function userOrders()
    {
        $user = auth()->user();
        $orders = $user->orders;

        return view('User.orders', compact('orders'));
    }

    public function details($id)
    {
        $order = Order::with(['payment', 'carts.eventSession.event'])
                ->findOrFail($id);

        if (!$order) {
            abort(404, 'Order not found');
        }

        $order->carts = $order->carts->sortBy(function ($cart) {
            return optional(optional($cart->eventSession)->event)->name ?? '';
        });


        foreach ($order->carts as $cart) {
            if ($cart->seats) {
                $cart->seats = $cart->seats->sortBy(['row', 'number']);
            }
        }

        return view('User.order_details', ['order' => $order]);
    }

    public function adminOrderDetails($id)
    {
        $order = Order::with(['payment', 'carts.eventSession.event'])
                ->findOrFail($id);


        return view('Admin.order_details', ['order' => $order]);
    }


}
