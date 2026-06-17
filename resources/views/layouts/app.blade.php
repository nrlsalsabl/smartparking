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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

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
    </style>
</head>

<body>

    <div class="d-flex">

        {{-- SIDEBAR --}}
        <div class="sidebar p-4">

            <h2 class="text-white mb-4">
                <i class="fa-solid fa-square-parking"></i> SmartPark
            </h2>

            <p class="text-secondary">
                <i class="fa-solid fa-user me-1"></i>
                {{ auth()->user()->role->role_name }}
            </p>

            <hr class="text-secondary">

            {{-- ADMIN --}}
            @if(auth()->user()->role->role_name == 'admin')

            <a href="/dashboard"><i class="fa-solid fa-gauge me-2"></i> Dashboard</a>
            <a href="/vehicle-types"><i class="fa-solid fa-car-side me-2"></i> Vehicle Types</a>
            <a href="/parking-areas"><i class="fa-solid fa-square-parking me-2"></i> Parking Areas</a>
            <a href="/transactions"><i class="fa-solid fa-receipt me-2"></i> Transactions</a>
            <a href="/activity-log"><i class="fa-solid fa-clock-rotate-left me-2"></i> Activity Logs</a>

            @endif

            {{-- CUSTOMER --}}
            @if(auth()->user()->role->role_name == 'customer')

            <a href="/dashboard"><i class="fa-solid fa-gauge me-2"></i> Dashboard</a>
            <a href="/vehicles"><i class="fa-solid fa-car me-2"></i> My Vehicles</a>
            <a href="/bookings"><i class="fa-solid fa-calendar-check me-2"></i> My Bookings</a>

            <a href="{{ route('customer.activity-log') }}">
                <i class="fa-solid fa-clock-rotate-left me-2"></i> Activity Log
            </a>

            @endif

            {{-- OFFICER --}}
            @if(auth()->user()->role->role_name == 'officer')

            <a href="/dashboard"><i class="fa-solid fa-gauge me-2"></i> Dashboard</a>
            <a href="/transactions"><i class="fa-solid fa-receipt me-2"></i> Transactions</a>

            @endif

            <hr class="text-secondary mt-4">

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-danger w-100">
                    <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
                </button>
            </form>

        </div>

        {{-- MAIN --}}
        <div class="main-content w-100">

            {{-- TOPBAR --}}
            <div class="topbar shadow-sm mb-4">

                <div class="d-flex justify-content-between align-items-center">

                    <div>
                        <h3 class="mb-1">
                            Welcome, {{ auth()->user()->name }}
                        </h3>
                        <small class="text-secondary">
                            Smart Parking Management System
                        </small>
                    </div>

                    <div class="d-flex align-items-center gap-3">

                        {{-- 🔔 NOTIFICATION --}}
                        <a href="{{ route('notifications.index') }}" class="position-relative text-dark">

                            <i class="fa-solid fa-bell fs-5"></i>

                            <span id="notifCount"
                                class="badge bg-danger position-absolute top-0 start-100 translate-middle"
                                style="display:none;">
                            </span>

                        </a>

                        {{-- ROLE --}}
                        <span class="badge bg-primary fs-6">
                            {{ auth()->user()->role->role_name }}
                        </span>

                    </div>

                </div>

            </div>

            {{-- CONTENT --}}
            @yield('content')

        </div>

    </div>

    {{-- AJAX NOTIFICATION --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        function loadNotif() {

    $.get('/notifications/count', function(res) {

        if (res.count > 0) {
            $('#notifCount').text(res.count).show();
        } else {
            $('#notifCount').hide();
        }

    });

}

loadNotif();
setInterval(loadNotif, 5000);
    </script>

</body>

</html>