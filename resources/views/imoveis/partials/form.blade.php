{{--
    resources/views/imoveis/partials/form.blade.php

    ATENÇÃO: este partial NÃO tem <form>, @csrf nem botão de submit.
    Quem inclui (create.blade.php / edit.blade.php) é que abre o <form>.
--}}
@php
    use App\Models\Client;
    use App\Models\Imovel;

    $imovel = $imovel ?? null;
    $papeis = Imovel::PAPEIS;

    // Linhas iniciais: old() tem prioridade; senão, os vínculos já salvos no imóvel.
    $iniciais = [];

    if (old('pessoas') !== null) {
        $enviadas = collect(old('pessoas'));
        $clientes = Client::whereIn('id', $enviadas->pluck('id')->filter())->get()->keyBy('id');

        foreach ($enviadas as $p) {
            $c = $clientes[$p['id'] ?? null] ?? null;
            if (!$c) continue;
            $iniciais[] = [
                'id'        => $c->id,
                'tipo'      => $c->tipo,
                'nome'      => $c->nome,
                'documento' => $c->documento_formatado,
                'papeis'    => array_values($p['papeis'] ?? []),
            ];
        }
    } elseif ($imovel) {
        foreach ($imovel->clients->groupBy('id') as $grupo) {
            $c = $grupo->first();
            $iniciais[] = [
                'id'        => $c->id,
                'tipo'      => $c->tipo,
                'nome'      => $c->nome,
                'documento' => $c->documento_formatado,
                'papeis'    => $grupo->pluck('pivot.papel')->values()->all(),
            ];
        }
    }

    // Helper de valor: old() > valor do imóvel > padrão
    $v = fn (string $campo, $padrao = '') => old($campo, data_get($imovel, $campo, $padrao));

    $classeInput = 'mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500';
    $classeLabel = 'block text-sm font-medium text-gray-700';

    $onus = [
        'onus_hipoteca'              => 'Hipoteca',
        'onus_alienacao_fiduciaria'  => 'Alienação fiduciária',
        'onus_penhora'               => 'Penhora',
        'onus_usufruto'              => 'Usufruto',
        'onus_indisponibilidade'     => 'Indisponibilidade',
        'onus_servidao'              => 'Servidão',
        'onus_promessa_compra_venda' => 'Promessa de compra e venda',
        'existe_acoes'               => 'Existem ações judiciais',
    ];

    $docs = [
        'doc_certidao_onus_reais'               => 'Certidão de ônus reais',
        'doc_certidao_acoes_reais'              => 'Certidão de ações reais',
        'doc_certidao_negativa_debitos'         => 'Certidão negativa de débitos',
        'doc_comprovante_inscricao_imobiliaria' => 'Comprovante de inscrição imobiliária',
        'doc_certidao_quitacao_condominial'     => 'Certidão de quitação condominial',
        'doc_convencao_condominio'              => 'Convenção de condomínio',
        'doc_planta_aprovacao'                  => 'Planta aprovada',
        'doc_habite_se'                         => 'Habite-se',
        'doc_ambiental'                         => 'Documentação ambiental',
        'doc_rural'                             => 'Documentação rural',
        'doc_ccir'                              => 'CCIR',
        'doc_car'                               => 'CAR',
        'doc_itr'                               => 'ITR',
    ];
@endphp

{{-- Erros de validação (todos) --}}
@if ($errors->any())
    <div class="mb-6 p-3 bg-red-100 text-red-800 rounded text-sm">
        <p class="font-semibold mb-1">Corrija os campos abaixo:</p>
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $m) <li>{{ $m }}</li> @endforeach
        </ul>
    </div>
@endif

