<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'event_session_id', 'team_player_id'];

    public function eventSession()
    {
        return $this->belongsTo(EventSession::class, 'event_session_id');
    }

    public function teamPlayer()
    {
        return $this->belongsTo(TeamPlayer::class);
    }
}
