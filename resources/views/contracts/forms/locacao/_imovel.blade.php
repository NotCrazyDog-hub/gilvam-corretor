<div>
    <p class="font-bold text-sm uppercase mb-1">III. Objeto da locação:</p>
    <div class="border border-black divide-y divide-black text-sm">
        @foreach ([
            'tipo' => 'TIPO DE IMÓVEL', 'localizacao' => 'LOCALIZAÇÃO',
            'documento_propriedade' => 'DOCUMENTO DE PROPRIEDADE', 'matricula' => 'MATRÍCULA',
            'inscricao_prefeitura' => 'INSCRIÇÃO PREFEITURA',
        ] as $field => $label)
            <div class="field-wrapper px-2 py-1.5"><div class="flex items-baseline gap-1">
                <label class="font-bold whitespace-nowrap">{{ $label }}:</label>
                <input name="imovel[{{ $field }}]" value="{{ $imovel[$field] ?? '' }}" class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
            </div></div>
        @endforeach
        <div class="field-wrapper px-2 py-1.5">
            <label class="font-bold block mb-1" for="editor-outros-dados">OUTROS DADOS:</label>
            <div id="editor-outros-dados" class="min-h-[100px] border border-slate-300 p-2 text-sm focus-within:border-black"></div>
            <input type="hidden" name="imovel[outros_dados]" id="input-outros-dados">
            <input type="hidden" name="content_json[outros_dados]" id="input-outros-dados-json">
        </div>
    </div>
</div>

<div>
    <p class="font-bold text-sm uppercase mb-1">IV. Condições da locação:</p>
    <div class="border border-black divide-y divide-black text-sm">
        @foreach ([
            'finalidade' => 'FINALIDADE DA LOCAÇÃO', 'prazo' => 'PRAZO DA LOCAÇÃO',
            'data_inicio' => 'INÍCIO DA LOCAÇÃO', 'data_termino' => 'TÉRMINO DA LOCAÇÃO',
            'valor_aluguel' => 'VALOR DO ALUGUEL MENSAL', 'indice_reajuste' => 'ÍNDICE DE REAJUSTE',
            'dia_vencimento' => 'DIA DE VENCIMENTO', 'garantia' => 'GARANTIA LOCATÍCIA',
        ] as $field => $label)
            <div class="field-wrapper px-2 py-1.5"><div class="flex items-baseline gap-1">
                <label class="font-bold whitespace-nowrap">{{ $label }}:</label>
                <input name="locacao[{{ $field }}]" value="{{ $locacao[$field] ?? '' }}" @if (in_array($field, ['data_inicio', 'data_termino'], true)) type="date" @endif @if ($field === 'data_termino') id="locacao_data_termino" {{ old('prazo_indeterminado', $contract?->prazo_indeterminado ?? false) ? 'disabled' : '' }} onchange="const dataTermino = document.getElementById('data_termino'); if (dataTermino) dataTermino.value = this.value;" @endif class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
            </div></div>
        @endforeach
        <div class="field-wrapper px-2 py-1.5">
            <label class="font-bold block mb-1" for="editor-condicoes">CONDIÇÕES DE PAGAMENTO:</label>
            <div id="editor-condicoes" class="min-h-[100px] border border-slate-300 p-2 text-sm focus-within:border-black"></div>
            <input type="hidden" name="locacao[condicoes_pagamento]" id="input-condicoes">
            <input type="hidden" name="content_json[condicoes_pagamento]" id="input-condicoes-json">
        </div>
    </div>
</div>
