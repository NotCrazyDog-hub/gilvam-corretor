<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class ContractGroup extends Model
{
    protected $fillable = [
        'uuid',
        'client_type',
        'client_id',
        'contract_type',
        'slug',
        'status',
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
}
