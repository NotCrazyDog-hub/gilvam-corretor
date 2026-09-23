<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $group ? 'Editar Contrato' : 'Novo Contrato de Corretagem' }}
            </h2>
        </div>
    </x-slot>

    <style>
        /* Os campos seguem o estilo "tabela" do .docx (sem borda própria no
           input), então marcar erro só com border-red-400 não aparecia —
           border-width continuava 0. Usamos outline (não afeta o layout)
           + fundo leve pra destacar o campo inválido. */
        .campo-invalido {
            background-color: #fef2f2;
            outline: 2px solid #f87171;
            outline-offset: -2px;
        }
    </style>

    @php

        $clausulasPadrao = ''
    @endphp

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-[900px] mx-auto bg-white p-8 rounded-lg shadow-sm font-sans text-black">

            {{-- Cabeçalho igual ao topo do .docx --}}
            <div class="text-center mb-6 leading-snug">
                <p class="font-bold text-sm uppercase">Contrato de Corretagem de Venda de Imóveis</p>
                <p class="font-bold text-sm uppercase">Com Cláusula de Exclusividade</p>
                <p class="font-bold text-sm">(Arts. 722 a 729 do Novo Código Civil c/c Art. 20, III da Lei nº 6.530/78)</p>
            </div>

            @if ($group)
                <p class="mb-6 text-sm text-slate-600 bg-slate-50 border border-slate-300 p-4">
                    Salvar aqui cria <strong class="text-slate-800">a versão {{ ($contract->version ?? 0) + 1 }}</strong>.
                    A versão {{ $contract->version ?? '—' }} atual não será alterada nem apagada.
                </p>
            @endif

            <form
                id="contract-form"
                method="POST"
                action="{{ $group ? route('contracts.update', $group) : route('contracts.store') }}"
                data-method="{{ $group ? 'PUT' : 'POST' }}"
                class="space-y-6"
            >
                @csrf

                @if ($group)
                    @method('PUT')
                @endif

                {{-- Dados do cliente --}}
                @if (!$group && ($clientType ?? null) === 'pf')
                    <fieldset class="border border-slate-300 p-4">
                        <legend class="px-2 text-xs font-semibold text-slate-600 uppercase tracking-wide">Cliente cadastrado</legend>

                        <div class="relative">
                            <input
                                type="text"
                                id="busca-cliente"
                                placeholder="Buscar por nome ou CPF..."
                                autocomplete="off"
                                class="w-full border border-slate-300 text-sm px-2 py-1.5 focus:border-black focus:ring-0 focus:outline-none"
                            >

                            <div id="resultados-busca-cliente"
                                 class="hidden absolute z-10 mt-1 w-full bg-white border border-slate-300 shadow-lg max-h-56 overflow-y-auto">
                            </div>
                        </div>
                    </fieldset>
                @endif

                <input type="hidden" name="client_type" value="{{ $clientType ?? old('client_type') }}">
                <input type="hidden" name="client_id" id="input-client-id" value="{{ $group->client_id ?? old('client_id') }}">

                <fieldset class="border border-slate-300 p-4">
                    <legend class="px-2 text-xs font-semibold text-slate-600 uppercase tracking-wide">
                        Vigência do contrato (uso interno)
                    </legend>

                    <div class="grid grid-cols-2 gap-4 items-end">
                        <div>
                            <label for="data_termino" class="block text-sm font-medium text-slate-700">Data de término</label>
                            <input type="date" name="data_termino" id="data_termino"
                                value="{{ old('data_termino', optional($contract?->data_termino)->format('Y-m-d')) }}"
                                {{ old('prazo_indeterminado', $contract?->prazo_indeterminado ?? false) ? 'disabled' : '' }}
                                class="mt-1 w-full border border-slate-300 text-sm px-2 py-1.5 focus:border-black focus:ring-0 focus:outline-none">
                            @error('data_termino')
                                <span class="text-red-600 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex items-center gap-2 pb-2">
                            <input type="checkbox" name="prazo_indeterminado" id="prazo_indeterminado" value="1"
                                {{ old('prazo_indeterminado', $contract?->prazo_indeterminado) ? 'checked' : '' }}
                                onchange="document.getElementById('data_termino').disabled = this.checked; if (this.checked) document.getElementById('data_termino').value = '';">
                            <label for="prazo_indeterminado" class="text-sm text-slate-700">Prazo indeterminado</label>
                        </div>
                    </div>
                </fieldset>
                
                @php
                    $contratante = $contract->contratante ?? [];
                    $contratado = $contract->contratado ?? [];
                    $imovel = $contract->imovel ?? [];
                    $assinatura = $contract->assinatura ?? [];
                @endphp

                {{-- I. CONTRATANTE — segue a ordem/pareamento de linhas do template .docx --}}
                <div>
                    <p class="font-bold text-sm uppercase mb-1">I. Contratante:</p>

                    <div class="border border-black divide-y divide-black text-sm">
                        {{-- linha 1: NOME --}}
                        <div class="field-wrapper px-2 py-1.5">
                            <div class="flex items-baseline gap-1">
                                <label class="font-bold whitespace-nowrap">NOME:</label>
                                <input name="contratante[nome]" value="{{ $contratante['nome'] ?? '' }}"
                                       class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                            </div>
                        </div>

                        {{-- linha 2: ESTADO CIVIL | PROFISSÃO --}}
                        <div class="grid grid-cols-2 divide-x divide-black">
                            <div class="field-wrapper px-2 py-1.5">
                                <div class="flex items-baseline gap-1">
                                    <label class="font-bold whitespace-nowrap">ESTADO CIVIL:</label>
                                    <input name="contratante[estado_civil]" value="{{ $contratante['estado_civil'] ?? '' }}"
                                           class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                                </div>
                            </div>
                            <div class="field-wrapper px-2 py-1.5">
                                <div class="flex items-baseline gap-1">
                                    <label class="font-bold whitespace-nowrap">PROFISSÃO:</label>
                                    <input name="contratante[profissao]" value="{{ $contratante['profissao'] ?? '' }}"
                                           class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                                </div>
                            </div>
                        </div>

                        {{-- linha 3: CPF | IDENTIDADE --}}
                        <div class="grid grid-cols-2 divide-x divide-black">
                            <div class="field-wrapper px-2 py-1.5">
                                <div class="flex items-baseline gap-1">
                                    <label class="font-bold whitespace-nowrap">CPF(MF) Nº:</label>
                                    <input name="contratante[cpf]" value="{{ $contratante['cpf'] ?? '' }}"
                                           class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                                </div>
                            </div>
                            <div class="field-wrapper px-2 py-1.5">
                                <div class="flex items-baseline gap-1">
                                    <label class="font-bold whitespace-nowrap">IDENTIDADE Nº:</label>
                                    <input name="contratante[identidade]" value="{{ $contratante['identidade'] ?? '' }}"
                                           class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                                </div>
                            </div>
                        </div>

                        {{-- linha 4: NOME DO CÔNJUGE --}}
                        <div class="field-wrapper px-2 py-1.5">
                            <div class="flex items-baseline gap-1">
                                <label class="font-bold whitespace-nowrap">NOME DO CÔNJUGE:</label>
                                <input name="contratante[conjuge_nome]" value="{{ $contratante['conjuge_nome'] ?? '' }}"
                                       class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                            </div>
                        </div>

                        {{-- linha 5: IDENTIDADE DO CÔNJUGE --}}
                        <div class="field-wrapper px-2 py-1.5">
                            <div class="flex items-baseline gap-1">
                                <label class="font-bold whitespace-nowrap">IDENTIDADE DO CÔNJUGE Nº:</label>
                                <input name="contratante[conjuge_identidade]" value="{{ $contratante['conjuge_identidade'] ?? '' }}"
                                       class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                            </div>
                        </div>

                        {{-- linha 6: ENDEREÇO --}}
                        <div class="field-wrapper px-2 py-1.5">
                            <div class="flex items-baseline gap-1">
                                <label class="font-bold whitespace-nowrap">ENDEREÇO:</label>
                                <input name="contratante[endereco]" value="{{ $contratante['endereco'] ?? '' }}"
                                       class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                            </div>
                        </div>

                        {{-- linha 7: TELEFONES --}}
                        <div class="field-wrapper px-2 py-1.5">
                            <div class="flex items-baseline gap-1">
                                <label class="font-bold whitespace-nowrap">TELEFONES:</label>
                                <input name="contratante[telefones]" value="{{ $contratante['telefones'] ?? '' }}"
                                       class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- II. CONTRATADO --}}
                <div>
                    <p class="font-bold text-sm uppercase mb-1">II. Contratado(a):</p>

                    <div class="border border-black divide-y divide-black text-sm">
                        {{-- linha 1: NOME --}}
                        <div class="field-wrapper px-2 py-1.5">
                            <div class="flex items-baseline gap-1">
                                <label class="font-bold whitespace-nowrap">NOME:</label>
                                <input name="contratado[nome]" value="{{ $contratado['nome'] ?? 'Gilvam Vieira' }}"
                                       class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                            </div>
                        </div>

                        {{-- linha 2: CPF/CNPJ | CRECI --}}
                        <div class="grid grid-cols-2 divide-x divide-black">
                            <div class="field-wrapper px-2 py-1.5">
                                <div class="flex items-baseline gap-1">
                                    <label class="font-bold whitespace-nowrap">CPF(MF) ou CNPJ(MF):</label>
                                    <input name="contratado[cpf_cnpj]" value="{{ $contratado['cpf_cnpj'] ?? '' }}"
                                           class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                                </div>
                            </div>
                            <div class="field-wrapper px-2 py-1.5">
                                <div class="flex items-baseline gap-1">
                                    <label class="font-bold whitespace-nowrap">CRECI - 15ª REGIÃO Nº:</label>
                                    <input name="contratado[creci]" value="{{ $contratado['creci'] ?? '' }}"
                                           class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                                </div>
                            </div>
                        </div>

                        {{-- linha 3: ENDEREÇO --}}
                        <div class="field-wrapper px-2 py-1.5">
                            <div class="flex items-baseline gap-1">
                                <label class="font-bold whitespace-nowrap">ENDEREÇO:</label>
                                <input name="contratado[endereco]" value="{{ $contratado['endereco'] ?? '' }}"
                                       class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                            </div>
                        </div>

                        {{-- linha 4: TELEFONES --}}
                        <div class="field-wrapper px-2 py-1.5">
                            <div class="flex items-baseline gap-1">
                                <label class="font-bold whitespace-nowrap">TELEFONES:</label>
                                <input name="contratado[telefones]" value="{{ $contratado['telefones'] ?? '' }}"
                                       class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- III. OBJETO DO CONTRATO --}}
                <div>
                    <p class="font-bold text-sm uppercase mb-1">III. Objeto do Contrato:</p>

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
                    </div>
                </div>

                {{-- CLÁUSULAS + ASSINATURAS (caixa única, sem os rótulos IV/V) --}}
                <div class="border border-black text-sm p-4 space-y-6">
                    <p>
                        Pelo presente instrumento particular, CONTRATANTE e CONTRATADO, têm justo e contratado,
                        a presente contratação, que será regida pelas cláusulas e condições a seguir:
                    </p>

                    <div class="field-wrapper">
                        <div id="editor-clausulas"
                            class="min-h-[220px] border border-slate-300 p-2 text-sm focus-within:border-black"></div>
                        <input type="hidden" name="clausulas" id="input-clausulas">
                        <input type="hidden" name="content_json[clausulas]" id="input-clausulas-json">
                    </div>

                    <div class="field-wrapper flex items-baseline gap-1">
                        <label class="font-bold whitespace-nowrap">LOCAL E DATA:</label>
                        <p class="text-sm">
                            <span class="inline-block border-b border-black w-48 align-bottom">&nbsp;</span>,
                            <span class="inline-block border-b border-black w-10 align-bottom mx-1">&nbsp;</span>
                            de
                            <span class="inline-block border-b border-black w-40 align-bottom mx-1">&nbsp;</span>
                            de
                            <span class="inline-block border-b border-black w-20 align-bottom mx-1">&nbsp;</span>
                        </p>
                    </div>

                    <div class="max-w-md mx-auto space-y-10 text-center pt-6">
                        <div class="border-t border-black pt-1">
                            <p class="font-bold">CONTRATANTE</p>
                        </div>
                        <div class="border-t border-black pt-1">
                            <p class="font-bold">CÔNJUGE</p>
                        </div>
                        <div class="border-t border-black pt-1">
                            <p class="font-bold">CONTRATADO</p>
                        </div>
                    </div>

                    <div>
                        <p class="font-bold mb-4">TESTEMUNHAS:</p>
                        <div class="grid grid-cols-2 gap-8 text-center text-xs">
                            <div class="border-t border-black pt-1">
                                <p>CPF(MF):</p>
                            </div>
                            <div class="border-t border-black pt-1">
                                <p>CPF(MF):</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ request('from') === 'versions' ? route('contracts.versions', $group) : route('contracts.index') }}"
                        class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 shadow-sm rounded-lg px-5 py-2.5 text-sm font-bold transition">
                        Cancelar ou voltar
                    </a>

                    <button type="submit" id="submit-btn"
                            class="bg-blue-600 hover:bg-blue-700 text-white shadow-sm rounded-lg px-5 py-2.5 text-sm font-bold transition disabled:opacity-50 disabled:cursor-not-allowed">
                        {{ $group ? 'Salvar nova versão' : 'Gerar contrato' }}
                    </button>
                </div>
            </form>

            <div id="resultado" class="hidden mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <a id="link-download" href="#" class="text-brand-green font-semibold hover:underline">
                    Baixar contrato gerado
                </a>
            </div>

            <div id="erro" class="hidden mt-6 p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-brand-red"></div>

        </div>
    </div>

    <script>
        window.contractFormConfig = {
            outrosDadosContent: @json($imovel['outros_dados'] ?? ''),
            condicoesContent: @json($imovel['condicoes_pagamento'] ?? ''),
            clausulasContent: @json($contract->clausulas ?? $clausulasPadrao),
        };
    </script>
</x-app-layout>