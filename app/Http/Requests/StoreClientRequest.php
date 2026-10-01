<?php

namespace App\Http\Requests;

use App\Models\Client;
use Illuminate\Validation\Rule;

class StoreClientRequest extends BaseClientRequest
{
    protected function tipo(): ?string
    {
        $tipo = $this->input('tipo');

        return in_array($tipo, [Client::TIPO_PF, Client::TIPO_PJ], true) ? $tipo : null;
    }

    protected function ignoredClient(): ?Client
    {
        return null;
    }

    public function rules(): array
    {
        return ['tipo' => ['required', Rule::in([Client::TIPO_PF, Client::TIPO_PJ])]] + parent::rules();
    }
}