<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contract extends Model
{
    protected $fillable = [
        'contract_group_id',
        'version',
        'content_json',
        'content_html',
        'contratante',
        'contratado',
        'imovel',
        'file_path',
        'file_format',
    ];

    protected $casts = [
        'content_json' => 'array',
        'contratante'  => 'array',
        'contratado'   => 'array',
        'imovel'       => 'array',
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
}