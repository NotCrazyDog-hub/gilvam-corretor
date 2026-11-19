<?php

namespace App\Domains\Imoveis\Http\Controllers;

use App\Domains\Imoveis\Models\Imovel;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImovelPdfController extends Controller
{
    public function __invoke(Imovel $imovel): Response
    {
        // Várias fotos grandes podem demorar e consumir memória.
        @set_time_limit(120);
        @ini_set('memory_limit', '512M');

        $imovel->load('imagens');

        // Capa primeiro, depois pela ordem cadastrada.
        $fotos = $imovel->imagens
            ->sortBy([['principal', 'desc'], ['ordem', 'asc']])
            ->map(fn ($imagem) => $this->imagemBase64($imagem))
            ->filter()
            ->values();

        $pdf = Pdf::loadView('imoveis.pdf', [
            'imovel' => $imovel,
            'fotos' => $fotos,
            'logo' => $this->logoBase64(),
            'geradoEm' => now()->format('d/m/Y H:i'),
        ])->setPaper('a4', 'portrait');

        $nome = 'imovel-' . $imovel->id . '-' . Str::slug($imovel->bairro ?: 'ficha') . '.pdf';

        return $pdf->download($nome);
    }

    /**
     * Logo da GV Corretora, em public/images/logo.png (ou logo-gv.png).
     * Se não existir, o PDF sai só com o nome em texto.
     */
    private function logoBase64(): ?string
    {
        foreach (['logo', 'logo-gv'] as $nome) {
            foreach (['png', 'jpg', 'jpeg'] as $ext) {
                $caminho = public_path("images/{$nome}.{$ext}");

                if (is_file($caminho)) {
                    $mime = $ext === 'png' ? 'image/png' : 'image/jpeg';

                    return "data:{$mime};base64," . base64_encode(file_get_contents($caminho));
                }
            }
        }

        return null;
    }

    /**
     * Devolve a foto em base64 (o dompdf não precisa buscar nada por URL).
     */
    private function imagemBase64($imagem): ?string
    {
        $conteudo = $this->lerImagem($imagem);

        if (!$conteudo) {
            Log::warning('PDF imóvel: imagem não encontrada', [
                'imagem_id' => $imagem->id ?? null,
                'disk' => $imagem->disk ?? null,
                'caminho' => $imagem->caminho ?? null,
            ]);

            return null;
        }

        return $this->reduzir($conteudo);
    }

    /**
     * Tenta, em ordem: disco configurado do Laravel, arquivo local e,
     * por fim, a URL pública da imagem (caso do Supabase).
     */
    private function lerImagem($imagem): ?string
    {
        $disk = $imagem->disk ?: null;
        $caminho = ltrim((string) $imagem->caminho, '/\\');

        // 1) Disco configurado no filesystems.php
        if ($disk && config("filesystems.disks.{$disk}")) {
            try {
                $storage = Storage::disk($disk);

                if ($storage->exists($caminho)) {
                    return $storage->get($caminho);
                }
            } catch (\Throwable $e) {
                Log::warning('PDF imóvel: erro ao ler pelo Storage', ['erro' => $e->getMessage()]);
            }
        }

        // 2) Arquivo local
        foreach ([
            storage_path('app/public/' . $caminho),
            storage_path('app/private/' . $caminho),
            storage_path('app/' . $caminho),
            public_path('storage/' . $caminho),
        ] as $local) {
            if (is_file($local)) {
                return file_get_contents($local);
            }
        }

        // 3) URL pública (a mesma usada na listagem: $imagem->url)
        try {
            $url = $imagem->url;

            if ($url) {
                $resposta = Http::timeout(20)->get($url);

                if ($resposta->successful() && $resposta->body() !== '') {
                    return $resposta->body();
                }

                Log::warning('PDF imóvel: falha ao baixar imagem pela URL', [
                    'url' => $url,
                    'status' => $resposta->status(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('PDF imóvel: erro ao baixar imagem pela URL', ['erro' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Reduz para no máximo 1000px de largura e converte para JPEG,
     * deixando o PDF leve (fundo branco para PNG com transparência).
     */
    private function reduzir(string $conteudo): ?string
    {
        if (function_exists('imagecreatefromstring')) {
            $origem = @imagecreatefromstring($conteudo);

            if ($origem !== false) {
                $largura = imagesx($origem);
                $altura = imagesy($origem);
                $novaLargura = min($largura, 1000);
                $novaAltura = (int) round($altura * ($novaLargura / $largura));

                $tela = imagecreatetruecolor($novaLargura, $novaAltura);
                imagefill($tela, 0, 0, imagecolorallocate($tela, 255, 255, 255));
                imagecopyresampled($tela, $origem, 0, 0, 0, 0, $novaLargura, $novaAltura, $largura, $altura);

                ob_start();
                imagejpeg($tela, null, 80);
                $jpeg = ob_get_clean();

                imagedestroy($origem);
                imagedestroy($tela);

                return 'data:image/jpeg;base64,' . base64_encode($jpeg);
            }
        }

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($conteudo) ?: 'image/jpeg';

        return "data:{$mime};base64," . base64_encode($conteudo);
    }
}
