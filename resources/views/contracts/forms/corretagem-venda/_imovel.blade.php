                {{-- III. OBJETO DO CONTRATO --}}
                <div>
                    <p class="font-bold text-sm uppercase mb-1">{{ $tipoContratoKey === 'doacao' ? 'III. Objeto da Doação:' : 'III. Objeto do Contrato:' }}</p>

                    <div class="border border-black divide-y divide-black text-sm">
                        {{-- linha 1: TIPO DE IMÓVEL --}}
                        <div class="field-wrapper px-2 py-1.5">
                            <div class="flex items-baseline gap-1">
                                <label class="font-bold whitespace-nowrap">TIPO DE IMÓVEL:</label>
                                <input name="imovel[tipo]" value="{{ $imovel['tipo'] ?? '' }}"
                                       class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                            </div>
                        </div>

                        {{-- linha 2: LOCALIZAÇÃO --}}
                        <div class="field-wrapper px-2 py-1.5">
                            <div class="flex items-baseline gap-1">
                                <label class="font-bold whitespace-nowrap">LOCALIZAÇÃO:</label>
                                <input name="imovel[localizacao]" value="{{ $imovel['localizacao'] ?? '' }}"
                                       class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                            </div>
                        </div>

                        {{-- linha 3: DOCUMENTO DE PROPRIEDADE --}}
                        <div class="field-wrapper px-2 py-1.5">
                            <div class="flex items-baseline gap-1">
                                <label class="font-bold whitespace-nowrap">DOCUMENTO DE PROPRIEDADE:</label>
                                <input name="imovel[documento_propriedade]" value="{{ $imovel['documento_propriedade'] ?? '' }}"
                                       class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                            </div>
                        </div>

                        {{-- linha 4: MATRÍCULA | INSCRIÇÃO PREFEITURA --}}
                        <div class="grid grid-cols-2 divide-x divide-black">
                            <div class="field-wrapper px-2 py-1.5">
                                <div class="flex items-baseline gap-1">
                                    <label class="font-bold whitespace-nowrap">MATRÍCULA:</label>
                                    <input name="imovel[matricula]" value="{{ $imovel['matricula'] ?? '' }}"
                                           class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                                </div>
                            </div>
                            <div class="field-wrapper px-2 py-1.5">
                                <div class="flex items-baseline gap-1">
                                    <label class="font-bold whitespace-nowrap">INSCRIÇÃO PREFEITURA:</label>
                                    <input name="imovel[inscricao_prefeitura]" value="{{ $imovel['inscricao_prefeitura'] ?? '' }}"
                                           class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                                </div>
                            </div>
                        </div>

                        {{-- linha 5: OUTROS DADOS --}}
                        <div class="field-wrapper px-2 py-1.5">
                            <label class="font-bold block mb-1" for="editor-outros-dados">OUTROS DADOS:</label>
                            <div id="editor-outros-dados"
                                 class="min-h-[100px] border border-slate-300 p-2 text-sm focus-within:border-black"></div>
                            <input type="hidden" name="imovel[outros_dados]" id="input-outros-dados">
                            <input type="hidden" name="content_json[outros_dados]" id="input-outros-dados-json">
                        </div>

                        @if ($tipoContratoKey === 'doacao')
                        <div class="field-wrapper px-2 py-1.5">
                            <div class="flex items-baseline gap-1">
                                <label class="font-bold whitespace-nowrap">VALOR ATRIBUÍDO AO IMÓVEL:</label>
                                <input type="text" inputmode="numeric" id="input-valor-atribuido" name="imovel[valor_atribuido]"
                                    value="{{ old('imovel.valor_atribuido', $imovel['valor_atribuido'] ?? '') }}" placeholder="R$ 0,00"
                                    class="flex-1 border-0 bg-transparent focus:ring-0 p-0 min-w-0">
                            </div>
                        </div>
                        <div class="field-wrapper px-2 py-1.5">
                            <label class="font-bold block mb-1">ÔNUS E GRAVAMES:</label>
                            <textarea name="imovel[onus]" rows="2" class="w-full border border-slate-300 p-2">{{ old('imovel.onus', $imovel['onus'] ?? '') }}</textarea>
                        </div>
                        @else
                        {{-- linha 6: PREÇO PARA VENDA --}}
                        <div class="field-wrapper px-2 py-1.5">
                            <div class="flex items-baseline gap-1">
                                <label class="font-bold whitespace-nowrap">PREÇO PARA VENDA:</label>
                                <input
                                    type="text"
                                    inputmode="numeric"
                                    id="input-preco-venda"
                                    name="imovel[preco_venda]"
                                    value="{{ $imovel['preco_venda'] ?? '' }}"
                                    placeholder="R$ 0,00"
                                    class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0"
                                >
                            </div>
                        </div>

                        {{-- linha 7: CONDIÇÕES DE PAGAMENTO --}}
                        <div class="field-wrapper px-2 py-1.5">
                            <label class="font-bold block mb-1" for="editor-condicoes">CONDIÇÕES DE PAGAMENTO:</label>
                            <div id="editor-condicoes"
                                 class="min-h-[100px] border border-slate-300 p-2 text-sm focus-within:border-black"></div>
                            <input type="hidden" name="imovel[condicoes_pagamento]" id="input-condicoes">
                            <input type="hidden" name="content_json[condicoes_pagamento]" id="input-condicoes-json">
                        </div>
                        @endif
                    </div>
                </div>

