<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'address_line', 'latitude', 'longitude',
                           'city', 'state', 'zip_code', 'max_capacity_per_session',
                           'price_per_seat', 'category', 'organizer_id'];

    protected $attributes = [
        'description' => '',
    ];


    public function images()
    {
        return $this->hasMany(EventImage::class);
    }

    public function sessions()
    {
        return $this->hasMany(EventSession::class);
    }

    public function favoritedByUsers()
    {
        return $this->belongsToMany(User::class, 'favorite_events')->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function organizer()
    {
        return $this->belongsTo(Organizer::class, 'organizer_id', 'user_id');
    }

    public function isPublishedByAdmin()
    {
        return $this->organizer_id === null; //Null organizer id consider as admin
    }

    public function getStatusAttribute()
    {
        $now = now();
        $ongoingSessions = $this->sessions()->where('start_time', '<=', $now)->where('end_time', '>=', $now)->exists();
        $upcomingSessions = $this->sessions()->where('start_time', '>', $now)->exists();

        if ($ongoingSessions) {
            return 'Ongoing';
        } elseif ($upcomingSessions) {
            return 'Upcoming';
        } else {
            return 'Past';
        }
    }


}




