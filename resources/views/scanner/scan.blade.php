<!DOCTYPE html>
<html>

<head>

    <title>QR Scanner</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body style="background:#f4f6f9;">

    <div class="container mt-5">

        <div class="card shadow border-0 rounded-4">

            <div class="card-body text-center">

                <h2 class="mb-4">

                    {{ strtoupper($type) }} SCANNER

                </h2>

                <div id="reader"></div>

                <p class="text-secondary mt-3">

                    Point camera to QR Code

                </p>

                <a href="/" class="btn btn-secondary mt-4">
                    Back
                </a>

            </div>

        </div>

    </div>


    <script src="https://unpkg.com/html5-qrcode"></script>

    <script src="https://unpkg.com/html5-qrcode"></script>

    <script>
        function onScanSuccess(decodedText)
{

    console.log(decodedText);



    /*
    |--------------------------------------------------------------------------
    | kalau hasil scan full url
    |--------------------------------------------------------------------------
    */

    if (
        decodedText.startsWith('http')
    ) {

        let url =
            new URL(decodedText);



        let parts =
            url.pathname.split('/');



        let token =
            parts[2];



        window.location.href =
            "/scan/" +
            token +
            "?type={{ $type }}";

    }



    /*
    |--------------------------------------------------------------------------
    | kalau hasil scan token langsung
    |--------------------------------------------------------------------------
    */

    else {

        window.location.href =
            "/scan/" +
            decodedText +
            "?type={{ $type }}";

    }

}



    function onScanFailure(error)
    {
        //
        // kosong biar ga spam 😭
        //
    }



    let html5QrcodeScanner =
        new Html5QrcodeScanner(
            "reader",
            {
                fps: 5,
                qrbox: 250,
                rememberLastUsedCamera: true,
                showTorchButtonIfSupported: true
            }
        );



    html5QrcodeScanner.render(
        onScanSuccess,
        onScanFailure
    );



    /*
    |--------------------------------------------------------------------------
    | HIDE DEBUG TEXT 😭🔥
    |--------------------------------------------------------------------------
    */

    setInterval(() => {

        let spans =
            document.querySelectorAll('span');

        spans.forEach(span => {

            if (
                span.innerText.includes(
                    'No MultiFormat Readers'
                )
            ) {

                span.style.display = 'none';

            }

        });

    }, 500);

    </script>

</body>

</html>