<!DOCTYPE html>
<html>

<head>

    <title>Payment Success</title>

    <meta http-equiv="refresh" content="5;url=/">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="d-flex
           justify-content-center
           align-items-center" style="height:100vh;
           background:#f4f6f9;">

    <div class="card shadow border-0 rounded-4 p-5 text-center">

        <h1 class="text-success">
            PAYMENT SUCCESS
        </h1>

        <p class="mt-3">

            Vehicle payment completed

        </p>

        <h3 class="text-primary">

            Rp
            {{ number_format($transaction->total_price) }}

        </h3>

        <p class="text-secondary mt-4">

            Redirecting to scanner in 5 seconds...

        </p>

    </div>

</body>

</html>