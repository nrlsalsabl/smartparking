@extends('layouts.app')

@section('content')

    <div class="container mt-5">

        <div class="d-flex justify-content-between mb-3">

            <h2>Bookings</h2>

            <a href="{{ route('bookings.create') }}" class="btn btn-primary">
                Create Booking
            </a>

        </div>

        @if(session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

        @endif

        @if(session('error'))

        <div class="alert alert-danger">
            {{ session('error') }}
        </div>

        @endif

        <div class="card shadow">

            <div class="card-body">

                <table class="table table-bordered">

                    <thead>

                        <tr>

                            <th>No</th>
                            <th>Booking Code</th>
                            <th>Vehicle</th>
                            <th>Area</th>
                            <th>Status</th>
                            <th>QR</th>
                            <th>Action</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($bookings as $item)

                        <tr>

                            <td>
                                {{ $loop->iteration }}
                            </td>

                            <td>
                                {{ $item->booking_code }}
                            </td>

                            <td>
                                {{ $item->vehicle->plate_number }}
                            </td>

                            <td>
                                {{ $item->parkingArea->area_name }}
                            </td>

                            <td>
                                {{ $item->status }}
                            </td>

                            <td>

                                {!! QrCode::size(80)->generate(
                                $item->qrCode->qr_token
                                ) !!}

                                <br>

                                <a href="{{ url('/scan/' . $item->qrCode->qr_token) }}"
                                    class="btn btn-success btn-sm mt-2">

                                    Scan QR

                                </a>

                            </td>

                            <td>

                                <form action="{{ route('bookings.destroy', $item->id) }}" method="POST">

                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-danger btn-sm">
                                        Delete
                                    </button>

                                </form>

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td colspan="7" class="text-center">
                                No Data
                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

                {{ $bookings->links() }}

            </div>

        </div>

    </div>

@endsection