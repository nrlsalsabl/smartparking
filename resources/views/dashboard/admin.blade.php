@extends('layouts.app')

@section('content')

<div class="row">

    <div class="col-md-4 mb-4">

        <div class="card card-custom shadow-sm">

            <div class="card-body">

                <h5>Total Transactions</h5>

                <h1>
                    {{ \App\Models\ParkingTransaction::count() }}
                </h1>

            </div>

        </div>

    </div>



    <div class="col-md-4 mb-4">

        <div class="card card-custom shadow-sm">

            <div class="card-body">

                <h5>Total Vehicles</h5>

                <h1>
                    {{ \App\Models\Vehicle::count() }}
                </h1>

            </div>

        </div>

    </div>



    <div class="col-md-4 mb-4">

        <div class="card card-custom shadow-sm">

            <div class="card-body">

                <h5>Total Bookings</h5>

                <h1>
                    {{ \App\Models\Booking::count() }}
                </h1>

            </div>

        </div>

    </div>

</div>


<div class="card card-custom shadow-sm">

    <div class="card-body">

        <h4>
            SmartPark Information
        </h4>

        <p>
            Welcome to SmartPark Dashboard.
            This system manages parking,
            booking, QR code,
            transactions, and payments.
        </p>

    </div>

</div>

@endsection