<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\ContractGroup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ContractVersionService
{
    /**
     * Cria o "grupo" (identidade estável) de um contrato novo.
     * O slug aqui é só um identificador legível pra URL/arquivo — nunca é
     * usado pra decidir se duas gerações pertencem ao mesmo contrato.
     */
    public function createGroup(array $data): ContractGroup
    {
        $baseSlug = Str::slug(($data['contratante']['nome'] ?? 'contrato') . '-' . now()->format('Y-m-d-His'));

        return ContractGroup::create([
            'client_type'   => $data['client_type'],
            'client_id'     => $data['client_id'],
            'contract_type' => $data['contract_type'] ?? 'Corretagem',
            'slug'          => $baseSlug,
        ]);
    }

    /**
     * Próximo número de versão para um grupo, calculado com lock pessimista
     * pra evitar corrida entre dois requests simultâneos gerando a mesma versão.
     */
    public function lockGroupForVersioning(ContractGroup $group): void
    {
        ContractGroup::whereKey($group->id)->lockForUpdate()->firstOrFail();
    }

    public function nextVersionNumber(ContractGroup $group): int
    {
        $max = Contract::where('contract_group_id', $group->id)->max('version');
        return ($max ?? 0) + 1;
    }

    public function persistVersion(
        ContractGroup $group,
        int $version,
        array $data,
        string $filePath,
        ?int $userId,
    ): Contract {
        return $group->versions()->create([
            'version'      => $version,
            'content_json' => $data['content_json'] ?? [],
            'content_html' => $data['content_html'] ?? null,
            'contratante'  => $data['contratante'],
            'contratado'   => $data['contratado'],
            'imovel'       => $data['imovel'],
            'file_path'    => $filePath,
            'file_format'  => 'docx',
            'generated_at' => now(),
            'created_by'   => $userId,
        ]);
    }
}