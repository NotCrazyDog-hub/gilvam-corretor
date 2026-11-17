<div>
    <p class="mb-1 text-sm font-bold uppercase">III. Objeto do contrato:</p>
    <div class="divide-y divide-black border border-black text-sm">
        <div class="field-wrapper px-2 py-1.5">
            <label for="servico-descricao" class="mb-1 block font-bold">DESCRIÇÃO DO SERVIÇO:</label>
            <textarea id="servico-descricao" name="servico[descricao]" rows="4"
                      class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">{{ old('servico.descricao', $servico['descricao'] ?? '') }}</textarea>
        </div>
        <div class="field-wrapper px-2 py-1.5">
            <label for="servico-local" class="mb-1 block font-bold">LOCAL DE EXECUÇÃO:</label>
            <textarea id="servico-local" name="servico[local]" rows="2"
                      class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">{{ old('servico.local', $servico['local'] ?? '') }}</textarea>
        </div>
        <div class="field-wrapper px-2 py-1.5">
            <label for="servico-entregaveis" class="mb-1 block font-bold">ENTREGÁVEIS / RESULTADOS ESPERADOS:</label>
            <textarea id="servico-entregaveis" name="servico[entregaveis]" rows="3"
                      class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">{{ old('servico.entregaveis', $servico['entregaveis'] ?? '') }}</textarea>
        </div>
        <div class="grid grid-cols-2 divide-x divide-black">
            <div class="field-wrapper px-2 py-1.5">
                <label for="servico-data-inicio" class="mb-1 block font-bold">DATA DE INÍCIO:</label>
                <input id="servico-data-inicio" type="date" name="servico[data_inicio]"
                       value="{{ old('servico.data_inicio', $servico['data_inicio'] ?? '') }}"
                       class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
            </div>
            <div class="field-wrapper px-2 py-1.5">
                <label for="servico-prazo" class="mb-1 block font-bold">PRAZO / VIGÊNCIA:</label>
                <input id="servico-prazo" name="servico[prazo]" value="{{ old('servico.prazo', $servico['prazo'] ?? '') }}"
                       placeholder="Ex.: prazo determinado ou prazo indeterminado"
                       class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
            </div>
        </div>
        <div class="field-wrapper px-2 py-1.5">
            <label for="servico-outros-dados" class="mb-1 block font-bold">OUTROS DADOS, DESPESAS E CONDIÇÕES:</label>
            <textarea id="servico-outros-dados" name="servico[outros_dados]" rows="3"
                      class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">{{ old('servico.outros_dados', $servico['outros_dados'] ?? '') }}</textarea>
        </div>
    </div>
</div>

<div>
    <p class="mb-1 text-sm font-bold uppercase">IV. Valor e forma de pagamento:</p>
    <div class="divide-y divide-black border border-black text-sm">
        <div class="grid grid-cols-2 divide-x divide-black">
            <div class="field-wrapper px-2 py-1.5">
                <label for="servico-valor" class="mb-1 block font-bold">VALOR TOTAL DOS SERVIÇOS:</label>
                <input id="servico-valor" name="servico[valor]" value="{{ old('servico.valor', $servico['valor'] ?? '') }}"
                       placeholder="R$ 0,00"
                       class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
            </div>
            <div class="field-wrapper px-2 py-1.5">
                <label for="servico-forma-pagamento" class="mb-1 block font-bold">FORMA DE PAGAMENTO:</label>
                <input id="servico-forma-pagamento" name="servico[forma_pagamento]" value="{{ old('servico.forma_pagamento', $servico['forma_pagamento'] ?? '') }}"
                       placeholder="Meio e condições de pagamento"
                       class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
            </div>
        </div>
        <div class="field-wrapper px-2 py-1.5">
            <label for="servico-vencimento" class="mb-1 block font-bold">VENCIMENTO / DATAS DE PAGAMENTO:</label>
            <input id="servico-vencimento" name="servico[vencimento]" value="{{ old('servico.vencimento', $servico['vencimento'] ?? '') }}"
                   placeholder="Datas ou periodicidade ajustada"
                   class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
        </div>
        <div class="field-wrapper px-2 py-1.5">
            <label for="servico-dados-pagamento" class="mb-1 block font-bold">DADOS PARA PAGAMENTO:</label>
            <input id="servico-dados-pagamento" name="servico[dados_pagamento]" value="{{ old('servico.dados_pagamento', $servico['dados_pagamento'] ?? '') }}"
                   placeholder="Dados bancários ou instruções de pagamento"
                   class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
        </div>
    </div>
</div>
