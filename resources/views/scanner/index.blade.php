<!DOCTYPE html>
<html>
<head>

    <title>SmartPark Scanner</title>

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>

<body
    class="d-flex
           justify-content-center
           align-items-center"
    style="height:100vh;
           background:#f4f6f9;"
>

<div class="card shadow border-0 rounded-4 p-5 text-center">

    <h1 class="mb-4">
        SmartPark Scanner
    </h1>

    <p class="text-secondary mb-4">

        Select scanner mode

    </p>

    <div class="d-grid gap-3">

        <a
            href="/scan-page/checkin"
            class="btn btn-success btn-lg"
        >
            CHECKIN
        </a>

        <a
            href="/scan-page/checkout"
            class="btn btn-danger btn-lg"
        >
            CHECKOUT
        </a>

    </div>

</div>

</body>
</html>