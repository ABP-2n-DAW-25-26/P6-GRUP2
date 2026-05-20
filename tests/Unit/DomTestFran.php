<?php

$dom = new \DOMDocument();

test('assignment dom contains expected elements', function () {

    $html = '
        <h4 id="farmacianame">Farmàcia Soler</h4>
        <a id="address">Obrir al mapa</a>
        <div id="map" class="z-0 h-80 w-full md:h-full"><div class="leaflet-pane leaflet-map-pane" style="transform: translate3d(0px, 0px, 0px);"></div></div>
    ';

    $dom = new DOMDocument();

    @$dom->loadHTML(
        '<?xml encoding="UTF-8">' . $html
    );

    expect(
        $dom->getElementById('address')->textContent
    )->toContain("Obrir al mapa");

    expect(
        $dom->getElementById('farmacianame')
    )->not->toBeNull();

    expect(
        $dom->getElementById('map')
    )->not->toBeNull();
});