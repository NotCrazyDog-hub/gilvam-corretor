<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\ContractGroup;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ContractGeneratorService
{
    private const TEMPLATE_PATH = 'templates/contrato-de-corretagem-pf.docx';

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
            $this->renderDocx($relativePath, $data);

            return $this->versioner->persistVersion($group, $version, $data, $relativePath, $userId);
        });
    }

    private function renderDocx(string $relativePath, array $data): void
    {
        if (!Storage::exists(self::TEMPLATE_PATH)) {
            throw new RuntimeException('Template de contrato não encontrado: ' . self::TEMPLATE_PATH);
        }

        // TemplateProcessor lida com placeholders quebrados em múltiplos
        // <w:r> pelo Word (o problema que tínhamos com str_replace no XML cru).
        // OBS: usa o delimitador padrão ${variavel} — o template .docx precisa
        // usar esse formato (ex: ${contratante.nome}), não {{contratante.nome}}.
        $processor = new TemplateProcessor(Storage::path(self::TEMPLATE_PATH));

        foreach ($this->flatten($this->contentForTemplate($data)) as $key => $value) {
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
    private function contentForTemplate(array $data): array
    {
        $imovel = $data['imovel'];

        // Campos de texto livre vêm como HTML do TipTap. setValue() só
        // substitui texto simples (não interpreta <w:br/> automaticamente),
        // então por ora achatamos pra texto corrido. Parágrafos viram uma
        // quebra simples; se precisarem preservar múltiplos parágrafos no
        // .docx final, o próximo passo é usar Html::addHtml() do PhpWord
        // pra inserir um bloco rico em vez de um valor de texto.
        foreach (['outros_dados', 'condicoes_pagamento'] as $htmlField) {
            if (!empty($imovel[$htmlField])) {
                $imovel[$htmlField] = $this->htmlToPlainText($imovel[$htmlField]);
            }
        }

        return [
            'contratante' => $data['contratante'],
            'contratado'  => $data['contratado'],
            'imovel'      => $imovel,
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