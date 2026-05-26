<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
     protected $fillable = [
        'transaction_id',
        'payment_method',
        'amount',
        'payment_proof',
        'payment_date',
        'status'
    ];

    public function transaction()
    {
        return $this->belongsTo(
            ParkingTransaction::class,
            'transaction_id'
        );
    }
}
