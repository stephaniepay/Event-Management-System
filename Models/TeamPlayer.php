<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamPlayer extends Model
{
    use HasFactory;

    protected $fillable = ['event_session_id', 'name', 'wikipedia_link', 'picture_filename','votes', 'is_winner'];

    public function session()
    {
        return $this->belongsTo(EventSession::class, 'event_session_id');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class, 'team_player_id');
    }

    public function is_winner()
    {
        return $this->is_winner;
    }

    public function eventThroughSession()
    {
        return $this->belongsToThrough(Event::class, EventSession::class);
    }
}
