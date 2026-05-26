<!DOCTYPE html>
<html>

<head>

    <title>Scan Failed</title>

    <meta
    http-equiv="refresh"
    content="9;url=/"
    >

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="d-flex
           justify-content-center
           align-items-center" style="height:100vh;
           background:#f4f6f9;">

    <div class="card shadow border-0 rounded-4 p-5 text-center">

        <h1 class="text-danger">
            SCAN FAILED
        </h1>

        <p class="mt-3">

            {{ $message }}

        </p>

        <a href="/" class="btn btn-primary mt-3">
            Scan Again
        </a>

        <p class="text-secondary mt-3">

        Redirecting to home in 7 seconds...

        </p>

    </div>

</body>

</html>