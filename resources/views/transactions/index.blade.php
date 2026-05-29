@extends('layouts.app')

@section('content')

<div class="container mt-5">

    <h2 class="mb-3">
        Parking Transactions
    </h2>

    @if(session('success'))

    <div class="alert alert-success">
        {{ session('success') }}
    </div>

    @endif

    <div class="card shadow">

        <div class="card-body">

            <div class="mb-3">

                <a href="/transactions/export/excel" class="btn btn-success">

                    Export Excel

                </a>

                <a href="/transactions/export/pdf" class="btn btn-danger">

                    Export PDF

                </a>

            </div>

            <form action="/transactions" method="GET" class="mb-3">

                <div class="row">

                    <div class="col-md-4">

                        <input type="text" name="search" class="form-control" placeholder="Search Plate Number..."
                            value="{{ request('search') }}">

                    </div>

                    <div class="col-md-2">

                        <button class="btn btn-primary w-100">

                            Search

                        </button>

                    </div>

                </div>

            </form>

            <table class="table table-bordered">

                <thead>

                    <tr>

                        <th>No</th>
                        <th>Vehicle</th>
                        <th>Checkin</th>
                        <th>Checkout</th>
                        <th>Duration</th>
                        <th>Total</th>
                        <th>Status</th>
                        {{-- <th>Action</th> --}}

                    </tr>

                </thead>

                <tbody>

                    @forelse($transactions as $item)

                    <tr>

                        <td>
                            {{ $loop->iteration }}
                        </td>

                        <td>
                            {{ $item->booking->vehicle->plate_number }}
                        </td>

                        <td>
                            {{ $item->checkin_at }}
                        </td>

                        <td>
                            {{ $item->checkout_at ?? '-' }}
                        </td>

                        <td>
                            {{ $item->duration ?? '-' }}
                        </td>

                        <td>

                            @if($item->total_price)

                            Rp
                            {{ number_format($item->total_price) }}

                            @else

                            -

                            @endif

                        </td>

                        <td>
                            {{ $item->status }}
                        </td>

                        {{-- <td>

                            @if($item->status == 'active')

                            <form action="{{ url('/checkout/' . $item->id) }}" method="POST">

                                @csrf

                                <button class="btn btn-danger btn-sm">
                                    Checkout
                                </button>

                            </form>

                            @endif

                        </td> --}}

                    </tr>

                    @empty

                    <tr>

                        <td colspan="8" class="text-center">
                            No Data
                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

            {{ $transactions->links() }}

        </div>

    </div>

</div>

@endsection