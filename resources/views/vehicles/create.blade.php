@extends('layouts.app')

@section('content')

    <div class="container mt-5">

        <div class="card shadow">

            <div class="card-body">

                <h2>Create Vehicle</h2>

                <form action="{{ route('vehicles.store') }}" method="POST" enctype="multipart/form-data">

                    @csrf

                    <div class="mb-3">

                        <label>Vehicle Type</label>

                        <select name="vehicle_type_id" class="form-control">

                            <option value="">
                                -- Select Type --
                            </option>

                            @foreach($vehicleTypes as $type)

                            <option value="{{ $type->id }}">
                                {{ $type->type_name }}
                            </option>

                            @endforeach

                        </select>

                    </div>

                    <div class="mb-3">

                        <label>Plate Number</label>

                        <input type="text" name="plate_number" class="form-control">

                    </div>

                    <div class="mb-3">

                        <label>Brand</label>

                        <input type="text" name="brand" class="form-control">

                    </div>

                    <div class="mb-3">

                        <label>Color</label>

                        <input type="text" name="color" class="form-control">

                    </div>

                    <div class="mb-3">

                        <label>Vehicle Photo</label>

                        <input type="file" name="vehicle_photo" class="form-control">

                    </div>

                    <button type="submit" class="btn btn-primary">
                        Save
                    </button>

                </form>

            </div>

        </div>

    </div>

@endsection