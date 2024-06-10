<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = "users";

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_organizer',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isAdmin()
    {
        return $this->email === 'admin@gmail.com';
    }

    public function hasVotedForPlayerInSession($playerId, $sessionId)
    {
        return Vote::where('user_id', $this->id)
            ->where('event_session_id', $sessionId)
            ->where('team_player_id', $playerId)
            ->exists();
    }

    public function hasVotedInSession($sessionId)
    {
        return Vote::where('user_id', $this->id)
            ->where('event_session_id', $sessionId)
            ->exists();
    }

    public function favoriteEvents()
    {
        return $this->belongsToMany(Event::class, 'favorite_events')->withTimestamps();
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function organizer()
    {
        return $this->hasOne(Organizer::class, 'user_id');
    }


}
