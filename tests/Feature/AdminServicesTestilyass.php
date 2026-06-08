<?php

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Helper: build an authenticated admin user.
 */
function adminUser(): User
{
    return User::factory()->create([
        'role' => 'admin',
        'email_verified_at' => now(),
    ]);
}

test('guest cannot reach the services index', function () {
    $this->withoutVite()
        ->get('/gestio-interna/services')
        ->assertRedirect();
});

test('admin can list services', function () {
    $this->actingAs(adminUser());

    Service::create([
        'name' => 'Test Service',
        'description' => 'A short description.',
        'duration_minutes' => 30,
        'icon' => 'pill',
    ]);

    $response = $this->withoutVite()->get('/gestio-interna/services');

    $response->assertOk();
});

test('admin can create a valid service', function () {
    $this->actingAs(adminUser());

    $response = $this->post('/gestio-interna/services', [
        'name' => 'Consulta General',
        'description' => 'Atencio general per a tots els pacients.',
        'duration_minutes' => 30,
        'icon' => 'stethoscope',
    ]);

    $response->assertRedirect('/gestio-interna/services');

    $this->assertDatabaseHas('services', [
        'name' => 'Consulta General',
        'duration_minutes' => 30,
        'icon' => 'stethoscope',
    ]);
});

test('service creation fails with invalid name (special characters not allowed)', function () {
    $this->actingAs(adminUser());

    $response = $this->from('/gestio-interna/services/create')->post('/gestio-interna/services', [
        'name' => 'Consulta @!',
        'description' => 'Bad service name',
        'duration_minutes' => 30,
        'icon' => 'pill',
    ]);

    $response->assertSessionHasErrors('name');
    $this->assertDatabaseMissing('services', ['name' => 'Consulta @!']);
});

test('service name with catalan middle dot is accepted', function () {
    $this->actingAs(adminUser());

    $response = $this->post('/gestio-interna/services', [
        'name' => 'Dermoanalisi Capil·lar',
        'description' => 'Anàlisi capil·lar amb punt volat.',
        'duration_minutes' => 30,
        'icon' => 'pill',
    ]);

    $response->assertRedirect('/gestio-interna/services');
    $this->assertDatabaseHas('services', ['name' => 'Dermoanalisi Capil·lar']);
});

test('service creation fails with non-integer duration', function () {
    $this->actingAs(adminUser());

    $response = $this->from('/gestio-interna/services/create')->post('/gestio-interna/services', [
        'name' => 'Consulta',
        'description' => 'OK',
        'duration_minutes' => 'not-a-number',
        'icon' => 'pill',
    ]);

    $response->assertSessionHasErrors('duration_minutes');
});

test('admin can update a service', function () {
    $this->actingAs(adminUser());

    $service = Service::create([
        'name' => 'Old',
        'description' => 'desc',
        'duration_minutes' => 20,
        'icon' => 'pill',
    ]);

    $response = $this->put("/gestio-interna/services/{$service->id}", [
        'name' => 'Nou Nom',
        'description' => 'Updated description',
        'duration_minutes' => 45,
        'icon' => 'heart',
    ]);

    $response->assertRedirect('/gestio-interna/services');

    $this->assertDatabaseHas('services', [
        'id' => $service->id,
        'name' => 'Nou Nom',
        'duration_minutes' => 45,
        'icon' => 'heart',
    ]);
});

test('admin can delete a service', function () {
    $this->actingAs(adminUser());

    $service = Service::create([
        'name' => 'To Delete',
        'description' => 'desc',
        'duration_minutes' => 15,
        'icon' => 'pill',
    ]);

    $this->delete("/gestio-interna/services/{$service->id}")->assertRedirect();

    $this->assertDatabaseMissing('services', ['id' => $service->id]);
});
