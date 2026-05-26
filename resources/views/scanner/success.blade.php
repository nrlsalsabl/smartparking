<!DOCTYPE html>
<html>
<head>

    <title>Scanner Result</title>

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

    <h1 class="text-success">

        {{ $title }}

    </h1>

    <p class="mt-3">

        {{ $message }}

    </p>

    <hr>

    <p>

        Booking:
        {{ $booking->booking_code }}

    </p>

    <p>

        Vehicle:
        {{ $booking->vehicle->plate_number }}

    </p>


    @isset($duration)

        <p>

            Duration:
            {{ $duration }} hour

        </p>

    @endisset


    @if(isset($total))

    <a
        href="/payment/{{ $booking->id }}"
        class="btn btn-success w-100 mt-3"
    >
        Pay Now
    </a>

    @endif


    <a
        href="/"
        class="btn btn-primary mt-3"
    >
        Scan Again
    </a>

</div>

</body>
</html>