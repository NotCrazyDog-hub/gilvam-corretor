<div>
    <p class="mb-1 text-sm font-bold uppercase">IV. Objeto do contrato:</p>

    <div class="border border-black divide-y divide-black text-sm">
        <div class="field-wrapper px-2 py-1.5">
            <div class="flex items-baseline gap-1">
                <label class="whitespace-nowrap font-bold">TIPO DE IMÓVEL:</label>
                <input name="imovel[tipo]" value="{{ $imovel['tipo'] ?? '' }}"
                       class="min-w-0 flex-1 border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
            </div>
        </div>
        <div class="field-wrapper px-2 py-1.5">
            <div class="flex items-baseline gap-1">
                <label class="whitespace-nowrap font-bold">LOCALIZAÇÃO:</label>
                <input name="imovel[localizacao]" value="{{ $imovel['localizacao'] ?? '' }}"
                       class="min-w-0 flex-1 border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
            </div>
        </div>
        <div class="field-wrapper px-2 py-1.5">
            <div class="flex items-baseline gap-1">
                <label class="whitespace-nowrap font-bold">DOCUMENTO DE PROPRIEDADE:</label>
                <input name="imovel[documento_propriedade]" value="{{ $imovel['documento_propriedade'] ?? '' }}"
                       class="min-w-0 flex-1 border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
            </div>
        </div>
        <div class="grid grid-cols-2 divide-x divide-black">
            <div class="field-wrapper px-2 py-1.5">
                <div class="flex items-baseline gap-1">
                    <label class="whitespace-nowrap font-bold">MATRÍCULA:</label>
                    <input name="imovel[matricula]" value="{{ $imovel['matricula'] ?? '' }}"
                           class="min-w-0 flex-1 border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
                </div>
            </div>
            <div class="field-wrapper px-2 py-1.5">
                <div class="flex items-baseline gap-1">
                    <label class="whitespace-nowrap font-bold">INSCRIÇÃO PREFEITURA:</label>
                    <input name="imovel[inscricao_prefeitura]" value="{{ $imovel['inscricao_prefeitura'] ?? '' }}"
                           class="min-w-0 flex-1 border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
                </div>
            </div>
        </div>
        <div class="field-wrapper px-2 py-1.5">
            <label class="mb-1 block font-bold" for="editor-outros-dados">OUTROS DADOS:</label>
            <div id="editor-outros-dados" class="min-h-[100px] border border-slate-300 p-2 text-sm focus-within:border-black"></div>
            <input type="hidden" name="imovel[outros_dados]" id="input-outros-dados">
            <input type="hidden" name="content_json[outros_dados]" id="input-outros-dados-json">
        </div>
    </div>
</div>
