<?php

namespace App\Domains\Contracts\Models;

use App\Domains\Contracts\Models\Contract;
use App\Domains\Clients\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ContractGroup extends Model
{
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

    public function getClientDisplayNameAttribute(): string
    {
        if ($this->client) {
            return $this->client->nome;
        }

        $contratante = $this->latestVersion?->contratante ?? [];

        return $contratante['razao_social'] ?? $contratante['nome'] ?? '—';
    }
}