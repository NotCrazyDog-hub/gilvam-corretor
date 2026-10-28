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

    /**
     * Texto inicial do editor de cláusulas para o tipo (HTML), ou '' se não houver.
     */
    public static function clausulasPadrao(?string $key): string
    {
        $arquivo = self::get($key)['clausulas_padrao'] ?? null;

        if (!$arquivo) {
            return '';
        }

        $path = resource_path(config('contracts.clausulas_dir') . '/' . basename($arquivo));

        return is_file($path) ? (string) file_get_contents($path) : '';
    }
}