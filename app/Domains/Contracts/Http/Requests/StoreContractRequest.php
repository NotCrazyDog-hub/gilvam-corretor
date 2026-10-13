<?php

namespace App\Domains\Contracts\Http\Requests;

class StoreContractRequest extends BaseContractRequest
{
    // Por enquanto, store usa exatamente as regras da base.
    // Fica aqui como ponto de extensão futuro (ex: campo obrigatório
    // só na criação, não na edição).
}