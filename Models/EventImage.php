<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventImage extends Model
{
    protected $fillable = ['event_id', 'filename'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
