import { preencherCampos, CAMPOS_IMOVEL, CAMPOS_CONTRATANTE, CAMPOS_LOCADOR, atualizarCamposPessoa } from './fields.js';

/**
 * Busca de imóvel. Ao escolher um imóvel, preenche os dados do imóvel e
 * lista as pessoas vinculadas a ele como proprietário/vendedor para o
 * usuário escolher quem será o contratante. Só seleciona sozinho quando
 * há exatamente uma pessoa (e do tipo PF/PJ do contrato em andamento).
 */
export function mountImovelSearch() {
    const bloco = document.querySelector('#bloco-busca-imovel');
    if (!bloco) return;

    const clientTypeInput = document.querySelector('#input-client-type');
    const isLocacao = document.querySelector('[name="contract_type"]')?.value === 'locacao';
    const camposContratante = isLocacao ? CAMPOS_LOCADOR : CAMPOS_CONTRATANTE;
    const buscaInput = bloco.querySelector('#busca-imovel');
    const resultadosDiv = bloco.querySelector('#resultados-busca-imovel');
    const blocoContratante = bloco.querySelector('#bloco-contratante');
    const lista = bloco.querySelector('#lista-candidatos');
    const aviso = bloco.querySelector('#aviso-candidatos');
    const imovelIdInput = document.querySelector('#input-imovel-id');
    const clientIdInput = document.querySelector('#input-client-id');

    let timeoutId;
    let currentController;

    const escolherContratante = (c) => {
        clientIdInput.value = c.id;
        clientTypeInput.value = c.tipo;
        atualizarCamposPessoa(c.tipo);
        const camposPessoa = document.querySelector(`[data-person-fields="${c.tipo}"]`);
        preencherCampos(camposContratante[c.tipo], c.dados, camposPessoa);
    };

    const limparContratante = () => {
        clientIdInput.value = '';
        clientTypeInput.value = '';
        preencherCampos(camposContratante.pf, {}, document.querySelector('[data-person-fields="pf"]'));
        preencherCampos(camposContratante.pj, {}, document.querySelector('[data-person-fields="pj"]'));
        atualizarCamposPessoa('');
    };

    const mostrarAviso = (texto) => {
        aviso.textContent = texto;
        aviso.classList.toggle('hidden', !texto);
    };

    const renderCandidatos = (imovel) => {
        lista.innerHTML = '';
        const candidatos = imovel.candidatos;

        blocoContratante.classList.remove('hidden');

        if (candidatos.length === 0) {
            mostrarAviso(
                'Este imóvel não tem proprietário nem vendedor vinculado.'
                + (imovel.proprietario_nome ? ` (Proprietário informado como texto: ${imovel.proprietario_nome}.)` : '')
                + ' Vincule o cliente no cadastro do imóvel.'
            );
            return;
        }

        candidatos.forEach((c) => {
            const compativel = true;
            const label = document.createElement('label');
            label.className = 'flex items-center gap-2 text-sm px-2 py-1.5 border border-slate-200 '
                + (compativel ? 'cursor-pointer hover:bg-slate-50' : 'opacity-50 cursor-not-allowed');

            const radio = document.createElement('input');
            radio.type = 'radio';
            radio.name = 'candidato_contratante';
            radio.disabled = !compativel;
            radio.addEventListener('change', () => escolherContratante(c));

            const texto = document.createElement('span');
            texto.textContent = `${c.nome} — ${c.documento ?? ''}`;

            const papeis = document.createElement('span');
            papeis.className = 'ml-auto text-xs text-slate-500';
            papeis.textContent = c.papeis_label + ` — pessoa ${c.tipo === 'pj' ? 'jurídica (CNPJ)' : 'física (CPF)'}`;

            label.append(radio, texto, papeis);
            lista.appendChild(label);

            // A escolha do contratante define PF/PJ a partir do documento cadastrado.
            if (candidatos.length === 1) {
                radio.checked = true;
                escolherContratante(c);
            }
        });

        mostrarAviso(candidatos.length > 1
            ? 'Este imóvel tem mais de uma pessoa vinculada. Escolha quem será o contratante.'
            : 'O CPF/CNPJ do cadastro define automaticamente se o contratante é pessoa física ou jurídica.');
    };

    const escolherImovel = (imovel) => {
        imovelIdInput.value = imovel.id;
        buscaInput.value = imovel.label;
        resultadosDiv.classList.add('hidden');

        preencherCampos(CAMPOS_IMOVEL, imovel.dados);
        limparContratante(); // não deixa dados do contratante anterior no contrato
        renderCandidatos(imovel);
    };

    const renderResultados = (imoveis) => {
        resultadosDiv.innerHTML = '';
        if (imoveis.length === 0) {
            resultadosDiv.classList.add('hidden');
            return;
        }
        imoveis.forEach((imovel) => {
            const item = document.createElement('div');
            item.className = 'resultado-item';
            item.textContent = imovel.label;
            item.addEventListener('click', () => escolherImovel(imovel));
            resultadosDiv.appendChild(item);
        });
        resultadosDiv.classList.remove('hidden');
    };

    buscaInput.addEventListener('input', () => {
        clearTimeout(timeoutId);
        imovelIdInput.value = '';
        clientIdInput.value = '';
        blocoContratante.classList.add('hidden');

        const q = buscaInput.value.trim();
        if (q.length < 2) {
            resultadosDiv.classList.add('hidden');
            return;
        }

        timeoutId = setTimeout(async () => {
            currentController?.abort();
            currentController = new AbortController();
            try {
                const res = await fetch(`/contratos/imoveis/busca?q=${encodeURIComponent(q)}`, {
                    signal: currentController.signal,
                    credentials: 'same-origin',
                });
                if (!res.ok) throw new Error('Falha na busca.');
                renderResultados(await res.json());
            } catch (err) {
                if (err.name !== 'AbortError') console.error('Erro ao buscar imóvel:', err);
            }
        }, 300);
    });

    document.addEventListener('click', (e) => {
        if (!resultadosDiv.contains(e.target) && e.target !== buscaInput) {
            resultadosDiv.classList.add('hidden');
        }
    });
}
