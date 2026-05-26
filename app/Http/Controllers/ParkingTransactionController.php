<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\ParkingTransaction;
use Carbon\Carbon;

class ParkingTransactionController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | TRANSACTION LIST
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $transactions = ParkingTransaction::with([
            'booking.vehicle',
            'booking.user'
        ])->latest()->paginate(10);

        return view('transactions.index', compact('transactions'));
    }



    /*
    |--------------------------------------------------------------------------
    | SCAN QR (TIDAK DIUBAH LOGIC UTAMA)
    |--------------------------------------------------------------------------
    */
    public function scan($token)
    {
        $type = request('type');

        $qr = QrCode::where('qr_token', $token)->first();

        if (!$qr) {
            return view('scanner.failed', ['message' => 'QR not found']);
        }

        if ($qr->expired_at < now()) {
            return view('scanner.failed', ['message' => 'QR expired']);
        }

        $booking = $qr->booking;

        /*
        | CHECKIN
        */
        if ($type == 'checkin' && $booking->status == 'pending') {

            ParkingTransaction::create([
                'booking_id' => $booking->id,
                'checkin_at' => now(),
                'payment_status' => 'unpaid',
                'status' => 'active'
            ]);

            $booking->update([
                'status' => 'active'
            ]);

            return view('scanner.success', [
                'title' => 'CHECKIN SUCCESS',
                'message' => 'Vehicle entered parking area',
                'booking' => $booking
            ]);
        }

        /*
        | CHECKOUT
        */
        if ($type == 'checkout' && $booking->status == 'active') {

            $transaction = ParkingTransaction::where('booking_id', $booking->id)
                ->where('status', 'active')
                ->first();

            if (!$transaction) {
                return view('scanner.failed', ['message' => 'Transaction not found']);
            }

            $checkin = Carbon::parse($transaction->checkin_at);
            $checkout = now();

            $duration = $checkin->diffInHours($checkout);
            if ($duration < 1) $duration = 1;

            $vehicleType = $booking->vehicle->vehicleType;

            $total = $duration * $vehicleType->price_per_hour;

            $transaction->update([
                'checkout_at' => $checkout,
                'duration' => $duration,
                'total_price' => $total,
                'status' => 'completed'
            ]);

            $booking->update([
                'status' => 'completed'
            ]);

            return view('scanner.success', [
                'title' => 'CHECKOUT SUCCESS',
                'message' => 'Vehicle exited parking area',
                'booking' => $booking,
                'total' => $total,
                'duration' => $duration,
                'transaction' => $transaction
            ]);
        }

        return view('scanner.failed', [
            'message' => 'Invalid action'
        ]);
    }



    /*
    |--------------------------------------------------------------------------
    | PAYMENT PAGE
    |--------------------------------------------------------------------------
    */
    public function payment($id)
    {
        $transaction = ParkingTransaction::findOrFail($id);

        return view('scanner.payment-method', compact('transaction'));
    }



    /*
    |--------------------------------------------------------------------------
    | PROCESS PAYMENT (SIMULATOR FIXED)
    |--------------------------------------------------------------------------
    */
    public function processPayment(Request $request, $id)
    {
        $transaction = ParkingTransaction::findOrFail($id);

        $method = $request->method;

        // 👉 VA number
        $vaNumber = null;

        if ($method == 'va_bca') {
            $vaNumber = '014' . rand(1000000000, 9999999999);
        } elseif ($method == 'va_mandiri') {
            $vaNumber = '008' . rand(1000000000, 9999999999);
        } elseif ($method == 'va_bni') {
            $vaNumber = '009' . rand(1000000000, 9999999999);
        } elseif ($method == 'va_bri') {
            $vaNumber = '002' . rand(1000000000, 9999999999);
        } else {
            return redirect()->back()->with('error', 'Invalid payment method');
        }

        /*
        | CREATE PAYMENT
        */
        $payment = Payment::create([
            'transaction_id' => $transaction->id,
            'payment_method' => $method,
            'amount' => $transaction->total_price,
            'payment_proof' => $vaNumber,
            'payment_date' => null,
            'status' => 'pending'
        ]);

        /*
        | TRANSACTION STATUS
        */
        $transaction->update([
            'payment_status' => 'unpaid'
        ]);

        /*
        | SIMULATOR ENGINE
        */
        dispatch(function () use ($payment, $transaction) {

            sleep(5);

            // 👉 FIX: hanya hasil valid untuk transaction
            $result = rand(1, 10) > 2 ? 'paid' : 'unpaid';

            /*
            | UPDATE PAYMENT
            */
            $payment->update([
                'status' => $result,
                'payment_date' => $result === 'paid' ? now() : null,
                'payment_proof' => $result === 'paid'
                    ? 'SIMULATED-PROOF'
                    : $payment->payment_proof
            ]);

            /*
            | UPDATE TRANSACTION
            */
            $transaction->update([
                'payment_status' => $result
            ]);

        })->afterResponse();

        return redirect()->route('payment.waiting', $payment->id);
    }



    /*
    |--------------------------------------------------------------------------
    | WAITING PAGE
    |--------------------------------------------------------------------------
    */
    public function waiting($paymentId)
    {
        $payment = Payment::findOrFail($paymentId);

        return view('scanner.payment-waiting', compact('payment'));
    }


    /*
    |--------------------------------------------------------------------------
    | STATUS CHECK (AJAX)
    |--------------------------------------------------------------------------
    */
    public function status($id)
    {
        $payment = Payment::findOrFail($id);

        return response()->json([
            'status' => $payment->status
        ]);
    }



    /*
    |--------------------------------------------------------------------------
    | SUCCESS PAGE
    |--------------------------------------------------------------------------
    */
    public function success($id)
    {
        $payment = Payment::findOrFail($id);

        return view('scanner.payment-success', compact('payment'));
    }



    /*
    |--------------------------------------------------------------------------
    | FAILED PAGE
    |--------------------------------------------------------------------------
    */
    public function failed($id)
    {
        $payment = Payment::findOrFail($id);

        return view('scanner.payment-failed', compact('payment'));
    }

    public function manualSuccess($id)
    {
        $payment = Payment::findOrFail($id);

        // langsung sukseskan payment
        $payment->update([
            'status' => 'paid',
            'payment_date' => now()
        ]);

        $transaction = ParkingTransaction::findOrFail($payment->transaction_id);

        $transaction->update([
            'payment_status' => 'paid'
        ]);

        return response()->json([
            'message' => 'success'
        ]);
    }
}