<?php

function getInertiaPage(string $url): array
{
    $dom = new DOMDocument;
    @$dom->loadHTML('<?xml encoding="UTF-8">'.file_get_contents($url));

    foreach ($dom->getElementsByTagName('script') as $script) {
        if ($script instanceof DOMElement && $script->getAttribute('data-page') === 'app') {
            return json_decode($script->textContent, true);
        }
    }

    return [];
}

test('loads the create assignment component', function () {

    $page = getInertiaPage('http://localhost/assignments/create');
    expect($page['component'])->toBe('Assignments/Create');
});

test('has name, address, phone_number and description props', function () {
    $page = getInertiaPage('http://localhost/assignments/create');
    expect($page['props'])->toHaveKeys(['name', 'address', 'phone_number', 'description']);
});
