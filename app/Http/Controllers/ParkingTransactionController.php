<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\ParkingTransaction;
use Carbon\Carbon;
use App\Exports\TransactionExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\User;

class ParkingTransactionController extends Controller
{
    /* =========================
    | INDEX
    ========================= */
    public function index(Request $request)
    {
        $search = $request->search;

        $transactions = ParkingTransaction::with([
            'booking.vehicle',
            'booking.user'
        ])
        ->when($search, function ($query) use ($search) {
            $query->whereHas('booking.vehicle', function ($q) use ($search) {
                $q->where('plate_number', 'like', "%$search%");
            });
        })
        ->latest()
        ->paginate(10);

        return view('transactions.index', compact('transactions', 'search'));
    }

    /* =========================
    | ACTIVITY LOGS
    ========================= */
    public function activityLogs(Request $request)
    {
        $search = $request->search;

        $logs = ActivityLog::with('user')
            ->when($search, function ($query) use ($search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                });
            })
            ->latest()
            ->paginate(10);

        return view('activity-log.index', compact('logs', 'search'));
    }

    /* =========================
    | SCAN QR (CHECKIN + CHECKOUT)
    ========================= */
    public function scan($token)
    {
        $type = request('type');

        $qr = QrCode::where('qr_token', $token)->first();

        if (!$qr) {
            return view('scanner.failed', ['message' => 'QR not found']);
        }

        $booking = $qr->booking;

        /* =====================
        | CHECKIN
        ===================== */
        if ($type == 'checkin' && $booking->status == 'pending') {

            ParkingTransaction::create([
                'booking_id' => $booking->id,
                'checkin_at' => now(),
                'payment_status' => 'unpaid',
                'status' => 'active'
            ]);

            $booking->update(['status' => 'active']);

            // ACTIVITY LOG (CUSTOMER)
            ActivityLog::create([
                'user_id' => $booking->user_id,
                'activity' =>
                    "Anda melakukan check-in menggunakan kendaraan {$booking->vehicle->plate_number}",
                'ip_address' => request()->ip()
            ]);

            // NOTIF CUSTOMER
            Notification::create([
                'user_id' => $booking->user_id,
                'title' => 'Check-in Berhasil',
                'message' => $booking->vehicle->plate_number . ' masuk area parkir',
                'is_read' => false
            ]);

            // NOTIF ADMIN
            $admins = User::whereHas('role', fn($q) => $q->where('role_name','admin'))->get();

            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'title' => 'CHECKIN - ' . $booking->user->name,
                    'message' => $booking->vehicle->plate_number . ' masuk area parkir',
                    'is_read' => false
                ]);
            }

            return view('scanner.success', [
                'title' => 'CHECKIN SUCCESS',
                'message' => 'Vehicle entered parking area',
                'booking' => $booking
            ]);
        }

        /* =====================
        | CHECKOUT
        ===================== */
        if ($type == 'checkout' && $booking->status == 'active') {

            $transaction = ParkingTransaction::where('booking_id', $booking->id)
                ->where('status', 'active')
                ->first();

            if (!$transaction) {
                return view('scanner.failed', ['message' => 'Transaction not found']);
            }

            $checkin = Carbon::parse($transaction->checkin_at);
            $checkout = now();

            $duration = max(1, $checkin->diffInHours($checkout));

            $vehicleType = $booking->vehicle->vehicleType;
            $total = $duration * $vehicleType->price_per_hour;

            $transaction->update([
                'checkout_at' => $checkout,
                'duration' => $duration,
                'total_price' => $total,
                'status' => 'completed'
            ]);

            $booking->update(['status' => 'completed']);

            // ACTIVITY LOG
            ActivityLog::create([
                'user_id' => $booking->user_id,
                'activity' =>
                    "Anda sudah melakukan check-out menggunakan kendaraan {$booking->vehicle->plate_number}",
                'ip_address' => request()->ip()
            ]);

            // NOTIF CUSTOMER
            Notification::create([
                'user_id' => $booking->user_id,
                'title' => 'Check-out Berhasil',
                'message' => $booking->vehicle->plate_number . ' keluar dari parkir',
                'is_read' => false
            ]);

            // NOTIF ADMIN
            $admins = User::whereHas('role', fn($q) => $q->where('role_name','admin'))->get();

            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'title' => 'CHECKOUT - ' . $booking->user->name,
                    'message' => $booking->vehicle->plate_number . ' keluar dari parkir',
                    'is_read' => false
                ]);
            }

            return view('scanner.success', [
                'title' => 'CHECKOUT SUCCESS',
                'message' => 'Vehicle exited parking area',
                'booking' => $booking,
                'total' => $total,
                'duration' => $duration,
                'transaction' => $transaction
            ]);
        }

        return view('scanner.failed', ['message' => 'Invalid action']);
    }

    /* =========================
    | PAYMENT
    ========================= */
    public function payment($id)
    {
        $transaction = ParkingTransaction::findOrFail($id);
        return view('scanner.payment-method', compact('transaction'));
    }

    public function processPayment(Request $request, $id)
    {
        $transaction = ParkingTransaction::findOrFail($id);
        $method = $request->method;

        $vaNumber = match($method) {
            'va_bca' => '014'.rand(1000000000,9999999999),
            'va_mandiri' => '008'.rand(1000000000,9999999999),
            'va_bni' => '009'.rand(1000000000,9999999999),
            'va_bri' => '002'.rand(1000000000,9999999999),
            default => null
        };

        if (!$vaNumber) {
            return back()->with('error','Invalid payment method');
        }

        $payment = Payment::create([
            'transaction_id' => $transaction->id,
            'payment_method' => $method,
            'amount' => $transaction->total_price,
            'payment_proof' => $vaNumber,
            'status' => 'pending'
        ]);

        /* =====================
        | ACTIVITY + NOTIF (PENDING)
        ===================== */
        ActivityLog::create([
            'user_id' => $transaction->booking->user_id,
            'activity' => "Payment pending {$transaction->booking->vehicle->plate_number}",
            'ip_address' => request()->ip()
        ]);

        Notification::create([
            'user_id' => $transaction->booking->user_id,
            'title' => 'Pembayaran Diproses',
            'message' => 'Pembayaran sedang diproses',
            'is_read' => false
        ]);

        /* =====================
        | SIMULASI PAYMENT SUCCESS
        ===================== */
        dispatch(function () use ($payment, $transaction) {

            sleep(5);

            $result = rand(1,10) > 2 ? 'paid' : 'unpaid';

            $payment->update([
                'status' => $result,
                'payment_date' => $result == 'paid' ? now() : null,
            ]);

            $transaction->update([
                'payment_status' => $result
            ]);

            /* =====================
            | SUCCESS ONLY
            ===================== */
            if ($result == 'paid') {

                // ACTIVITY LOG SUCCESS
                ActivityLog::create([
                    'user_id' => $transaction->booking->user_id,
                    'activity' => "Payment SUCCESS {$transaction->booking->vehicle->plate_number}",
                    'ip_address' => request()->ip()
                ]);

                // CUSTOMER NOTIF
                Notification::create([
                    'user_id' => $transaction->booking->user_id,
                    'title' => 'Pembayaran Berhasil',
                    'message' => 'Pembayaran sukses untuk ' . $transaction->booking->vehicle->plate_number,
                    'is_read' => false
                ]);

                // ADMIN NOTIF
                $admins = User::whereHas('role', fn($q) => $q->where('role_name','admin'))->get();

                foreach ($admins as $admin) {
                    Notification::create([
                        'user_id' => $admin->id,
                        'title' => 'PAYMENT SUCCESS - ' . $transaction->booking->user->name,
                        'message' => $transaction->booking->vehicle->plate_number . ' sudah bayar',
                        'is_read' => false
                    ]);
                }
            }

        })->afterResponse();

        return redirect()->route('payment.waiting', $payment->id);
    }

    public function waiting($id)
    {
        return view('scanner.payment-waiting', [
            'payment' => Payment::findOrFail($id)
        ]);
    }

    public function status($id)
    {
        return response()->json([
            'status' => Payment::findOrFail($id)->status
        ]);
    }

    public function success($id)
    {
        return view('scanner.payment-success', [
            'payment' => Payment::findOrFail($id)
        ]);
    }

    public function failed($id)
    {
        return view('scanner.payment-failed', [
            'payment' => Payment::findOrFail($id)
        ]);
    }

    public function exportExcel()
    {
        return Excel::download(
            new TransactionExport,
            'laporan-transaksi.xlsx'
        );
    }

    public function exportPdf()
    {
        $transactions = ParkingTransaction::with([
            'booking.vehicle',
            'booking.user'
        ])->latest()->get();

        $pdf = Pdf::loadView('transactions.pdf', compact('transactions'));

        return $pdf->download('laporan-transaksi.pdf');
    }
}