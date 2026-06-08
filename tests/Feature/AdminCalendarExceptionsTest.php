<?php

use App\Models\CalendarException;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function calendarAdminUser(): User
{
    return User::factory()->create([
        'role' => 'admin',
        'email_verified_at' => now(),
    ]);
}

test('guest cannot reach calendar exceptions index', function () {
    $this->withoutVite()
        ->get('/gestio-interna/calendar-exceptions')
        ->assertRedirect();
});

test('admin can list calendar exceptions', function () {
    $this->actingAs(calendarAdminUser());

    CalendarException::create(['day' => '2026-12-25']);

    $this->withoutVite()
        ->get('/gestio-interna/calendar-exceptions')
        ->assertOk();
});

test('admin can add a calendar exception', function () {
    $this->actingAs(calendarAdminUser());

    $this->post('/gestio-interna/calendar-exceptions', ['day' => '2026-08-15'])
        ->assertRedirect('/gestio-interna/calendar-exceptions');

    $this->assertDatabaseHas('calendar_exceptions', ['day' => '2026-08-15']);
});

test('cannot add a duplicate calendar exception', function () {
    $this->actingAs(calendarAdminUser());

    CalendarException::create(['day' => '2026-08-15']);

    $this->post('/gestio-interna/calendar-exceptions', ['day' => '2026-08-15'])
        ->assertSessionHasErrors('day');

    $this->assertDatabaseCount('calendar_exceptions', 1);
});

test('store rejects invalid date', function () {
    $this->actingAs(calendarAdminUser());

    $this->post('/gestio-interna/calendar-exceptions', ['day' => 'not-a-date'])
        ->assertSessionHasErrors('day');
});

test('admin can delete a calendar exception', function () {
    $this->actingAs(calendarAdminUser());

    $exception = CalendarException::create(['day' => '2026-12-25']);

    $this->delete("/gestio-interna/calendar-exceptions/{$exception->id}")
        ->assertRedirect('/gestio-interna/calendar-exceptions');

    $this->assertDatabaseMissing('calendar_exceptions', ['id' => $exception->id]);
});
