@extends('layouts.app')

@section('content')

<div class="container mt-5">

    <div class="d-flex justify-content-between mb-3">

        <h2>Parking Areas</h2>

        <a
            href="{{ route('parking-areas.create') }}"
            class="btn btn-primary"
        >
            Add Parking Area
        </a>

    </div>

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
                        <th>Area</th>
                        <th>Capacity</th>
                        <th>Location</th>
                        <th>Action</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($parkingAreas as $item)

                    <tr>

                        <td>
                            {{ $loop->iteration }}
                        </td>

                        <td>
                            {{ $item->area_name }}
                        </td>

                        <td>
                            {{ $item->capacity }}
                        </td>

                        <td>
                            {{ $item->location }}
                        </td>

                        <td>

                            <a
                                href="{{ route('parking-areas.edit', $item->id) }}"
                                class="btn btn-warning btn-sm"
                            >
                                Edit
                            </a>

                            <form
                                action="{{ route('parking-areas.destroy', $item->id) }}"
                                method="POST"
                                class="d-inline"
                            >

                                @csrf
                                @method('DELETE')

                                <button
                                    class="btn btn-danger btn-sm"
                                    type="submit"
                                >
                                    Delete
                                </button>

                            </form>

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td colspan="5" class="text-center">
                            No Data
                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

            {{ $parkingAreas->links() }}

        </div>

    </div>

</div>

@endsection