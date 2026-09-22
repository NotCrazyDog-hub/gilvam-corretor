@php
    $imovel = $imovel ?? null;
    $old = fn ($campo, $default = null) => old($campo, $imovel?->$campo ?? $default);
    $checked = fn ($campo) => old($campo, $imovel?->$campo ?? false) ? 'checked' : '';
@endphp

<div class="space-y-8">

    {{-- Tipo e Endereço --}}
    <section>
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Tipo e Endereço</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo do imóvel *</label>
                <select name="tipo" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="">Selecione...</option>
                    @foreach ($tipos as $valor => $label)
                        <option value="{{ $valor }}" @selected($old('tipo') === $valor)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('tipo') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Rua/Avenida *</label>
                <input type="text" name="logradouro" value="{{ $old('logradouro') }}" required
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                @error('logradouro') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Número</label>
                <input type="text" name="numero" value="{{ $old('numero') }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Complemento</label>
                <input type="text" name="complemento" value="{{ $old('complemento') }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bairro *</label>
                <input type="text" name="bairro" value="{{ $old('bairro') }}" required
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                @error('bairro') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">CEP</label>
                <input type="text" name="cep" value="{{ $old('cep') }}" placeholder="00000-000"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Município *</label>
                <input type="text" name="municipio" value="{{ $old('municipio') }}" required
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                @error('municipio') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado (UF) *</label>
                <input type="text" name="estado" value="{{ $old('estado') }}" required maxlength="2"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm uppercase">
                @error('estado') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    </section>

    {{-- Áreas --}}
    <section>
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Áreas (m²)</h3>
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Terreno</label>
                <input type="number" step="0.01" name="area_terreno" value="{{ $old('area_terreno') }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Construída</label>
                <input type="number" step="0.01" name="area_construida" value="{{ $old('area_construida') }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Privativa</label>
                <input type="number" step="0.01" name="area_privativa" value="{{ $old('area_privativa') }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Comum</label>
                <input type="number" step="0.01" name="area_comum" value="{{ $old('area_comum') }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Total</label>
                <input type="number" step="0.01" name="area_total" value="{{ $old('area_total') }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
        </div>
    </section>

    {{-- Registro e identificação --}}
    <section>
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Registro e Identificação</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nº da matrícula</label>
                <input type="text" name="numero_matricula" value="{{ $old('numero_matricula') }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Cartório de Registro de Imóveis</label>
                <input type="text" name="cartorio_registro" value="{{ $old('cartorio_registro') }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Inscrição imobiliária / IPTU</label>
                <input type="text" name="inscricao_imobiliaria" value="{{ $old('inscricao_imobiliaria') }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cadastro municipal</label>
                <input type="text" name="cadastro_municipal" value="{{ $old('cadastro_municipal') }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nº do lote</label>
                <input type="text" name="numero_lote" value="{{ $old('numero_lote') }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nº da quadra</label>
                <input type="text" name="numero_quadra" value="{{ $old('numero_quadra') }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Condomínio (se houver)</label>
                <input type="text" name="condominio_nome" value="{{ $old('condominio_nome') }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bloco</label>
                <input type="text" name="bloco" value="{{ $old('bloco') }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Andar</label>
                <input type="text" name="andar" value="{{ $old('andar') }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nº da unidade</label>
                <input type="text" name="numero_unidade" value="{{ $old('numero_unidade') }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Vaga(s) de garagem</label>
                <input type="number" min="0" name="vagas_garagem" value="{{ $old('vagas_garagem') }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fração ideal</label>
                <input type="text" name="fracao_ideal" value="{{ $old('fracao_ideal') }}" placeholder="ex: 1/20"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div class="sm:col-span-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Confrontações</label>
                <textarea name="confrontacoes" rows="2"
                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ $old('confrontacoes') }}</textarea>
            </div>
            <div class="sm:col-span-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Características adicionais</label>
                <textarea name="caracteristicas_adicionais" rows="2"
                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ $old('caracteristicas_adicionais') }}</textarea>
            </div>
        </div>
    </section>

    {{-- Situação jurídica --}}
    <section>
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Situação Jurídica</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Proprietário</label>
                <input type="text" name="proprietario_nome" value="{{ $old('proprietario_nome') }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Matrícula atualizada em</label>
                <input type="date" name="matricula_atualizada_em" value="{{ $old('matricula_atualizada_em') }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Situação tributária</label>
                <input type="text" name="situacao_tributaria" value="{{ $old('situacao_tributaria') }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div class="sm:col-span-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Situação perante o condomínio</label>
                <input type="text" name="situacao_condominio" value="{{ $old('situacao_condominio') }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
        </div>

        <p class="text-sm font-medium text-gray-700 mb-2">Ônus e direitos reais existentes</p>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-4">
            @foreach ([
                'onus_hipoteca' => 'Hipoteca',
                'onus_alienacao_fiduciaria' => 'Alienação fiduciária',
                'onus_penhora' => 'Penhora',
                'onus_usufruto' => 'Usufruto',
                'onus_indisponibilidade' => 'Indisponibilidade',
                'onus_servidao' => 'Servidão',
                'onus_promessa_compra_venda' => 'Promessa de compra e venda registrada',
            ] as $campo => $label)
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="hidden" name="{{ $campo }}" value="0">
                    <input type="checkbox" name="{{ $campo }}" value="1" {{ $checked($campo) }}
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    {{ $label }}
                </label>
            @endforeach
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Outros direitos reais</label>
                <input type="text" name="outros_direitos_reais" value="{{ $old('outros_direitos_reais') }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label class="flex items-center gap-2 text-sm text-gray-700 mt-6">
                    <input type="hidden" name="existe_acoes" value="0">
                    <input type="checkbox" name="existe_acoes" value="1" {{ $checked('existe_acoes') }}
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    Existem ações que possam atingir o imóvel
                </label>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Descrição das ações (se houver)</label>
                <textarea name="descricao_acoes" rows="2"
                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">{{ $old('descricao_acoes') }}</textarea>
            </div>
        </div>
    </section>

    {{-- Documentação --}}
    <section>
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Documentação Disponível</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
            @foreach ([
                'doc_certidao_onus_reais' => 'Certidão de ônus reais',
                'doc_certidao_acoes_reais' => 'Certidão de ações reais/reipersecutórias',
                'doc_certidao_negativa_debitos' => 'Certidão negativa de débitos municipais/IPTU',
                'doc_comprovante_inscricao_imobiliaria' => 'Comprovante de inscrição imobiliária',
                'doc_certidao_quitacao_condominial' => 'Certidão de quitação condominial',
                'doc_convencao_condominio' => 'Convenção de condomínio',
                'doc_planta_aprovacao' => 'Planta / aprovação',
                'doc_habite_se' => 'Habite-se',
                'doc_ambiental' => 'Documentação ambiental',
                'doc_rural' => 'Documentação rural',
                'doc_ccir' => 'CCIR',
                'doc_car' => 'CAR',
                'doc_itr' => 'ITR',
            ] as $campo => $label)
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="hidden" name="{{ $campo }}" value="0">
                    <input type="checkbox" name="{{ $campo }}" value="1" {{ $checked($campo) }}
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    {{ $label }}
                </label>
            @endforeach
        </div>
    </section>

</div>
