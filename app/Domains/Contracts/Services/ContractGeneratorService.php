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

    private const LINHAS_LOCADOR_SO_PF = [
        'locador.nome', 'locador.estado_civil', 'locador.cpf', 'locador.conjuge_nome', 'locador.conjuge_identidade',
    ];

    private const LINHAS_LOCADOR_SO_PJ = [
        'locador.razao_social', 'locador.nome_fantasia', 'locador.cnpj', 'locador.representante_legal_nome', 'locador.representante_legal_cargo',
    ];

    private const CAMPOS_LOCADOR = [
        'nome', 'estado_civil', 'profissao', 'cpf', 'identidade', 'conjuge_nome', 'conjuge_identidade',
        'razao_social', 'nome_fantasia', 'inscricao_estadual', 'cnpj', 'endereco', 'telefones',
        'representante_legal_nome', 'representante_legal_cargo', 'representante_legal_cpf',
    ];

    private const CAMPOS_LOCATARIO = [
        'nome', 'estado_civil', 'profissao', 'cpf_cnpj', 'identidade', 'endereco', 'telefones',
    ];

    private const CAMPOS_LOCACAO = [
        'finalidade', 'prazo', 'data_inicio', 'data_termino', 'valor_aluguel',
        'indice_reajuste', 'dia_vencimento', 'condicoes_pagamento', 'garantia',
    ];

    private const CAMPOS_IMOVEL_LOCACAO = [
        'tipo', 'localizacao', 'documento_propriedade', 'matricula', 'inscricao_prefeitura', 'outros_dados',
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
            $contractType = $group?->contract_type ?? ($data['contract_type'] ?? null);
            if ($contractType !== null) {
                $data['contract_type'] = $contractType;
            }
            if ($contractType === 'locacao') {
                $data = $this->versioner->normalizeLocacao($data);
            }

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
            $linhasSoPessoa = $contractType === 'locacao'
                ? ($isPj ? self::LINHAS_LOCADOR_SO_PF : self::LINHAS_LOCADOR_SO_PJ)
                : ($isPj ? self::LINHAS_SO_PF : self::LINHAS_SO_PJ);
            foreach ($linhasSoPessoa as $placeholder) {
                $processor->deleteRow($placeholder);
            }
        }

        $html = new HtmlToWord();
        $camposHtml = $contractType === 'locacao'
            ? ['imovel.outros_dados', 'locacao.condicoes_pagamento']
            : ['imovel.outros_dados', 'imovel.condicoes_pagamento'];
        foreach ($camposHtml as $campo) {
            [$grupo, $nomeCampo] = explode('.', $campo, 2);
            $processor->setComplexValue(
                $campo,
                $html->toTextRun($data[$grupo][$nomeCampo] ?? '')
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

    private function formatDate(?string $value): string
    {
        if (blank($value)) {
            return '';
        }

        return preg_match('/^\d{4}-\d{2}-\d{2}/', $value)
            ? \Illuminate\Support\Carbon::parse($value)->format('d/m/Y')
            : $value;
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

        if ($contractType === 'locacao') {
            $fill = static fn (array $fields, array $values): array => array_merge(
                array_fill_keys($fields, ''),
                array_intersect_key($values, array_flip($fields)),
            );

            $locacao = $fill(self::CAMPOS_LOCACAO, $data['locacao'] ?? []);
            $locacao['data_inicio'] = $this->formatDate($locacao['data_inicio']);
            $locacao['data_termino'] = $this->formatDate($locacao['data_termino']);

            $content['locador'] = $fill(self::CAMPOS_LOCADOR, $data['locador'] ?? $data['contratante'] ?? []);
            $content['locatario'] = $fill(self::CAMPOS_LOCATARIO, $data['locatario'] ?? $data['contratado'] ?? []);
            $content['imovel'] = $fill(self::CAMPOS_IMOVEL_LOCACAO, $data['imovel'] ?? []);
            $content['locacao'] = $locacao;

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
