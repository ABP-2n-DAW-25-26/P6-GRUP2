<?php

it('returns ok for the legal notice page', function () {
    $this->get(route('avis-legal'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Legal/AvisLegal'));
});

it('returns ok for the privacy policy page', function () {
    $this->get(route('privacy-policy'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Legal/Privacitat'));
});

it('returns ok for the cookie policy page', function () {
    $this->get(route('cookie-policy'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Legal/Cookies'));
});

it('legal pages are publicly accessible without authentication', function () {
    $this->get(route('avis-legal'))->assertOk();
    $this->get(route('privacy-policy'))->assertOk();
    $this->get(route('cookie-policy'))->assertOk();
});
