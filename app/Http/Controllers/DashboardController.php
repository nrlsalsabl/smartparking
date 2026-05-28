<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\ParkingTransaction;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role->role_name;

        if ($role == 'admin') {

            $totalVehicles = Vehicle::count();

            $totalBookings = Booking::count();

            $totalTransactions = ParkingTransaction::count();

           $chartData = ParkingTransaction::selectRaw(
                'DATE(created_at) as date, COUNT(*) as total'
            )
            ->groupByRaw('DATE(created_at)')
            ->orderByRaw('DATE(created_at) DESC')
            ->take(7)
            ->get()
            ->reverse()
            ->values();
            return view('dashboard.admin', compact(
                'totalVehicles',
                'totalBookings',
                'totalTransactions',
                'chartData'
            ));

        } elseif ($role == 'officer') {

            return view('dashboard.officer');

        } else {

            // 🔥 INI YANG KURANG SEBELUMNYA
            $bookings = Booking::with(['vehicle', 'parkingArea', 'parkingTransaction'])
                ->where('user_id', $user->id)
                ->latest()
                ->get();

            return view('dashboard.customer', compact('bookings'));
        }
    }
}