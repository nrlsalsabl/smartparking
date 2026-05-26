@extends('layouts.app')

@section('content')

    <div class="container mt-5">

        <div class="card shadow">

            <div class="card-body">

                <h2>Edit Vehicle</h2>

                <form action="{{ route('vehicles.update', $vehicle->id) }}" method="POST" enctype="multipart/form-data">

                    @csrf
                    @method('PUT')

                    <div class="mb-3">

                        <label>Vehicle Type</label>

                        <select name="vehicle_type_id" class="form-control">

                            @foreach($vehicleTypes as $type)

                            <option value="{{ $type->id }}" {{ $vehicle->vehicle_type_id == $type->id ? 'selected' : ''
                                }}
                                >
                                {{ $type->type_name }}
                            </option>

                            @endforeach

                        </select>

                    </div>

                    <div class="mb-3">

                        <label>Plate Number</label>

                        <input type="text" name="plate_number" class="form-control"
                            value="{{ $vehicle->plate_number }}">

                    </div>

                    <div class="mb-3">

                        <label>Brand</label>

                        <input type="text" name="brand" class="form-control" value="{{ $vehicle->brand }}">

                    </div>

                    <div class="mb-3">

                        <label>Color</label>

                        <input type="text" name="color" class="form-control" value="{{ $vehicle->color }}">

                    </div>

                    <div class="mb-3">

                        <label>Vehicle Photo</label>

                        <input type="file" name="vehicle_photo" class="form-control">

                    </div>

                    <button type="submit" class="btn btn-primary">
                        Update
                    </button>

                </form>

            </div>

        </div>

    </div>

@endsection