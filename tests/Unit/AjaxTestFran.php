<?php

use App\Http\Controllers\Admin\admin_pharmacies_controller;
use App\Models\Pharmacy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('returns JSON with all pharmacys', function () {
    Pharmacy::create(['name' => 'farmacia_dia', 'latitude' => 1, 'longitude' => 2]);
    Pharmacy::create(['name' => 'farmacia_nit', 'latitude' => 2, 'longitude' => 3]);

    $request = Request::create('/admin/pharmacies', 'GET', [], [], [], [
        'HTTP_ACCEPT' => 'application/json',
    ]);

    $response = (new admin_pharmacies_controller)->index($request);

    expect($response->getData())->toHaveCount(2);
});

it('returns one pharmacy searched by name', function () {
    Pharmacy::create(['name' => 'farmacia_migdia', 'latitude' => 1, 'longitude' => 4]);
    Pharmacy::create(['name' => 'farmacia_tarda', 'latitude' => 1, 'longitude' => 3]);

    $request = Request::create('/admin/pharmacies/filter', 'GET', ['search' => 'farmacia_migdia'], [], [], [
        'HTTP_ACCEPT' => 'application/json',
    ]);

    $response = (new admin_pharmacies_controller)->filter($request);

    expect($response->getData())->toHaveCount(1)
        ->and($response->getData()[0]->name)->toBe('farmacia_migdia');
});
