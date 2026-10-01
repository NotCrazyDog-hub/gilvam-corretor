<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImovelImagem extends Model
{
    protected $table = 'imovel_imagens';

    protected $fillable = ['imovel_id', 'caminho', 'principal', 'ordem'];

    protected $casts = ['principal' => 'boolean'];

    public function imovel(): BelongsTo
    {
        return $this->belongsTo(Imovel::class);
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->caminho);
    }
}
