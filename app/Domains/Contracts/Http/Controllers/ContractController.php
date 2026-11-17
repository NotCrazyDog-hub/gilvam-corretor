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
use App\Domains\Contracts\Services\ContractTypes;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class ContractController extends Controller
{
    public function __construct(private ContractGeneratorService $generator) {}

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $contractType = $request->query('contract_type');
        $situacao = $request->query('status');
        $sort = $request->query('sort', 'generated');
        $direction = $request->query('direction', 'desc');
        $situacoes = ['Vigente', 'Vencendo', 'Vencido', 'Indeterminado'];
        $sortOptions = ['contract', 'client', 'type', 'property', 'status', 'validity', 'generated'];

        $contractType = in_array($contractType, ContractTypes::keys(), true)
            ? $contractType
            : null;
        $situacao = in_array($situacao, $situacoes, true) ? $situacao : null;
        $sort = in_array($sort, $sortOptions, true) ? $sort : 'generated';
        $direction = in_array($direction, ['asc', 'desc'], true) ? $direction : 'desc';

        $query = ContractGroup::query()
            ->select('contract_groups.*')
            ->leftJoin('clients', 'clients.id', '=', 'contract_groups.client_id')
            ->leftJoin('imoveis', 'imoveis.id', '=', 'contract_groups.imovel_id')
            ->when($contractType !== null, fn (Builder $groups) =>
                $groups->where('contract_groups.contract_type', $contractType)
            )
            ->when($situacao !== null, fn (Builder $groups) =>
                $groups->whereHas('latestVersion', fn (Builder $version) =>
                    $this->filterBySituation($version, $situacao)
                )
            )
            ->when(mb_strlen($search) >= 2, fn (Builder $groups) =>
                $this->searchGroups($groups, $search)
            )
            ->with(['latestVersion.creator', 'client', 'imovel']);

        $latestContractValue = fn (string $column) => Contract::query()
            ->select($column)
            ->whereColumn('contracts.contract_group_id', 'contract_groups.id')
            ->orderByDesc('contracts.version')
            ->limit(1);

        match ($sort) {
            'contract' => $query->orderBy('contract_groups.id', $direction),
            'client' => $query->orderBy('clients.nome', $direction),
            'type' => $query->orderBy('contract_groups.contract_type', $direction),
            'property' => $query->orderBy('imoveis.logradouro', $direction)
                ->orderBy('imoveis.numero', $direction),
            'status', 'validity' => $query->orderBy($latestContractValue('data_termino'), $direction),
            default => $query->orderBy($latestContractValue('created_at'), $direction),
        };

        $groups = $query
            ->orderBy('contract_groups.id', 'desc')
            ->paginate(20)
            ->withQueryString();

        $queryState = array_filter([
            'q' => $search,
            'contract_type' => $contractType,
            'status' => $situacao,
            'sort' => $sort,
            'direction' => $direction,
        ], fn ($value) => $value !== null && $value !== '');

        return view('contracts.index', [
            'groups' => $groups,
            'search' => $search,
            'contractType' => $contractType,
            'situacao' => $situacao,
            'situacoes' => $situacoes,
            'sort' => $sort,
            'direction' => $direction,
            'queryState' => $queryState,
        ]);
    }

    private function searchGroups(Builder $groups, string $term): void
    {
        $like = '%' . mb_strtolower($term) . '%';

        $groups->where(function (Builder $query) use ($term, $like) {
            $query->whereHas('client', fn (Builder $client) => $client->search($term))
                ->orWhereHas('imovel', function (Builder $imovel) use ($like) {
                    $imovel->where(function (Builder $property) use ($like) {
                        foreach ([
                            'logradouro', 'numero', 'complemento', 'bairro', 'municipio',
                            'numero_matricula', 'inscricao_imobiliaria', 'condominio_nome',
                            'numero_unidade',
                        ] as $column) {
                            $property->orWhereRaw("LOWER({$column}) LIKE ?", [$like]);
                        }
                    });
                })
                ->orWhereHas('latestVersion', function (Builder $version) use ($like) {
                    $version->where(function (Builder $snapshot) use ($like) {
                        foreach (['localizacao', 'matricula', 'inscricao_prefeitura', 'tipo'] as $field) {
                            $snapshot->orWhere("imovel->{$field}", 'like', $like);
                        }
                    });
                });

            if (ctype_digit($term)) {
                $query->orWhere('contract_groups.id', (int) $term);
            }

            if (preg_match('/^([A-Z]{2,5})-(\d{4})-(\d{1,10})$/i', $term, $code)) {
                $prefix = strtoupper($code[1]);
                $contractTypes = array_keys(array_filter(
                    ContractGroup::referenceCodePrefixes(),
                    fn (string $candidatePrefix) => $candidatePrefix === $prefix,
                ));

                if ($contractTypes !== []) {
                    $query->orWhere(function (Builder $reference) use ($contractTypes, $code) {
                        $reference->whereIn('contract_groups.contract_type', $contractTypes)
                            ->whereYear('contract_groups.created_at', (int) $code[2])
                            ->where('contract_groups.id', (int) $code[3]);
                    });
                }
            }
        });
    }

    private function filterBySituation(Builder $version, string $situacao): void
    {
        $today = Carbon::today();

        match ($situacao) {
            'Vencido' => $version->whereDate('data_termino', '<', $today),
            'Vencendo' => $version->whereDate('data_termino', '>=', $today)
                ->whereDate('data_termino', '<=', $today->copy()->addDays(30)),
            'Vigente' => $version->whereDate('data_termino', '>', $today->copy()->addDays(30)),
            default => $version->where(function (Builder $query) {
                $query->where('prazo_indeterminado', true)
                    ->orWhereNull('data_termino');
            }),
        };
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
