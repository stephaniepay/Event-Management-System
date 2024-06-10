<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organizer extends Model
{
    use HasFactory;

    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id', 'name'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function organizerRequests()
    {
        return $this->hasMany(OrganizerRequest::class);
    }

    protected static function booted()
    {
        static::creating(function ($organizer) {
            if (empty($organizer->picture_filename)) {
                $organizer->picture_filename = 'default.png';
            }
        });
    }


}
