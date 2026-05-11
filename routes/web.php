<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\AssignmentsController;
use App\Http\Controllers\Contactans;

use App\Http\Controllers\Admin\admin_index_controller;
use App\Http\Controllers\Admin\admin_users_controller;
use App\Http\Controllers\Admin\admin_pharmacyguards_controller;
use App\Http\Controllers\Admin\admin_pharmacies_controller;
use App\Http\Controllers\Admin\MailController;
use App\Http\Controllers\Admin\AdminServiceController;
use App\Http\Controllers\Admin\AssignmentsController as AdminAssignmentsController;
use App\Http\Controllers\Admin\admin_workshops_controller;
use App\Http\Controllers\Admin\ServiceScheduleController;

//
// ✅ MAIN HOME ROUTE (ONLY ONE)
//
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/pharmacyguard/{data}', [HomeController::class, 'getpg'])->name('getpg');

//
// SERVICES / APPOINTMENTS
//
Route::get('/pedir-cita', [ServiceController::class, 'index'])->name('pedir-cita');
Route::post('/appointments', [ServiceController::class, 'store'])->name('appointments.store');
Route::get('/appointments/pdf', [ServiceController::class, 'downloadPdf'])->name('appointments.pdf');
Route::get('/appointments/booked-times', [ServiceController::class, 'getBookedTimes']);
Route::get('/appointments/schedule', [ServiceController::class, 'getSchedule']);

//
// ADMIN ROUTES
//
Route::prefix('admin')->middleware(['auth', 'verified', 'isAdmin'])->group(function () {
    Route::get('/', [admin_index_controller::class, 'index'])->name('admindashboard');

    Route::resource('users', admin_users_controller::class);
    Route::resource('mail', MailController::class);

    Route::get('pharmacyguards/filter', [admin_pharmacyguards_controller::class, 'filter'])->name('pharmacyguards.filter');
    Route::resource('pharmacyguards', admin_pharmacyguards_controller::class);

    Route::get('pharmacies/filter', [admin_pharmacies_controller::class, 'filter'])->name('pharmacies.filter');
    Route::resource('pharmacies', admin_pharmacies_controller::class);

    Route::resource('services', AdminServiceController::class);
    Route::resource('workshops', admin_workshops_controller::class);
    Route::resource('adminAssignments', AdminAssignmentsController::class);
    Route::resource('service-schedules', ServiceScheduleController::class);
});

//
// ASSIGNMENTS
//
Route::resource('assignments', AssignmentsController::class);
Route::post('/assignments/code', [AssignmentsController::class, 'code'])->name('assignments.code');

//
// CONTACT
//
Route::get('/contactans', [Contactans::class, 'index'])->name('contactans');

require __DIR__ . '/settings.php';
