<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class EventSession extends Model
{
    use HasFactory;

    protected $fillable = ['event_id', 'start_time', 'end_time', 'id', 'max_capacity_per_session','winner_player_id'];

    protected $casts = [
        'start_time' => 'datetime:Y-m-d\TH:i',
        'end_time' => 'datetime:Y-m-d\TH:i',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function seats()
    {
        return $this->hasMany(Seat::class);
    }

    public function isSeatInUserCart($seatId) {
        $userId = auth()->id();
        return Cart::where('user_id', $userId)
                    ->whereJsonContains('seat_ids', $seatId)
                    ->exists();
    }

    public function teamPlayers()
    {
        return $this->hasMany(TeamPlayer::class);
    }

    //Automatically call this function when a new event session is created
    protected static function booted()
    {
        static::created(function ($session) {
            app('App\Http\Controllers\SessionController')->generateSeatMap($session->id);
        });
    }

    public function getRelatedOrdersAttribute()
    {
        $sessionIds = Order::all()->pluck('seat_ids')->flatten()
            ->map(function ($seatId) {
                return explode('_', $seatId)[0];
            })->unique();

        if ($sessionIds->contains($this->id)) {
            return Order::whereJsonContains('seat_ids', (string) $this->id)->get();
        }

        return collect();
    }

    public function orders()
    {
        return $this->hasManyThrough(Order::class, Seat::class, 'event_session_id', 'id', 'id', 'order_id');
    }

    public function organizer()
    {
        return $this->event->organizer();
    }

    public function getStatusAttribute()
    {
        $now = now();

        if($this->start_time > $now) {
            return 'Upcoming';
        } elseif ($this->end_time < $now) {
            return 'Past';
        } else {
            return 'Ongoing';
        }
    }
}
