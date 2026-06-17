<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VehicleTypeController;
use App\Http\Controllers\ParkingAreaController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ParkingTransactionController;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Http\Controllers\NotificationController;


// Route::get('/', function () {
//     return view('welcome');
// });

/*
|--------------------------------------------------------------------------
| PUBLIC QR SCANNER
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('scanner.index');
});

Route::get(
    '/scan-page/{type}',
    function ($type) {

        return view(
            'scanner.scan',
            compact('type')
        );
    }
);

Route::get(
    '/scan/{token}',
    [ParkingTransactionController::class, 'scan']
);

/*
|--------------------------------------------------------------------------
| PAYMENT
|--------------------------------------------------------------------------
*/

Route::prefix('payments')->group(function () {

    Route::get(
        '/{id}',
        [ParkingTransactionController::class, 'payment']
    );

    Route::post(
        '/process/{id}',
        [ParkingTransactionController::class, 'processPayment']
    );

    Route::get(
        '/waiting/{id}',
        [ParkingTransactionController::class, 'waiting']
    )->name('payment.waiting');

    Route::get(
        '/status/{id}',
        [ParkingTransactionController::class, 'status']
    );

    Route::get(
        '/success/{id}',
        [ParkingTransactionController::class, 'success']
    );

    Route::get(
        '/failed/{id}',
        [ParkingTransactionController::class, 'failed']
    );

    Route::post(
        '/manual-success/{id}',
        [ParkingTransactionController::class, 'manualSuccess']
    );

});

Route::get(
    '/dashboard',
    [DashboardController::class, 'index']
)->middleware(['auth'])->name('dashboard');

Route::get(
    '/transactions/export/excel',
    [ParkingTransactionController::class, 'exportExcel']
);

Route::get(
    '/transactions/export/pdf',
    [ParkingTransactionController::class, 'exportPdf']
);

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:admin'
])->group(function () {

    Route::resource(
        'vehicle-types',
        VehicleTypeController::class
    );

    Route::resource(
        'parking-areas',
        ParkingAreaController::class
    );

});

Route::middleware([
    'auth',
    'role:admin,officer'
])->group(function () {

    Route::get(
        '/transactions',
        [ParkingTransactionController::class, 'index']
    );

    Route::get(
        '/activity-log',
        [ParkingTransactionController::class, 'activityLogs']
    )->name('activity-log');

});

/*
|--------------------------------------------------------------------------
| CUSTOMER
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:customer'
])->group(function () {

    Route::resource(
        'vehicles',
        VehicleController::class
    );

    Route::resource(
        'bookings',
        BookingController::class
    );

    Route::get(
        '/customer/activity-log',
        [DashboardController::class, 'customerActivityLogs']
    )->name('customer.activity-log');

});


/*
|--------------------------------------------------------------------------
| PROFILE
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get(
        '/profile',
        [ProfileController::class, 'edit']
    )->name('profile.edit');

    Route::patch(
        '/profile',
        [ProfileController::class, 'update']
    )->name('profile.update');

    Route::delete(
        '/profile',
        [ProfileController::class, 'destroy']
    )->name('profile.destroy');

    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::get('/notifications/count', [NotificationController::class, 'count']);

    Route::get('/notifications/read/{id}', [NotificationController::class, 'read'])
        ->name('notifications.read');

});



require __DIR__.'/auth.php';