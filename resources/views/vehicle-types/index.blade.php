@extends('layouts.app')

@section('content')

<div class="card card-custom shadow-sm">

    <div class="card-body">

        <div class="d-flex
                    justify-content-between
                    mb-3">

            <h3>
                Vehicle Types
            </h3>

            <a
                href="{{ route('vehicle-types.create') }}"
                class="btn btn-primary"
            >
                Add Data
            </a>

        </div>

        @if(session('success'))

            <div class="alert alert-success">

                {{ session('success') }}

            </div>

        @endif

        <table class="table table-bordered">

            <thead>

                <tr>

                    <th>No</th>
                    <th>Name</th>
                    <th>Price / Hour</th>
                    <th>Action</th>

                </tr>

            </thead>

            <tbody>

                @forelse($vehicleTypes as $item)

                <tr>

                    <td>
                        {{ $loop->iteration }}
                    </td>

                    <td>
                        {{ $item->type_name }}
                    </td>

                    <td>
                        Rp
                        {{ number_format($item->price_per_hour) }}
                    </td>

                    <td>

                        <a
                            href="{{ route('vehicle-types.edit', $item->id) }}"
                            class="btn btn-warning btn-sm"
                        >
                            Edit
                        </a>

                        <form
                            action="{{ route('vehicle-types.destroy', $item->id) }}"
                            method="POST"
                            class="d-inline"
                        >

                            @csrf
                            @method('DELETE')

                            <button
                                class="btn btn-danger btn-sm"
                            >
                                Delete
                            </button>

                        </form>

                    </td>

                </tr>

                @empty

                <tr>

                    <td colspan="4" class="text-center">

                        No Data

                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

        {{ $vehicleTypes->links() }}

    </div>

</div>

@endsection