@php
    $tipoCliente = $clientType ?? $group?->client?->tipo ?? old('client_type');
    $isPj = $tipoCliente === 'pj';

    // Tipo de contrato: do grupo (edição), do old() ou da query string (?contract_type=).
    // Fica aqui no topo porque o cabeçalho da página também usa.
    $tipoContratoKey = \App\Domains\Contracts\Services\ContractTypes::resolve(
        $group?->contract_type ?? $contractType ?? old('contract_type', request('contract_type'))
    );
    $tipoContrato = config("contracts.types.$tipoContratoKey");
    $templateAvailable = $templateAvailable ?? (!empty($tipoContrato['form_supported'])
        && \Illuminate\Support\Facades\Storage::disk(config('contracts.templates_disk'))
            ->exists(config('contracts.templates_dir') . '/' . $tipoContrato['template']));

    // Cláusulas: na edição vale o que foi salvo na versão atual (JSON do TipTap);
    // só contrato novo começa com o texto padrão do tipo.
    $clausulasSalvas = $contract?->content_json['clausulas'] ?? null;
    $clausulasInicial = $clausulasSalvas
        ? json_decode($clausulasSalvas, true)
        : \App\Domains\Contracts\Services\ContractTypes::clausulasPadrao($tipoContratoKey);
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $group ? 'Editar Contrato' : 'Novo ' . $tipoContrato['titulo'] }}
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

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-[900px] mx-auto bg-white p-8 rounded-lg shadow-sm font-sans text-black">

            {{-- Cabeçalho igual ao topo do .docx (vem do tipo de contrato) --}}
            <div class="text-center mb-6 leading-snug">
                <p class="font-bold text-sm uppercase">{{ $tipoContrato['titulo'] }}</p>
                @if (!empty($tipoContrato['subtitulo']))
                    <p class="font-bold text-sm uppercase">{{ $tipoContrato['subtitulo'] }}</p>
                @endif
                @if (!empty($tipoContrato['base_legal']))
                    <p class="font-bold text-sm">{{ $tipoContrato['base_legal'] }}</p>
                @endif
            </div>

            @if ($group)
                <p class="mb-6 text-sm text-slate-600 bg-slate-50 border border-slate-300 p-4">
                    Salvar aqui cria <strong class="text-slate-800">a versão {{ ($contract->version ?? 0) + 1 }}</strong>.
                    A versão {{ $contract->version ?? '—' }} atual não será alterada nem apagada.
                </p>
            @endif

            @unless ($templateAvailable)
                <p class="mb-6 text-sm text-amber-800 bg-amber-50 border border-amber-300 p-4">
                    Este tipo ainda não está pronto para gerar. Adicione o modelo
                    <strong>{{ $tipoContrato['template'] }}</strong> em
                    <code>storage/app/public/templates</code> e implemente o formulário correspondente.
                </p>
            @endunless

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
                @if (!$group)
                    <fieldset id="bloco-busca-imovel" data-tipo="{{ $clientType ?? '' }}" class="border border-slate-300 p-4">
                        <legend class="px-2 text-xs font-semibold text-slate-600 uppercase tracking-wide">Imóvel</legend>

                        <div class="relative">
                            <input type="text" id="busca-imovel" autocomplete="off"
                                placeholder="Buscar por endereço, bairro, matrícula ou inscrição..."
                                class="w-full border border-slate-300 text-sm px-2 py-1.5 focus:border-black focus:ring-0 focus:outline-none">
                            <div id="resultados-busca-imovel"
                                class="hidden absolute z-10 mt-1 w-full bg-white border border-slate-300 shadow-lg max-h-56 overflow-y-auto"></div>
                        </div>

                        <div id="bloco-contratante" class="hidden mt-4 space-y-2">
                            <p class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Quem será o contratante?</p>
                            <div id="lista-candidatos" class="space-y-1"></div>
                            <p id="aviso-candidatos" class="hidden text-sm text-amber-700 bg-amber-50 border border-amber-200 p-2"></p>
                        </div>
                    </fieldset>
                @endif

                <input type="hidden" name="contract_type" value="{{ $tipoContratoKey }}">
                <input type="hidden" name="imovel_id" id="input-imovel-id" value="{{ $group->imovel_id ?? old('imovel_id') }}">
                <input type="hidden" name="client_type" id="input-client-type" value="{{ $clientType ?? old('client_type') }}">
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
                    <p class="font-bold text-sm uppercase mb-1">{{ $tipoContratoKey === 'doacao' ? 'I. Doador:' : 'I. Contratante:' }}</p>

                    <div class="border border-black text-sm">
                        <div data-person-fields="pj" class="divide-y divide-black" @if (!$isPj) hidden @endif>
                            @include('contracts._contratante-pj')
                        </div>
                        <div data-person-fields="pf" class="divide-y divide-black" @if ($isPj) hidden @endif>
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
                </div>

                @if ($tipoContratoKey === 'doacao')
                    @php($tipoDonatario = old('donatario.tipo', $contratado['tipo'] ?? 'pf'))
                    <div>
                        <p class="font-bold text-sm uppercase mb-1">II. Donatário(a):</p>
                        <div class="border border-black divide-y divide-black text-sm">
                            <div class="field-wrapper px-2 py-1.5">
                                <label for="donatario-tipo" class="font-bold mr-2">TIPO DE PESSOA:</label>
                                <select id="donatario-tipo" name="donatario[tipo]" class="border border-slate-300 text-sm px-2 py-1">
                                    <option value="pf" @selected($tipoDonatario === 'pf')>Pessoa física</option>
                                    <option value="pj" @selected($tipoDonatario === 'pj')>Pessoa jurídica</option>
                                </select>
                            </div>

                            <div data-donatario-fields="pf" @if ($tipoDonatario !== 'pf') hidden @endif>
                                <div class="field-wrapper px-2 py-1.5"><label class="font-bold">NOME:</label>
                                    <input name="donatario[nome]" value="{{ old('donatario.nome', $contratado['nome'] ?? '') }}" class="w-full border-0 bg-transparent focus:ring-0 p-0"></div>
                                <div class="grid grid-cols-2 divide-x divide-black">
                                    <div class="field-wrapper px-2 py-1.5"><label class="font-bold">ESTADO CIVIL:</label><input name="donatario[estado_civil]" value="{{ old('donatario.estado_civil', $contratado['estado_civil'] ?? '') }}" class="w-full border-0 bg-transparent focus:ring-0 p-0"></div>
                                    <div class="field-wrapper px-2 py-1.5"><label class="font-bold">PROFISSÃO:</label><input name="donatario[profissao]" value="{{ old('donatario.profissao', $contratado['profissao'] ?? '') }}" class="w-full border-0 bg-transparent focus:ring-0 p-0"></div>
                                </div>
                                <div class="grid grid-cols-2 divide-x divide-black">
                                    <div class="field-wrapper px-2 py-1.5"><label class="font-bold">CPF:</label><input name="donatario[cpf]" value="{{ old('donatario.cpf', $contratado['cpf'] ?? '') }}" class="w-full border-0 bg-transparent focus:ring-0 p-0" data-mask="cpf"></div>
                                    <div class="field-wrapper px-2 py-1.5"><label class="font-bold">IDENTIDADE:</label><input name="donatario[identidade]" value="{{ old('donatario.identidade', $contratado['identidade'] ?? '') }}" class="w-full border-0 bg-transparent focus:ring-0 p-0"></div>
                                </div>
                                <div class="grid grid-cols-2 divide-x divide-black">
                                    <div class="field-wrapper px-2 py-1.5"><label class="font-bold">NOME DO CÔNJUGE:</label><input name="donatario[conjuge_nome]" value="{{ old('donatario.conjuge_nome', $contratado['conjuge_nome'] ?? '') }}" class="w-full border-0 bg-transparent focus:ring-0 p-0"></div>
                                    <div class="field-wrapper px-2 py-1.5"><label class="font-bold">IDENTIDADE DO CÔNJUGE:</label><input name="donatario[conjuge_identidade]" value="{{ old('donatario.conjuge_identidade', $contratado['conjuge_identidade'] ?? '') }}" class="w-full border-0 bg-transparent focus:ring-0 p-0"></div>
                                </div>
                            </div>

                            <div data-donatario-fields="pj" @if ($tipoDonatario !== 'pj') hidden @endif>
                                <div class="field-wrapper px-2 py-1.5"><label class="font-bold">RAZÃO SOCIAL:</label><input name="donatario[razao_social]" value="{{ old('donatario.razao_social', $contratado['razao_social'] ?? '') }}" class="w-full border-0 bg-transparent focus:ring-0 p-0"></div>
                                <div class="grid grid-cols-2 divide-x divide-black">
                                    <div class="field-wrapper px-2 py-1.5"><label class="font-bold">NOME FANTASIA:</label><input name="donatario[nome_fantasia]" value="{{ old('donatario.nome_fantasia', $contratado['nome_fantasia'] ?? '') }}" class="w-full border-0 bg-transparent focus:ring-0 p-0"></div>
                                    <div class="field-wrapper px-2 py-1.5"><label class="font-bold">INSCRIÇÃO ESTADUAL:</label><input name="donatario[inscricao_estadual]" value="{{ old('donatario.inscricao_estadual', $contratado['inscricao_estadual'] ?? '') }}" class="w-full border-0 bg-transparent focus:ring-0 p-0"></div>
                                </div>
                                <div class="field-wrapper px-2 py-1.5"><label class="font-bold">CNPJ:</label><input name="donatario[cnpj]" value="{{ old('donatario.cnpj', $contratado['cnpj'] ?? '') }}" class="w-full border-0 bg-transparent focus:ring-0 p-0" data-mask="cnpj"></div>
                                <div class="field-wrapper px-2 py-1.5"><label class="font-bold">REPRESENTANTE LEGAL:</label><input name="donatario[representante_legal_nome]" value="{{ old('donatario.representante_legal_nome', $contratado['representante_legal_nome'] ?? '') }}" class="w-full border-0 bg-transparent focus:ring-0 p-0"></div>
                                <div class="grid grid-cols-2 divide-x divide-black">
                                    <div class="field-wrapper px-2 py-1.5"><label class="font-bold">CARGO:</label><input name="donatario[representante_legal_cargo]" value="{{ old('donatario.representante_legal_cargo', $contratado['representante_legal_cargo'] ?? '') }}" class="w-full border-0 bg-transparent focus:ring-0 p-0"></div>
                                    <div class="field-wrapper px-2 py-1.5"><label class="font-bold">CPF DO REPRESENTANTE:</label><input name="donatario[representante_legal_cpf]" value="{{ old('donatario.representante_legal_cpf', $contratado['representante_legal_cpf'] ?? '') }}" class="w-full border-0 bg-transparent focus:ring-0 p-0" data-mask="cpf"></div>
                                </div>
                            </div>

                            <div class="field-wrapper px-2 py-1.5"><label class="font-bold">ENDEREÇO:</label><input name="donatario[endereco]" value="{{ old('donatario.endereco', $contratado['endereco'] ?? '') }}" class="w-full border-0 bg-transparent focus:ring-0 p-0"></div>
                            <div class="field-wrapper px-2 py-1.5"><label class="font-bold">TELEFONES:</label><input name="donatario[telefones]" value="{{ old('donatario.telefones', $contratado['telefones'] ?? '') }}" class="w-full border-0 bg-transparent focus:ring-0 p-0"></div>
                        </div>
                    </div>
                @else
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
                                    <input name="contratado[cpf_cnpj]" value="{{ $contratado['cpf_cnpj'] ?? '28851441000190' }}"
                                           class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                                </div>
                            </div>
                            <div class="field-wrapper px-2 py-1.5">
                                <div class="flex items-baseline gap-1">
                                    <label class="font-bold whitespace-nowrap">CRECI - 15ª REGIÃO Nº:</label>
                                    <input name="contratado[creci]" value="{{ $contratado['creci'] ?? '11564' }}"
                                           class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                                </div>
                            </div>
                        </div>

                        {{-- linha 3: ENDEREÇO --}}
                        <div class="field-wrapper px-2 py-1.5">
                            <div class="flex items-baseline gap-1">
                                <label class="font-bold whitespace-nowrap">ENDEREÇO:</label>
                                <input name="contratado[endereco]" value="{{ $contratado['endereco'] ?? 'Rua Cônego Agostinho, 1597 - Centro, Russas - CE, 62900-000, Brasil' }}"
                                       class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                            </div>
                        </div>

                        {{-- linha 4: TELEFONES --}}
                        <div class="field-wrapper px-2 py-1.5">
                            <div class="flex items-baseline gap-1">
                                <label class="font-bold whitespace-nowrap">TELEFONES:</label>
                                <input name="contratado[telefones]" value="{{ $contratado['telefones'] ?? '8834110124' }}"
                                       class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                            </div>
                        </div>
                    </div>
                </div>
                @endif

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

                {{-- CLÁUSULAS + ASSINATURAS (caixa única, sem os rótulos IV/V) --}}
                <div class="border border-black text-sm p-4 space-y-6">
                    <p>
                        @if ($tipoContratoKey === 'doacao')
                            Pelo presente instrumento, DOADOR e DONATÁRIO têm justo e contratado a presente doação, que será regida pelas cláusulas e condições a seguir:
                        @else
                        Pelo presente instrumento particular, CONTRATANTE e CONTRATADO, têm justo e contratado,
                        a presente contratação, que será regida pelas cláusulas e condições a seguir:
                        @endif
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
                            <p class="font-bold">{{ $tipoContratoKey === 'doacao' ? 'DOADOR' : 'CONTRATANTE' }}</p>
                        </div>
                        <div class="border-t border-black pt-1">
                            <p class="font-bold" data-assinatura-segundo>{{ $isPj ? 'REPRESENTANTE LEGAL' : 'CÔNJUGE' }}</p>
                        </div>
                        <div class="border-t border-black pt-1">
                            <p class="font-bold">{{ $tipoContratoKey === 'doacao' ? 'DONATÁRIO' : 'CONTRATADO' }}</p>
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

                {{-- Botões: centralizados, um sobre o outro (principal em cima, cancelar embaixo) --}}
                <div class="flex flex-col items-center gap-3 pt-4">
                    <button type="submit" id="submit-btn" {{ !$templateAvailable ? 'disabled' : '' }}
                            class="w-full max-w-sm bg-green-600 hover:bg-green-700 text-white shadow-sm rounded-lg px-8 py-3.5 text-base font-bold transition disabled:opacity-50 disabled:cursor-not-allowed">
                        {{ $group ? 'Salvar nova versão' : 'Gerar contrato' }}
                    </button>

                    <a href="{{ request('from') === 'versions' ? route('contracts.versions', $group) : route('contracts.index') }}"
                       class="w-full max-w-sm text-center bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 shadow-sm rounded-lg px-8 py-3.5 text-base font-bold transition">
                        Cancelar ou voltar
                    </a>
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
            clausulasContent: @json($clausulasInicial),
        };
    </script>
</x-app-layout>
