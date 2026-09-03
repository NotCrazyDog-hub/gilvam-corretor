<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\ContractGroup;
use App\Services\ContractGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContractController extends Controller
{
    public function __construct(private ContractGeneratorService $generator) {}

    /**
     * Listagem separada por tipo de cliente (pf/pj), mostrando a versão vigente.
     */
    public function index(Request $request)
    {
        $clientType = $request->query('client_type'); // 'pf' | 'pj' | null

        $groups = ContractGroup::query()
            ->when($clientType, fn ($q) => $q->where('client_type', $clientType))
            ->with('latestVersion')
            ->latest()
            ->paginate(20);

        return view('contracts.index', compact('groups', 'clientType'));
    }

    public function create()
    {
        return view('contracts.form', [
            'group' => null,
            'contract' => null,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        $contract = $this->generator->generate(
            group: null,
            data: $data,
            userId: $request->user()?->id,
        );

        return response()->json($this->contractPayload($contract));
    }

    /**
     * Carrega a versão vigente no formulário/TipTap para edição.
     */
    public function edit(ContractGroup $group)
    {
        $contract = $group->latestVersion()->firstOrFail();

        return view('contracts.form', [
            'group' => $group,
            'contract' => $contract,
        ]);
    }

    /**
     * "Editar" nunca sobrescreve: cria uma versão nova dentro do mesmo grupo.
     */
    public function update(Request $request, ContractGroup $group)
    {
        $data = $this->validatedData($request);

        $contract = $this->generator->generate(
            group: $group,
            data: $data,
            userId: $request->user()?->id,
        );

        return response()->json($this->contractPayload($contract));
    }

    /**
     * Histórico de versões de um contrato.
     */
    public function versions(ContractGroup $group)
    {
        $versions = $group->versions()->get();

        return view('contracts.versions', compact('group', 'versions'));
    }

    public function download(ContractGroup $group, int $version)
    {
        $contract = $group->versions()->where('version', $version)->firstOrFail();

        abort_unless($contract->file_path && Storage::exists($contract->file_path), 404);

        return Storage::download($contract->file_path, $contract->fileName());
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'client_type'   => 'nullable|string|in:pf,pj',
            'client_id'     => 'nullable|integer',
            'contract_type' => 'nullable|string|max:255',

            'contratante'   => 'required|array',
            'contratante.nome' => 'required|string|max:255',
            'contratante.cpf'  => 'required|string|max:20',

            'contratado'    => 'required|array',
            'contratado.nome'     => 'required|string|max:255',
            'contratado.cpf_cnpj' => 'required|string|max:20',

            'imovel'        => 'required|array',

            'content_json'  => 'nullable|array',
            'content_html'  => 'nullable|string',
        ]);
    }

    private function contractPayload(Contract $contract): array
    {
        return [
            'group_uuid' => $contract->group->uuid,
            'version' => $contract->version,
            'download_url' => route('contracts.download', [
                'group' => $contract->group->uuid,
                'version' => $contract->version,
            ]),
        ];
    }
}