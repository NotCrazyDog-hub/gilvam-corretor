<?php

it('loads the map page successfully', function () {
    $this->get('/imoveis/mapa')->assertOk()->assertSee('Localização do Imóvel');
});
