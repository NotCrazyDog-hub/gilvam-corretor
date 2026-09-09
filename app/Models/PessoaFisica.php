<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class PessoaFisica extends Model
{
    protected $table = 'pessoas_fisicas';
    protected $fillable = [
        'nome', 'cpf', 'rg', 'data_nascimento', 'estado_civil',
        'nacionalidade', 'profissao', 'telefone', 'email', 'endereco',
        'cpf_hash', 'rg_hash',
    ];

    protected function setCpfAttribute($value): void
    {
        $this->attributes['cpf'] = Crypt::encryptString($value);
        $this->attributes['cpf_hash'] = hash('sha256', $value);
    }

    protected function getCpfAttribute($value): string
    {
        return Crypt::decryptString($value);
    }

    protected function setRgAttribute($value): void
    {
        if ($value) {
            $this->attributes['rg'] = Crypt::encryptString($value);
            $this->attributes['rg_hash'] = hash('sha256', $value);
        }
    }

    protected function getRgAttribute($value): ?string
    {
        return $value ? Crypt::decryptString($value) : null;
    }
}
