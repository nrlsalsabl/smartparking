@extends('layouts.app')

@section('content')

<div class="container mt-5">

    <div class="card shadow">

        <div class="card-body">

            <h2 class="mb-4">
                Edit Vehicle Type
            </h2>

            <form
                action="{{ route('vehicle-types.update', $vehicleType->id) }}"
                method="POST"
            >

                @csrf
                @method('PUT')

                <div class="mb-3">

                    <label class="form-label">
                        Vehicle Type
                    </label>

                    <input
                        type="text"
                        name="type_name"
                        class="form-control"
                        value="{{ old('type_name', $vehicleType->type_name) }}"
                    >

                    @error('type_name')

                        <small class="text-danger">
                            {{ $message }}
                        </small>

                    @enderror

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Price Per Hour
                    </label>

                    <input
                        type="number"
                        name="price_per_hour"
                        class="form-control"
                        value="{{ old('price_per_hour', $vehicleType->price_per_hour) }}"
                    >

                    @error('price_per_hour')

                        <small class="text-danger">
                            {{ $message }}
                        </small>

                    @enderror

                </div>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Update
                </button>

                <a
                    href="{{ route('vehicle-types.index') }}"
                    class="btn btn-secondary"
                >
                    Back
                </a>

            </form>

        </div>

    </div>

</div>

@endsection