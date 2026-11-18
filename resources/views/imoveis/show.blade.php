@php
    $onusMap = [
        'onus_hipoteca' => 'Hipoteca',
        'onus_alienacao_fiduciaria' => 'Alienação fiduciária',
        'onus_penhora' => 'Penhora',
        'onus_usufruto' => 'Usufruto',
        'onus_indisponibilidade' => 'Indisponibilidade',
        'onus_servidao' => 'Servidão',
        'onus_promessa_compra_venda' => 'Promessa de compra e venda registrada',
    ];

    $docMap = [
        'doc_certidao_onus_reais' => 'Certidão de ônus reais',
        'doc_certidao_acoes_reais' => 'Certidão de ações reais/reipersecutórias',
        'doc_certidao_negativa_debitos' => 'Certidão negativa de débitos municipais/IPTU',
        'doc_comprovante_inscricao_imobiliaria' => 'Comprovante de inscrição imobiliária',
        'doc_certidao_quitacao_condominial' => 'Certidão de quitação condominial',
        'doc_convencao_condominio' => 'Convenção de condomínio',
        'doc_planta_aprovacao' => 'Planta / aprovação',
        'doc_habite_se' => 'Habite-se',
        'doc_ambiental' => 'Documentação ambiental',
        'doc_rural' => 'Documentação rural',
        'doc_ccir' => 'CCIR',
        'doc_car' => 'CAR',
        'doc_itr' => 'ITR',
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $imovel->tipo_label }} — {{ $imovel->bairro }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <a href="{{ route('imoveis.index') }}"
               class="inline-flex items-center gap-1 text-sm text-gray-600 hover:text-gray-900">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Voltar para a lista
            </a>

            @if ($imovel->possui_onus)
                <div class="p-4 bg-amber-50 text-amber-800 rounded border border-amber-200 text-sm">
                    ⚠️ Este imóvel possui ônus ou direitos reais registrados. Veja detalhes na seção "Situação Jurídica".
                </div>
            @endif

            {{-- Fotos --}}
            @if ($imovel->imagens->isNotEmpty())
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Fotos</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        @foreach ($imovel->imagens as $imagem)
                            <a href="{{ $imagem->url }}" target="_blank" class="relative block border border-gray-200 rounded-lg overflow-hidden group">
                                <img src="{{ $imagem->url }}" alt="Foto do imóvel"
                                     class="w-full h-56 object-cover group-hover:opacity-90 transition">
                                @if ($imagem->principal)
                                    <span class="absolute top-2 left-2 bg-blue-600 text-white text-xs px-2 py-0.5 rounded">
                                        Capa
                                    </span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Endereço e áreas --}}
            <div class="bg-white shadow-md rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Endereço e Áreas</h3>
                <p class="text-gray-800 mb-4">{{ $imovel->endereco_completo }} @if($imovel->cep) — CEP {{ $imovel->cep }} @endif</p>

                <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 text-sm">
                    <div><span class="text-gray-400 block text-xs uppercase">Terreno</span>{{ $imovel->area_terreno ?? '—' }} m²</div>
                    <div><span class="text-gray-400 block text-xs uppercase">Construída</span>{{ $imovel->area_construida ?? '—' }} m²</div>
                    <div><span class="text-gray-400 block text-xs uppercase">Privativa</span>{{ $imovel->area_privativa ?? '—' }} m²</div>
                    <div><span class="text-gray-400 block text-xs uppercase">Comum</span>{{ $imovel->area_comum ?? '—' }} m²</div>
                    <div><span class="text-gray-400 block text-xs uppercase">Total</span>{{ $imovel->area_total ?? '—' }} m²</div>
                </div>
            </div>

            {{-- Registro --}}
            <div class="bg-white shadow-md rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Registro e Identificação</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                    <div><span class="text-gray-400 block text-xs uppercase">Matrícula</span>{{ $imovel->numero_matricula ?: '—' }}</div>
                    <div class="sm:col-span-2"><span class="text-gray-400 block text-xs uppercase">Cartório</span>{{ $imovel->cartorio_registro ?: '—' }}</div>
                    <div><span class="text-gray-400 block text-xs uppercase">Inscrição imobiliária / IPTU</span>{{ $imovel->inscricao_imobiliaria ?: '—' }}</div>
                    <div><span class="text-gray-400 block text-xs uppercase">Cadastro municipal</span>{{ $imovel->cadastro_municipal ?: '—' }}</div>
                    <div><span class="text-gray-400 block text-xs uppercase">Lote / Quadra</span>{{ $imovel->numero_lote ?: '—' }} / {{ $imovel->numero_quadra ?: '—' }}</div>
                    <div><span class="text-gray-400 block text-xs uppercase">Condomínio</span>{{ $imovel->condominio_nome ?: '—' }}</div>
                    <div><span class="text-gray-400 block text-xs uppercase">Bloco / Andar / Unidade</span>{{ $imovel->bloco ?: '—' }} / {{ $imovel->andar ?: '—' }} / {{ $imovel->numero_unidade ?: '—' }}</div>
                    <div><span class="text-gray-400 block text-xs uppercase">Vaga(s) de garagem</span>{{ $imovel->vagas_garagem ?? '—' }}</div>
                    <div><span class="text-gray-400 block text-xs uppercase">Fração ideal</span>{{ $imovel->fracao_ideal ?: '—' }}</div>
                </div>

                @if ($imovel->confrontacoes)
                    <div class="mt-4 text-sm">
                        <span class="text-gray-400 block text-xs uppercase">Confrontações</span>
                        <p class="text-gray-700">{{ $imovel->confrontacoes }}</p>
                    </div>
                @endif

                @if ($imovel->caracteristicas_adicionais)
                    <div class="mt-4 text-sm">
                        <span class="text-gray-400 block text-xs uppercase">Características adicionais</span>
                        <p class="text-gray-700">{{ $imovel->caracteristicas_adicionais }}</p>
                    </div>
                @endif
            </div>

            {{-- Situação jurídica --}}
            <div class="bg-white shadow-md rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Situação Jurídica</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm mb-4">
                    <div><span class="text-gray-400 block text-xs uppercase">Proprietário</span>{{ $imovel->proprietario_nome ?: '—' }}</div>
                    <div><span class="text-gray-400 block text-xs uppercase">Matrícula atualizada em</span>{{ $imovel->matricula_atualizada_em?->format('d/m/Y') ?? '—' }}</div>
                    <div><span class="text-gray-400 block text-xs uppercase">Situação tributária</span>{{ $imovel->situacao_tributaria ?: '—' }}</div>
                    <div class="sm:col-span-3"><span class="text-gray-400 block text-xs uppercase">Situação perante o condomínio</span>{{ $imovel->situacao_condominio ?: '—' }}</div>
                </div>

                <p class="text-xs text-gray-400 uppercase mb-2">Ônus e direitos reais</p>
                @php $onusAtivos = collect($onusMap)->filter(fn ($label, $campo) => $imovel->$campo); @endphp
                @if ($onusAtivos->isEmpty())
                    <p class="text-sm text-gray-500 mb-4">Nenhum ônus registrado.</p>
                @else
                    <ul class="text-sm text-gray-700 list-disc list-inside mb-4">
                        @foreach ($onusAtivos as $label)
                            <li>{{ $label }}</li>
                        @endforeach
                    </ul>
                @endif

                @if ($imovel->outros_direitos_reais)
                    <p class="text-sm text-gray-700 mb-4"><span class="text-gray-400 text-xs uppercase block">Outros direitos reais</span>{{ $imovel->outros_direitos_reais }}</p>
                @endif

                <p class="text-sm">
                    <span class="text-gray-400 block text-xs uppercase">Ações que possam atingir o imóvel</span>
                    {{ $imovel->existe_acoes ? 'Sim' : 'Não' }}
                </p>
                @if ($imovel->existe_acoes && $imovel->descricao_acoes)
                    <p class="text-sm text-gray-700 mt-1">{{ $imovel->descricao_acoes }}</p>
                @endif
            </div>

            {{-- Documentação --}}
            <div class="bg-white shadow-md rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Documentação Disponível</h3>
                @php $docsDisponiveis = collect($docMap)->filter(fn ($label, $campo) => $imovel->$campo); @endphp
                @if ($docsDisponiveis->isEmpty())
                    <p class="text-sm text-gray-500">Nenhum documento marcado como disponível ainda.</p>
                @else
                    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-y-1 text-sm text-gray-700">
                        @foreach ($docsDisponiveis as $label)
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ $label }}
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <div class="flex gap-2, flex justify-end">
                <a href="{{ route('imoveis.edit', $imovel) }}"
                   class="bg-yellow-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-yellow-600 transition">
                    Editar
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
