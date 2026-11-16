<div>
    <p class="mb-1 text-sm font-bold uppercase">V. Preço, forma de pagamento e corretagem:</p>

    <div class="border border-black divide-y divide-black text-sm">
        <div class="field-wrapper px-2 py-1.5">
            <div class="flex items-baseline gap-1">
                <label class="whitespace-nowrap font-bold">PREÇO TOTAL DA VENDA:</label>
                <input type="text" inputmode="numeric" id="input-preco-venda" name="imovel[preco_venda]"
                       value="{{ $imovel['preco_venda'] ?? '' }}" placeholder="R$ 0,00"
                       class="min-w-0 flex-1 border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
            </div>
        </div>
        <div class="field-wrapper px-2 py-1.5">
            <div class="flex items-baseline gap-1">
                <label class="whitespace-nowrap font-bold">SINAL / ARRAS:</label>
                <input name="negocio[sinal]" value="{{ $negocio['sinal'] ?? '' }}"
                       placeholder="Valor e condições, se houver"
                       class="min-w-0 flex-1 border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
            </div>
        </div>
        <div class="field-wrapper px-2 py-1.5">
            <label class="mb-1 block font-bold" for="editor-condicoes">SALDO E FORMA DE PAGAMENTO:</label>
            <div id="editor-condicoes" class="min-h-[100px] border border-slate-300 p-2 text-sm focus-within:border-black"></div>
            <input type="hidden" name="imovel[condicoes_pagamento]" id="input-condicoes">
            <input type="hidden" name="content_json[condicoes_pagamento]" id="input-condicoes-json">
        </div>
        <div class="field-wrapper px-2 py-1.5">
            <div class="flex items-baseline gap-1">
                <label class="whitespace-nowrap font-bold">PRAZO PARA ESCRITURA PÚBLICA:</label>
                <input name="negocio[prazo_escritura]" value="{{ $negocio['prazo_escritura'] ?? '' }}"
                       placeholder="Ex.: 30 dias após a quitação"
                       class="min-w-0 flex-1 border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
            </div>
        </div>
        <div class="field-wrapper px-2 py-1.5">
            <div class="flex items-baseline gap-1">
                <label class="whitespace-nowrap font-bold">DATA DE ENTREGA / IMISSÃO NA POSSE:</label>
                <input type="date" name="negocio[data_posse]" value="{{ $negocio['data_posse'] ?? '' }}"
                       class="min-w-0 flex-1 border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
            </div>
        </div>
        <div class="field-wrapper px-2 py-1.5">
            <div class="flex items-baseline gap-1">
                <label class="whitespace-nowrap font-bold">COMISSÃO DE CORRETAGEM:</label>
                <input name="negocio[comissao]" value="{{ $negocio['comissao'] ?? '' }}"
                       placeholder="Valor ou percentual e condições de pagamento"
                       class="min-w-0 flex-1 border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
            </div>
        </div>
        <div class="field-wrapper px-2 py-1.5">
            <div class="flex items-baseline gap-1">
                <label class="whitespace-nowrap font-bold">COMISSÃO PAGA POR:</label>
                <input name="negocio[comissao_responsavel]" value="{{ $negocio['comissao_responsavel'] ?? '' }}"
                       placeholder="Vendedor(a), comprador(a) ou divisão ajustada"
                       class="min-w-0 flex-1 border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
            </div>
        </div>
    </div>
</div>
