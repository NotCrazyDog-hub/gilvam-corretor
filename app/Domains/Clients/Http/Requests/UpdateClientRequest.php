<?php

namespace App\Domains\Clients\Http\Requests;

use App\Domains\Clients\Models\Client;

class UpdateClientRequest extends BaseClientRequest
{
    // O tipo não muda na edição: vem do próprio cliente da rota.
    protected function tipo(): ?string
    {
        return $this->ignoredClient()->tipo;
    }

    protected function ignoredClient(): ?Client
    {
        return $this->route('client');
    }
}