<?php

namespace App\Models;

use App\Traits\FormataDocumentos;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\ContractGroup;

class PessoaFisica extends Model
{
    use FormataDocumentos;

    protected $table = 'pessoas_fisicas';

    protected $fillable = [
        'nome', 'cpf', 'rg', 'data_nascimento', 'estado_civil',
        'conjuge_nome', 'conjuge_rg',
        'nacionalidade', 'profissao',
        'telefone', 'telefone_secundario',
        'email', 'endereco',
        'cpf_hash', 'rg_hash',
    ];

    // CPF (criptografado + hash para unicidade)
    protected function setCpfAttribute($value): void
    {
        $this->attributes['cpf'] = Crypt::encryptString($value);
        $this->attributes['cpf_hash'] = hash('sha256', $value);
    }

    protected function getCpfAttribute($value): string
    {
        return Crypt::decryptString($value);
    }

    // RG (criptografado + hash; limpa os dois se vier vazio)
    protected function setRgAttribute($value): void
    {
        $this->attributes['rg'] = $value ? Crypt::encryptString($value) : null;
        $this->attributes['rg_hash'] = $value ? hash('sha256', $value) : null;
    }

    protected function getRgAttribute($value): ?string
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    // RG do cônjuge (criptografado, sem hash)
    protected function setConjugeRgAttribute($value): void
    {
        $this->attributes['conjuge_rg'] = $value ? Crypt::encryptString($value) : null;
    }

    protected function getConjugeRgAttribute($value): ?string
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    public function contractGroups(): MorphMany
    {
        return $this->morphMany(ContractGroup::class, 'client');
    }

    public function getCpfFormatadoAttribute(): ?string
    {
        return $this->formatarCpf($this->cpf);
    }

    public function getTelefoneFormatadoAttribute(): ?string
    {
        return $this->formatarTelefone($this->telefone);
    }

    public function getTelefoneSecundarioFormatadoAttribute(): ?string
    {
        return $this->telefone_secundario
            ? $this->formatarTelefone($this->telefone_secundario)
            : null;
    }
}
