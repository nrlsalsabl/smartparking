<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Booking;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role->role_name;

        if ($role == 'admin') {

            return view('dashboard.admin');

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