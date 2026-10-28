<?php

namespace App\Domains\Contracts\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Contracts\Http\Requests\StoreContractRequest;
use App\Domains\Contracts\Http\Requests\UpdateContractRequest;
use App\Domains\Clients\Models\Client;
use App\Domains\Contracts\Models\Contract;
use App\Domains\Imoveis\Models\Imovel;
use App\Domains\Contracts\Models\ContractGroup;
use App\Domains\Contracts\Services\ContractGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContractController extends Controller
{
    public function __construct(private ContractGeneratorService $generator) {}

    /**
     * Listagem com filtro por tipo de cliente (pf/pj) e busca por contratante
     * (nome, nome fantasia, CPF ou CNPJ), mostrando a versão vigente.
     */
    public function index(Request $request)
    {
        $clientType = $request->query('client_type'); // 'pf' | 'pj' | null
        $busca = trim((string) $request->query('q', ''));

        $groups = ContractGroup::query()
            ->when(in_array($clientType, [Client::TIPO_PF, Client::TIPO_PJ], true), fn ($q) =>
                $q->whereHas('client', fn ($c) => $c->where('tipo', $clientType))
            )
            ->when(mb_strlen($busca) >= 2, fn ($q) =>
                $q->whereHas('client', fn ($c) => $c->search($busca))
            )
            ->with(['latestVersion', 'client'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('contracts.index', compact('groups', 'clientType', 'busca'));
    }

    public function create(Request $request, string $contractType)
    {
        abort_unless(in_array($contractType, \App\Domains\Contracts\Services\ContractTypes::keys(), true), 404);
        $contractType = \App\Domains\Contracts\Services\ContractTypes::resolve($contractType);
        $tipo = config("contracts.types.$contractType");

        return view('contracts.form', [
            'group' => null,
            'contract' => null,
            'clientType' => null,
            'contractType' => $contractType,
            'templateAvailable' => !empty($tipo['form_supported']) && Storage::disk(config('contracts.templates_disk'))
                ->exists(config('contracts.templates_dir') . '/' . $tipo['template']),
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
            'contractType' => $group->contract_type,
            'templateAvailable' => !empty(\App\Domains\Contracts\Services\ContractTypes::get($group->contract_type)['form_supported'])
                && Storage::disk(config('contracts.templates_disk'))
                    ->exists(config('contracts.templates_dir') . '/' . \App\Domains\Contracts\Services\ContractTypes::get($group->contract_type)['template']),
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

    public function searchImoveis(Request $request)
    {
        $termo = trim((string) $request->query('q', ''));
        if (mb_strlen($termo) < 2) {
            return response()->json([]);
        }

        $like = '%' . mb_strtolower($termo) . '%';

        $imoveis = Imovel::query()
            ->where(function ($q) use ($like) {
                foreach (['logradouro', 'bairro', 'municipio', 'numero_matricula', 'inscricao_imobiliaria', 'condominio_nome'] as $coluna) {
                    $q->orWhereRaw("LOWER({$coluna}) LIKE ?", [$like]);
                }
            })
            ->with(['clients' => fn ($q) => $q->wherePivotIn('papel', Imovel::PAPEIS_CONTRATANTE)])
            ->orderBy('logradouro')
            ->limit(10)
            ->get();

        return response()->json($imoveis->map(fn (Imovel $i) => $this->imovelParaContrato($i))->values());
    }

    private function imovelParaContrato(Imovel $imovel): array
    {
        return [
            'id'    => $imovel->id,
            'label' => $imovel->endereco_completo
                . ($imovel->numero_matricula ? " — Mat. {$imovel->numero_matricula}" : ''),
            'proprietario_nome' => $imovel->proprietario_nome,
            'dados' => [
                'tipo'                  => $imovel->tipo_label,
                'localizacao'           => $imovel->endereco_completo,
                'documento_propriedade' => $imovel->cartorio_registro
                    ? "Matrícula — {$imovel->cartorio_registro}" : 'Matrícula',
                'matricula'             => $imovel->numero_matricula,
                'inscricao_prefeitura'  => $imovel->inscricao_imobiliaria ?: $imovel->cadastro_municipal,
            ],
            'candidatos' => $imovel->candidatosContratante()->map(fn (array $c) => [
                'id'           => $c['client']->id,
                'tipo'         => $c['client']->tipo,
                'nome'         => $c['client']->nome,
                'documento'    => $c['client']->documento_formatado,
                'papeis'       => $c['papeis'],
                'papeis_label' => collect($c['papeis'])->map(fn ($p) => Imovel::PAPEIS[$p])->join(' e '),
                'dados'        => $c['client']->toSearchResult(),
            ])->values(),
        ];
    }
}