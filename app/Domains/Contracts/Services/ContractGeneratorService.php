<?php

namespace App\Domains\Contracts\Services;

use App\Domains\Clients\Models\Client;
use App\Domains\Contracts\Models\Contract;
use App\Domains\Contracts\Models\ContractGroup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;
use RuntimeException;

class ContractGeneratorService
{
    private const LINHAS_SO_PF = [
        'contratante.nome',
        'contratante.estado_civil',
        'contratante.cpf',
        'contratante.conjuge_nome',
        'contratante.conjuge_identidade',
    ];

    private const LINHAS_SO_PJ = [
        'contratante.razao_social',
        'contratante.nome_fantasia',
        'contratante.cnpj',
        'contratante.representante_legal_nome',
        'contratante.representante_legal_cargo',
    ];

    private const LINHAS_DOACAO_SO_PF = [
        'nome',
        'estado_civil',
        'cpf',
        'conjuge_nome',
        'conjuge_identidade',
    ];

    private const LINHAS_DOACAO_SO_PJ = [
        'razao_social',
        'nome_fantasia',
        'cnpj',
        'representante_legal_nome',
        'representante_legal_cargo',
    ];

    public function __construct(private ContractVersionService $versioner)
    {
    }

    /**
     * Gera uma nova versão do contrato.
     */
    public function generate(?ContractGroup $group, array $data, ?int $userId = null): Contract
    {
        return DB::transaction(function () use ($group, $data, $userId) {
            $group = $group ?? $this->versioner->createGroup($data);
            $this->versioner->lockGroupForVersioning($group);

            $version = $this->versioner->nextVersionNumber($group);
            $relativePath = "contracts/{$group->uuid}/v{$version}.docx";
            $isPj = $group->client->tipo === Client::TIPO_PJ;

            $this->renderDocx($relativePath, $data, $isPj, $group->contract_type);

            return $this->versioner->persistVersion(
                $group,
                $version,
                $data,
                $relativePath,
                $userId
            );
        });
    }

    private function renderDocx(
        string $relativePath,
        array $data,
        bool $isPj,
        string $contractType
    ): void {
        $tipo = ContractTypes::get($contractType);

        $templates = Storage::disk(config('contracts.templates_disk'));
        $templatePath = config('contracts.templates_dir') . '/' . $tipo['template'];

        if (!$templates->exists($templatePath)) {
            throw new RuntimeException('Template de contrato não encontrado: ' . $templatePath);
        }

        $processor = new TemplateProcessor($templates->path($templatePath));

        if ($contractType === 'doacao') {
            $this->deleteDonationPersonRows($processor, 'doador', $isPj);

            $tipoDonatario = $data['donatario']['tipo'] ?? 'pf';
            $this->deleteDonationPersonRows(
                $processor,
                'donatario',
                $tipoDonatario === 'pj'
            );
        } else {
            foreach ($isPj ? self::LINHAS_SO_PF : self::LINHAS_SO_PJ as $placeholder) {
                $processor->deleteRow($placeholder);
            }
        }

        $html = new HtmlToWord();

        foreach (['outros_dados', 'condicoes_pagamento'] as $campo) {
            $processor->setComplexValue(
                "imovel.$campo",
                $html->toTextRun($data['imovel'][$campo] ?? '')
            );
        }

        $processor->replaceXmlBlock(
            'clausulas',
            $html->toParagraphsXml($data['clausulas'] ?? ''),
            'w:p'
        );

        foreach ($this->flatten($this->contentForTemplate($data, $isPj, $tipo, $contractType)) as $key => $value) {
            $processor->setValue($key, $value);
        }

        Storage::makeDirectory(dirname($relativePath));
        $processor->saveAs(Storage::path($relativePath));
    }

    private function deleteDonationPersonRows(
        TemplateProcessor $processor,
        string $prefix,
        bool $isPj
    ): void {
        $placeholders = $isPj
            ? self::LINHAS_DOACAO_SO_PF
            : self::LINHAS_DOACAO_SO_PJ;

        foreach ($placeholders as $placeholder) {
            $processor->deleteRow("{$prefix}.{$placeholder}");
        }
    }

    /**
     * Prepara os dados e os nomes de placeholders usados pelo modelo.
     */
    private function contentForTemplate(
        array $data,
        bool $isPj,
        array $tipo,
        string $contractType
    ): array {
        $content = [
            'contrato' => [
                'titulo' => $tipo['titulo'],
                'subtitulo' => $tipo['subtitulo'] ?? '',
                'base_legal' => $tipo['base_legal'] ?? '',
            ],
            'imovel' => $data['imovel'] ?? [],
            'assinatura_segunda_parte' => $isPj
                ? 'REPRESENTANTE LEGAL'
                : 'CÔNJUGE',
        ];

        if ($contractType === 'doacao') {
            $content['doador'] = $data['contratante'] ?? [];
            $content['donatario'] = $data['donatario'] ?? [];

            return $content;
        }

        $content['contratante'] = $data['contratante'] ?? [];
        $content['contratado'] = $data['contratado'] ?? [];

        return $content;
    }

    private function flatten(array $data, string $prefix = ''): array
    {
        $result = [];

        foreach ($data as $key => $value) {
            $fullKey = $prefix === ''
                ? $key
                : "{$prefix}.{$key}";

            $result += is_array($value)
                ? $this->flatten($value, $fullKey)
                : [$fullKey => trim((string) $value)];
        }

        return $result;
    }
}