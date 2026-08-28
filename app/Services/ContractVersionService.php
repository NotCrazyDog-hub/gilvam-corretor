<?php
namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ContractVersionService
{
    public function nextFilePath(string $slug): string
    {
        $dir = "contracts/{$slug}";
        Storage::makeDirectory($dir);

        $existing = collect(Storage::files($dir))
            ->filter(fn ($f) => preg_match('/^v(\d+)_/', basename($f)))
            ->map(fn ($f) => (int) preg_replace('/^v(\d+)_.*/', '$1', basename($f)));

        $nextVersion = $existing->isEmpty() ? 1 : $existing->max() + 1;

        return "{$dir}/v{$nextVersion}_" . now()->format('Y-m-d_His') . ".docx";
    }
}