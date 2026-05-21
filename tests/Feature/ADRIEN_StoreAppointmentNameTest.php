<?php

use App\Http\Requests\StoreAppointmentRequest;
use Illuminate\Support\Facades\Validator;

describe('StoreAppointmentRequest customer_name validation', function () {
    it('accepta un nom vàlid', function () {
        $validator = Validator::make(
            ['customer_name' => 'Joan Soler'],
            (new StoreAppointmentRequest)->rules(),
        );

        expect($validator->errors()->has('customer_name'))->toBeFalse();
    });

    it('accepta un nom amb apòstrof', function () {
        $validator = Validator::make(
            ['customer_name' => "Maria d'Arc"],
            (new StoreAppointmentRequest)->rules(),
        );

        expect($validator->errors()->has('customer_name'))->toBeFalse();
    });

    it('rebutja un nom amb números', function () {
        $validator = Validator::make(
            ['customer_name' => 'Joan123'],
            (new StoreAppointmentRequest)->rules(),
        );

        expect($validator->errors()->has('customer_name'))->toBeTrue();
    });

    it('rebutja un nom buit', function () {
        $validator = Validator::make(
            ['customer_name' => ''],
            (new StoreAppointmentRequest)->rules(),
        );

        expect($validator->errors()->has('customer_name'))->toBeTrue();
    });
});
