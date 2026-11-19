@php
    $type = \App\Domains\Contracts\Services\ContractTypes::get($group->contract_type);
    $content = $contract->content_json ?? [];
    $sections = match ($group->contract_type) {
        'locacao' => [
            'LOCADOR(A)' => $contract->contratante ?? [],
            'LOCATÁRIO(A)' => $contract->contratado ?? [],
            'IMÓVEL' => $contract->imovel ?? [],
            'DADOS DA LOCAÇÃO' => $content['locacao'] ?? [],
        ],
        'compra_venda' => [
            'VENDEDOR(A)' => $contract->contratante ?? [],
            'COMPRADOR(A)' => $content['compra_venda']['comprador'] ?? [],
            'CORRETOR(A)' => $contract->contratado ?? [],
            'IMÓVEL' => $contract->imovel ?? [],
            'DADOS DA NEGOCIAÇÃO' => $content['compra_venda']['negocio'] ?? [],
        ],
        'doacao' => [
            'DOADOR(A)' => $contract->contratante ?? [],
            'DONATÁRIO(A)' => $contract->contratado ?? [],
            'IMÓVEL' => $contract->imovel ?? [],
        ],
        'prestacao_servicos' => [
            'CONTRATANTE' => $contract->contratante ?? [],
            'CONTRATADO(A)' => $contract->contratado ?? [],
            'DADOS DOS SERVIÇOS' => $content['prestacao_servicos'] ?? [],
        ],
        default => [
            'CONTRATANTE' => $contract->contratante ?? [],
            'CONTRATADO(A)' => $contract->contratado ?? [],
            'IMÓVEL' => $contract->imovel ?? [],
        ],
    };

    $labels = [
        'nome' => 'Nome', 'razao_social' => 'Razão social', 'nome_fantasia' => 'Nome fantasia',
        'cpf' => 'CPF', 'cnpj' => 'CNPJ', 'rg' => 'RG', 'inscricao_estadual' => 'Inscrição estadual',
        'email' => 'E-mail', 'telefone' => 'Telefone', 'celular' => 'Celular',
        'estado_civil' => 'Estado civil', 'profissao' => 'Profissão', 'nacionalidade' => 'Nacionalidade',
        'endereco' => 'Endereço', 'localizacao' => 'Localização', 'matricula' => 'Matrícula',
        'inscricao_prefeitura' => 'Inscrição municipal', 'tipo' => 'Tipo', 'outros_dados' => 'Outros dados',
        'finalidade' => 'Finalidade', 'prazo' => 'Prazo', 'data_inicio' => 'Data de início',
        'data_termino' => 'Data de término', 'valor_aluguel' => 'Valor do aluguel',
        'indice_reajuste' => 'Índice de reajuste', 'dia_vencimento' => 'Dia de vencimento',
        'garantia' => 'Garantia', 'condicoes_pagamento' => 'Condições de pagamento',
        'valor' => 'Valor', 'forma_pagamento' => 'Forma de pagamento', 'data_posse' => 'Data da posse',
        'objeto' => 'Objeto', 'descricao' => 'Descrição', 'valor_total' => 'Valor total',
    ];

    $flatten = function (array $values, string $prefix = '') use (&$flatten, $labels): array {
        $rows = [];
        foreach ($values as $key => $value) {
            if (is_int($key)) {
                if (is_array($value)) {
                    $rows = array_merge($rows, $flatten($value, $prefix));
                }
                continue;
            }
            $label = $labels[$key] ?? \Illuminate\Support\Str::headline(str_replace('_', ' ', (string) $key));
            $label = $prefix === '' ? $label : $prefix . ' — ' . $label;
            if (is_array($value)) {
                $rows = array_merge($rows, $flatten($value, $label));
            } elseif (is_scalar($value) && trim((string) $value) !== '') {
                $rows[] = [$label, (string) $value];
            }
        }
        return $rows;
    };

    $clauseValue = $content['clausulas'] ?? '';
    $clauseDocument = is_string($clauseValue) ? json_decode($clauseValue, true) : null;
    $clauseText = '';
    if (is_array($clauseDocument)) {
        $collectText = function (array $node) use (&$collectText): string {
            if (($node['type'] ?? null) === 'hardBreak') {
                return "\n";
            }
            $text = $node['text'] ?? '';
            foreach ($node['content'] ?? [] as $child) {
                if (is_array($child)) {
                    $text .= $collectText($child);
                }
            }
            return in_array($node['type'] ?? '', ['paragraph', 'heading', 'listItem'], true)
                ? trim($text) . "\n\n"
                : $text;
        };
        $clauseText = trim($collectText($clauseDocument));
    } elseif (is_string($clauseValue)) {
        $clauseText = trim(strip_tags($clauseValue));
    }
@endphp

<article class="mx-auto max-w-4xl bg-white px-6 py-8 text-slate-800 shadow-sm sm:px-12 sm:py-12">
    <header class="mb-10 border-b border-slate-200 pb-7 text-center">
        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">
            {{ $group->reference_code }} · Versão {{ $contract->version }}
        </p>
        <h1 class="mt-4 text-xl font-bold uppercase leading-relaxed text-slate-900 sm:text-2xl">
            {{ $type['titulo'] ?? $type['nome'] }}
        </h1>
        @if (!empty($type['subtitulo']))
            <p class="mt-1 text-sm font-semibold uppercase text-slate-700">{{ $type['subtitulo'] }}</p>
        @endif
        @if (!empty($type['base_legal']))
            <p class="mt-2 text-xs font-medium uppercase text-slate-600">{{ $type['base_legal'] }}</p>
        @endif
    </header>

    <div class="mb-8 grid gap-3 border-b border-slate-200 pb-6 text-sm sm:grid-cols-2">
        <p><span class="font-semibold text-slate-600">Gerado em:</span> {{ $contract->created_at?->format('d/m/Y \à\s H:i') ?? '—' }}</p>
        <p><span class="font-semibold text-slate-600">Validade:</span>
            {{ $contract->prazo_indeterminado || !$contract->data_termino ? 'Indeterminada' : $contract->data_termino->format('d/m/Y') }}
        </p>
    </div>

    @foreach ($sections as $heading => $values)
        @php($rows = is_array($values) ? $flatten($values) : [])
        @if ($rows !== [])
            <section class="mb-8">
                <h2 class="mb-3 border-b border-slate-300 pb-2 text-sm font-bold uppercase tracking-wide text-slate-800">{{ $heading }}</h2>
                <dl class="space-y-2">
                    @foreach ($rows as [$label, $value])
                        <div class="grid gap-1 text-sm sm:grid-cols-[minmax(9rem,0.35fr)_1fr] sm:gap-4">
                            <dt class="font-semibold text-slate-600">{{ $label }}</dt>
                            <dd class="whitespace-pre-line break-words text-slate-800">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </section>
        @endif
    @endforeach

    @if ($clauseText !== '')
        <section class="mb-8">
            <h2 class="mb-3 border-b border-slate-300 pb-2 text-sm font-bold uppercase tracking-wide text-slate-800">Cláusulas</h2>
            <div class="whitespace-pre-line text-justify text-sm leading-7 text-slate-700">{{ $clauseText }}</div>
        </section>
    @endif

    <footer class="mt-10 border-t border-slate-200 pt-4 text-center text-xs text-slate-500">
        Pré-visualização dos dados registrados nesta versão do contrato.
    </footer>
</article>
