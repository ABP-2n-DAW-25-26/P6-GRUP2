<?php

use App\Rules\TurnstileRule;
use Tests\TestCase;

// This is a unit test for the TurnstileRule, which is used to validate Cloudflare Turnstile responses.
uses(TestCase::class);

test('turnstile rule fails when the secret key is not configured', function () {
    // If the secret key isn't set, the rule should fail immediately without trying to call Cloudflare's API.
    config(['services.turnstile.secret_key' => null]);

    $rule = new TurnstileRule;

    $failedWith = null;
    $fail = function (string $message) use (&$failedWith) {
        $failedWith = $message;
    };

    // Act
    $rule->validate('cf-turnstile-response', 'any-token', $fail);

    // Assert
    expect($failedWith)
        ->not->toBeNull()
        ->and($failedWith)->toContain('turnstile');
});

test('turnstile rule does not fail immediately when a secret is configured', function () {
    // If the secret key is set, the rule should attempt to validate the token (which will fail due to the fake secret, but that's not what we're testing here).
    // The key point is that it should NOT fail with the "not configured" message.
    config(['services.turnstile.secret_key' => 'fake-secret']);

    $rule = new TurnstileRule;

    $failedWith = null;
    $fail = function (string $message) use (&$failedWith) {
        $failedWith = $message;
    };

    // Act
    try {
        $rule->validate('cf-turnstile-response', 'any-token', $fail);
    } catch (Throwable $e) {
        // We expect an exception due to the fake secret, but we want to ignore it for this test.
    }

    // The "no secret" fail message must NOT have fired.
    expect($failedWith)->not->toBe('Cloudflare turnstile no esta configurado..');
});
