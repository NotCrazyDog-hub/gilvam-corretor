<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContractRequest;
use App\Http\Requests\UpdateContractRequest;
use App\Models\Client;
use App\Models\Contract;
use App\Models\ContractGroup;
use App\Services\ContractGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContractController extends Controller
{
    public function __construct(private ContractGeneratorService $generator) {}

    /**
     * Listagem com filtro por tipo de cliente (pf/pj), mostrando a versão vigente.
     */
    public function index(Request $request)
    {
        $clientType = $request->query('client_type'); // 'pf' | 'pj' | null

        $groups = ContractGroup::query()
            ->when(in_array($clientType, [Client::TIPO_PF, Client::TIPO_PJ], true), fn ($q) =>
                $q->whereHas('client', fn ($c) => $c->where('tipo', $clientType))
            )
            ->with(['latestVersion', 'client'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('contracts.index', compact('groups', 'clientType'));
    }

    public function create()
    {
        return view('contracts.form', [
            'group' => null,
            'contract' => null,
        ]);
    }

    public function store(StoreContractRequest $request)
    {
        $data = $request->validated();

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
        $group->loadMissing('client');

        return view('contracts.form', [
            'group' => $group,
            'contract' => $contract,
            'clientType' => $group->client->tipo,
        ]);
    }

    /**
     * "Editar" nunca sobrescreve: cria uma versão nova dentro do mesmo grupo.
     */
    public function update(UpdateContractRequest $request, ContractGroup $group)
    {
        $data = $request->validated();

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

    public function createForPessoaFisica()
    {
        return view('contracts.form', [
            'group' => null,
            'contract' => null,
            'clientType' => Client::TIPO_PF,
        ]);
    }

    public function createForPessoaJuridica()
    {
        return view('contracts.form', [
            'group' => null,
            'contract' => null,
            'clientType' => Client::TIPO_PJ,
        ]);
    }

    /**
     * Buscas usadas pelo autocomplete do formulário (contract-form.js).
     * Mantive os dois endpoints/URLs para não precisar mexer no JS.
     */
    public function searchPessoasFisicas(Request $request)
    {
        return $this->searchClients($request, Client::TIPO_PF);
    }

    public function searchPessoasJuridicas(Request $request)
    {
        return $this->searchClients($request, Client::TIPO_PJ);
    }

    private function searchClients(Request $request, string $tipo)
    {
        $query = trim((string) $request->query('q', ''));

        if (mb_strlen($query) < 2) {
            return response()->json([]);
        }

        $clients = Client::ofType($tipo)
            ->search($query)
            ->limit(10)
            ->get();

        return response()->json($clients->map->toSearchResult()->values());
    }

    private function contractPayload(Contract $contract): array
    {
        return [
            'group_uuid' => $contract->group->uuid,
            'version' => $contract->version,
            'file_name' => $contract->fileName(),
            'download_url' => route('contracts.download', [
                'group' => $contract->group->uuid,
                'version' => $contract->version,
            ]),
        ];
    }
}