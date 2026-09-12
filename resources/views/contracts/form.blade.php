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

                <fieldset class="border border-slate-200 rounded-lg p-5">
                    <legend class="px-2 text-sm font-semibold text-slate-800">I. Contratante</legend>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <input name="contratante[nome]" value="{{ $contratante['nome'] ?? '' }}" placeholder="Nome" required
                               class="rounded-md border-slate-300 text-sm focus:border-brand-red focus:ring-brand-red">

                        <input name="contratante[estado_civil]" value="{{ $contratante['estado_civil'] ?? '' }}" placeholder="Estado civil"
                               class="rounded-md border-slate-300 text-sm focus:border-brand-red focus:ring-brand-red">

                        <input name="contratante[cpf]" value="{{ $contratante['cpf'] ?? '' }}" placeholder="CPF" required
                               class="rounded-md border-slate-300 text-sm focus:border-brand-red focus:ring-brand-red">

                        <input name="contratante[conjuge_nome]" value="{{ $contratante['conjuge_nome'] ?? '' }}" placeholder="Nome do cônjuge"
                               class="rounded-md border-slate-300 text-sm focus:border-brand-red focus:ring-brand-red">

                        <input name="contratante[conjuge_cpf]" value="{{ $contratante['conjuge_cpf'] ?? '' }}" placeholder="CPF do cônjuge"
                               class="rounded-md border-slate-300 text-sm focus:border-brand-red focus:ring-brand-red">

                        <input name="contratante[endereco]" value="{{ $contratante['endereco'] ?? '' }}" placeholder="Endereço"
                               class="sm:col-span-2 rounded-md border-slate-300 text-sm focus:border-brand-red focus:ring-brand-red">

                        <input name="contratante[telefones]" value="{{ $contratante['telefones'] ?? '' }}" placeholder="Telefones"
                               class="rounded-md border-slate-300 text-sm focus:border-brand-red focus:ring-brand-red">

                        <input name="contratante[profissao]" value="{{ $contratante['profissao'] ?? '' }}" placeholder="Profissão"
                               class="rounded-md border-slate-300 text-sm focus:border-brand-red focus:ring-brand-red">

                        <input name="contratante[identidade]" value="{{ $contratante['identidade'] ?? '' }}" placeholder="Identidade"
                               class="rounded-md border-slate-300 text-sm focus:border-brand-red focus:ring-brand-red">

                        <input name="contratante[conjuge_identidade]" value="{{ $contratante['conjuge_identidade'] ?? '' }}" placeholder="Identidade do cônjuge"
                               class="rounded-md border-slate-300 text-sm focus:border-brand-red focus:ring-brand-red">
                    </div>
                </fieldset>

                <fieldset class="border border-slate-200 rounded-lg p-5">
                    <legend class="px-2 text-sm font-semibold text-slate-800">II. Contratado(a)</legend>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <input name="contratado[nome]" value="{{ $contratado['nome'] ?? 'Gilvam Vieira' }}" placeholder="Nome / Razão social" required
                               class="rounded-md border-slate-300 text-sm focus:border-brand-red focus:ring-brand-red">

                        <input name="contratado[cpf_cnpj]" value="{{ $contratado['cpf_cnpj'] ?? '' }}" placeholder="CPF ou CNPJ" required
                               class="rounded-md border-slate-300 text-sm focus:border-brand-red focus:ring-brand-red">

                        <input name="contratado[endereco]" value="{{ $contratado['endereco'] ?? '' }}" placeholder="Endereço"
                               class="sm:col-span-2 rounded-md border-slate-300 text-sm focus:border-brand-red focus:ring-brand-red">

                        <input name="contratado[telefones]" value="{{ $contratado['telefones'] ?? '' }}" placeholder="Telefones"
                               class="rounded-md border-slate-300 text-sm focus:border-brand-red focus:ring-brand-red">

                        <input name="contratado[creci]" value="{{ $contratado['creci'] ?? '' }}" placeholder="CRECI"
                               class="rounded-md border-slate-300 text-sm focus:border-brand-red focus:ring-brand-red">
                    </div>
                </fieldset>

                <fieldset class="border border-slate-200 rounded-lg p-5">
                    <legend class="px-2 text-sm font-semibold text-slate-800">III. Objeto do Contrato</legend>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                        <input name="imovel[tipo]" value="{{ $imovel['tipo'] ?? '' }}" placeholder="Tipo de imóvel"
                               class="rounded-md border-slate-300 text-sm focus:border-brand-red focus:ring-brand-red">

                        <input name="imovel[localizacao]" value="{{ $imovel['localizacao'] ?? '' }}" placeholder="Localização"
                               class="sm:col-span-2 rounded-md border-slate-300 text-sm focus:border-brand-red focus:ring-brand-red">

                        <input name="imovel[documento_propriedade]" value="{{ $imovel['documento_propriedade'] ?? '' }}" placeholder="Documento de propriedade"
                               class="rounded-md border-slate-300 text-sm focus:border-brand-red focus:ring-brand-red">

                        <input name="imovel[matricula]" value="{{ $imovel['matricula'] ?? '' }}" placeholder="Matrícula"
                               class="rounded-md border-slate-300 text-sm focus:border-brand-red focus:ring-brand-red">

                        <input name="imovel[inscricao_prefeitura]" value="{{ $imovel['inscricao_prefeitura'] ?? '' }}" placeholder="Inscrição prefeitura"
                               class="rounded-md border-slate-300 text-sm focus:border-brand-red focus:ring-brand-red">

                        <input name="imovel[preco_venda]" value="{{ $imovel['preco_venda'] ?? '' }}" placeholder="Preço para venda"
                               class="rounded-md border-slate-300 text-sm focus:border-brand-red focus:ring-brand-red">
                    </div>

                    <label class="block text-sm font-medium text-slate-700 mb-1" for="editor-outros-dados">
                        Outros dados
                    </label>
                    <div id="editor-outros-dados"
                         class="min-h-[120px] border border-slate-300 rounded-md p-3 text-sm mb-6 focus-within:border-brand-red"></div>
                    <input type="hidden" name="imovel[outros_dados]" id="input-outros-dados">
                    <input type="hidden" name="content_json[outros_dados]" id="input-outros-dados-json">

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
                        Cancelar
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