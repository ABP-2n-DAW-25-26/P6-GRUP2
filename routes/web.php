<?php

use App\Http\Controllers\Admin\admin_index_controller;
use App\Http\Controllers\Admin\admin_pharmacies_controller;
use App\Http\Controllers\Admin\admin_pharmacyguards_controller;
use App\Http\Controllers\Admin\admin_users_controller;
use App\Http\Controllers\Admin\admin_workshops_controller;
use App\Http\Controllers\Admin\AdminServiceController;
use App\Http\Controllers\Admin\AssignmentsController as AdminAssignmentsController;
use App\Http\Controllers\Admin\EmailsController;
use App\Http\Controllers\Admin\MailController;
use App\Http\Controllers\Admin\ServiceScheduleController;
use App\Http\Controllers\AppointmentsController;
use App\Http\Controllers\AssignmentsController;
use App\Http\Controllers\Contactans;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\public_workshops_controller;
use Illuminate\Support\Facades\Route;

//
// MAIN HOME ROUTE
//
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/pharmacyguard/{data}', [HomeController::class, 'getpg'])->name('getpg');

//
// SERVICES / APPOINTMENTS
//
Route::get('/appointments/create', [AppointmentsController::class, 'index'])->name('appointments.create');
Route::post('/appointments', [AppointmentsController::class, 'store'])->name('appointments.store');
Route::get('/appointments/pdf', [AppointmentsController::class, 'downloadPdf'])->name('appointments.pdf');
Route::get('/appointments/booked-times', [AppointmentsController::class, 'getBookedTimes']);
Route::get('/appointments/schedule', [AppointmentsController::class, 'getSchedule']);

//
// ADMIN ROUTES
//
Route::prefix('admin')->middleware(['auth', 'verified', 'isAdmin'])->group(function () {
    Route::get('/', [admin_index_controller::class, 'index'])->name('admindashboard');

    Route::resource('users', admin_users_controller::class);
    Route::resource('mail', MailController::class);
    Route::resource('emails', EmailsController::class);

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
Route::post('/assignments/verify-code', [AssignmentsController::class, 'verifyCode'])->name('assignments.verify-code');

//
// WORKSHOPS
//
Route::get('/workshops', [public_workshops_controller::class, 'index'])->name('workshops');
Route::get('/workshops/{workshop}', [public_workshops_controller::class, 'show'])->name('workshops.detail');
Route::post('/workshops/{workshop}/inscribe', [public_workshops_controller::class, 'inscribe'])->name('workshops.inscribe');

//
// CONTACT
//
Route::get('/contact-us', [Contactans::class, 'index'])->name('contactans');

require __DIR__.'/settings.php';
