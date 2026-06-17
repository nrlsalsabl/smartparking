<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class NotificationController extends Controller
{
    // 🔔 COUNT NOTIF (AJAX)
    public function count()
    {
        $user = Auth::user();

        if ($user->role->role_name == 'admin') {

            // ADMIN hanya hitung notif admin (bukan customer)
            $count = Notification::whereHas('user.role', function ($q) {
                $q->where('role_name', 'admin');
            })->where('is_read', false)->count();

        } else {

            // CUSTOMER hanya notif dia sendiri
            $count = Notification::where('user_id', $user->id)
                ->where('is_read', false)
                ->count();
        }

        return response()->json([
            'count' => $count
        ]);
    }

    // 📩 LIST NOTIFICATION
    public function index()
    {
        $user = Auth::user();

        if ($user->role->role_name == 'admin') {

            // ADMIN: hanya notif dari user admin-side event
            $notifications = Notification::whereHas('user.role', function ($q) {
                    $q->where('role_name', 'admin');
                })
                ->latest()
                ->paginate(10);

        } else {

            // CUSTOMER: hanya notif dia sendiri
            $notifications = Notification::where('user_id', $user->id)
                ->latest()
                ->paginate(10);
        }

        return view('notifications.index', compact('notifications'));
    }

    // ✅ MARK AS READ
    public function read($id)
    {
        $notif = Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $notif->update([
            'is_read' => true
        ]);

        return back();
    }
}