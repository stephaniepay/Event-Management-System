<?php

namespace App\Http\Controllers;

use App\Models\User;



class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['orders.payment'])->get()->reject(function($user) {
            return $user->isAdmin();
        })->map(function($user) {
            $totalAmount = $user->orders->sum(function($order) {
                return $order->total_amount;
            });
            $user->total_amount = $totalAmount;
            return $user;
        });

        return view('Admin.user_list', compact('users'));
    }


}
