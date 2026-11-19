<?php

use App\Domains\Clients\Models\Client;
use App\Domains\Contracts\Models\Contract;
use App\Domains\Contracts\Models\ContractGroup;
use App\Domains\Imoveis\Models\Imovel;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

function createContractUpdateFixture(string $contractType): array
{
    $user = User::factory()->admin()->create();
    $client = Client::create([
        'tipo' => Client::TIPO_PF,
        'nome' => 'Cliente de teste',
        'endereco' => 'Rua de teste, 100',
    ]);
    $imovel = Imovel::create([
        'tipo' => 'casa',
        'logradouro' => 'Rua de teste',
        'numero' => '100',
        'bairro' => 'Centro',
        'municipio' => 'Russas',
        'estado' => 'CE',
        'numero_matricula' => '12345',
    ]);

    DB::table('client_imovel')->insert([
        'client_id' => $client->id,
        'imovel_id' => $imovel->id,
        'papel' => 'proprietario',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $group = ContractGroup::create([
        'client_id' => $client->id,
        'imovel_id' => $imovel->id,
        'contract_type' => $contractType,
        'slug' => 'contrato-de-teste-' . $contractType,
    ]);

    $group->versions()->create([
        'version' => 1,
        'content_json' => [],
        'content_html' => null,
        'contratante' => ['nome' => 'Cliente de teste'],
        'contratado' => [],
        'imovel' => [],
        'data_termino' => null,
        'prazo_indeterminado' => true,
        'file_path' => "contracts/{$group->uuid}/v1.docx",
        'file_format' => 'docx',
        'created_by' => $user->id,
    ]);

    Storage::fake(config('contracts.templates_disk'));
    Storage::fake(config('filesystems.default'));

    $template = config("contracts.types.{$contractType}.template");
    $templateContents = file_get_contents(base_path('storage/app/public/templates/' . $template));
    Storage::disk(config('contracts.templates_disk'))
        ->put(config('contracts.templates_dir') . '/' . $template, $templateContents);

    return compact('user', 'client', 'imovel', 'group');
}

function validContractUpdatePayload(string $contractType, Client $client, Imovel $imovel): array
{
    $person = [
        'nome' => 'Pessoa do contrato',
        'estado_civil' => 'Solteiro(a)',
        'profissao' => 'Corretor(a)',
        'cpf' => '123.456.789-00',
        'identidade' => '1234567',
        'endereco' => 'Rua de teste, 100',
        'telefones' => '(88) 99999-9999',
    ];

    $payload = [
        'client_type' => Client::TIPO_PF,
        'client_id' => $client->id,
        'imovel_id' => $imovel->id,
        'contract_type' => $contractType,
        'contratante' => $person,
        'contratado' => $person + [
            'tipo' => 'pf',
            'cpf_cnpj' => '123.456.789-00',
            'creci' => '12345-F',
        ],
        'imovel' => [
            'tipo' => 'Casa',
            'localizacao' => 'Rua de teste, 100, Centro, Russas/CE',
            'documento_propriedade' => 'Matrícula',
            'matricula' => '12345',
            'inscricao_prefeitura' => '98765',
            'preco_venda' => 'R$ 300.000,00',
            'valor_atribuido' => 'R$ 300.000,00',
            'outros_dados' => '',
        ],
        'prazo_indeterminado' => '1',
        'clausulas' => '<p>Cláusulas de teste.</p>',
        'content_json' => [],
    ];

    return match ($contractType) {
        'locacao' => array_merge($payload, [
            'locador' => $person,
            'locatario' => [
                'nome' => 'Locatário de teste',
                'estado_civil' => 'Solteiro(a)',
                'profissao' => 'Autônomo(a)',
                'cpf_cnpj' => '987.654.321-00',
                'identidade' => '7654321',
                'endereco' => 'Rua locatário, 200',
            ],
            'locacao' => [
                'finalidade' => 'Residencial',
                'prazo' => 'Indeterminado',
                'data_inicio' => now()->subDay()->format('Y-m-d'),
                'valor_aluguel' => 'R$ 1.500,00',
                'indice_reajuste' => 'IPCA',
                'dia_vencimento' => '10',
                'garantia' => 'Caução',
            ],
        ]),
        'compra_venda' => array_merge($payload, [
            'vendedor' => $person + ['tipo' => 'pf', 'nacionalidade' => 'Brasileira'],
            'comprador' => $person + [
                'tipo' => 'pf',
                'nacionalidade' => 'Brasileira',
                'email' => 'comprador@example.com',
                'regime_bens' => 'Separação total',
            ],
            'negocio' => [
                'sinal' => 'R$ 30.000,00',
                'prazo_escritura' => '30 dias',
                'data_posse' => now()->addMonth()->format('Y-m-d'),
                'comissao' => 'R$ 15.000,00',
                'comissao_responsavel' => 'Vendedor(a)',
            ],
        ]),
        'doacao' => array_merge($payload, [
            'donatario' => $person + ['tipo' => 'pf'],
        ]),
        'prestacao_servicos' => array_merge($payload, [
            'contratado' => $person + [
                'tipo' => 'pf',
                'cpf_cnpj' => '123.456.789-00',
                'email' => 'prestador@example.com',
                'creci' => '12345-F',
            ],
            'servico' => [
                'descricao' => 'Serviços imobiliários de teste',
                'local' => 'Russas/CE',
                'entregaveis' => 'Relatório de atividades',
                'data_inicio' => now()->format('Y-m-d'),
                'prazo' => 'Indeterminado',
                'outros_dados' => '',
                'valor' => 'R$ 2.000,00',
                'forma_pagamento' => 'Transferência bancária',
                'vencimento' => 'Todo dia 10',
                'dados_pagamento' => '',
            ],
        ]),
        default => $payload,
    };
}

it('creates and persists a new version for every contract type with an active form', function (string $contractType) {
    $fixture = createContractUpdateFixture($contractType);
    $payload = validContractUpdatePayload($contractType, $fixture['client'], $fixture['imovel']);

    $this->actingAs($fixture['user'])
        ->putJson(route('contracts.update', $fixture['group']), $payload)
        ->assertOk()
        ->assertJsonPath('version', 2);

    $latestVersion = $fixture['group']->versions()->where('version', 2)->firstOrFail();

    expect($fixture['group']->versions()->count())->toBe(2)
        ->and(Storage::exists($latestVersion->file_path))->toBeTrue();

    if ($contractType === 'locacao') {
        expect($latestVersion->prazo_indeterminado)->toBeTrue()
            ->and($latestVersion->data_termino)->toBeNull()
            ->and($latestVersion->content_json['locacao']['data_termino'])->toBeNull();

        $endDate = now()->addYear()->format('Y-m-d');
        $payload['prazo_indeterminado'] = '0';
        $payload['data_termino'] = $endDate;
        $payload['locacao']['data_termino'] = $endDate;

        $this->actingAs($fixture['user'])
            ->putJson(route('contracts.update', $fixture['group']), $payload)
            ->assertOk()
            ->assertJsonPath('version', 3);

        $fixedTermVersion = $fixture['group']->versions()->where('version', 3)->firstOrFail();

        expect($fixedTermVersion->prazo_indeterminado)->toBeFalse()
            ->and($fixedTermVersion->data_termino->format('Y-m-d'))->toBe($endDate)
            ->and($fixedTermVersion->content_json['locacao']['data_termino'])->toBe($endDate);
    }
})->with([
    'corretagem com exclusividade' => 'corretagem_venda_exclusividade',
    'corretagem sem exclusividade' => 'corretagem_venda_sem_exclusividade',
    'locação' => 'locacao',
    'compra e venda' => 'compra_venda',
    'doação' => 'doacao',
    'prestação de serviços' => 'prestacao_servicos',
]);
