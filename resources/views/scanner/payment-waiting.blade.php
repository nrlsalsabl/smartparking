<!DOCTYPE html>
<html>
<head>
    <title>Payment Processing</title>

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
            width: 380px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            text-align: center;
        }

        .title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .status {
            padding: 5px 10px;
            background: orange;
            color: white;
            display: inline-block;
            border-radius: 20px;
            margin-bottom: 15px;
        }

        .va-box {
            background: #f1f1f1;
            padding: 15px;
            border-radius: 10px;
            margin: 15px 0;
        }

        .va-number {
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 2px;
            color: #333;
        }

        button {
            width: 100%;
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

        .hint {
            font-size: 12px;
            color: gray;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="card">

    <div class="title">Payment Processing</div>

    <div class="status" id="status">
        {{ $payment->status }}
    </div>

    <p>Metode: {{ strtoupper($payment->payment_method) }}</p>

    <div class="va-box">
        <div>Virtual Account</div>
        <div class="va-number">
            {{ $payment->payment_proof }}
        </div>
    </div>

    <button onclick="payNow()">
        Saya Sudah Bayar
    </button>

    <div class="hint">
        Setelah klik, sistem akan verifikasi otomatis...
    </div>

</div>

<script>
function payNow() {

    fetch("/payments/manual-success/{{ $payment->id }}", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Content-Type": "application/json",
            "Accept": "application/json"
        }
    })
    .then(res => res.json())
    .then(() => {

        document.getElementById('status').innerText = "PAID";
        document.getElementById('status').style.background = "green";

        setTimeout(() => {
            window.location.href = "/";
        }, 5000);

    });

}
</script>

</body>
</html>