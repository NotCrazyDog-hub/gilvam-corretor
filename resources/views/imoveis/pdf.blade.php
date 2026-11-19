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

    $onusAtivos = collect($onusMap)->filter(fn ($label, $campo) => $imovel->$campo);
    $docsDisponiveis = collect($docMap)->filter(fn ($label, $campo) => $imovel->$campo);
@endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Ficha do imóvel #{{ $imovel->id }}</title>
    <style>
        @page { margin: 110px 36px 60px 36px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1f2937; }

        header { position: fixed; top: -90px; left: 0; right: 0; height: 70px; border-bottom: 2px solid #d91f26; }
        header table { width: 100%; }
        header .logo { height: 52px; }
        header .marca { font-size: 18px; font-weight: bold; color: #d91f26; }
        header .sub { font-size: 9px; color: #6b7280; text-align: right; }

        footer { position: fixed; bottom: -40px; left: 0; right: 0; font-size: 8px; color: #9ca3af;
                 border-top: 1px solid #e5e7eb; padding-top: 6px; }

        h1 { font-size: 16px; margin: 0 0 4px 0; color: #111827; }
        .endereco { font-size: 11px; color: #374151; margin-bottom: 14px; }

        .secao { margin-bottom: 14px; page-break-inside: avoid; }
        .secao h2 { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #d91f26;
                    border-bottom: 1px solid #d1d5db; padding-bottom: 3px; margin: 0 0 8px 0; }

        table.dados { width: 100%; border-collapse: collapse; }
        table.dados td { vertical-align: top; padding: 3px 8px 6px 0; width: 33.33%; }
        .rotulo { display: block; font-size: 8px; text-transform: uppercase; color: #9ca3af; }

        table.fotos { width: 100%; border-collapse: separate; border-spacing: 6px; }
        table.fotos td { width: 50%; vertical-align: top; text-align: center; }
        table.fotos img { width: 100%; border: 1px solid #e5e7eb; }
        .capa { font-size: 8px; color: #d91f26; text-align: left; }

        .alerta { background: #fffbeb; border: 1px solid #fde68a; color: #92400e; padding: 8px; margin-bottom: 14px; }
        ul { margin: 0 0 6px 0; padding-left: 14px; }
        li { margin-bottom: 2px; }
    </style>
</head>
<body>

    <header>
        <table>
            <tr>
                <td>
                    @if ($logo)
                        <img src="{{ $logo }}" class="logo" alt="GV Corretora">
                    @else
                        <span class="marca">GV Corretora</span>
                    @endif
                </td>
                <td class="sub">
                    Ficha do imóvel<br>
                    Gerado em {{ $geradoEm }}
                </td>
            </tr>
        </table>
    </header>

    <footer>
        GV Corretora — Ficha do imóvel #{{ $imovel->id }} — Documento gerado em {{ $geradoEm }}
    </footer>

    <main>
        <h1>{{ $imovel->tipo_label }} — {{ $imovel->bairro }}</h1>
        <div class="endereco">
            {{ $imovel->endereco_completo }}@if ($imovel->cep) — CEP {{ $imovel->cep }}@endif
        </div>

        @if ($imovel->possui_onus)
            <div class="alerta">
                Este imóvel possui ônus ou direitos reais registrados. Veja a seção "Situação jurídica".
            </div>
        @endif

        {{-- Fotos --}}
        @if ($fotos->isNotEmpty())
            <div class="secao">
                <h2>Fotos</h2>
                <table class="fotos">
                    @foreach ($fotos->chunk(2) as $linha)
                        <tr>
                            @foreach ($linha as $i => $foto)
                                <td>
                                    @if ($loop->parent->first && $loop->first)
                                        <div class="capa">Capa</div>
                                    @endif
                                    <img src="{{ $foto }}" alt="Foto do imóvel">
                                </td>
                            @endforeach
                            @if ($linha->count() < 2)
                                <td></td>
                            @endif
                        </tr>
                    @endforeach
                </table>
            </div>
        @endif

        {{-- Áreas --}}
        <div class="secao">
            <h2>Áreas</h2>
            <table class="dados">
                <tr>
                    <td><span class="rotulo">Terreno</span>{{ $imovel->area_terreno ?? '—' }} m²</td>
                    <td><span class="rotulo">Construída</span>{{ $imovel->area_construida ?? '—' }} m²</td>
                    <td><span class="rotulo">Privativa</span>{{ $imovel->area_privativa ?? '—' }} m²</td>
                </tr>
                <tr>
                    <td><span class="rotulo">Comum</span>{{ $imovel->area_comum ?? '—' }} m²</td>
                    <td><span class="rotulo">Total</span>{{ $imovel->area_total ?? '—' }} m²</td>
                    <td></td>
                </tr>
            </table>
        </div>

        {{-- Registro --}}
        <div class="secao">
            <h2>Registro e identificação</h2>
            <table class="dados">
                <tr>
                    <td><span class="rotulo">Matrícula</span>{{ $imovel->numero_matricula ?: '—' }}</td>
                    <td colspan="2"><span class="rotulo">Cartório</span>{{ $imovel->cartorio_registro ?: '—' }}</td>
                </tr>
                <tr>
                    <td><span class="rotulo">Inscrição imobiliária / IPTU</span>{{ $imovel->inscricao_imobiliaria ?: '—' }}</td>
                    <td><span class="rotulo">Cadastro municipal</span>{{ $imovel->cadastro_municipal ?: '—' }}</td>
                    <td><span class="rotulo">Lote / Quadra</span>{{ $imovel->numero_lote ?: '—' }} / {{ $imovel->numero_quadra ?: '—' }}</td>
                </tr>
                <tr>
                    <td><span class="rotulo">Condomínio</span>{{ $imovel->condominio_nome ?: '—' }}</td>
                    <td><span class="rotulo">Bloco / Andar / Unidade</span>{{ $imovel->bloco ?: '—' }} / {{ $imovel->andar ?: '—' }} / {{ $imovel->numero_unidade ?: '—' }}</td>
                    <td><span class="rotulo">Vaga(s) de garagem</span>{{ $imovel->vagas_garagem ?? '—' }}</td>
                </tr>
                <tr>
                    <td><span class="rotulo">Fração ideal</span>{{ $imovel->fracao_ideal ?: '—' }}</td>
                    <td></td>
                    <td></td>
                </tr>
            </table>

            @if ($imovel->confrontacoes)
                <p><span class="rotulo">Confrontações</span>{{ $imovel->confrontacoes }}</p>
            @endif

            @if ($imovel->caracteristicas_adicionais)
                <p><span class="rotulo">Características adicionais</span>{{ $imovel->caracteristicas_adicionais }}</p>
            @endif
        </div>

        {{-- Situação jurídica --}}
        <div class="secao">
            <h2>Situação jurídica</h2>
            <table class="dados">
                <tr>
                    <td><span class="rotulo">Proprietário</span>{{ $imovel->proprietario_nome ?: '—' }}</td>
                    <td><span class="rotulo">Matrícula atualizada em</span>{{ $imovel->matricula_atualizada_em?->format('d/m/Y') ?? '—' }}</td>
                    <td><span class="rotulo">Situação tributária</span>{{ $imovel->situacao_tributaria ?: '—' }}</td>
                </tr>
                <tr>
                    <td colspan="3"><span class="rotulo">Situação perante o condomínio</span>{{ $imovel->situacao_condominio ?: '—' }}</td>
                </tr>
            </table>

            <span class="rotulo">Ônus e direitos reais</span>
            @if ($onusAtivos->isEmpty())
                <p>Nenhum ônus registrado.</p>
            @else
                <ul>
                    @foreach ($onusAtivos as $label)
                        <li>{{ $label }}</li>
                    @endforeach
                </ul>
            @endif

            @if ($imovel->outros_direitos_reais)
                <p><span class="rotulo">Outros direitos reais</span>{{ $imovel->outros_direitos_reais }}</p>
            @endif

            <p>
                <span class="rotulo">Ações que possam atingir o imóvel</span>
                {{ $imovel->existe_acoes ? 'Sim' : 'Não' }}
                @if ($imovel->existe_acoes && $imovel->descricao_acoes)
                    — {{ $imovel->descricao_acoes }}
                @endif
            </p>
        </div>

        {{-- Documentação --}}
        <div class="secao">
            <h2>Documentação disponível</h2>
            @if ($docsDisponiveis->isEmpty())
                <p>Nenhum documento marcado como disponível ainda.</p>
            @else
                <ul>
                    @foreach ($docsDisponiveis as $label)
                        <li>{{ $label }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    </main>

</body>
</html>
