<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QrCode;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\ParkingTransaction;
use Carbon\Carbon;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\User;

class ApiParkingTransactionController extends Controller
{
    /* ====================================================
    | SCAN QR (CHECKIN + CHECKOUT VIA API)
    | ==================================================== */
    public function scan(Request $request, $token)
    {
        $type = $request->query('type'); // mengambil ?type=checkin atau checkout

        $qr = QrCode::where('qr_token', $token)->first();

        if (!$qr) {
            return response()->json([
                'status'  => 'error',
                'message' => 'QR Code tidak ditemukan'
            ], 404);
        }

        $booking = $qr->booking;

        /* =====================
        | LAUNCH CHECKIN
        | ===================== */
        if ($type == 'checkin' && $booking->status == 'pending') {

            $transaction = ParkingTransaction::create([
                'booking_id'     => $booking->id,
                'checkin_at'     => now(),
                'payment_status' => 'unpaid',
                'status'         => 'active'
            ]);

            $booking->update(['status' => 'active']);

            // ACTIVITY LOG (CUSTOMER)
            ActivityLog::create([
                'user_id'    => $booking->user_id,
                'activity'   => "Anda melakukan check-in menggunakan kendaraan {$booking->vehicle->plate_number}",
                'ip_address' => $request->ip()
            ]);

            // NOTIF CUSTOMER
            Notification::create([
                'user_id' => $booking->user_id,
                'title'   => 'Check-in Berhasil',
                'message' => $booking->vehicle->plate_number . ' masuk area parkir',
                'is_read' => false
            ]);

            // NOTIF ADMIN
            $admins = User::whereHas('role', fn($q) => $q->where('role_name', 'admin'))->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'title'   => 'CHECKIN - ' . $booking->user->name,
                    'message' => $booking->vehicle->plate_number . ' masuk area parkir',
                    'is_read' => false
                ]);
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Check-in sukses, kendaraan masuk area parkir.',
                'data'    => [
                    'transaction' => $transaction,
                    'booking'     => $booking->load('vehicle', 'user')
                ]
            ], 200);
        }

        /* =====================
        | LAUNCH CHECKOUT
        | ===================== */
        if ($type == 'checkout' && $booking->status == 'active') {

            $transaction = ParkingTransaction::where('booking_id', $booking->id)
                ->where('status', 'active')
                ->first();

            if (!$transaction) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Transaksi aktif tidak ditemukan'
                ], 404);
            }

            $checkin  = Carbon::parse($transaction->checkin_at);
            $checkout = now();

            $duration = max(1, $checkin->diffInHours($checkout));
            $vehicleType = $booking->vehicle->vehicleType;
            $total = $duration * $vehicleType->price_per_hour;

            $transaction->update([
                'checkout_at' => $checkout,
                'duration'    => $duration,
                'total_price' => $total,
                'status'      => 'completed'
            ]);

            $booking->update(['status' => 'completed']);

            // ACTIVITY LOG
            ActivityLog::create([
                'user_id'    => $booking->user_id,
                'activity'   => "Anda sudah melakukan check-out menggunakan kendaraan {$booking->vehicle->plate_number}",
                'ip_address' => $request->ip()
            ]);

            // NOTIF CUSTOMER
            Notification::create([
                'user_id' => $booking->user_id,
                'title'   => 'Check-out Berhasil',
                'message' => $booking->vehicle->plate_number . ' keluar dari parkir',
                'is_read' => false
            ]);

            // NOTIF ADMIN
            $admins = User::whereHas('role', fn($q) => $q->where('role_name', 'admin'))->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'title'   => 'CHECKOUT - ' . $booking->user->name,
                    'message' => $booking->vehicle->plate_number . ' keluar dari parkir',
                    'is_read' => false
                ]);
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Check-out sukses, kendaraan keluar area parkir.',
                'data'    => [
                    'total_price' => $total,
                    'duration'    => $duration . ' Jam',
                    'transaction' => $transaction,
                    'booking'     => $booking->load('vehicle', 'user')
                ]
            ], 200);
        }

        return response()->json([
            'status'  => 'error',
            'message' => 'Aksi tidak valid atau status booking tidak sesuai'
        ], 400);
    }

    /* ====================================================
    | PROCESS PAYMENT (SIMULASI VIA API)
    | ==================================================== */
    public function processPayment(Request $request, $id)
    {
        $transaction = ParkingTransaction::findOrFail($id);
        $method = $request->method;

        $vaNumber = match($method) {
            'va_bca'     => '014'.rand(1000000000,9999999999),
            'va_mandiri' => '008'.rand(1000000000,9999999999),
            'va_bni'     => '009'.rand(1000000000,9999999999),
            'va_bri'     => '002'.rand(1000000000,9999999999),
            default      => null
        };

        if (!$vaNumber) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Metode pembayaran tidak valid'
            ], 400);
        }

        $payment = Payment::create([
            'transaction_id' => $transaction->id,
            'payment_method' => $method,
            'amount'         => $transaction->total_price,
            'payment_proof'  => $vaNumber,
            'status'         => 'pending'
        ]);

        ActivityLog::create([
            'user_id'    => $transaction->booking->user_id,
            'activity'   => "Payment pending {$transaction->booking->vehicle->plate_number}",
            'ip_address' => $request->ip()
        ]);

        Notification::create([
            'user_id' => $transaction->booking->user_id,
            'title'   => 'Pembayaran Diproses',
            'message' => 'Pembayaran sedang diproses',
            'is_read' => false
        ]);

        // Simulasi Pembayaran Async (Tetap berjalan di background)
        dispatch(function () use ($payment, $transaction, $request) {
            sleep(5);
            $result = rand(1,10) > 2 ? 'paid' : 'unpaid';

            $payment->update([
                'status'       => $result,
                'payment_date' => $result == 'paid' ? now() : null,
            ]);

            $transaction->update([
                'payment_status' => $result
            ]);

            if ($result == 'paid') {
                ActivityLog::create([
                    'user_id'    => $transaction->booking->user_id,
                    'activity'   => "Payment SUCCESS {$transaction->booking->vehicle->plate_number}",
                    'ip_address' => $request->ip()
                ]);

                Notification::create([
                    'user_id' => $transaction->booking->user_id,
                    'title'   => 'Pembayaran Berhasil',
                    'message' => 'Pembayaran sukses untuk ' . $transaction->booking->vehicle->plate_number,
                    'is_read' => false
                ]);

                $admins = User::whereHas('role', fn($q) => $q->where('role_name','admin'))->get();
                foreach ($admins as $admin) {
                    Notification::create([
                        'user_id' => $admin->id,
                        'title'   => 'PAYMENT SUCCESS - ' . $transaction->booking->user->name,
                        'message' => $transaction->booking->vehicle->plate_number . ' sudah bayar',
                        'is_read' => false
                    ]);
                }
            }
        })->afterResponse();

        // Mengembalikan detail VA langsung ke Mobile App, tidak perlu redirect view waiting lagi
        return response()->json([
            'status'  => 'success',
            'message' => 'Virtual Account berhasil dibuat. Silakan lakukan pembayaran.',
            'data'    => [
                'payment_id'     => $payment->id,
                'virtual_account'=> $vaNumber,
                'amount'         => $payment->amount,
                'status'         => $payment->status
            ]
        ], 201);
    }

    /* ====================================================
    | CHECK STATUS PAYMENT (SANGAT BERGUNA UNTUK MOBILE)
    | ==================================================== */
    public function getPaymentStatus($id)
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json(['status' => 'error', 'message' => 'Data pembayaran tidak ditemukan'], 404);
        }

        return response()->json([
            'status'         => 'success',
            'payment_status' => $payment->status
        ], 200);
    }
}