{{-- ===================== PESSOAS VINCULADAS ===================== --}}
<fieldset class="mb-6">
    <legend class="text-lg font-semibold text-gray-800 mb-3">Pessoas vinculadas</legend>

    <div class="relative mb-3">
        <input type="search" id="busca-pessoa" autocomplete="off"
               placeholder="Buscar cliente por nome, CPF ou CNPJ..."
               class="w-full rounded-full border-gray-300 text-sm px-5 py-2.5 focus:border-blue-500 focus:ring-blue-500">
        <div id="resultados"
             class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-64 overflow-y-auto"></div>
    </div>

    <div id="lista-pessoas" class="space-y-2"></div>
    <p id="vazio" class="hidden text-sm text-gray-400">Nenhuma pessoa vinculada ainda.</p>
    <p class="mt-2 text-xs text-gray-400">Marque ao menos um papel por pessoa; sem papel, o vínculo não é salvo.</p>

    {{-- Os inputs hidden (pessoas[i][...]) são gerados pelo JS aqui --}}
    <div id="hidden-inputs"></div>
</fieldset>

{{-- ===================== FOTOS ===================== --}}
{{-- ===================== FOTOS ===================== --}}
{{-- Substitua o <fieldset class="mb-8"> inteiro de "Fotos" em imoveis/partials/form.blade.php por este --}}
<fieldset class="mb-8">
    <legend class="text-lg font-semibold text-gray-800 mb-3">Fotos</legend>

    @if ($imovel && $imovel->imagens->isNotEmpty())
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
            @foreach ($imovel->imagens as $img)
                <label class="block border border-gray-200 rounded-lg p-2 text-xs text-gray-600">
                    <img src="{{ asset('storage/' . $img->caminho) }}" alt="Foto do imóvel"
                         class="w-full h-28 object-cover rounded mb-2">
                    <span class="inline-flex items-center gap-2">
                        <input type="checkbox" name="remover_imagens[]" value="{{ $img->id }}"
                               class="rounded border-gray-300 text-rose-600">
                        Remover{{ $img->principal ? ' (capa)' : '' }}
                    </span>
                </label>
            @endforeach
        </div>
    @endif

    <label for="imagens" id="dropzone"
           class="flex flex-col items-center justify-center w-full h-52 border-2 border-dashed border-gray-300 rounded-xl bg-gray-50 hover:bg-blue-50 hover:border-blue-400 cursor-pointer transition text-center">
        <svg class="w-12 h-12 text-gray-400 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none"
             viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
        </svg>
        <p class="text-sm text-gray-600"><span class="font-semibold text-blue-600">Clique para enviar</span> ou arraste as fotos aqui</p>
        <p class="mt-1 text-xs text-gray-400">Até 10 imagens (jpg, png ou webp), 4 MB cada. A primeira vira a capa.</p>
        <input type="file" name="imagens[]" id="imagens" multiple
               accept="image/jpeg,image/png,image/webp" class="hidden">
    </label>

    <p id="aviso-imagens" class="hidden mt-2 text-sm text-rose-600"></p>

    <div id="previews" class="grid grid-cols-2 md:grid-cols-5 gap-3 mt-3"></div>

    <script>
    (function () {
        const MAX    = 10;
        const MAX_MB = 4;
        const TIPOS  = ['image/jpeg', 'image/png', 'image/webp'];

        const zona  = document.getElementById('dropzone');
        const input = document.getElementById('imagens');
        const prev  = document.getElementById('previews');
        const aviso = document.getElementById('aviso-imagens');

        // Guarda todas as fotos escolhidas, em várias rodadas
        const dt = new DataTransfer();

        const chave = f => f.name + '|' + f.size + '|' + f.lastModified;

        function adicionar(arquivos) {
            const erros = [];
            let estourouLimite = false;

            Array.from(arquivos).forEach(f => {
                if (!TIPOS.includes(f.type)) {
                    erros.push(f.name + ': formato não permitido.');
                    return;
                }
                if (f.size > MAX_MB * 1024 * 1024) {
                    erros.push(f.name + ': maior que ' + MAX_MB + ' MB.');
                    return;
                }
                if (Array.from(dt.files).some(x => chave(x) === chave(f))) {
                    return; // já adicionada
                }
                if (dt.items.length >= MAX) {
                    estourouLimite = true;
                    return;
                }
                dt.items.add(f);
            });

            if (estourouLimite) erros.push('Limite de ' + MAX + ' imagens por envio.');

            input.files = dt.files;
            mostrarAviso(erros);
            render();
        }

        function remover(i) {
            dt.items.remove(i);
            input.files = dt.files;
            mostrarAviso([]);
            render();
        }

        function mostrarAviso(erros) {
            aviso.textContent = erros.join(' ');
            aviso.classList.toggle('hidden', erros.length === 0);
        }

        function render() {
            prev.replaceChildren();

            Array.from(dt.files).forEach((f, i) => {
                const card = document.createElement('div');
                card.className = 'relative';

                const img = document.createElement('img');
                img.src = URL.createObjectURL(f);
                img.alt = f.name;
                img.className = 'w-full h-24 object-cover rounded-lg border border-gray-200';
                img.onload = () => URL.revokeObjectURL(img.src);

                const x = document.createElement('button');
                x.type = 'button';
                x.title = 'Remover';
                x.setAttribute('aria-label', 'Remover foto');
                x.textContent = '×';
                x.className = 'absolute top-1 right-1 w-6 h-6 leading-none rounded-full bg-black/60 text-white text-sm hover:bg-rose-600 transition';
                x.onclick = () => remover(i);

                card.append(img, x);

                if (i === 0) {
                    const capa = document.createElement('span');
                    capa.textContent = 'Capa';
                    capa.className = 'absolute bottom-1 left-1 px-2 py-0.5 rounded-full bg-blue-600 text-white text-xs font-medium';
                    card.appendChild(capa);
                }

                prev.appendChild(card);
            });
        }

        // Clique: acumula em vez de substituir
        input.addEventListener('change', () => adicionar(Array.from(input.files)));

        // Arrastar e soltar
        ['dragenter', 'dragover'].forEach(ev => zona.addEventListener(ev, e => {
            e.preventDefault();
            zona.classList.add('bg-blue-50', 'border-blue-400');
        }));
        ['dragleave', 'drop'].forEach(ev => zona.addEventListener(ev, e => {
            e.preventDefault();
            zona.classList.remove('bg-blue-50', 'border-blue-400');
        }));
        zona.addEventListener('drop', e => adicionar(Array.from(e.dataTransfer.files)));
    })();
    </script>
