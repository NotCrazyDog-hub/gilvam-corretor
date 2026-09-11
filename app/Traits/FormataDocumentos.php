<?php

namespace App\Traits;

trait FormataDocumentos
{
    protected function formatarCpf(?string $valor): ?string
    {
        if (!$valor) {
            return null;
        }

        $numeros = preg_replace('/\D/', '', $valor);

        if (strlen($numeros) !== 11) {
            return $valor;
        }

        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $numeros);
    }

    protected function formatarCnpj(?string $valor): ?string
    {
        if (!$valor) {
            return null;
        }

        $numeros = preg_replace('/\D/', '', $valor);

        if (strlen($numeros) !== 14) {
            return $valor;
        }

        return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $numeros);
    }

    protected function formatarTelefone(?string $valor): ?string
    {
        if (!$valor) {
            return null;
        }

        $numeros = preg_replace('/\D/', '', $valor);

        if (strlen($numeros) === 11) {
            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $numeros);
        }

        if (strlen($numeros) === 10) {
            return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $numeros);
        }

        return $valor;
    }
}
