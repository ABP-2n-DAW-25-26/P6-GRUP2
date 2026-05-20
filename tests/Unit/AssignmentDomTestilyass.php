<?php

test('assignment form contains name input', function () {

    $html = '<input id="name" />';

    $dom = new DOMDocument();

    @$dom->loadHTML($html);

    expect(
        $dom->getElementById('name')
    )->not->toBeNull();
});