</fieldset>

{{-- ===================== DADOS DO IMÓVEL ===================== --}}
<fieldset class="mb-8">
    <legend class="text-lg font-semibold text-gray-800 mb-3">Dados do imóvel</legend>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label for="tipo" class="{{ $classeLabel }}">Tipo *</label>
            <select name="tipo" id="tipo" class="{{ $classeInput }}" required>
                <option value="">Selecione...</option>
                @foreach (Imovel::TIPOS as $valor => $rotulo)
                    <option value="{{ $valor }}" @selected($v('tipo') === $valor)>{{ $rotulo }}</option>
                @endforeach
            </select>
        </div>
    </div>
</fieldset>

{{-- ===================== ENDEREÇO ===================== --}}
<fieldset class="mb-8">
    <legend class="text-lg font-semibold text-gray-800 mb-3">Endereço</legend>

    <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
        <div class="md:col-span-4">
            <label for="logradouro" class="{{ $classeLabel }}">Logradouro *</label>
            <input type="text" name="logradouro" id="logradouro" value="{{ $v('logradouro') }}"
                   class="{{ $classeInput }}" required>
        </div>
        <div class="md:col-span-1">
            <label for="numero" class="{{ $classeLabel }}">Número</label>
            <input type="text" name="numero" id="numero" value="{{ $v('numero') }}" class="{{ $classeInput }}">
        </div>
        <div class="md:col-span-1">
            <label for="cep" class="{{ $classeLabel }}">CEP</label>
            <input type="text" name="cep" id="cep" maxlength="9" value="{{ $v('cep') }}" class="{{ $classeInput }}">
        </div>

        <div class="md:col-span-2">
            <label for="complemento" class="{{ $classeLabel }}">Complemento</label>
            <input type="text" name="complemento" id="complemento" value="{{ $v('complemento') }}" class="{{ $classeInput }}">
        </div>
        <div class="md:col-span-2">
            <label for="bairro" class="{{ $classeLabel }}">Bairro *</label>
            <input type="text" name="bairro" id="bairro" value="{{ $v('bairro') }}" class="{{ $classeInput }}" required>
        </div>
        <div class="md:col-span-1">
            <label for="municipio" class="{{ $classeLabel }}">Município *</label>
            <input type="text" name="municipio" id="municipio" value="{{ $v('municipio') }}" class="{{ $classeInput }}" required>
        </div>
        <div class="md:col-span-1">
            <label for="estado" class="{{ $classeLabel }}">UF *</label>
            <input type="text" name="estado" id="estado" maxlength="2" value="{{ $v('estado') }}"
                   class="{{ $classeInput }} uppercase" required>
        </div>
    </div>
