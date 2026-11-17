<?php

namespace App\Domains\Contracts\Services;

use App\Domains\Contracts\Models\Contract;
use App\Domains\Contracts\Models\ContractGroup;
use Illuminate\Support\Str;

class ContractVersionService
{
    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function normalizeLocacao(array $data): array
    {
        $data['locador'] = $data['locador'] ?? $data['contratante'] ?? [];
        $data['locatario'] = $data['locatario'] ?? $data['contratado'] ?? [];
        $data['contratante'] = $data['locador'];
        $data['contratado'] = $data['locatario'];
        $data['content_json'] = array_merge($data['content_json'] ?? [], [
            'locacao' => $data['locacao'] ?? [],
        ]);
        $data['data_termino'] = $data['locacao']['data_termino'] ?? null;
        $data['prazo_indeterminado'] = empty($data['data_termino']);
        $data['contract_type'] = 'locacao';

        return $data;
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function normalizeCompraVenda(array $data): array
    {
        $data['vendedor'] = $data['vendedor'] ?? [];
        $data['contratante'] = $data['vendedor'];
        $data['content_json'] = array_merge($data['content_json'] ?? [], [
            'compra_venda' => [
                'comprador' => $data['comprador'] ?? [],
                'negocio' => $data['negocio'] ?? [],
            ],
        ]);
        $data['contract_type'] = 'compra_venda';

        return $data;
    }

    /**
     * Cria o "grupo" (identidade estável) de um contrato novo.
     * O slug aqui é só um identificador legível pra URL/arquivo — nunca é
     * usado pra decidir se duas gerações pertencem ao mesmo contrato.
     */
    public function createGroup(array $data): ContractGroup
    {
        $contratante = $data['locador'] ?? $data['contratante'] ?? [];
        $baseSlug = Str::slug(
            ($contratante['razao_social'] ?? $contratante['nome'] ?? 'contrato')
            . '-' . now()->format('Y-m-d-His')
        );

        return ContractGroup::create([
            'client_id'     => $data['client_id'],
            'contract_type' => ContractTypes::resolve($data['contract_type'] ?? null),
            'slug'          => $baseSlug,
            'imovel_id' => $data['imovel_id'] ?? null,
        ]);
    }

    /**
     * Lock pessimista para evitar que dois requests simultâneos gerem a mesma versão.
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
        $isLocacao = ($data['contract_type'] ?? null) === 'locacao';
        $isCompraVenda = ($data['contract_type'] ?? null) === 'compra_venda';
        $contentData = $data['content_json'] ?? [];

        if ($isLocacao) {
            $contentData['locacao'] = $data['locacao'] ?? [];
        }

        if ($isCompraVenda) {
            $contentData['compra_venda'] = [
                'comprador' => $data['comprador'] ?? [],
                'negocio' => $data['negocio'] ?? [],
            ];
        }

        if (($data['contract_type'] ?? null) === 'prestacao_servicos') {
            $contentData['prestacao_servicos'] = $data['prestacao_servicos'] ?? [];
        }

        return $group->versions()->create([
            'version'              => $version,
            'content_json'         => $contentData,
            'content_html'         => $data['content_html'] ?? null,
            'contratante'          => $data['contratante'],
            'contratado'           => $data['donatario'] ?? ($data['locatario'] ?? $data['contratado']),
            'imovel'               => $data['imovel'] ?? [],
            'data_termino'         => $data['locacao']['data_termino'] ?? $data['data_termino'] ?? null,
            'prazo_indeterminado'  => $data['prazo_indeterminado'] ?? false,
            'file_path'            => $filePath,
            'file_format'          => 'docx',
            'created_by'           => $userId,
        ]);
    }
}
