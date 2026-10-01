<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Contract extends Model
{
    private const DIAS_ALERTA_VENCIMENTO = 30;

    protected $fillable = [
        'contract_group_id',
        'version',
        'content_json',
        'content_html',
        'contratante',
        'contratado',
        'imovel',
        'data_termino',
        'prazo_indeterminado',
        'file_path',
        'file_format',
    ];

    protected $casts = [
        'content_json' => 'array',
        'contratante'  => 'array',
        'contratado'   => 'array',
        'imovel'       => 'array',
        'data_termino' => 'date',
        'prazo_indeterminado' => 'boolean',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(ContractGroup::class, 'contract_group_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function fileName(): string
    {
        return "contrato-{$this->group->slug}-v{$this->version}." . $this->file_format;
    }

    public function getSituacaoAttribute(): string
    {
        if ($this->prazo_indeterminado || !$this->data_termino) {
            return 'Indeterminado';
        }

        $hoje = \Carbon\Carbon::today();

        if ($hoje->greaterThan($this->data_termino)) {
            return 'Vencido';
        }

        if ($hoje->diffInDays($this->data_termino, false) <= self::DIAS_ALERTA_VENCIMENTO) {
            return 'Vencendo';
        }

        return 'Vigente';
    }

    public function getDiasParaVencerAttribute(): ?int
    {
        if ($this->prazo_indeterminado || !$this->data_termino) {
            return null;
        }

        return (int) \Carbon\Carbon::today()->diffInDays($this->data_termino, false);
    }

    public function getTempoRestanteAttribute(): ?string
{
    if ($this->prazo_indeterminado || !$this->data_termino) {
        return null;
    }

    $hoje = \Carbon\Carbon::today();
    $dias = (int) $hoje->diffInDays($this->data_termino, false);

    if ($dias < 0) {
        $diasAtraso = abs($dias);
        return 'vencido há ' . $diasAtraso . ' ' . Str::plural('dia', $diasAtraso);
    }

    if ($dias === 0) {
        return 'vence hoje';
    }

    if ($dias <= 30) {
        return $dias . ' ' . Str::plural('dia', $dias);
    }

    $meses = (int) floor($hoje->diffInMonths($this->data_termino));
    return '~' . $meses . ' ' . ($meses === 1 ? 'mês' : 'meses');
}
}