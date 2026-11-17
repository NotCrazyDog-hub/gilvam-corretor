import { preencherCampos, CAMPOS_CONTRATANTE, atualizarCamposPessoa } from './fields.js';

document.addEventListener('DOMContentLoaded', () => {
    const bloco = document.getElementById('bloco-busca-cliente-servico');
    if (!bloco) return;

    const input = document.getElementById('busca-cliente-servico');
    const resultados = document.getElementById('resultados-cliente-servico');
    const aviso = document.getElementById('aviso-cliente-servico');
    const clientId = document.getElementById('input-client-id');
    const clientType = document.getElementById('input-client-type');
    let timeoutId;
    let controller;

    const limparSelecao = () => {
        clientId.value = '';
        clientType.value = '';
        atualizarCamposPessoa('');
        preencherCampos(CAMPOS_CONTRATANTE.pf, {}, document.querySelector('[data-person-fields="pf"]'));
        preencherCampos(CAMPOS_CONTRATANTE.pj, {}, document.querySelector('[data-person-fields="pj"]'));
    };

    const selecionarCliente = (cliente, tipo) => {
        clientId.value = cliente.id;
        clientType.value = tipo;
        atualizarCamposPessoa(tipo);
        preencherCampos(CAMPOS_CONTRATANTE[tipo], cliente, document.querySelector(`[data-person-fields="${tipo}"]`));
        input.value = cliente.razao_social ?? cliente.nome ?? '';
        resultados.classList.add('hidden');
        aviso.textContent = `Contratante selecionado: ${input.value}`;
    };

    const buscarClientes = async (termo) => {
        controller?.abort();
        controller = new AbortController();
        const signal = controller.signal;
        const urls = ['pf', 'pj'].map((tipo) => {
            const url = new URL(bloco.dataset[`${tipo}Url`], window.location.origin);
            url.searchParams.set('q', termo);
            return fetch(url, { signal, credentials: 'same-origin' }).then((resposta) => {
                if (!resposta.ok) throw new Error('Falha ao buscar clientes.');
                return resposta.json();
            }).then((clientes) => clientes.map((cliente) => ({ ...cliente, tipo })));
        });

        return (await Promise.all(urls)).flat();
    };

    input.addEventListener('input', () => {
        clearTimeout(timeoutId);
        limparSelecao();
        resultados.replaceChildren();
        const termo = input.value.trim();

        if (termo.length < 2) {
            resultados.classList.add('hidden');
            aviso.textContent = 'Selecione um cliente cadastrado para preencher os dados do contratante.';
            return;
        }

        aviso.textContent = 'Buscando clientes...';
        timeoutId = setTimeout(async () => {
            try {
                const clientes = await buscarClientes(termo);
                resultados.replaceChildren();

                clientes.forEach((cliente) => {
                    const botao = document.createElement('button');
                    const nome = cliente.razao_social ?? cliente.nome ?? 'Cliente';
                    const documento = cliente.cnpj ?? cliente.cpf ?? '';
                    botao.type = 'button';
                    botao.className = 'block w-full border-b border-slate-100 px-3 py-2 text-left text-sm hover:bg-slate-50';
                    botao.textContent = `${nome} — ${documento} (${cliente.tipo === 'pj' ? 'Pessoa jurídica' : 'Pessoa física'})`;
                    botao.addEventListener('click', () => selecionarCliente(cliente, cliente.tipo));
                    resultados.appendChild(botao);
                });

                resultados.classList.toggle('hidden', clientes.length === 0);
                aviso.textContent = clientes.length
                    ? 'Selecione um resultado para preencher os dados do contratante.'
                    : 'Nenhum cliente encontrado.';
            } catch (erro) {
                if (erro.name !== 'AbortError') {
                    aviso.textContent = 'Não foi possível buscar clientes. Tente novamente.';
                    console.error('Erro ao buscar contratante:', erro);
                }
            }
        }, 300);
    });
});
