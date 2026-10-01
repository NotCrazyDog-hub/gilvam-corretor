@php
    use App\Http\Requests\ImovelRequest;
    use App\Models\PessoaFisica;
    use App\Models\PessoaJuridica;

    $imovel  = $imovel ?? null;
    $papeis  = ImovelRequest::PAPEIS;
    $tipos   = ['fisica' => PessoaFisica::class, 'juridica' => PessoaJuridica::class];

    // Linhas iniciais: old() tem prioridade; senão, os vínculos já salvos no imóvel.
    $iniciais = old('pessoas');
    if ($iniciais === null) {
        $iniciais = [];
        if ($imovel) {
            foreach ($imovel->pessoasFisicas as $p) {
                $iniciais[] = ['pessoa_id' => $p->id, 'pessoa_type' => PessoaFisica::class,
                               'papel' => $p->pivot->papel, 'nome' => $p->nome]; // AJUSTE: campo de nome da PF
            }
            foreach ($imovel->pessoasJuridicas as $p) {
                $iniciais[] = ['pessoa_id' => $p->id, 'pessoa_type' => PessoaJuridica::class,
                               'papel' => $p->pivot->papel, 'nome' => $p->razao_social]; // AJUSTE: campo de nome da PJ
            }
        }
    }
@endphp

<form method="POST"
      action="{{ $imovel ? route('imoveis.update', $imovel) : route('imoveis.store') }}"
      id="form-imovel" novalidate>
    @csrf
    @if ($imovel) @method('PUT') @endif

    {{-- AJUSTE: campos do próprio imóvel --}}
    {{-- <div class="mb-3">
        <label class="form-label" for="endereco">Endereço</label>
        <input type="text" id="endereco" name="endereco" class="form-control @error('endereco') is-invalid @enderror"
               value="{{ old('endereco', $imovel->endereco ?? '') }}">
        @error('endereco') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div> --}}

    <fieldset class="mb-4">
        <legend class="h5">Pessoas vinculadas</legend>

        <div class="position-relative mb-3">
            <input type="search" id="busca-pessoa" class="form-control" autocomplete="off"
                   placeholder="Buscar por nome, razão social ou CNPJ...">
            <div id="resultados" class="list-group position-absolute w-100 shadow-sm d-none" style="z-index: 10; max-height: 260px; overflow-y: auto;"></div>
            <div class="form-text">A busca por CPF não está disponível (campo criptografado).</div>
        </div>

        <div id="lista-pessoas" class="vstack gap-2"></div>
        <div id="vazio" class="text-muted small d-none">Nenhuma pessoa vinculada ainda.</div>

        @if ($errors->has('pessoas') || collect($errors->keys())->contains(fn ($k) => str_starts_with($k, 'pessoas.')))
            <div class="alert alert-danger mt-3 mb-0 py-2">
                <ul class="mb-0">
                    @foreach ($errors->get('pessoas*') as $mensagens)
                        @foreach ($mensagens as $m) <li>{{ $m }}</li> @endforeach
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Os inputs hidden (pessoas[i][...]) são gerados pelo JS aqui --}}
        <div id="hidden-inputs"></div>
    </fieldset>

    <button type="submit" class="btn btn-primary">{{ $imovel ? 'Salvar alterações' : 'Cadastrar imóvel' }}</button>
</form>

