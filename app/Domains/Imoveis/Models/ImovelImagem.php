<?php

namespace App\Domains\Imoveis\Models;

use App\Domains\Shared\Services\ImageStorage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImovelImagem extends Model
{
    protected $table = 'imovel_imagens';

    protected $fillable = ['imovel_id', 'caminho', 'disk', 'principal', 'ordem'];

    protected $casts = ['principal' => 'boolean'];

    public function imovel(): BelongsTo
    {
        return $this->belongsTo(Imovel::class);
    }

    public function getUrlAttribute(): string
    {
        if ($this->disk === 'supabase') {
            return app(ImageStorage::class)->url($this->caminho);
        }

        return asset('storage/' . $this->caminho);
    }
}
