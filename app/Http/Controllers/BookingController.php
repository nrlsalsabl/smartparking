<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\ParkingArea;
use App\Models\QrCode;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Notification;
use App\Models\User;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bookings = Booking::with([
            'vehicle',
            'parkingArea',
            'qrCode'
        ])

        ->where(
            'user_id',
            auth()->id()
        )

        ->latest()
        ->paginate(10);

        return view(
            'bookings.index',
            compact('bookings')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vehicles = Vehicle::where(
            'user_id',
            auth()->id()
        )->get();

        $parkingAreas = ParkingArea::all();

        return view(
            'bookings.create',
            compact(
                'vehicles',
                'parkingAreas'
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([

            'vehicle_id' => 'required',

            'parking_area_id' => 'required',

        ]);

        $parkingArea = ParkingArea::findOrFail(
            $request->parking_area_id
        );

        $activeBooking = Booking::where(
            'parking_area_id',
            $parkingArea->id
        )
        ->whereIn('status', [
            'pending',
            'active'
        ])
        ->count();

        if ($activeBooking >= $parkingArea->capacity) {

            return back()->with(
                'error',
                'Parking area full'
            );
        }

        $bookingCode =
            'BOOK-' .
            strtoupper(Str::random(8));

        $booking = Booking::create([

            'user_id' => auth()->id(),

            'vehicle_id' => $request->vehicle_id,

            'parking_area_id' => $request->parking_area_id,

            'booking_code' => $bookingCode,

            'booking_time' => now(),

            'expired_at' => now()->addMinutes(30),

            'status' => 'pending'

        ]);

        QrCode::create([

            'booking_id' => $booking->id,

            'qr_token' => Str::uuid(),

            'expired_at' => now()->addMinutes(30),

            'is_used' => false

        ]);

        /*
        |--------------------------------------------------------------------------
        | ACTIVITY LOG
        |--------------------------------------------------------------------------
        */

        $vehicle = Vehicle::findOrFail(
            $request->vehicle_id
        );

        ActivityLog::create([

            'user_id' => auth()->id(),

            'activity' =>
                'Anda membuat booking menggunakan kendaraan ' .
                $vehicle->vehicle_name .
                ' dengan plat ' .
                $vehicle->plate_number,

            'ip_address' => $request->ip()

        ]);

        Notification::create([
            'user_id' => auth()->id(),
            'title' => 'Booking Berhasil',
            'message' => 'Booking kendaraan ' . $vehicle->plate_number . ' berhasil dibuat',
            'is_read' => false
        ]);

        $admins = User::whereHas('role', function ($q) {
            $q->where('role_name', 'admin');
        })->get();

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'title' => auth()->user()->name . ' melakukan booking',
                'message' =>
                    'Kendaraan: ' . $booking->vehicle->vehicle_name .
                    ' (' . $booking->vehicle->plate_number . ')' . "\n" .
                    'Gate: ' . $booking->parkingArea->area_name,
                'is_read' => false
            ]);
        }

        return redirect()
            ->route('bookings.index')
            ->with('success', 'Booking created');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $booking = Booking::where(
            'user_id',
            auth()->id()
        )->findOrFail($id);

        $booking->delete();

        return redirect()
            ->route('bookings.index')
            ->with('success', 'Booking deleted');
    }
}