@extends('layouts.app')

@section('content')

<div class="container">

    <!-- HEADER -->
    <div class="card card-custom shadow-sm mb-4">
        <div class="card-body">
            <h3>Customer Dashboard</h3>
            <p>Manage your vehicles and bookings.</p>
        </div>
    </div>

    <!-- SUMMARY -->
    <div class="row mb-4">

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5>Total Booking</h5>
                    <h3>{{ $bookings->count() }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5>Active</h5>
                    <h3>
                        {{ $bookings->where('status','active')->count() }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5>Completed</h5>
                    <h3>
                        {{ $bookings->where('status','completed')->count() }}
                    </h3>
                </div>
            </div>
        </div>

    </div>

    <!-- BOOKING LIST -->
    <div class="card shadow-sm">
        <div class="card-body">

            <h5>Your Booking History</h5>

            <table class="table table-striped mt-3">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Vehicle</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Checkin</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($bookings as $booking)
                    <tr>
                        <td>#{{ $booking->id }}</td>

                        <td>
                            {{ $booking->vehicle->plate_number ?? '-' }}
                        </td>

                        <td>
                            @if($booking->status == 'active')
                                <span class="badge bg-warning">Active</span>
                            @else
                                <span class="badge bg-success">Completed</span>
                            @endif
                        </td>

                        <td>
                            @if($booking->parkingTransaction?->payment_status == 'paid')
                                <span class="badge bg-success">Paid</span>
                            @else
                                <span class="badge bg-danger">Unpaid</span>
                            @endif
                        </td>

                        <td>
                            {{ $booking->parkingTransaction->checkin_at ?? '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>

            </table>

        </div>
    </div>

</div>

@endsection