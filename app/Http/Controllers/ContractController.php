<?php
namespace App\Http\Controllers;

use App\Services\ContractGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ContractController extends Controller
{
    public function __construct(private ContractGeneratorService $generator) {}

    public function create()
    {
        return view('contracts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'contratante' => 'required|array',
            'contratado' => 'required|array',
            'imovel' => 'required|array',
        ]);

        $slug = Str::slug($data['contratante']['nome'] ?? 'contrato');

        $path = $this->generator->generate($slug, $data);

        return response()->json([
            'path' => $path,
            'download_url' => route('contracts.download', [
                'slug' => $slug,
                'filename' => basename($path),
            ]),
        ]);
    }

    public function download(string $slug, string $filename)
    {
        $path = "contracts/{$slug}/{$filename}";
        abort_unless(Storage::exists($path), 404);

        return Storage::download($path);
    }
}