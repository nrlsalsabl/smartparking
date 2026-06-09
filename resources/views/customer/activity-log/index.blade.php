@extends('layouts.app')

@section('content')

<div class="container mt-5">

    <h2 class="mb-3">
        My Activity Logs
    </h2>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow">

        <div class="card-body">

            <form action="{{ route('customer.activity-log') }}" method="GET" class="mb-3">

                <div class="row">

                    <div class="col-md-4">

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Search Activity..."
                            value="{{ request('search') }}"
                        >

                    </div>

                    <div class="col-md-2">

                        <button class="btn btn-primary w-100">
                            Search
                        </button>

                    </div>

                </div>

            </form>

            <table class="table table-bordered">

                <thead>

                    <tr>
                        <th>No</th>
                        <th>Activity</th>
                        <th>IP Address</th>
                        <th>Date</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($logs as $log)

                        <tr>

                            <td>
                                {{ $logs->firstItem() + $loop->index }}
                            </td>

                            <td>
                                {{ $log->activity }}
                            </td>

                            <td>
                                {{ $log->ip_address }}
                            </td>

                            <td>
                                {{ $log->created_at->format('d M Y H:i') }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="4" class="text-center">
                                No Activity Logs
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

            {{ $logs->links() }}

        </div>

    </div>

</div>

@endsection