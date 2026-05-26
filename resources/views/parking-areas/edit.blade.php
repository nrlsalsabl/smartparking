@extends('layouts.app')

@section('content')

    <div class="container mt-5">

        <div class="card shadow">

            <div class="card-body">

                <h2>Edit Parking Area</h2>

                <form action="{{ route('parking-areas.update', $parkingArea->id) }}" method="POST">

                    @csrf
                    @method('PUT')

                    <div class="mb-3">

                        <label>Area Name</label>

                        <input type="text" name="area_name" class="form-control" value="{{ $parkingArea->area_name }}">

                    </div>

                    <div class="mb-3">

                        <label>Capacity</label>

                        <input type="number" name="capacity" class="form-control" value="{{ $parkingArea->capacity }}">

                    </div>

                    <div class="mb-3">

                        <label>Location</label>

                        <input type="text" name="location" class="form-control" value="{{ $parkingArea->location }}">

                    </div>

                    <div class="mb-3">

                        <label>Description</label>

                        <textarea name="description" class="form-control">{{ $parkingArea->description }}</textarea>

                    </div>

                    <button type="submit" class="btn btn-primary">
                        Update
                    </button>

                </form>

            </div>

        </div>

    </div>

@endsection