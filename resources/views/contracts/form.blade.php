<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $group ? 'Editar Contrato' : 'Novo Contrato de Corretagem' }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-[900px] mx-auto bg-white p-8 rounded-lg shadow-sm">

            @if ($group)
                <p class="mb-6 text-sm text-slate-600 bg-slate-50 border border-slate-200 rounded-lg p-4">
                    Salvar aqui cria <strong class="text-slate-800">a versão {{ ($contract->version ?? 0) + 1 }}</strong>.
                    A versão {{ $contract->version ?? '—' }} atual não será alterada nem apagada.
                </p>
            @endif

            <form
                id="contract-form"
                method="POST"
                action="{{ $group ? route('contracts.update', $group) : route('contracts.store') }}"
                data-method="{{ $group ? 'PUT' : 'POST' }}"
                class="space-y-8"
            >
                @csrf

                @if ($group)
                    @method('PUT')
                @endif

                {{-- Dados do cliente --}}
                @if (!$group && ($clientType ?? null) === 'pf')
                    <fieldset class="border border-slate-200 rounded-lg p-5">
                        <legend class="px-2 text-sm font-semibold text-slate-800">Cliente cadastrado</legend>

                        <div class="relative">
                            <input
                                type="text"
                                id="busca-cliente"
                                placeholder="Buscar por nome ou CPF..."
                                autocomplete="off"
                                class="w-full rounded-md border-slate-300 text-sm focus:border-brand-red focus:ring-brand-red"
                            >

                            <div id="resultados-busca-cliente"
                                 class="hidden absolute z-10 mt-1 w-full bg-white border border-slate-200 rounded-md shadow-lg max-h-56 overflow-y-auto">
                            </div>
                        </div>
                    </fieldset>
                @endif

                <input type="hidden" name="client_type" value="{{ $clientType ?? old('client_type') }}">
                <input type="hidden" name="client_id" id="input-client-id" value="{{ $group->client_id ?? old('client_id') }}">

                @php
                    $contratante = $contract->contratante ?? [];
                    $contratado = $contract->contratado ?? [];
                    $imovel = $contract->imovel ?? [];
                @endphp

                {{-- I. CONTRATANTE — segue a ordem/pareamento de linhas do template .docx --}}
                <fieldset class="border border-slate-200 rounded-lg p-5">
                    <legend class="px-2 text-sm font-semibold text-slate-800">I. Contratante</legend>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- linha 1: NOME --}}
                        <div class="field-wrapper sm:col-span-2">
                            <input name="contratante[nome]" value="{{ $contratante['nome'] ?? '' }}" placeholder="Nome"
                                   class="w-full rounded-md border-slate-300 text-sm focus:border-brand-red focus:ring-brand-red">
                        </div>

                        {{-- linha 2: ESTADO CIVIL | PROFISSÃO --}}
                        <div class="field-wrapper">
                            <input name="contratante[estado_civil]" value="{{ $contratante['estado_civil'] ?? '' }}" placeholder="Estado civil"
                                   class="w-full rounded-md border-slate-300 text-sm focus:border-brand-red focus:ring-brand-red">
                        </div>

                        <div class="field-wrapper">
                            <input name="contratante[profissao]" value="{{ $contratante['profissao'] ?? '' }}" placeholder="Profissão"
                                   class="w-full rounded-md border-slate-300 text-sm focus:border-brand-red focus:ring-brand-red">
                        </div>

                        {{-- linha 3: CPF | IDENTIDADE --}}
                        <div class="field-wrapper">
                            <input name="contratante[cpf]" value="{{ $contratante['cpf'] ?? '' }}" placeholder="CPF"
                                   class="w-full rounded-md border-slate-300 text-sm focus:border-brand-red focus:ring-brand-red">
                        </div>

                        <div class="field-wrapper">
                            <input name="contratante[identidade]" value="{{ $contratante['identidade'] ?? '' }}" placeholder="Identidade"
                                   class="w-full rounded-md border-slate-300 text-sm focus:border-brand-red focus:ring-brand-red">
                        </div>

                        {{-- linha 4: NOME DO CÔNJUGE --}}
                        <div class="field-wrapper sm:col-span-2">
                            <input name="contratante[conjuge_nome]" value="{{ $contratante['conjuge_nome'] ?? '' }}" placeholder="Nome do cônjuge"
                                   class="w-full rounded-md border-slate-300 text-sm focus:border-brand-red focus:ring-brand-red">
                        </div>

                        {{-- linha 5: ENDEREÇO | IDENTIDADE DO CÔNJUGE (pareamento do template) --}}
                        <div class="field-wrapper">
                            <input name="contratante[endereco]" value="{{ $contratante['endereco'] ?? '' }}" placeholder="Endereço"
                                   class="w-full rounded-md border-slate-300 text-sm focus:border-brand-red focus:ring-brand-red">
                        </div>

                        <div class="field-wrapper">
                            <input name="contratante[conjuge_identidade]" value="{{ $contratante['conjuge_identidade'] ?? '' }}" placeholder="Identidade do cônjuge"
                                   class="w-full rounded-md border-slate-300 text-sm focus:border-brand-red focus:ring-brand-red">
                        </div>

                        {{-- linha 6: TELEFONES --}}
                        <div class="field-wrapper sm:col-span-2">
                            <input name="contratante[telefones]" value="{{ $contratante['telefones'] ?? '' }}" placeholder="Telefones"
                                   class="w-full rounded-md border-slate-300 text-sm focus:border-brand-red focus:ring-brand-red">
                        </div>
                    </div>
                </fieldset>

                {{-- II. CONTRATADO --}}
                <fieldset class="border border-slate-200 rounded-lg p-5">
                    <legend class="px-2 text-sm font-semibold text-slate-800">II. Contratado(a)</legend>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- linha 1: NOME --}}
                        <div class="field-wrapper sm:col-span-2">
                            <input name="contratado[nome]" value="{{ $contratado['nome'] ?? 'Gilvam Vieira' }}" placeholder="Nome / Razão social"
                                   class="w-full rounded-md border-slate-300 text-sm focus:border-brand-red focus:ring-brand-red">
                        </div>

                        {{-- linha 2: CPF/CNPJ | CRECI --}}
                        <div class="field-wrapper">
                            <input name="contratado[cpf_cnpj]" value="{{ $contratado['cpf_cnpj'] ?? '' }}" placeholder="CPF ou CNPJ"
                                   class="w-full rounded-md border-slate-300 text-sm focus:border-brand-red focus:ring-brand-red">
                        </div>

                        <div class="field-wrapper">
                            <input name="contratado[creci]" value="{{ $contratado['creci'] ?? '' }}" placeholder="CRECI"
                                   class="w-full rounded-md border-slate-300 text-sm focus:border-brand-red focus:ring-brand-red">
                        </div>

                        {{-- linha 3: ENDEREÇO --}}
                        <div class="field-wrapper sm:col-span-2">
                            <input name="contratado[endereco]" value="{{ $contratado['endereco'] ?? '' }}" placeholder="Endereço"
                                   class="w-full rounded-md border-slate-300 text-sm focus:border-brand-red focus:ring-brand-red">
                        </div>

                        {{-- linha 4: TELEFONES --}}
                        <div class="field-wrapper sm:col-span-2">
                            <input name="contratado[telefones]" value="{{ $contratado['telefones'] ?? '' }}" placeholder="Telefones"
                                   class="w-full rounded-md border-slate-300 text-sm focus:border-brand-red focus:ring-brand-red">
                        </div>
                    </div>
                </fieldset>

                {{-- III. OBJETO DO CONTRATO --}}
                <fieldset class="border border-slate-200 rounded-lg p-5">
                    <legend class="px-2 text-sm font-semibold text-slate-800">III. Objeto do Contrato</legend>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                        {{-- linha 1: TIPO DE IMÓVEL --}}
                        <div class="field-wrapper sm:col-span-2">
                            <input name="imovel[tipo]" value="{{ $imovel['tipo'] ?? '' }}" placeholder="Tipo de imóvel"
                                   class="w-full rounded-md border-slate-300 text-sm focus:border-brand-red focus:ring-brand-red">
                        </div>

                        {{-- linha 2: LOCALIZAÇÃO --}}
                        <div class="field-wrapper sm:col-span-2">
                            <input name="imovel[localizacao]" value="{{ $imovel['localizacao'] ?? '' }}" placeholder="Localização"
                                   class="w-full rounded-md border-slate-300 text-sm focus:border-brand-red focus:ring-brand-red">
                        </div>

                        {{-- linha 3: DOCUMENTO DE PROPRIEDADE --}}
                        <div class="field-wrapper sm:col-span-2">
                            <input name="imovel[documento_propriedade]" value="{{ $imovel['documento_propriedade'] ?? '' }}" placeholder="Documento de propriedade"
                                   class="w-full rounded-md border-slate-300 text-sm focus:border-brand-red focus:ring-brand-red">
                        </div>

                        {{-- linha 4: MATRÍCULA | INSCRIÇÃO PREFEITURA --}}
                        <div class="field-wrapper">
                            <input name="imovel[matricula]" value="{{ $imovel['matricula'] ?? '' }}" placeholder="Matrícula"
                                   class="w-full rounded-md border-slate-300 text-sm focus:border-brand-red focus:ring-brand-red">
                        </div>

                        <div class="field-wrapper">
                            <input name="imovel[inscricao_prefeitura]" value="{{ $imovel['inscricao_prefeitura'] ?? '' }}" placeholder="Inscrição prefeitura"
                                   class="w-full rounded-md border-slate-300 text-sm focus:border-brand-red focus:ring-brand-red">
                        </div>
                    </div>

                    {{-- linha 5: OUTROS DADOS --}}
                    <label class="block text-sm font-medium text-slate-700 mb-1" for="editor-outros-dados">
                        Outros dados
                    </label>
                    <div id="editor-outros-dados"
                         class="min-h-[120px] border border-slate-300 rounded-md p-3 text-sm mb-6 focus-within:border-brand-red"></div>
                    <input type="hidden" name="imovel[outros_dados]" id="input-outros-dados">
                    <input type="hidden" name="content_json[outros_dados]" id="input-outros-dados-json">

                    {{-- linha 6: PREÇO PARA VENDA --}}
                    <div class="field-wrapper mb-6">
                        <input
                            type="text"
                            inputmode="numeric"
                            id="input-preco-venda"
                            name="imovel[preco_venda]"
                            value="{{ $imovel['preco_venda'] ?? '' }}"
                            placeholder="R$ 0,00"
                            class="w-full rounded-md border-slate-300 text-sm focus:border-brand-red focus:ring-brand-red"
                        >
                    </div>

                    {{-- linha 7: CONDIÇÕES DE PAGAMENTO --}}
                    <label class="block text-sm font-medium text-slate-700 mb-1" for="editor-condicoes">
                        Condições de pagamento
                    </label>
                    <div id="editor-condicoes"
                         class="min-h-[120px] border border-slate-300 rounded-md p-3 text-sm focus-within:border-brand-red"></div>
                    <input type="hidden" name="imovel[condicoes_pagamento]" id="input-condicoes">
                    <input type="hidden" name="content_json[condicoes_pagamento]" id="input-condicoes-json">
                </fieldset>

                <div class="flex justify-end gap-3">
                    <a href="{{ request('from') === 'versions' ? route('contracts.versions', $group) : route('contracts.index') }}"
                        class="bg-white hover:bg-red-50 text-brand-red border border-brand-red shadow-sm rounded-lg px-5 py-2.5 text-sm font-bold transition">
                        Cancelar e voltar
                    </a>

                    <button type="submit" id="submit-btn"
                            class="bg-brand-red hover:bg-brand-red-hover text-white shadow-sm rounded-lg px-5 py-2.5 text-sm font-bold transition disabled:opacity-50 disabled:cursor-not-allowed">
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
        };
    </script>
</x-app-layout>