<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
     protected $fillable = [
        'user_id',
        'vehicle_id',
        'parking_area_id',
        'booking_code',
        'booking_time',
        'expired_at',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function parkingArea()
    {
        return $this->belongsTo(ParkingArea::class);
    }

    public function qrCode()
    {
        return $this->hasOne(QrCode::class);
    }

    public function transaction()
    {
        return $this->hasOne(ParkingTransaction::class);
    }
}
