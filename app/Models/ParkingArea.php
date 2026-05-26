<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParkingArea extends Model
{
    protected $fillable = [
        'area_name',
        'capacity',
        'location',
        'description'
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
