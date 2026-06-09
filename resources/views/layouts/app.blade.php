<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>SmartPark</title>

    @vite([
    'resources/css/app.css',
    'resources/js/app.js'
    ])

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f6f9;
        }

        .sidebar {
            width: 260px;
            min-height: 100vh;
            background: #212529;
            position: fixed;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 12px 18px;
            border-radius: 10px;
            margin-bottom: 10px;
            transition: .3s;
        }

        .sidebar a:hover {
            background: #343a40;
        }

        .main-content {
            margin-left: 260px;
            padding: 30px;
        }

        .topbar {
            background: white;
            padding: 20px;
            border-radius: 20px;
        }

        .card-custom {
            border: none;
            border-radius: 20px;
        }
    </style>

</head>

<body>

    <div class="d-flex">

        {{-- SIDEBAR --}}
        <div class="sidebar p-4">

            <h2 class="text-white mb-4">
                SmartPark
            </h2>

            <p class="text-secondary">
                {{ auth()->user()->role->role_name }}
            </p>

            <hr class="text-secondary">

            {{-- ADMIN --}}
            @if(auth()->user()->role->role_name == 'admin')

            <a href="/dashboard">
                Dashboard
            </a>

            <a href="/vehicle-types">
                Vehicle Types
            </a>

            <a href="/parking-areas">
                Parking Areas
            </a>

            <a href="/transactions">
                Transactions
            </a>

            <a href="/activity-log">
                Activity Logs
            </a>

            @endif


            {{-- CUSTOMER --}}
            @if(auth()->user()->role->role_name == 'customer')

            <a href="/dashboard">
                Dashboard
            </a>

            <a href="/vehicles">
                My Vehicles
            </a>

            <a href="/bookings">
                My Bookings
            </a>

            <li class="nav-item">
                <a href="{{ route('customer.activity-log') }}" class="nav-link">
                    <i class="fas fa-history"></i>
                    <span>Activity Log</span>
                </a>
            </li>

            @endif


            {{-- OFFICER --}}
            @if(auth()->user()->role->role_name == 'officer')

            <a href="/dashboard">
                Dashboard
            </a>

            <a href="/transactions">
                Transactions
            </a>

            @endif


            <hr class="text-secondary mt-4">

            <form action="{{ route('logout') }}" method="POST">

                @csrf

                <button class="btn btn-danger w-100">
                    Logout
                </button>

            </form>

        </div>



        {{-- MAIN --}}
        <div class="main-content w-100">

            {{-- TOPBAR --}}
            <div class="topbar shadow-sm mb-4">

                <div class="d-flex
                        justify-content-between
                        align-items-center">

                    <div>

                        <h3 class="mb-1">

                            Welcome,
                            {{ auth()->user()->name }}

                        </h3>

                        <small class="text-secondary">

                            Smart Parking Management System

                        </small>

                    </div>

                    <span class="badge bg-primary fs-6">

                        {{ auth()->user()->role->role_name }}

                    </span>

                </div>

            </div>


            {{-- CONTENT --}}
            @yield('content')

        </div>

    </div>

</body>

</html>