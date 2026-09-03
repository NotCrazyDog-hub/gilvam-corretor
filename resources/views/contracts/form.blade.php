<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $group ? 'Editar Contrato' : 'Novo Contrato de Corretagem' }}</title>
    @vite(['resources/css/app.css', 'resources/js/contract-form.js'])
</head>
<body>
    <div class="container">
        <h1>{{ $group ? 'Editar Contrato (nova versão)' : 'Novo Contrato de Corretagem' }}</h1>

        @if ($group)
            <p class="hint">
                Salvar aqui cria a <strong>versão {{ ($contract->version ?? 0) + 1 }}</strong>.
                A versão {{ $contract->version ?? '—' }} atual não será alterada nem apagada.
            </p>
        @endif

        <form
            id="contract-form"
            method="POST"
            action="{{ $group ? route('contracts.update', $group) : route('contracts.store') }}"
            data-method="{{ $group ? 'PUT' : 'POST' }}"
        >
            @csrf
            @if ($group) @method('PUT') @endif

            @php
                $contratante = $contract->contratante ?? [];
                $contratado = $contract->contratado ?? [];
                $imovel = $contract->imovel ?? [];
            @endphp

            <fieldset>
                <legend>I. Contratante</legend>
                <div class="grid">
                    <input name="contratante[nome]" value="{{ $contratante['nome'] ?? '' }}" placeholder="Nome" class="input" required>
                    <input name="contratante[estado_civil]" value="{{ $contratante['estado_civil'] ?? '' }}" placeholder="Estado civil" class="input">
                    <input name="contratante[cpf]" value="{{ $contratante['cpf'] ?? '' }}" placeholder="CPF" class="input" required>
                    <input name="contratante[conjuge_nome]" value="{{ $contratante['conjuge_nome'] ?? '' }}" placeholder="Nome do cônjuge" class="input">
                    <input name="contratante[conjuge_cpf]" value="{{ $contratante['conjuge_cpf'] ?? '' }}" placeholder="CPF do cônjuge" class="input">
                    <input name="contratante[endereco]" value="{{ $contratante['endereco'] ?? '' }}" placeholder="Endereço" class="input col-span-2">
                    <input name="contratante[telefones]" value="{{ $contratante['telefones'] ?? '' }}" placeholder="Telefones" class="input">
                    <input name="contratante[profissao]" value="{{ $contratante['profissao'] ?? '' }}" placeholder="Profissão" class="input">
                    <input name="contratante[identidade]" value="{{ $contratante['identidade'] ?? '' }}" placeholder="Identidade" class="input">
                    <input name="contratante[conjuge_identidade]" value="{{ $contratante['conjuge_identidade'] ?? '' }}" placeholder="Identidade do cônjuge" class="input">
                </div>
            </fieldset>

            <fieldset>
                <legend>II. Contratado(a)</legend>
                <div class="grid">
                    <input name="contratado[nome]" value="{{ $contratado['nome'] ?? '' }}" placeholder="Nome / Razão social" class="input" required>
                    <input name="contratado[cpf_cnpj]" value="{{ $contratado['cpf_cnpj'] ?? '' }}" placeholder="CPF ou CNPJ" class="input" required>
                    <input name="contratado[endereco]" value="{{ $contratado['endereco'] ?? '' }}" placeholder="Endereço" class="input col-span-2">
                    <input name="contratado[telefones]" value="{{ $contratado['telefones'] ?? '' }}" placeholder="Telefones" class="input">
                    <input name="contratado[creci]" value="{{ $contratado['creci'] ?? '' }}" placeholder="CRECI" class="input">
                </div>
            </fieldset>

            <fieldset>
                <legend>III. Objeto do Contrato</legend>
                <div class="grid">
                    <input name="imovel[tipo]" value="{{ $imovel['tipo'] ?? '' }}" placeholder="Tipo de imóvel" class="input">
                    <input name="imovel[localizacao]" value="{{ $imovel['localizacao'] ?? '' }}" placeholder="Localização" class="input col-span-2">
                    <input name="imovel[documento_propriedade]" value="{{ $imovel['documento_propriedade'] ?? '' }}" placeholder="Documento de propriedade" class="input">
                    <input name="imovel[matricula]" value="{{ $imovel['matricula'] ?? '' }}" placeholder="Matrícula" class="input">
                    <input name="imovel[inscricao_prefeitura]" value="{{ $imovel['inscricao_prefeitura'] ?? '' }}" placeholder="Inscrição prefeitura" class="input">
                    <input name="imovel[preco_venda]" value="{{ $imovel['preco_venda'] ?? '' }}" placeholder="Preço para venda" class="input">
                </div>

                <label class="field-label" for="editor-outros-dados">Outros dados</label>
                <div id="editor-outros-dados" class="editor-box"></div>
                <input type="hidden" name="imovel[outros_dados]" id="input-outros-dados">
                <input type="hidden" name="content_json[outros_dados]" id="input-outros-dados-json">

                <label class="field-label" for="editor-condicoes">Condições de pagamento</label>
                <div id="editor-condicoes" class="editor-box"></div>
                <input type="hidden" name="imovel[condicoes_pagamento]" id="input-condicoes">
                <input type="hidden" name="content_json[condicoes_pagamento]" id="input-condicoes-json">
            </fieldset>

            <button type="submit" id="submit-btn">
                {{ $group ? 'Salvar nova versão' : 'Gerar contrato' }}
            </button>
        </form>

        <div id="resultado" class="hidden">
            <a id="link-download" href="#">Baixar contrato gerado</a>
        </div>

        <div id="erro" class="hidden"></div>
    </div>

    {{-- Pré-carrega o conteúdo do TipTap quando estamos editando uma versão existente --}}
    <script>
        window.contractFormConfig = {
            outrosDadosContent: @json($imovel['outros_dados'] ?? ''),
            condicoesContent: @json($imovel['condicoes_pagamento'] ?? ''),
        };
    </script>
</body>
</html>