</fieldset>

{{-- ===================== ÁREAS ===================== --}}
<fieldset class="mb-8">
    <legend class="text-lg font-semibold text-gray-800 mb-3">Áreas (m²)</legend>

    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        @foreach ([
            'area_terreno'    => 'Terreno',
            'area_construida' => 'Construída',
            'area_privativa'  => 'Privativa',
            'area_comum'      => 'Comum',
            'area_total'      => 'Total',
        ] as $campo => $rotulo)
            <div>
                <label for="{{ $campo }}" class="{{ $classeLabel }}">{{ $rotulo }}</label>
                <input type="number" step="0.01" min="0" name="{{ $campo }}" id="{{ $campo }}"
                       value="{{ $v($campo) }}" class="{{ $classeInput }}">
            </div>
        @endforeach
    </div>
</fieldset>

{{-- ===================== REGISTRO / IDENTIFICAÇÃO ===================== --}}
<fieldset class="mb-8">
    <legend class="text-lg font-semibold text-gray-800 mb-3">Registro e identificação</legend>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach ([
            'numero_matricula'      => 'Nº da matrícula',
            'cartorio_registro'     => 'Cartório de registro',
            'inscricao_imobiliaria' => 'Inscrição imobiliária',
            'cadastro_municipal'    => 'Cadastro municipal',
            'numero_lote'           => 'Lote',
            'numero_quadra'         => 'Quadra',
            'condominio_nome'       => 'Condomínio',
            'bloco'                 => 'Bloco',
            'andar'                 => 'Andar',
            'numero_unidade'        => 'Unidade',
            'fracao_ideal'          => 'Fração ideal',
        ] as $campo => $rotulo)
            <div>
                <label for="{{ $campo }}" class="{{ $classeLabel }}">{{ $rotulo }}</label>
                <input type="text" name="{{ $campo }}" id="{{ $campo }}" value="{{ $v($campo) }}" class="{{ $classeInput }}">
            </div>
        @endforeach

        <div>
            <label for="vagas_garagem" class="{{ $classeLabel }}">Vagas de garagem</label>
            <input type="number" min="0" name="vagas_garagem" id="vagas_garagem"
                   value="{{ $v('vagas_garagem') }}" class="{{ $classeInput }}">
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        <div>
            <label for="confrontacoes" class="{{ $classeLabel }}">Confrontações</label>
            <textarea name="confrontacoes" id="confrontacoes" rows="3" class="{{ $classeInput }}">{{ $v('confrontacoes') }}</textarea>
        </div>
        <div>
            <label for="caracteristicas_adicionais" class="{{ $classeLabel }}">Características adicionais</label>
            <textarea name="caracteristicas_adicionais" id="caracteristicas_adicionais" rows="3" class="{{ $classeInput }}">{{ $v('caracteristicas_adicionais') }}</textarea>
        </div>
    </div>
</fieldset>

