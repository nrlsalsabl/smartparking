<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\ParkingArea;
use App\Models\QrCode;
use App\Models\Vehicle;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class ApiBookingController extends Controller
{
    /**
     * Tampilkan riwayat booking (Fleksibel sesuai Role)
     */
    public function index()
    {
        $user = auth('api')->user();
        
        // Memulai query dengan eager loading agar relasinya ikut terbawa ke JSON
        $query = Booking::with(['vehicle', 'parkingArea', 'qrCode', 'user']);

        // JIKA BUKAN ADMIN (Misal Customer), batasi hanya data miliknya sendiri
        if ($user->role->role_name !== 'admin') {
            $query->where('user_id', $user->id);
        }

        // Jika Admin, baris where di atas dilewati, sehingga mengambil semua data booking
        $bookings = $query->latest()->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Berhasil mengambil data riwayat booking',
            'role_akses' => $user->role->role_name,
            'data'    => $bookings
        ], 200);
    }

    /**
     * Simpan transaksi booking baru (Khusus Customer)
     */
    public function store(Request $request)
    {
        // Fitur membuat booking biasanya hanya untuk customer
        if (auth('api')->user()->role->role_name !== 'customer') {
            return response()->json([
                'status' => 'error',
                'message' => 'Hanya akun Customer yang dapat melakukan booking parkir.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'vehicle_id'      => 'required|exists:vehicles,id',
            'parking_area_id' => 'required|exists:parking_areas,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $parkingArea = ParkingArea::findOrFail($request->parking_area_id);

        $activeBooking = Booking::where('parking_area_id', $parkingArea->id)
            ->whereIn('status', ['pending', 'active'])
            ->count();

        if ($activeBooking >= $parkingArea->capacity) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal membuat booking, area parkir sudah penuh.'
            ], 422);
        }

        $bookingCode = 'BOOK-' . strtoupper(Str::random(8));
        $userId = auth('api')->id();
        $currentUser = auth('api')->user();

        $booking = Booking::create([
            'user_id'         => $userId,
            'vehicle_id'      => $request->vehicle_id,
            'parking_area_id' => $request->parking_area_id,
            'booking_code'    => $bookingCode,
            'booking_time'    => now(),
            'expired_at'      => now()->addMinutes(30),
            'status'          => 'pending'
        ]);

        $qrCode = QrCode::create([
            'booking_id' => $booking->id,
            'qr_token'   => Str::uuid(),
            'expired_at' => now()->addMinutes(30),
            'is_used'    => false
        ]);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);

        ActivityLog::create([
            'user_id'    => $userId,
            'activity'   => 'Anda membuat booking menggunakan kendaraan ' . $vehicle->vehicle_name . ' dengan plat ' . $vehicle->plate_number,
            'ip_address' => $request->ip()
        ]);

        Notification::create([
            'user_id' => $userId,
            'title'   => 'Booking Berhasil',
            'message' => 'Booking kendaraan ' . $vehicle->plate_number . ' berhasil dibuat',
            'is_read' => false
        ]);

        $admins = User::whereHas('role', function ($q) {
            $q->where('role_name', 'admin');
        })->get();

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'title'   => $currentUser->name . ' melakukan booking',
                'message' => 'Kendaraan: ' . $booking->vehicle->vehicle_name . ' (' . $booking->vehicle->plate_number . ')' . "\n" . 'Gate: ' . $booking->parkingArea->area_name,
                'is_read' => false
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Booking berhasil dibuat.',
            'data'    => [
                'booking' => $booking->load('vehicle', 'parkingArea'),
                'qr_code' => $qrCode
            ]
        ], 201);
    }

    /**
     * Tampilkan detail satu data booking (Fleksibel)
     */
    public function show(string $id)
    {
        $user = auth('api')->user();
        $query = Booking::with(['vehicle', 'parkingArea', 'qrCode', 'user']);

        // Jika bukan admin, dia hanya boleh melihat detail miliknya sendiri
        if ($user->role->role_name !== 'admin') {
            $query->where('user_id', $user->id);
        }

        $booking = $query->find($id);

        if (!$booking) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data booking tidak ditemukan atau Anda tidak memiliki akses.'
            ], 404);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Detail booking ditemukan.',
            'data'    => $booking
        ], 200);
    }

    /**
     * Hapus / Batalkan data booking (Fleksibel)
     */
    public function destroy(string $id)
    {
        $user = auth('api')->user();
        $query = Booking::query();

        // Jika bukan admin, dia hanya bisa menghapus miliknya sendiri
        if ($user->role->role_name !== 'admin') {
            $query->where('user_id', $user->id);
        }

        $booking = $query->find($id);

        if (!$booking) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data tidak ditemukan atau Anda tidak memiliki akses untuk menghapusnya.'
            ], 404);
        }

        $booking->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Booking berhasil dihapus/dibatalkan.'
        ], 200);
    }
}