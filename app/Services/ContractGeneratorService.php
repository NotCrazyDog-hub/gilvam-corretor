<?php
namespace App\Services;

use Illuminate\Support\Facades\Storage;
use ZipArchive;
use RuntimeException;

class ContractGeneratorService
{
    public function __construct(private ContractVersionService $versioner) {}

    public function generate(string $slug, array $fields): string
    {
        $templateRelative = 'templates/contrato-de-corretagem-de-venda-de-imoveis.docx';
        $outputRelative = $this->versioner->nextFilePath($slug);

        Storage::copy($templateRelative, $outputRelative);

        $zip = new ZipArchive();
        if ($zip->open(Storage::path($outputRelative)) !== true) {
            throw new RuntimeException("Não foi possível abrir o docx gerado.");
        }

        $xml = $zip->getFromName('word/document.xml');

        foreach ($this->flatten($fields) as $key => $value) {
            $xml = str_replace('{{' . $key . '}}', $this->escapeXml($value), $xml);
        }

        $zip->deleteName('word/document.xml');
        $zip->addFromString('word/document.xml', $xml);
        $zip->close();

        return $outputRelative;
    }

    private function flatten(array $data, string $prefix = ''): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            $fullKey = $prefix === '' ? $key : "{$prefix}.{$key}";
            $result += is_array($value)
                ? $this->flatten($value, $fullKey)
                : [$fullKey => (string) $value];
        }
        return $result;
    }

    private function escapeXml(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_COMPAT, 'UTF-8');
    }
}