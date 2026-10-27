<?php

namespace App\Domains\Clients\Services\Support;

class DocumentHash
{
    /**
     * HMAC com a APP_KEY: impede descobrir um CPF por força bruta só com o hash do banco.
     * Com $digitsOnly, "123.456.789-00" e "12345678900" geram o mesmo hash.
     */
    public static function make(?string $value, bool $digitsOnly = true): ?string
    {
        $normalized = $digitsOnly
            ? preg_replace('/\D/', '', (string) $value)
            : mb_strtolower(trim((string) $value));

        return $normalized === '' ? null : hash_hmac('sha256', $normalized, config('app.key'));
    }
}