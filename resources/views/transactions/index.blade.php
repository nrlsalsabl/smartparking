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
                            <th>Action</th>

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

                            <td>

                                @if($item->status == 'active')

                                <form action="{{ url('/checkout/' . $item->id) }}" method="POST">

                                    @csrf

                                    <button class="btn btn-danger btn-sm">
                                        Checkout
                                    </button>

                                </form>

                                @endif

                            </td>

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