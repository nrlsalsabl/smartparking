@extends('layouts.app')

@section('content')

<div class="container mt-5">

    <div class="d-flex justify-content-between mb-3">

        <h2>Vehicles</h2>

        <a href="{{ route('vehicles.create') }}" class="btn btn-primary">
            Add Vehicle
        </a>

    </div>

    @if(session('success'))

    <div class="alert alert-success">
        {{ session('success') }}
    </div>

    @endif

    <div class="card shadow">

        <div class="card-body">

            <form method="GET">

                <div class="row mb-3">

                    <div class="col-md-4">

                        <input type="text" name="search" class="form-control" placeholder="Search plate number..."
                            value="{{ $search }}">

                    </div>

                    <div class="col-md-2">

                        <button class="btn btn-primary">
                            Search
                        </button>

                    </div>

                </div>

            </form>

            <table class="table table-bordered">

                <thead>

                    <tr>

                        <th>No</th>
                        <th>Photo</th>
                        <th>Plate</th>
                        <th>Type</th>
                        <th>Brand</th>
                        <th>Color</th>
                        <th>Action</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($vehicles as $item)

                    <tr>

                        <td>
                            {{ $loop->iteration }}
                        </td>

                        <td>

                            @if($item->vehicle_photo)

                            <img src="{{ asset('storage/' . $item->vehicle_photo) }}" width="80">

                            @else

                            No Photo

                            @endif

                        </td>

                        <td>
                            {{ $item->plate_number }}
                        </td>

                        <td>
                            {{ $item->vehicleType->type_name }}
                        </td>

                        <td>
                            {{ $item->brand }}
                        </td>

                        <td>
                            {{ $item->color }}
                        </td>

                        <td>

                            <a href="{{ route('vehicles.edit', $item->id) }}" class="btn btn-warning btn-sm">
                                Edit
                            </a>

                            <form action="{{ route('vehicles.destroy', $item->id) }}" method="POST" class="d-inline">

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

            {{ $vehicles->links() }}

        </div>

    </div>

</div>

@endsection