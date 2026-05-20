<?php

use App\Models\Service;
use App\Models\ServiceSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function adminForSchedules(): User
{
    return User::factory()->create([
        'role' => 'admin',
        'email_verified_at' => now(),
    ]);
}

function sampleService(): Service
{
    return Service::create([
        'name' => 'Analisi de sang',
        'description' => 'Extraccio basica',
        'duration_minutes' => 30,
        'icon' => 'droplet',
    ]);
}

test('admin can create a schedule for a service', function () {
    $this->actingAs(adminForSchedules());
    $service = sampleService();

    $response = $this->post('/admin/service-schedules', [
        'service_id' => $service->id,
        'day_of_week' => 1, // Monday
        'start_time' => '09:00',
        'end_time' => '12:00',
    ]);

    $response->assertRedirect("/admin/services/{$service->id}");

    $this->assertDatabaseHas('service_schedules', [
        'service_id' => $service->id,
        'day_of_week' => 1,
    ]);
});

test('schedule creation fails when end_time is before start_time', function () {
    $this->actingAs(adminForSchedules());
    $service = sampleService();

    $response = $this->from('/admin/service-schedules/create?service_id='.$service->id)
        ->post('/admin/service-schedules', [
            'service_id' => $service->id,
            'day_of_week' => 1,
            'start_time' => '12:00',
            'end_time' => '09:00',
        ]);

    $response->assertSessionHasErrors('end_time');
});

test('schedule creation fails for invalid day_of_week', function () {
    $this->actingAs(adminForSchedules());
    $service = sampleService();

    $response = $this->from('/admin/service-schedules/create?service_id='.$service->id)
        ->post('/admin/service-schedules', [
            'service_id' => $service->id,
            'day_of_week' => 9, // out of 1..7
            'start_time' => '09:00',
            'end_time' => '10:00',
        ]);

    $response->assertSessionHasErrors('day_of_week');
});

test('admin can delete a schedule', function () {
    $this->actingAs(adminForSchedules());
    $service = sampleService();

    $schedule = ServiceSchedule::create([
        'service_id' => $service->id,
        'day_of_week' => 2,
        'start_time' => '09:00:00',
        'end_time' => '12:00:00',
    ]);

    $this->delete("/admin/service-schedules/{$schedule->id}")
        ->assertRedirect("/admin/services/{$service->id}");

    $this->assertDatabaseMissing('service_schedules', ['id' => $schedule->id]);
});