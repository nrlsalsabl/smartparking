@extends('layouts.app')

@section('content')

<div class="container mt-5">

    <div class="card shadow">

        <div class="card-body">

            <h2>Create Booking</h2>

            <form action="{{ route('bookings.store') }}" method="POST">

                @csrf

                <div class="mb-3">

                    <label>Vehicle</label>

                    <select name="vehicle_id" class="form-control">

                        <option value="">
                            -- Select Vehicle --
                        </option>

                        @foreach($vehicles as $vehicle)

                        <option value="{{ $vehicle->id }}">

                            {{ $vehicle->plate_number }}

                        </option>

                        @endforeach

                    </select>

                </div>

                <div class="mb-3">

                    <label>Parking Area</label>

                    <select name="parking_area_id" class="form-control">

                        <option value="">
                            -- Select Area --
                        </option>

                        @foreach($parkingAreas as $area)

                        <option value="{{ $area->id }}">

                            {{ $area->area_name }}
                            -
                            Capacity:
                            {{ $area->capacity }}

                        </option>

                        @endforeach

                    </select>

                </div>

                <button type="submit" class="btn btn-primary">
                    Booking Now
                </button>

            </form>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if(session('error'))
<script>
    Swal.fire({
                icon: 'error',
                title: 'Gagal Booking',
                text: '{{ session('error') }}',
            });
</script>
@endif

@endsection