<!DOCTYPE html>
<html>
<head>

    <title>QR Scanner</title>

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>

<body style="background:#f4f6f9;">

<div class="container mt-5">

    <div class="card shadow border-0 rounded-4">

        <div class="card-body text-center">

            <h2 class="mb-4">

                {{ strtoupper($type) }} SCANNER

            </h2>

            <div id="reader"></div>

            <a
                href="/"
                class="btn btn-secondary mt-4"
            >
                Back
            </a>

        </div>

    </div>

</div>


<script src="https://unpkg.com/html5-qrcode"></script>

<script>

    function onScanSuccess(decodedText)
    {
        window.location.href =
            "/scan/" +
            decodedText +
            "?type={{ $type }}";
    }

    let html5QrcodeScanner =
        new Html5QrcodeScanner(
            "reader",
            {
                fps: 10,
                qrbox: 250
            }
        );

    html5QrcodeScanner.render(
        onScanSuccess
    );

</script>

</body>
</html>