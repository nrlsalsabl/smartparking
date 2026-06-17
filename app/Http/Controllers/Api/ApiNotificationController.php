<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;

class ApiNotificationController extends Controller
{
    /**
     * Hitung jumlah notifikasi yang belum dibaca (Unread Count)
     */
    public function count()
    {
        $user = auth('api')->user();

        if ($user->role->role_name == 'admin') {
            // ADMIN: Hitung notif yang ditujukan untuk sisi admin
            $count = Notification::whereHas('user.role', function ($q) {
                $q->where('role_name', 'admin');
            })->where('is_read', false)->count();
        } else {
            // CUSTOMER: Hanya hitung notif miliknya sendiri
            $count = Notification::where('user_id', $user->id)
                ->where('is_read', false)
                ->count();
        }

        return response()->json([
            'status' => 'success',
            'count'  => $count
        ], 200);
    }

    /**
     * Tampilkan daftar notifikasi (Fleksibel sesuai Role)
     */
    public function index()
    {
        $user = auth('api')->user();

        if ($user->role->role_name == 'admin') {
            // ADMIN: Lihat semua notifikasi event admin-side
            $notifications = Notification::whereHas('user.role', function ($q) {
                    $q->where('role_name', 'admin');
                })
                ->latest()
                ->get(); // Gunakan get() atau paginate() sesuai kebutuhan aplikasi mobile kamu
        } else {
            // CUSTOMER: Hanya lihat notifikasi miliknya sendiri
            $notifications = Notification::where('user_id', $user->id)
                ->latest()
                ->get();
        }

        return response()->json([
            'status'     => 'success',
            'message'    => 'Berhasil mengambil data notifikasi',
            'role_akses' => $user->role->role_name,
            'data'       => $notifications
        ], 200);
    }

    /**
     * Tandai notifikasi sebagai sudah dibaca (Mark as Read)
     */
    public function read($id)
    {
        $user = auth('api')->user();
        
        // Memulai query dasar pencarian data notifikasi
        $query = Notification::query();

        if ($user->role->role_name == 'admin') {
            // Jika admin, pastikan notifikasi yang mau dibaca memang notifikasi milik/untuk ranah admin
            $query->whereHas('user.role', function ($q) {
                $q->where('role_name', 'admin');
            });
        } else {
            // Jika customer, kunci harus milik ID-nya sendiri
            $query->where('user_id', $user->id);
        }

        $notif = $query->where('id', $id)->first();

        if (!$notif) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Notifikasi tidak ditemukan atau Anda tidak memiliki akses.'
            ], 404);
        }

        $notif->update([
            'is_read' => true
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Notifikasi berhasil ditandai sebagai dibaca.',
            'data'    => $notif
        ], 200);
    }
}