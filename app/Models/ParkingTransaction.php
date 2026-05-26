<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParkingTransaction extends Model
{
    protected $fillable = [
        'booking_id',
        'checkin_at',
        'checkout_at',
        'duration',
        'total_price',
        'payment_status',
        'status'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'transaction_id');
    }
}
