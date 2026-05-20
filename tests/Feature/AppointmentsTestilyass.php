<?php

// use App\Mail\ReservationCreated;
use App\Models\Service;
use App\Models\ServiceAppointment;
use App\Models\ServiceSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

// use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

/**
 * Build a service with a wide weekly schedule so any weekday is bookable.
 */
function bookableService(): Service
{
    $service = Service::create([
        'name' => 'Tensio',
        'description' => 'Control de tensio',
        'duration_minutes' => 30,
        'icon' => 'activity',
    ]);

    foreach (range(1, 7) as $day) {
        ServiceSchedule::create([
            'service_id' => $service->id,
            'day_of_week' => $day,
            'start_time' => '08:00:00',
            'end_time' => '20:00:00',
        ]);
    }

    return $service;
}

// test('the pedir-cita page renders with bookable services', function () {
//     bookableService();

//     $this->withoutVite()
//         ->get('/pedir-cita')
//         ->assertOk()
//         ->assertInertia(fn (Assert $page) => $page->component('PedirCita'));
// });

// test('a guest can create an appointment with valid data', function () {
//     Mail::fake();
//     $service = bookableService();

//     // Pick a date 7 days in the future, 10:00.
//     $date = now()->addDays(7)->format('Y-m-d');

//     $response = $this->post('/appointments', [
//         'service_id' => $service->id,
//         'customer_name' => 'Joan Perez',
//         'customer_phone' => '600123123',
//         'customer_email' => 'joan@example.com',
//         'appointment_date' => $date,
//         'start_time' => '10:00',
//     ]);

//     $response->assertRedirect('/pedir-cita');

//     $this->assertDatabaseHas('service_appointments', [
//         'service_id' => $service->id,
//         'customer_email' => 'joan@example.com',
//         'appointment_date' => $date,
//         'start_time' => '10:00:00',
//         'status' => 'pending',
//     ]);

//     Mail::assertSent(ReservationCreated::class);
// });

test('appointment fails with invalid phone number', function () {
    $service = bookableService();
    $date = now()->addDays(7)->format('Y-m-d');

    $response = $this->from('/appointments/create')->post('/appointments', [
        'service_id' => $service->id,
        'customer_name' => 'Joan',
        'customer_phone' => '12345', // too short / wrong prefix
        'customer_email' => 'joan@example.com',
        'appointment_date' => $date,
        'start_time' => '10:00',
    ]);

    $response->assertSessionHasErrors('customer_phone');
});

test('appointment fails for past dates', function () {
    $service = bookableService();
    $past = now()->subDay()->format('Y-m-d');

    $response = $this->from('/appointments/create')->post('/appointments', [
        'service_id' => $service->id,
        'customer_name' => 'Joan',
        'customer_phone' => '600123123',
        'customer_email' => 'joan@example.com',
        'appointment_date' => $past,
        'start_time' => '10:00',
    ]);

    $response->assertSessionHasErrors('appointment_date');
});

test('appointment fails when the slot is already booked', function () {
    Mail::fake();
    $service = bookableService();
    $date = now()->addDays(7)->format('Y-m-d');

    ServiceAppointment::create([
        'service_id' => $service->id,
        'customer_name' => 'Other',
        'customer_phone' => '600000000',
        'customer_email' => 'other@example.com',
        'appointment_date' => $date,
        'start_time' => '10:00:00',
        'end_time' => '10:30:00',
        'status' => 'pending',
    ]);

    $response = $this->from('/appointments/create')->post('/appointments', [
        'service_id' => $service->id,
        'customer_name' => 'Joan',
        'customer_phone' => '600123123',
        'customer_email' => 'joan@example.com',
        'appointment_date' => $date,
        'start_time' => '10:00',
    ]);

    $response->assertSessionHasErrors('start_time');
});

test('booked-times AJAX endpoint returns JSON', function () {
    $service = bookableService();
    $date = now()->addDays(3)->format('Y-m-d');

    ServiceAppointment::create([
        'service_id' => $service->id,
        'customer_name' => 'X',
        'customer_phone' => '600000000',
        'customer_email' => 'x@example.com',
        'appointment_date' => $date,
        'start_time' => '11:00:00',
        'end_time' => '11:30:00',
        'status' => 'pending',
    ]);

    $response = $this->getJson("/appointments/booked-times?date={$date}&service_id={$service->id}");

    $response->assertOk()->assertJsonFragment(['11:00']);
});

test('schedule AJAX endpoint returns weekly slots', function () {
    $service = bookableService();

    $response = $this->getJson("/appointments/schedule?service_id={$service->id}");

    $response->assertOk();
    expect($response->json())->not->toBeEmpty();
});
