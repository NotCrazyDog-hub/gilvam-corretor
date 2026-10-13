<?php

namespace App\Domains\Contracts\Services;

use App\Domains\Contracts\Models\Contract;
use App\Domains\Contracts\Models\ContractGroup;
use App\Domains\Clients\Models\Client;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ContractGeneratorService
{
    // Template único para PF e PJ (troque o nome do arquivo em storage).
    private const TEMPLATE_PATH = 'templates/contrato-de-corretagem.docx';

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

    public function __construct(private ContractVersionService $versioner) {}

    /**
     * Gera uma NOVA versão do contrato.
     * - Se $group for null: cria um ContractGroup novo (contrato novo).
     * - Se $group for informado: cria a próxima versão dentro do mesmo grupo
     *   (isso é o "editar" — nunca sobrescreve nem apaga a versão anterior).
     */
    public function generate(?ContractGroup $group, array $data, ?int $userId = null): Contract
    {
        return DB::transaction(function () use ($group, $data, $userId) {
            $group = $group ?? $this->versioner->createGroup($data);
            $this->versioner->lockGroupForVersioning($group);

            $version = $this->versioner->nextVersionNumber($group);

            $relativePath = "contracts/{$group->uuid}/v{$version}.docx";
            $isPj = $group->client->tipo === Client::TIPO_PJ;
            $this->renderDocx($relativePath, $data, $isPj);

            return $this->versioner->persistVersion($group, $version, $data, $relativePath, $userId);
        });
    }

    /**
     * AJUSTE: compare com o valor que você grava em client_type
     * (nome da classe ou alias do morphMap).
     */

    private function renderDocx(string $relativePath, array $data, bool $isPj): void
    {
        if (!Storage::exists(self::TEMPLATE_PATH)) {
            throw new RuntimeException('Template de contrato não encontrado: ' . self::TEMPLATE_PATH);
        }

        // TemplateProcessor lida com placeholders quebrados em múltiplos
        // <w:r> pelo Word (o problema que tínhamos com str_replace no XML cru).
        // OBS: usa o delimitador padrão ${variavel} — o template .docx precisa
        // usar esse formato (ex: ${contratante.nome}), não {{contratante.nome}}.
        $processor = new TemplateProcessor(Storage::path(self::TEMPLATE_PATH));

        // Remove primeiro as linhas do tipo de pessoa que NÃO se aplica.
        // Precisa vir antes do setValue, porque a busca é pelo placeholder.
        foreach ($isPj ? self::LINHAS_SO_PF : self::LINHAS_SO_PJ as $placeholder) {
            $processor->deleteRow($placeholder);
        }

        foreach ($this->flatten($this->contentForTemplate($data, $isPj)) as $key => $value) {
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
    private function contentForTemplate(array $data, bool $isPj): array
    {
        $imovel = $data['imovel'];

        foreach (['outros_dados', 'condicoes_pagamento'] as $htmlField) {
            if (!empty($imovel[$htmlField])) {
                $imovel[$htmlField] = $this->htmlToPlainText($imovel[$htmlField]);
            }
        }

        return [
            'contratante' => $data['contratante'],
            'contratado'  => $data['contratado'],
            'imovel'      => $imovel,
            'clausulas'   => !empty($data['clausulas']) ? $this->htmlToPlainText($data['clausulas']) : '',
            // Rótulo da segunda assinatura: cônjuge (PF) ou representante legal (PJ).
            'assinatura_segunda_parte' => $isPj ? 'REPRESENTANTE LEGAL' : 'CÔNJUGE',
        ];
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