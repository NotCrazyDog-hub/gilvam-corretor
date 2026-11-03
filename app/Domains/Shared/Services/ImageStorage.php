<?php

namespace App\Domains\Shared\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class ImageStorage
{
    /** @return array{disk: string, path: string} */
    public function store(UploadedFile $file, string $directory): array
    {
        $path = trim($directory, '/') . '/' . Str::uuid() . '.' . $file->guessExtension();
        $contents = file_get_contents($file->getRealPath());
        $mimeType = $file->getMimeType() ?: 'application/octet-stream';

        if ($contents === false) {
            throw new RuntimeException('Não foi possível ler a imagem enviada.');
        }

        $this->request()
            ->withHeaders([
                'Content-Type' => $mimeType,
                'x-upsert' => 'false',
            ])
            ->withBody($contents, $mimeType)
            ->post($this->objectUrl($path))
            ->throw();

        return ['disk' => 'supabase', 'path' => $path];
    }

    public function delete(string $path, ?string $disk = null): void
    {
        if (($disk ?? 'public') !== 'supabase') {
            Storage::disk($disk ?? 'public')->delete($path);
            return;
        }

        $this->request()
            ->delete('/storage/v1/object/' . rawurlencode($this->bucket()), ['prefixes' => [$path]])
            ->throw();

        Cache::forget($this->signedUrlCacheKey($path));
    }

    public function url(string $path): string
    {
        return Cache::remember($this->signedUrlCacheKey($path), now()->addMinutes(50), function () use ($path): string {
            $signedPath = $this->request()
                ->post(
                    '/storage/v1/object/sign/' . rawurlencode($this->bucket()) . '/' . $this->encodePath($path),
                    ['expiresIn' => 3600],
                )
                ->throw()
                ->json('signedURL');

            if (! is_string($signedPath) || $signedPath === '') {
                throw new RuntimeException('O Supabase não retornou o link temporário da imagem.');
            }

            if (str_starts_with($signedPath, 'https://') || str_starts_with($signedPath, 'http://')) {
                return $signedPath;
            }

            return rtrim($this->baseUrl(), '/') . '/storage/v1/' . ltrim($signedPath, '/');
        });
    }

    private function signedUrlCacheKey(string $path): string
    {
        return 'supabase-image-url.' . hash('sha256', $this->bucket() . '/' . $path);
    }

    private function request(): PendingRequest
    {
        $key = (string) config('services.supabase.secret_key');

        if ($key === '') {
            throw new RuntimeException('Configure SUPABASE_SECRET_KEY para usar imagens do Supabase Storage.');
        }

        $request = Http::baseUrl(rtrim($this->baseUrl(), '/'))
            ->acceptJson()
            ->withHeaders(['apikey' => $key])
            ->timeout(30);

        if (! str_starts_with($key, 'sb_')) {
            $request->withToken($key);
        }

        return $request;
    }

    private function objectUrl(string $path): string
    {
        return '/storage/v1/object/' . rawurlencode($this->bucket()) . '/' . $this->encodePath($path);
    }

    private function encodePath(string $path): string
    {
        return implode('/', array_map('rawurlencode', explode('/', $path)));
    }

    private function baseUrl(): string
    {
        $url = (string) config('services.supabase.url');

        if ($url === '') {
            throw new RuntimeException('Configure SUPABASE_URL para usar imagens do Supabase Storage.');
        }

        return $url;
    }

    private function bucket(): string
    {
        $bucket = (string) config('services.supabase.storage_bucket');

        if ($bucket === '') {
            throw new RuntimeException('Configure SUPABASE_STORAGE_BUCKET com o nome do bucket de imagens.');
        }

        return $bucket;
    }
}
