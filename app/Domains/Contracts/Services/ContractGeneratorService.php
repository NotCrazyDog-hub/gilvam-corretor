<?php

namespace App\Domains\Contracts\Services;

use App\Domains\Contracts\Models\Contract;
use App\Domains\Contracts\Models\ContractGroup;
use App\Domains\Contracts\Services\ContractTypes;
use App\Domains\Clients\Models\Client;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ContractGeneratorService
{
    // Linhas do bloco do contratante que só existem para um tipo de pessoa.
    // Cada chave é um placeholder que aparece em UMA linha da tabela.
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

    private const CAMPOS_PF = [
        'nome', 'estado_civil', 'profissao', 'cpf', 'identidade',
        'conjuge_nome', 'conjuge_identidade',
    ];

    private const CAMPOS_PJ = [
        'razao_social', 'nome_fantasia', 'inscricao_estadual', 'cnpj',
        'representante_legal_nome', 'representante_legal_cargo', 'representante_legal_cpf',
    ];

    public function __construct(private ContractVersionService $versioner) {}

    /**
     * Gera uma NOVA versão do contrato.
     * - Se $group for null: cria um ContractGroup novo (contrato novo).
     * - Se $group for informado: cria a próxima versão dentro do mesmo grupo
     *   (isso é o "editar" — nunca sobrescreve nem apaga a versão anterior).
     *
     * O tipo de contrato (e, portanto, o template) é definido na criação do
     * grupo e fica fixo nas versões seguintes.
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

            return $this->versioner->persistVersion($group, $version, $data, $relativePath, $userId);
        });
    }

    private function renderDocx(string $relativePath, array $data, bool $isPj, string $contractType): void
    {
        $tipo = ContractTypes::get($contractType);

        // Templates ficam em storage/app/public/templates (disco 'public').
        // Os contratos gerados continuam no disco padrão (privado).
        $templates    = Storage::disk(config('contracts.templates_disk'));
        $templatePath = config('contracts.templates_dir') . '/' . $tipo['template'];

        if (!$templates->exists($templatePath)) {
            throw new RuntimeException('Template de contrato não encontrado: ' . $templatePath);
        }

        // TemplateProcessor lida com placeholders quebrados em múltiplos
        // <w:r> pelo Word (o problema que tínhamos com str_replace no XML cru).
        // OBS: o delimitador padrão é ${variavel}. Se o seu .docx usa
        // {{variavel}}, descomente a linha do setMacroChars abaixo.
        $processor = new TemplateProcessor($templates->path($templatePath));
        // $processor->setMacroChars('{{', '}}');

        // Remove primeiro as linhas do tipo de pessoa que NÃO se aplica.
        // Precisa vir antes do setValue, porque a busca é pelo placeholder.
        if ($contractType === 'doacao') {
            $this->deletePartyRows($processor, 'doador', $isPj);
            $this->deletePartyRows($processor, 'donatario', ($data['donatario']['tipo'] ?? 'pf') === 'pj');
        } else {
            foreach ($isPj ? self::LINHAS_SO_PF : self::LINHAS_SO_PJ as $placeholder) {
                $processor->deleteRow($placeholder);
            }
        }

        foreach ($this->flatten($this->contentForTemplate($data, $isPj, $tipo)) as $key => $value) {
            $processor->setValue($key, $value);
        }

        Storage::makeDirectory(dirname($relativePath));
        $processor->saveAs(Storage::path($relativePath));
    }

    /**
     * Dados vindos dos campos de formulário (contratante/contratado/imovel)
     * + o texto livre do TipTap (content_html) já convertido pra texto puro,
     * já que TemplateProcessor::setValue só substitui texto simples.
     */
    private function contentForTemplate(array $data, bool $isPj, array $tipo): array
    {
        $imovel = $data['imovel'];

        foreach (['outros_dados', 'condicoes_pagamento'] as $htmlField) {
            if (!empty($imovel[$htmlField])) {
                $imovel[$htmlField] = $this->htmlToPlainText($imovel[$htmlField]);
            }
        }

        if (($data['contract_type'] ?? null) === 'doacao') {
            return [
                'doador' => $data['contratante'],
                'donatario' => $data['donatario'],
                'imovel' => array_merge($imovel, [
                    'valor_atribuido' => $imovel['valor_atribuido'] ?? '',
                    'onus' => $imovel['onus'] ?? '',
                ]),
                'clausulas' => !empty($data['clausulas']) ? $this->htmlToPlainText($data['clausulas']) : '',
                'assinatura_segunda_parte' => $isPj ? 'REPRESENTANTE LEGAL' : 'CÔNJUGE',
            ];
        }

        return [
            // Título do contrato: o .docx pode usar ${contrato.titulo},
            // ${contrato.subtitulo} e ${contrato.base_legal}.
            'contrato' => [
                'titulo'     => $tipo['titulo'],
                'subtitulo'  => $tipo['subtitulo'] ?? '',
                'base_legal' => $tipo['base_legal'] ?? '',
            ],
            'contratante' => $data['contratante'],
            'contratado'  => $data['contratado'],
            'imovel'      => $imovel,
            'clausulas'   => !empty($data['clausulas']) ? $this->htmlToPlainText($data['clausulas']) : '',
            // Rótulo da segunda assinatura: cônjuge (PF) ou representante legal (PJ).
            'assinatura_segunda_parte' => $isPj ? 'REPRESENTANTE LEGAL' : 'CÔNJUGE',
        ];
    }

    private function deletePartyRows(TemplateProcessor $processor, string $party, bool $isPj): void
    {
        $fieldsToDelete = $isPj ? self::CAMPOS_PF : self::CAMPOS_PJ;

        foreach ($fieldsToDelete as $field) {
            $processor->deleteRow("{$party}.{$field}");
        }
    }

    private function htmlToPlainText(string $html): string
    {
        $withBreaks = preg_replace('/<\/p>|<br\s*\/?>/i', "\n", $html);
        return trim(html_entity_decode(strip_tags($withBreaks), ENT_QUOTES, 'UTF-8'));
    }

    private function flatten(array $data, string $prefix = ''): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            $fullKey = $prefix === '' ? $key : "{$prefix}.{$key}";
            $result += is_array($value)
                ? $this->flatten($value, $fullKey)
                : [$fullKey => trim((string) $value)];
        }
        return $result;
    }
}
