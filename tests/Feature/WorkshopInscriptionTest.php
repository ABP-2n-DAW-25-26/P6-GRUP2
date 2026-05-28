<?php

use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeWorkshop(array $attributes = []): Workshop
{
    return Workshop::create(array_merge([
        'name' => 'Taller de prova',
        'description' => 'Descripció de prova',
        'workshop_date' => today()->addDays(7)->toDateString(),
        'start_time' => '10:00:00',
        'end_time' => '12:00:00',
        'max_attendees' => null,
        'is_active' => true,
    ], $attributes));
}

it('marks a past workshop as is_past in the show response', function () {
    $workshop = makeWorkshop(['workshop_date' => today()->subDay()->toDateString()]);

    $this->get(route('workshops.detail', $workshop))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('workshops/workshopdetails')
            ->where('workshop.is_past', true)
        );
});

it('marks a future workshop as not is_past in the show response', function () {
    $workshop = makeWorkshop(['workshop_date' => today()->addDay()->toDateString()]);

    $this->get(route('workshops.detail', $workshop))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('workshops/workshopdetails')
            ->where('workshop.is_past', false)
        );
});

it('marks today\'s workshop as not is_past', function () {
    $workshop = makeWorkshop(['workshop_date' => today()->toDateString()]);

    $this->get(route('workshops.detail', $workshop))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('workshops/workshopdetails')
            ->where('workshop.is_past', false)
        );
});

it('returns 403 when trying to inscribe to a past workshop', function () {
    $workshop = makeWorkshop(['workshop_date' => today()->subDay()->toDateString()]);

    $response = $this->post(route('workshops.inscribe', $workshop), [
        'name' => 'Joan Pérez',
        'email' => 'joan@example.com',
        'phone' => '600123456',
    ]);

    expect($response->status())->toBe(403);
});

it('does not block inscription to a future workshop', function () {
    $workshop = makeWorkshop(['workshop_date' => today()->addDay()->toDateString()]);

    $response = $this->post(route('workshops.inscribe', $workshop), [
        'name' => 'Joan Pérez',
        'email' => 'joan@example.com',
        'phone' => '600123456',
    ]);

    expect($response->status())->not->toBe(403);
});
