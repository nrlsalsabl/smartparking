@extends('layouts.app')

@section('content')

<div class="container mt-5">

    <div class="card shadow">

        <div class="card-body">

            <h2>Create Parking Area</h2>

            <form
                action="{{ route('parking-areas.store') }}"
                method="POST"
            >

                @csrf

                <div class="mb-3">

                    <label>Area Name</label>

                    <input
                        type="text"
                        name="area_name"
                        class="form-control"
                    >

                </div>

                <div class="mb-3">

                    <label>Capacity</label>

                    <input
                        type="number"
                        name="capacity"
                        class="form-control"
                    >

                </div>

                <div class="mb-3">

                    <label>Location</label>

                    <input
                        type="text"
                        name="location"
                        class="form-control"
                    >

                </div>

                <div class="mb-3">

                    <label>Description</label>

                    <textarea
                        name="description"
                        class="form-control"
                    ></textarea>

                </div>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Save
                </button>

            </form>

        </div>

    </div>

</div>

@endsection