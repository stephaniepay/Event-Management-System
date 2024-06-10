<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    
    protected $fillable = ['user_id', 'event_session_id', 'total_amount', 'is_purchased'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function eventSession()
    {
        return $this->belongsTo(EventSession::class, 'event_session_id');
    }

    public function getSeatIdsAttribute()
    {
        return json_decode($this->attributes['seat_ids'], true);
    }

    public function getSeats()
    {
        return Seat::whereIn('id', $this->seat_ids)->get();
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}