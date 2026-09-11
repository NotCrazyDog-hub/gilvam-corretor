<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\ContractGroup;
use App\Services\ContractGeneratorService;
use App\Http\Requests\StoreContractRequest;
use App\Http\Requests\UpdateContractRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\PessoaFisica;
use Illuminate\Support\Facades\DB;

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

        return view('contracts.form', [
            'group' => $group,
            'contract' => $contract,
            'clientType' => $group->client_type,
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

    public function chooseClientType()
    {
        return view('contracts.create-choice');
    }

    public function createForPessoaFisica()
    {
        return view('contracts.form', [
            'group' => null,
            'contract' => null,
            'clientType' => 'pf',
        ]);
    }

    public function createForPessoaJuridica()
    {
        // Documento de PJ ainda não está pronto — placeholder por enquanto
        return view('contracts.create-pj-pending');
    }

    public function searchPessoasFisicas(Request $request)
    {
        $query = trim((string) $request->query('q', ''));
        if (mb_strlen($query) < 2) {
            return response()->json([]);
        }

        $cpfDigits = preg_replace('/\D/', '', $query);

        $pessoas = PessoaFisica::query()
            ->where('nome', 'like', "%{$query}%")
            ->when(strlen($cpfDigits) >= 11, function ($q) use ($cpfDigits) {
                $q->orWhere('cpf_hash', hash('sha256', $cpfDigits));
            })
            ->limit(10)
            ->get();

        return response()->json($pessoas->map(fn (PessoaFisica $p) => [
            'id'           => $p->id,
            'nome'         => $p->nome,
            'cpf'          => $p->cpf, // decriptado pelo accessor do model
            'endereco'     => $p->endereco,
            'telefone'     => $p->telefone,
            'profissao'    => $p->profissao,
            'estado_civil' => $p->estado_civil,
        ]));
    }
}