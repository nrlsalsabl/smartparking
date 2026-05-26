<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
     protected $fillable = [
        'user_id',
        'vehicle_type_id',
        'plate_number',
        'brand',
        'color',
        'vehicle_photo',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
