@php
    $type = \App\Domains\Contracts\Services\ContractTypes::get($group->contract_type);
    $content = $contract->content_json ?? [];
    $sections = match ($group->contract_type) {
        'locacao' => [
            'LOCADOR(A)' => $contract->contratante ?? [],
            'LOCATÁRIO(A)' => $contract->contratado ?? [],
            'OBJETO DA LOCAÇÃO' => $contract->imovel ?? [],
            'CONDIÇÕES DA LOCAÇÃO' => $content['locacao'] ?? [],
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

    $openingText = match ($group->contract_type) {
        'locacao' => 'Pelo presente instrumento particular, LOCADOR(A) e LOCATÁRIO(A), têm justo e contratado, a presente locação, que será regida pelas cláusulas e condições a seguir:',
        'compra_venda' => 'Pelo presente instrumento particular, VENDEDOR(A) e COMPRADOR(A), com a interveniência do(a) CORRETOR(A), têm justo e contratado a compra e venda do imóvel descrito neste instrumento, que será regida pelas cláusulas e condições a seguir:',
        'doacao' => 'Pelo presente instrumento, DOADOR(A) e DONATÁRIO(A) têm justo e contratado a presente doação, que será regida pelas cláusulas e condições a seguir:',
        'prestacao_servicos' => 'Pelo presente instrumento particular, CONTRATANTE e CONTRATADO(A), qualificados neste formulário, ajustam a prestação dos serviços descritos neste instrumento, conforme as cláusulas e condições abaixo:',
        default => null,
    };
    $signatures = match ($group->contract_type) {
        'locacao' => ['LOCADOR(A)', 'CÔNJUGE / REPRESENTANTE LEGAL', 'LOCATÁRIO(A)'],
        'doacao' => ['DOADOR(A)', 'CÔNJUGE / REPRESENTANTE LEGAL', 'DONATÁRIO(A)'],
        default => [],
    };

    $labels = [
        'nome' => 'Nome', 'razao_social' => 'Razão social', 'nome_fantasia' => 'Nome fantasia',
        'cpf' => 'CPF(MF) Nº', 'cnpj' => 'CNPJ(MF) Nº', 'cpf_cnpj' => 'CPF(MF) ou CNPJ(MF)',
        'rg' => 'Identidade Nº', 'identidade' => 'Identidade Nº', 'inscricao_estadual' => 'Inscrição estadual',
        'representante_legal_nome' => 'Representante legal', 'representante_legal_cargo' => 'Cargo do representante',
        'representante_legal_cpf' => 'CPF(MF) do representante', 'conjuge_nome' => 'Nome do cônjuge',
        'conjuge_identidade' => 'Identidade do cônjuge',
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
                $displayValue = (string) $value;
                if (in_array($key, ['data_inicio', 'data_termino', 'data_posse'], true)
                    && preg_match('/^\d{4}-\d{2}-\d{2}/', $displayValue)) {
                    $displayValue = \Illuminate\Support\Carbon::parse($displayValue)->format('d/m/Y');
                }
                $rows[] = [$label, $displayValue];
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

<article class="mx-auto max-w-[900px] bg-white p-6 font-sans text-black shadow-sm sm:p-8">
    <header class="mb-6 text-center leading-snug">
        <h1 class="text-sm font-bold uppercase">
            {{ $type['titulo'] ?? $type['nome'] }}
        </h1>
        @if (!empty($type['subtitulo']))
            <p class="text-sm font-bold uppercase">{{ $type['subtitulo'] }}</p>
        @endif
        @if (!empty($type['base_legal']))
            <p class="text-sm font-bold">{{ $type['base_legal'] }}</p>
        @endif
    </header>

    @foreach ($sections as $heading => $values)
        @php($rows = is_array($values) ? $flatten($values) : [])
        @if ($rows !== [])
            @php($roman = ['I', 'II', 'III', 'IV', 'V', 'VI'][$loop->index] ?? (string) ($loop->iteration))
            <section class="mb-5">
                <h2 class="mb-1 text-sm font-bold uppercase">{{ $roman }}. {{ $heading }}:</h2>
                <dl class="divide-y divide-black border border-black text-sm">
                    @foreach ($rows as [$label, $value])
                        <div class="flex flex-col gap-0.5 px-2 py-1.5 sm:flex-row sm:items-baseline sm:gap-1">
                            <dt class="shrink-0 font-bold uppercase">{{ $label }}:</dt>
                            <dd class="whitespace-pre-line break-words">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </section>
        @endif
    @endforeach

    <section class="mt-5 space-y-6 border border-black p-4 text-sm">
        <p>{{ $openingText ?? 'Pelo presente instrumento particular, as partes qualificadas neste formulário ajustam o presente contrato, que será regido pelas cláusulas e condições a seguir:' }}</p>

        @if ($clauseText !== '')
            <div class="whitespace-pre-line text-justify leading-relaxed">{{ $clauseText }}</div>
        @endif

        <p><span class="font-bold">LOCAL E DATA:</span>
            <span class="ml-2 inline-block min-w-[10rem] border-b border-black">&nbsp;</span>,
            <span class="mx-1 inline-block min-w-[2rem] border-b border-black">&nbsp;</span> de
            <span class="mx-1 inline-block min-w-[8rem] border-b border-black">&nbsp;</span> de
            <span class="mx-1 inline-block min-w-[4rem] border-b border-black">&nbsp;</span>
        </p>

        @if ($signatures !== [])
            <div class="mx-auto max-w-md space-y-8 pt-2 text-center">
                @foreach ($signatures as $signatory)
                    <div class="border-t border-black pt-1 font-bold uppercase">{{ $signatory }}</div>
                @endforeach
            </div>
        @endif

        <div>
            <p class="mb-8 font-bold">TESTEMUNHAS:</p>
            <div class="grid grid-cols-2 gap-8 text-center text-xs">
                <div class="border-t border-black pt-1">CPF:</div>
                <div class="border-t border-black pt-1">CPF:</div>
            </div>
        </div>
    </section>
</article>
