<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\ContractGroup;

class PessoaJuridica extends Model
{
    protected $table = 'pessoas_juridicas';
    protected $fillable = [
        'razao_social',
        'nome_fantasia',
        'cnpj',
        'inscricao_estadual',
        'endereco',
        'representante_legal_nome',
        'representante_legal_cpf',
        'representante_legal_cpf_hash',
        'representante_legal_cargo',
    ];

    protected function setRepresentanteLegalCpfAttribute($value): void
    {
        $this->attributes['representante_legal_cpf'] = Crypt::encryptString($value);
        $this->attributes['representante_legal_cpf_hash'] = hash('sha256', $value);
    }

    protected function getRepresentanteLegalCpfAttribute($value): string
    {
        return Crypt::decryptString($value);
    }

    protected function getRepresentanteLegalCpfFormatadoAttribute(): string
    {
        $cpf = preg_replace('/\D/', '', $this->representante_legal_cpf ?? '');

        if (strlen($cpf) !== 11) {
            return $this->representante_legal_cpf ?? '—';
        }

        return preg_replace(
            '/(\d{3})(\d{3})(\d{3})(\d{2})/',
            '$1.$2.$3-$4',
            $cpf
        );
    }

    protected function getCnpjFormatadoAttribute(): string
    {
        $cnpj = preg_replace('/\D/', '', $this->cnpj ?? '');

        if (strlen($cnpj) !== 14) {
            return $this->cnpj ?? '—';
        }

        return preg_replace(
            '/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/',
            '$1.$2.$3/$4-$5',
            $cnpj
        );
    }

    public function contractGroups(): MorphMany
    {
        return $this->morphMany(ContractGroup::class, 'client');
    }
}
