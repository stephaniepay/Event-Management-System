<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'row', 'number', 'is_booked',
                           'event_session_id', 'order_id',
                           'payment_id', 'price', 'cart_id', 'section'];

    public function eventSession()
    {
        return $this->belongsTo(EventSession::class);
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function isSeatInUserCart()
    {
        $userId = auth()->id();
        return Cart::where('user_id', $userId)
                    ->whereJsonContains('seat_ids', $this->id)
                    ->exists();
    }
}
