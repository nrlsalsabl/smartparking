<?php

namespace App\Exports;

use App\Models\ParkingTransaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TransactionExport implements
    FromCollection,
    WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    
    public function collection()
    {
        return ParkingTransaction::with([
            'booking.vehicle',
            'booking.user'
        ])

        ->get()

        ->map(function ($item) {

            return [

                'No' =>
                    $item->id,

                'Plate' =>
                    $item->booking->vehicle->plate_number,

                'User' =>
                    $item->booking->user->name,

                'Total' =>
                    $item->total_price,

                'Status' =>
                    $item->payment_status,

                'Checkin' =>
                    $item->checkin_at,

                'Checkout' =>
                    $item->checkout_at,

            ];
        });
    }

    public function headings(): array
    {
        return [

            'No',
            'Plate',
            'User',
            'Total',
            'Status',
            'Checkin',
            'Checkout'

        ];
    }
}