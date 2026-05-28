<!DOCTYPE html>
<html>
<head>
    <title>Payment Method</title>

    <style>
        body {
            font-family: Arial;
            background: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            width: 360px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .info {
            background: #f1f1f1;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }

        select {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        button {
            width: 100%;
            margin-top: 20px;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: #2563eb;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background: #1e4ed8;
        }

        .price {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="card">

    <h2>Payment</h2>

    <div class="price">
        Rp {{ number_format($transaction->total_price) }}
    </div>

    <div class="info">
        Transaction ID: {{ $transaction->id }} <br>
        Status: {{ $transaction->status }}
    </div>

    <form action="/payments/process/{{ $transaction->id }}" method="POST">
        @csrf

        <label>Pilih Metode Pembayaran</label>

        <select name="method" required>
            <option value="va_bca">VA BCA</option>
            <option value="va_mandiri">VA Mandiri</option>
            <option value="va_bni">VA BNI</option>
            <option value="va_bri">VA BRI</option>
        </select>

        <button type="submit">
            Bayar Sekarang
        </button>
    </form>

</div>

</body>
</html>