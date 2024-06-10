<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = ['seat_ids'];
    protected $fillable = ['user_id', 'total_amount','payment_id'];

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getSessionIdAttribute()
    {
        $seatIds = json_decode($this->seat_ids, true);
        if (count($seatIds) > 0) {
            $firstSeatId = $seatIds[0];
            $parts = explode('_', $firstSeatId);
            return $parts[0] ?? null;
        }

        return null;
    }

    public function eventSession()
    {
        return $this->belongsTo(EventSession::class, 'id', 'session_id');
    }
}
