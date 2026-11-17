<div>
    <p class="mb-1 text-sm font-bold uppercase">III. Objeto, prazo e remuneração:</p>
    <div class="divide-y divide-black border border-black text-sm">
        <div class="field-wrapper px-2 py-1.5">
            <label for="servico-objeto" class="mb-1 block font-bold">OBJETO E ESCOPO DOS SERVIÇOS:</label>
            <textarea id="servico-objeto" name="prestacao_servicos[objeto]" rows="4"
                      class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">{{ old('prestacao_servicos.objeto', $servico['objeto'] ?? '') }}</textarea>
        </div>
        <div class="field-wrapper px-2 py-1.5">
            <label for="servico-local" class="mb-1 block font-bold">LOCAL / FORMA DE EXECUÇÃO:</label>
            <textarea id="servico-local" name="prestacao_servicos[local_execucao]" rows="2"
                      class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">{{ old('prestacao_servicos.local_execucao', $servico['local_execucao'] ?? '') }}</textarea>
        </div>
        <div class="grid grid-cols-2 divide-x divide-black">
            <div class="field-wrapper px-2 py-1.5">
                <label for="servico-data-inicio" class="mb-1 block font-bold">DATA DE INÍCIO:</label>
                <input id="servico-data-inicio" type="date" name="prestacao_servicos[data_inicio]"
                       value="{{ old('prestacao_servicos.data_inicio', $servico['data_inicio'] ?? '') }}"
                       class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
            </div>
            <div class="field-wrapper px-2 py-1.5">
                <label for="servico-prazo" class="mb-1 block font-bold">PRAZO / CRONOGRAMA:</label>
                <input id="servico-prazo" name="prestacao_servicos[prazo]" value="{{ old('prestacao_servicos.prazo', $servico['prazo'] ?? '') }}"
                       placeholder="Prazo determinado ou etapas acordadas"
                       class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
            </div>
        </div>
        <div class="field-wrapper px-2 py-1.5">
            <label for="servico-entregaveis" class="mb-1 block font-bold">ENTREGÁVEIS / CRITÉRIOS DE ACEITE:</label>
            <textarea id="servico-entregaveis" name="prestacao_servicos[entregaveis]" rows="3"
                      class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">{{ old('prestacao_servicos.entregaveis', $servico['entregaveis'] ?? '') }}</textarea>
        </div>
        <div class="grid grid-cols-2 divide-x divide-black">
            <div class="field-wrapper px-2 py-1.5">
                <label for="servico-remuneracao" class="mb-1 block font-bold">VALOR DA REMUNERAÇÃO:</label>
                <input id="servico-remuneracao" name="prestacao_servicos[remuneracao]" value="{{ old('prestacao_servicos.remuneracao', $servico['remuneracao'] ?? '') }}"
                       placeholder="Valor e moeda"
                       class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
            </div>
            <div class="field-wrapper px-2 py-1.5">
                <label for="servico-pagamento" class="mb-1 block font-bold">FORMA E DATAS DE PAGAMENTO:</label>
                <input id="servico-pagamento" name="prestacao_servicos[forma_pagamento]" value="{{ old('prestacao_servicos.forma_pagamento', $servico['forma_pagamento'] ?? '') }}"
                       placeholder="Parcelas, vencimentos e meio de pagamento"
                       class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
            </div>
        </div>
        <div class="field-wrapper px-2 py-1.5">
            <label for="servico-despesas" class="mb-1 block font-bold">DESPESAS / REEMBOLSOS AJUSTADOS:</label>
            <textarea id="servico-despesas" name="prestacao_servicos[despesas]" rows="2"
                      class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">{{ old('prestacao_servicos.despesas', $servico['despesas'] ?? '') }}</textarea>
        </div>
        <div class="field-wrapper px-2 py-1.5">
            <label for="servico-aviso-previo" class="mb-1 block font-bold">AVISO PRÉVIO PARA ENCERRAMENTO:</label>
            <input id="servico-aviso-previo" name="prestacao_servicos[aviso_previo]" value="{{ old('prestacao_servicos.aviso_previo', $servico['aviso_previo'] ?? '') }}"
                   placeholder="Prazo ou condição ajustada pelas partes"
                   class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
        </div>
        <div class="field-wrapper px-2 py-1.5">
            <label for="servico-observacoes" class="mb-1 block font-bold">OUTRAS CONDIÇÕES:</label>
            <textarea id="servico-observacoes" name="prestacao_servicos[observacoes]" rows="3"
                      class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">{{ old('prestacao_servicos.observacoes', $servico['observacoes'] ?? '') }}</textarea>
        </div>
    </div>
</div>