{{-- ===================== SITUAÇÃO JURÍDICA ===================== --}}
<fieldset class="mb-8">
    <legend class="text-lg font-semibold text-gray-800 mb-3">Situação jurídica</legend>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label for="proprietario_nome" class="{{ $classeLabel }}">Proprietário (conforme matrícula)</label>
            <input type="text" name="proprietario_nome" id="proprietario_nome"
                   value="{{ $v('proprietario_nome') }}" class="{{ $classeInput }}">
        </div>
        <div>
            <label for="matricula_atualizada_em" class="{{ $classeLabel }}">Matrícula atualizada em</label>
            @php
                $dataMat = $v('matricula_atualizada_em');
                if ($dataMat instanceof \Carbon\CarbonInterface) $dataMat = $dataMat->format('Y-m-d');
            @endphp
            <input type="date" name="matricula_atualizada_em" id="matricula_atualizada_em"
                   value="{{ $dataMat }}" class="{{ $classeInput }}">
        </div>
        <div>
            <label for="outros_direitos_reais" class="{{ $classeLabel }}">Outros direitos reais</label>
            <input type="text" name="outros_direitos_reais" id="outros_direitos_reais"
                   value="{{ $v('outros_direitos_reais') }}" class="{{ $classeInput }}">
        </div>
        <div>
            <label for="situacao_condominio" class="{{ $classeLabel }}">Situação do condomínio</label>
            <input type="text" name="situacao_condominio" id="situacao_condominio"
                   value="{{ $v('situacao_condominio') }}" class="{{ $classeInput }}">
        </div>
        <div>
            <label for="situacao_tributaria" class="{{ $classeLabel }}">Situação tributária</label>
            <input type="text" name="situacao_tributaria" id="situacao_tributaria"
                   value="{{ $v('situacao_tributaria') }}" class="{{ $classeInput }}">
        </div>
    </div>

    <p class="mt-4 text-sm font-medium text-gray-700">Ônus e ações</p>
    <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
        @foreach ($onus as $campo => $rotulo)
            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                {{-- hidden 0 garante que desmarcar também é enviado (importante no update) --}}
                <input type="hidden" name="{{ $campo }}" value="0">
                <input type="checkbox" name="{{ $campo }}" value="1"
                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                       @checked((bool) $v($campo, false))>
                {{ $rotulo }}
            </label>
        @endforeach
    </div>

    <div class="mt-4">
        <label for="descricao_acoes" class="{{ $classeLabel }}">Descrição das ações</label>
        <textarea name="descricao_acoes" id="descricao_acoes" rows="3" class="{{ $classeInput }}">{{ $v('descricao_acoes') }}</textarea>
    </div>
</fieldset>

{{-- ===================== DOCUMENTAÇÃO ===================== --}}
<fieldset class="mb-8">
    <legend class="text-lg font-semibold text-gray-800 mb-3">Documentação disponível</legend>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
        @foreach ($docs as $campo => $rotulo)
            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                <input type="hidden" name="{{ $campo }}" value="0">
                <input type="checkbox" name="{{ $campo }}" value="1"
                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                       @checked((bool) $v($campo, false))>
                {{ $rotulo }}
            </label>
        @endforeach
    </div>
</fieldset>

