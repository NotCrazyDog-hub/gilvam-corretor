<?php

namespace App\Domains\Contracts\Services;

class ContractTypes
{
    public static function keys(): array
    {
        return array_keys(config('contracts.types'));
    }

    // Aceita chave inválida ou antiga (ex.: 'Corretagem' dos registros já salvos).
    public static function resolve(?string $key): string
    {
        return in_array($key, self::keys(), true) ? $key : config('contracts.default');
    }

    public static function get(?string $key): array
    {
        return config('contracts.types.' . self::resolve($key));
    }
}