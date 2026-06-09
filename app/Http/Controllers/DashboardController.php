<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\ParkingTransaction;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $role = $user->role->role_name;

        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        */

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

            // Activity seluruh user
            $logs = ActivityLog::latest()
                ->paginate(10);

            return view(
                'dashboard.admin',
                compact(
                    'totalVehicles',
                    'totalBookings',
                    'totalTransactions',
                    'chartData',
                    'logs'
                )
            );

        }

        /*
        |--------------------------------------------------------------------------
        | OFFICER
        |--------------------------------------------------------------------------
        */

        elseif ($role == 'officer') {

            return view(
                'dashboard.officer'
            );

        }

        /*
        |--------------------------------------------------------------------------
        | CUSTOMER
        |--------------------------------------------------------------------------
        */

        else {

            $bookings = Booking::with([
                'vehicle',
                'parkingArea',
                'parkingTransaction'
            ])
            ->where(
                'user_id',
                $user->id
            )
            ->latest()
            ->get();

            // Activity customer sendiri
            $logs = ActivityLog::where(
                'user_id',
                auth()->id()
            )
            ->latest()
            ->take(10)
            ->get();

            return view(
                'dashboard.customer',
                compact(
                    'bookings',
                    'logs'
                )
            );
        }
    }

    public function customerActivityLogs(Request $request)
    {
        $search = $request->search;

        $logs = ActivityLog::where('user_id', auth()->id())
            ->when($search, function ($query) use ($search) {
                $query->where('activity', 'like', '%' . $search . '%');
            })
            ->latest()
            ->paginate(10);

        return view(
            'customer.activity-log.index',
            compact('logs', 'search')
        );
    }
}