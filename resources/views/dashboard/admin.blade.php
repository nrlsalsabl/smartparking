@extends('layouts.app')

@section('content')

<div class="container">

    <h2 class="mb-4">
        Admin Dashboard
    </h2>



    <div class="row mb-4">

        <div class="col-md-4">

            <div class="card shadow border-0">

                <div class="card-body">

                    <h5>Total Vehicles</h5>

                    <h2>
                        {{ $totalVehicles }}
                    </h2>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card shadow border-0">

                <div class="card-body">

                    <h5>Total Bookings</h5>

                    <h2>
                        {{ $totalBookings }}
                    </h2>

                </div>

            </div>

        </div>

        <div class="col-md-4">

            <div class="card shadow border-0">

                <div class="card-body">

                    <h5>Total Transactions</h5>

                    <h2>
                        {{ $totalTransactions }}
                    </h2>

                </div>

            </div>

        </div>

    </div>



    <div class="card shadow border-0">

        <div class="card-body">

            <h4 class="mb-4">
                Parking Transactions Chart
            </h4>

            <div style="height:300px;">
                <canvas id="parkingChart"></canvas>
            </div>

        </div>

    </div>


    <div class="card shadow mt-4">

        <div class="card-header">

            <b>Activity Logs</b>

        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <thead>

                    <tr>

                        <th>Activity</th>

                        <th>IP Address</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($logs as $log)

                    <tr>

                        <td>
                            {{ str_replace('Anda', $log->user->name, $log->activity) }}
                        </td>

                        <td>

                            {{ $log->ip_address }}

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td colspan="2">

                            Tidak ada aktivitas

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

            {{ $logs->links() }}

        </div>

    </div>

</div>



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const chartData = @json($chartData);

    const labels =
        chartData.map(
            item => item.date
        );

    const totals =
        chartData.map(
            item => item.total
        );

    new Chart(

        document.getElementById(
            'parkingChart'
        ),

        {
            type: 'bar',

            data: {

                labels: labels,

                datasets: [{

                    label:
                        'Transactions',

                    data: totals,

                    borderWidth: 1,
                    borderRadius: 8,
                    backgroundColor: '#2563eb',
                }]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false
            }

        }

    );

</script>

@endsection