<script>
(function () {
    const URL_BUSCA = @json(route('imoveis.clientes.busca')); // espera ?q= e retorna [{id, tipo, nome, documento}]
    const PAPEIS    = @json($papeis);
    const INICIAIS  = @json($iniciais);

    const $busca = document.getElementById('busca-pessoa');
    const $res   = document.getElementById('resultados');
    const $lista = document.getElementById('lista-pessoas');
    const $vazio = document.getElementById('vazio');
    const $hid   = document.getElementById('hidden-inputs');

    // id do cliente => { id, tipo, nome, documento, papeis:Set }
    const pessoas = new Map();

    function adicionar(c, papeis) {
        const id = Number(c.id);
        if (!pessoas.has(id)) {
            pessoas.set(id, { id, tipo: c.tipo, nome: c.nome, documento: c.documento || '', papeis: new Set() });
        }
        (papeis || []).forEach(p => pessoas.get(id).papeis.add(p));
    }

    INICIAIS.forEach(r => adicionar(r, r.papeis));

    function render() {
        $lista.replaceChildren();
        $vazio.classList.toggle('hidden', pessoas.size > 0);

        pessoas.forEach((p, id) => {
            const card = document.createElement('div');
            card.className = 'border border-gray-200 rounded-lg p-3 bg-white';

            const topo = document.createElement('div');
            topo.className = 'flex items-center justify-between mb-2';

            const nome = document.createElement('div');
            nome.className = 'font-semibold text-sm text-gray-900';
            nome.textContent = p.nome;

            const badge = document.createElement('span');
            const pj = p.tipo === 'pj';
            badge.className = 'ml-2 inline-block px-2 py-0.5 text-xs font-bold rounded-full '
                + (pj ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700');
            badge.textContent = pj ? 'PJ' : 'PF';
            nome.appendChild(badge);

            if (p.documento) {
                const doc = document.createElement('span');
                doc.className = 'ml-2 font-normal text-xs text-gray-500';
                doc.textContent = p.documento;
                nome.appendChild(doc);
            }

            const rm = document.createElement('button');
            rm.type = 'button';
            rm.className = 'text-xs text-rose-600 bg-rose-50 hover:bg-rose-100 px-3 py-1 rounded-lg transition';
            rm.textContent = 'Remover';
            rm.onclick = () => { pessoas.delete(id); render(); };

            topo.append(nome, rm);

            const lista = document.createElement('div');
            lista.className = 'flex flex-wrap gap-4';
            Object.entries(PAPEIS).forEach(([papel, rotulo]) => {
                const wrap = document.createElement('label');
                wrap.className = 'inline-flex items-center gap-2 text-sm text-gray-700';
                const cb = document.createElement('input');
                cb.type = 'checkbox';
                cb.className = 'rounded border-gray-300 text-blue-600 focus:ring-blue-500';
                cb.checked = p.papeis.has(papel);
                cb.onchange = () => { cb.checked ? p.papeis.add(papel) : p.papeis.delete(papel); sincronizarHidden(); };
                wrap.append(cb, document.createTextNode(rotulo));
                lista.appendChild(wrap);
            });

            card.append(topo, lista);
            $lista.appendChild(card);
        });
        sincronizarHidden();
    }

    // pessoas[n][id] e pessoas[n][papeis][] para cada cliente listado.
    function sincronizarHidden() {
        $hid.replaceChildren();
        let n = 0;
        pessoas.forEach(p => {
            const add = (nome, valor) => {
                const inp = document.createElement('input');
                inp.type = 'hidden'; inp.name = nome; inp.value = valor;
                $hid.appendChild(inp);
            };
            add(`pessoas[${n}][id]`, p.id);
            p.papeis.forEach(papel => add(`pessoas[${n}][papeis][]`, papel));
            n++;
        });
    }

    // ---- Busca com debounce ----
    let timer, controller;
    $busca.addEventListener('input', () => {
        clearTimeout(timer);
        const q = $busca.value.trim();
        if (q.length < 2) return fecharResultados();
        timer = setTimeout(() => buscar(q), 300);
    });
    // Enter na busca não pode enviar o formulário
    $busca.addEventListener('keydown', e => { if (e.key === 'Enter') e.preventDefault(); });

    document.addEventListener('click', e => {
        if (!e.target.closest('#busca-pessoa, #resultados')) fecharResultados();
    });

    async function buscar(q) {
        controller?.abort();
        controller = new AbortController();
        try {
            const r = await fetch(URL_BUSCA + '?q=' + encodeURIComponent(q), {
                headers: { 'Accept': 'application/json' }, signal: controller.signal,
            });
            if (!r.ok) throw new Error(r.status);
            mostrarResultados(await r.json());
        } catch (e) {
            if (e.name !== 'AbortError') mostrarMensagem('Erro ao buscar. Tente novamente.');
        }
    }

    function mostrarMensagem(texto) {
        $res.replaceChildren();
        const d = document.createElement('div');
        d.className = 'px-4 py-2 text-sm text-gray-500';
        d.textContent = texto;
        $res.appendChild(d);
        $res.classList.remove('hidden');
    }

    function mostrarResultados(itens) {
        if (!itens.length) return mostrarMensagem('Nenhum resultado.');
        $res.replaceChildren();
        itens.forEach(it => {
            const b = document.createElement('button');
            b.type = 'button';
            b.className = 'block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-blue-50';
            b.textContent = it.nome + (it.documento ? ' — ' + it.documento : '') + (it.tipo === 'pj' ? ' (PJ)' : ' (PF)');
            b.onclick = () => {
                adicionar(it, []);
                $busca.value = '';
                fecharResultados();
                render();
            };
            $res.appendChild(b);
        });
        $res.classList.remove('hidden');
    }

    function fecharResultados() { $res.classList.add('hidden'); $res.replaceChildren(); }

    render();
})();
</script>
