<?php

namespace App\Domains\Contracts\Models;

use App\Domains\Contracts\Models\Contract;
use App\Domains\Clients\Models\Client;
use App\Domains\Imoveis\Models\Imovel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ContractGroup extends Model
{
    private const REFERENCE_CODE_PREFIXES = [
        'compra_venda' => 'VEN',
        'locacao' => 'LOC',
        'comodato' => 'COM',
        'prestacao_servicos' => 'SER',
        'trabalho' => 'TRB',
        'empreitada' => 'EMP',
        'parceria' => 'PAR',
        'sociedade' => 'SOC',
        'doacao' => 'DOA',
        'permuta' => 'PER',
        'intermediacao_exclusividade' => 'IEX',
        'intermediacao_sem_exclusividade' => 'ISE',
        'corretagem_venda_exclusividade' => 'BEX',
        'corretagem_venda_sem_exclusividade' => 'BSE',
        'mandato_procuracao' => 'MAN',
        'administracao' => 'ADM',
        'arrendamento' => 'ARR',
        'cessao_direitos' => 'CES',
        'confissao_divida' => 'CDV',
        'financiamento' => 'FIN',
        'emprestimo' => 'EMT',
        'franquia' => 'FRA',
        'representacao_comercial' => 'RCO',
        'distribuicao' => 'DIS',
        'fornecimento' => 'FOR',
        'transporte' => 'TRP',
        'seguro' => 'SEG',
        'agencia' => 'AGE',
        'concessao' => 'CEC',
        'licenca_uso' => 'LIC',
        'aditivo' => 'ADI',
        'rescisao_distrato' => 'RES',
        'Corretagem' => 'CTR',
    ];

    protected $fillable = [
        'uuid',
        'client_id',
        'contract_type',
        'slug',
        'status',
        'imovel_id',
    ];

    protected static function booted()
    {
        static::creating(function (ContractGroup $group) {
            $group->uuid ??= (string) Str::uuid();
        });
    }

    public function versions()
    {
        return $this->hasMany(Contract::class)->orderBy('version');
    }

    /**
     * Versão vigente (a mais recente) do contrato.
     */
    public function latestVersion()
    {
        return $this->hasOne(Contract::class)->latestOfMany('version');
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function imovel(): BelongsTo
    {
        return $this->belongsTo(Imovel::class);
    }

    /** @return array<string, string> */
    public static function referenceCodePrefixes(): array
    {
        return self::REFERENCE_CODE_PREFIXES;
    }

    public function getReferenceCodeAttribute(): string
    {
        $prefix = self::REFERENCE_CODE_PREFIXES[$this->contract_type] ?? 'CTR';
        $year = $this->created_at?->format('Y') ?? '0000';

        return sprintf('%s-%s-%04d', $prefix, $year, $this->id);
    }

    public function getImovelDisplayNameAttribute(): string
    {
        $imovel = $this->imovel;

        if ($imovel) {
            $unidade = $imovel->numero_unidade
                ? ($imovel->tipo_label . ' ' . $imovel->numero_unidade)
                : $imovel->complemento;
            $condominio = $imovel->condominio_nome
                ? 'Ed. ' . $imovel->condominio_nome
                : null;
            $identificacao = array_filter([$unidade, $condominio]);

            return $identificacao !== []
                ? implode(' · ', $identificacao)
                : $imovel->tipo_label;
        }

        $snapshot = $this->latestVersion?->imovel ?? [];

        return $snapshot['tipo'] ?? 'Imóvel do contrato';
    }

    public function getImovelDisplayAddressAttribute(): string
    {
        $imovel = $this->imovel;

        if ($imovel) {
            return $imovel->endereco_completo;
        }

        $snapshot = $this->latestVersion?->imovel ?? [];

        return $snapshot['localizacao'] ?? '';
    }

    public function getImovelDisplayReferenceAttribute(): ?string
    {
        $imovel = $this->imovel;

        if ($imovel?->numero_matricula) {
            return 'Matrícula ' . $imovel->numero_matricula;
        }

        $snapshot = $this->latestVersion?->imovel ?? [];
        $matricula = $snapshot['matricula'] ?? null;

        return $matricula ? 'Matrícula ' . $matricula : null;
    }

    public function getClientDisplayNameAttribute(): string
    {
        if ($this->client) {
            return $this->client->nome;
        }

        $contratante = $this->latestVersion?->contratante ?? [];

        return $contratante['razao_social'] ?? $contratante['nome'] ?? '—';
    }
}
