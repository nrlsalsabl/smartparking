<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QrCode extends Model
{
    protected $fillable = [
        'booking_id',
        'qr_token',
        'expired_at',
        'is_used'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
