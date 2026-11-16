<?php

namespace App\Domains\Clients\Models;

use App\Domains\Clients\Services\Support\DocumentHash;
use App\Domains\Clients\Services\Traits\FormataDocumentos;
use App\Domains\Contracts\Models\ContractGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Crypt;

class Client extends Model
{
    use FormataDocumentos;

    public const TIPO_PF = 'pf';
    public const TIPO_PJ = 'pj';

    protected $fillable = [
        'tipo', 'nome', 'nome_fantasia', 'endereco',
        // PF
        'cpf', 'rg', 'data_nascimento', 'estado_civil',
        'conjuge_nome', 'conjuge_rg', 'nacionalidade', 'profissao',
        'telefone', 'telefone_secundario', 'email',
        // PJ
        'cnpj', 'inscricao_estadual',
        'representante_legal_nome', 'representante_legal_cpf', 'representante_legal_cargo',
    ];

    protected $casts = [
        'data_nascimento' => 'date',
    ];

    /* ---------- Relações ---------- */

    public function contractGroups(): HasMany
    {
        return $this->hasMany(ContractGroup::class, 'client_id');
    }

    /* ---------- Scopes ---------- */

    public function scopeOfType(Builder $query, string $tipo): Builder
    {
        return $query->where('tipo', $tipo);
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        $term   = trim($term);
        $digits = preg_replace('/\D/', '', $term);
        $like   = '%' . mb_strtolower($term) . '%';

        return $query->where(function (Builder $q) use ($like, $digits) {
            $q->whereRaw('LOWER(nome) LIKE ?', [$like])
              ->orWhereRaw('LOWER(nome_fantasia) LIKE ?', [$like]);

            if (strlen($digits) === 11) {
                $q->orWhere('cpf_hash', DocumentHash::make($digits));
            }

            if (strlen($digits) >= 4) {
                $q->orWhere('cnpj', 'like', "%{$digits}%");
            }
        });
    }

    /* ---------- Helpers ---------- */

    public function isPf(): bool
    {
        return $this->tipo === self::TIPO_PF;
    }

    public function isPj(): bool
    {
        return $this->tipo === self::TIPO_PJ;
    }

    /**
     * Mesmo formato que as buscas de pessoa física/jurídica devolviam antes,
     * para o JS do formulário de contrato continuar funcionando.
     */
    public function toSearchResult(): array
    {
        if ($this->isPj()) {
            return [
                'id'                        => $this->id,
                'razao_social'              => $this->nome,
                'nome_fantasia'             => $this->nome_fantasia,
                'cnpj'                      => $this->cnpj,
                'inscricao_estadual'        => $this->inscricao_estadual,
                'endereco'                  => $this->endereco,
                'email'                     => $this->email,
                'telefones'                 => implode(' / ', array_filter([$this->telefone, $this->telefone_secundario])),
                'representante_legal_nome'  => $this->representante_legal_nome,
                'representante_legal_cargo' => $this->representante_legal_cargo,
                'representante_legal_cpf'   => $this->representante_legal_cpf,
            ];
        }

        return [
            'id'           => $this->id,
            'nome'         => $this->nome,
            'cpf'          => $this->cpf,
            'rg'           => $this->rg,
            'endereco'     => $this->endereco,
            'telefone'     => $this->telefone,
            'profissao'    => $this->profissao,
            'nacionalidade' => $this->nacionalidade,
            'email'        => $this->email,
            'estado_civil' => $this->estado_civil,
            'conjuge_nome' => $this->conjuge_nome,
            'conjuge_rg'   => $this->conjuge_rg,
        ];
    }

    /* ---------- Mutators / accessors (campos criptografados) ---------- */

    protected function setCpfAttribute($value): void
    {
        $digits = preg_replace('/\D/', '', (string) $value);

        $this->attributes['cpf']      = $digits !== '' ? Crypt::encryptString($digits) : null;
        $this->attributes['cpf_hash'] = DocumentHash::make($digits);
    }

    protected function getCpfAttribute($value): ?string
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    protected function setRgAttribute($value): void
    {
        $this->attributes['rg']      = $value ? Crypt::encryptString($value) : null;
        $this->attributes['rg_hash'] = $value ? DocumentHash::make($value, digitsOnly: false) : null;
    }

    protected function getRgAttribute($value): ?string
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    protected function setConjugeRgAttribute($value): void
    {
        $this->attributes['conjuge_rg'] = $value ? Crypt::encryptString($value) : null;
    }

    protected function getConjugeRgAttribute($value): ?string
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    protected function setRepresentanteLegalCpfAttribute($value): void
    {
        $digits = preg_replace('/\D/', '', (string) $value);

        $this->attributes['representante_legal_cpf']      = $digits !== '' ? Crypt::encryptString($digits) : null;
        $this->attributes['representante_legal_cpf_hash'] = DocumentHash::make($digits);
    }

    protected function getRepresentanteLegalCpfAttribute($value): ?string
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    protected function setCnpjAttribute($value): void
    {
        $digits = preg_replace('/\D/', '', (string) $value);

        $this->attributes['cnpj'] = $digits !== '' ? $digits : null;
    }

    /* ---------- Formatação ---------- */

    protected function getCpfFormatadoAttribute(): ?string
    {
        return $this->formatarCpf($this->cpf);
    }

    protected function getCnpjFormatadoAttribute(): ?string
    {
        return $this->formatarCnpj($this->cnpj);
    }

    protected function getRepresentanteLegalCpfFormatadoAttribute(): ?string
    {
        return $this->formatarCpf($this->representante_legal_cpf);
    }

    protected function getTelefoneFormatadoAttribute(): ?string
    {
        return $this->formatarTelefone($this->telefone);
    }

    protected function getTelefoneSecundarioFormatadoAttribute(): ?string
    {
        return $this->formatarTelefone($this->telefone_secundario);
    }

    protected function getDocumentoFormatadoAttribute(): ?string
    {
        return $this->isPj() ? $this->cnpj_formatado : $this->cpf_formatado;
    }

    protected function getTipoLabelAttribute(): string
    {
        return $this->isPj() ? 'Jurídica' : 'Física';
    }
}