<script>
(function () {
    const URL_BUSCA = @json(route('pessoas.busca')); // AJUSTE: nome da rota; espera ?q= e retorna [{id, tipo, nome, documento}]
    const TIPOS     = @json($tipos);                 // 'fisica' | 'juridica' => classe do model
    const PAPEIS    = @json($papeis);
    const INICIAIS  = @json($iniciais);

    const $busca = document.getElementById('busca-pessoa');
    const $res   = document.getElementById('resultados');
    const $lista = document.getElementById('lista-pessoas');
    const $vazio = document.getElementById('vazio');
    const $hid   = document.getElementById('hidden-inputs');

    // chave "classe|id" => { id, type, nome, papeis:Set }
    const pessoas = new Map();
    const chave = (type, id) => type + '|' + id;
    const label = p => p.replace(/_/g, ' ').replace(/^./, c => c.toUpperCase());

    function adicionar(type, id, nome, papel) {
        const k = chave(type, id);
        if (!pessoas.has(k)) pessoas.set(k, { id: Number(id), type, nome, papeis: new Set() });
        if (papel) pessoas.get(k).papeis.add(papel);
    }

    INICIAIS.forEach(r => adicionar(r.pessoa_type, r.pessoa_id, r.nome || ('Pessoa #' + r.pessoa_id), r.papel));

    function render() {
        $lista.replaceChildren();
        $hid.replaceChildren();
        $vazio.classList.toggle('d-none', pessoas.size > 0);

        let i = 0;
        pessoas.forEach((p, k) => {
            const card = document.createElement('div');
            card.className = 'border rounded p-2';

            const topo = document.createElement('div');
            topo.className = 'd-flex justify-content-between align-items-center mb-2';
            const nome = document.createElement('strong');
            nome.textContent = p.nome;
            const badge = document.createElement('span');
            badge.className = 'badge bg-secondary ms-2';
            badge.textContent = p.type === TIPOS.juridica ? 'PJ' : 'PF';
            nome.appendChild(badge);
            const rm = document.createElement('button');
            rm.type = 'button'; rm.className = 'btn btn-sm btn-outline-danger'; rm.textContent = 'Remover';
            rm.onclick = () => { pessoas.delete(k); render(); };
            topo.append(nome, rm);

            const papeis = document.createElement('div');
            papeis.className = 'd-flex flex-wrap gap-3';
            PAPEIS.forEach(papel => {
                const wrap = document.createElement('div');
                wrap.className = 'form-check';
                const cb = document.createElement('input');
                cb.type = 'checkbox'; cb.className = 'form-check-input';
                cb.id = 'p-' + i + '-' + papel;
                cb.checked = p.papeis.has(papel);
                cb.onchange = () => { cb.checked ? p.papeis.add(papel) : p.papeis.delete(papel); sincronizarHidden(); };
                const lb = document.createElement('label');
                lb.className = 'form-check-label'; lb.htmlFor = cb.id; lb.textContent = label(papel);
                wrap.append(cb, lb);
                papeis.appendChild(wrap);
            });

            card.append(topo, papeis);
            $lista.appendChild(card);
            i++;
        });
        sincronizarHidden();
    }

    // Um hidden por (pessoa, papel). Pessoa sem papel marcado não é enviada.
    function sincronizarHidden() {
        $hid.replaceChildren();
        let n = 0;
        pessoas.forEach(p => p.papeis.forEach(papel => {
            [['pessoa_id', p.id], ['pessoa_type', p.type], ['papel', papel]].forEach(([campo, valor]) => {
                const inp = document.createElement('input');
                inp.type = 'hidden'; inp.name = `pessoas[${n}][${campo}]`; inp.value = valor;
                $hid.appendChild(inp);
            });
            n++;
        }));
    }

    // ---- Busca com debounce ----
    let timer, controller;
    $busca.addEventListener('input', () => {
        clearTimeout(timer);
        const q = $busca.value.trim();
        if (q.length < 2) return fecharResultados();
        timer = setTimeout(() => buscar(q), 300);
    });
    document.addEventListener('click', e => { if (!e.target.closest('#busca-pessoa, #resultados')) fecharResultados(); });

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
        d.className = 'list-group-item text-muted'; d.textContent = texto;
        $res.appendChild(d); $res.classList.remove('d-none');
    }

    function mostrarResultados(itens) {
        const lista = Array.isArray(itens) ? itens : (itens.data || []);
        if (!lista.length) return mostrarMensagem('Nenhum resultado.');
        $res.replaceChildren();
        lista.forEach(it => {
            const type = String(it.tipo || '').toLowerCase().includes('jur') ? TIPOS.juridica : TIPOS.fisica;
            const b = document.createElement('button');
            b.type = 'button'; b.className = 'list-group-item list-group-item-action';
            b.textContent = it.nome + (it.documento ? ' — ' + it.documento : '') + (type === TIPOS.juridica ? ' (PJ)' : ' (PF)');
            b.onclick = () => {
                adicionar(type, it.id, it.nome, null);
                $busca.value = ''; fecharResultados(); render();
            };
            $res.appendChild(b);
        });
        $res.classList.remove('d-none');
    }

    function fecharResultados() { $res.classList.add('d-none'); $res.replaceChildren(); }

    render();
})();
</script>
