<!DOCTYPE html>
<html>

<head>

    <title>Payment</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="d-flex
           justify-content-center
           align-items-center" style="height:100vh;
           background:#f4f6f9;">

    <div class="card shadow border-0 rounded-4 p-5 text-center">

        <h1 class="mb-4">
            PAYMENT
        </h1>

        <p>

            Vehicle:
            {{ $transaction->booking->vehicle->plate_number }}

        </p>

        <p>

            Duration:
            {{ $transaction->duration }} hour

        </p>

        <h2 class="text-primary mb-4">

            Rp
            {{ number_format($transaction->total_price) }}

        </h2>

        <form action="/payment/{{ $transaction->id }}" method="POST">

            @csrf

            <button class="btn btn-success btn-lg w-100">
                PAY NOW
            </button>

        </form>

    </div>

</body>

</html>