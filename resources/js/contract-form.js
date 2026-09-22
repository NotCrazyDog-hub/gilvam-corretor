import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';

/**
 * Mapeia chaves de erro (dot notation, como o Laravel devolve) para o
 * seletor do elemento que deve ser marcado visualmente. Usado para os
 * campos dos editores TipTap, cujo input real é um <input type="hidden">
 * sem visibilidade pro usuário — nesses casos, redirecionamos o erro
 * pro container do editor.
 */
const MAPA_ERRO_PARA_ELEMENTO_VISIVEL = {
    'imovel.outros_dados': '#editor-outros-dados',
    'imovel.condicoes_pagamento': '#editor-condicoes',
    'clausulas': '#editor-clausulas',
};

/**
 * Monta um editor TipTap e mantém dois inputs hidden sincronizados:
 * - hiddenJsonSelector: o JSON do TipTap (fonte da verdade, reeditável depois)
 * - hiddenHtmlSelector: o HTML renderizado (usado no back pra gerar o .docx)
 */
function mountEditor({ editorSelector, hiddenJsonSelector, hiddenHtmlSelector, initialContent }) {
    const el = document.querySelector(editorSelector);
    const hiddenJson = document.querySelector(hiddenJsonSelector);
    const hiddenHtml = document.querySelector(hiddenHtmlSelector);

    if (!el || !hiddenJson || !hiddenHtml) {
        console.error(`Elementos não encontrados para o editor: ${editorSelector}`);
        return null;
    }

    const editor = new Editor({
        element: el,
        extensions: [StarterKit],
        content: initialContent || '',
        onUpdate: ({ editor }) => {
            hiddenJson.value = JSON.stringify(editor.getJSON());
            hiddenHtml.value = editor.getHTML();
        },
    });

    // Popula os hidden inputs já no carregamento (importante na edição,
    // quando o editor nasce com conteúdo vindo do banco).
    hiddenJson.value = JSON.stringify(editor.getJSON());
    hiddenHtml.value = editor.getHTML();

    return editor;
}

function formDataToNestedObject(formData) {
    const result = {};
    for (const [key, value] of formData.entries()) {
        if (key === '_token' || key === '_method') continue;
        const match = key.match(/^([^\[]+)\[([^\]]+)\]$/);
        if (match) {
            const [, group, field] = match;
            result[group] = result[group] || {};
            result[group][field] = value;
        } else {
            result[key] = value;
        }
    }
    return result;
}

/**
 * Junta os campos de formulário com o JSON de cada editor TipTap presente na página.
 * Cada editor grava seu JSON/HTML num par de inputs hidden com data-field
 * indicando o caminho no payload (ex: data-field="imovel.outros_dados").
 */
function buildPayload(form) {
    const formData = new FormData(form);
    const payload = formDataToNestedObject(formData);
    return payload;
}

async function downloadContract(url, filename) {
    const res = await fetch(url, { credentials: 'same-origin' });
    if (!res.ok) throw new Error('Falha ao baixar o arquivo.');
    const blob = await res.blob();

    if ('showSaveFilePicker' in window) {
        try {
            const handle = await window.showSaveFilePicker({ suggestedName: filename });
            const writable = await handle.createWritable();
            await writable.write(blob);
            await writable.close();
            return;
        } catch (err) {
            if (err.name === 'AbortError') return; // usuário cancelou o diálogo
            console.warn('showSaveFilePicker falhou, usando fallback:', err);
        }
    }

    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = filename;
    a.click();
    URL.revokeObjectURL(a.href);
}

/**
 * Busca/autocomplete de pessoa física cadastrada. Ao selecionar um
 * resultado, preenche os campos do contratante automaticamente (mas eles
 * continuam editáveis — o snapshot salvo é o que estiver no form no submit,
 * não o cadastro em si).
 */
function mountClientSearch({ inputSelector, resultsSelector, clientIdSelector, searchUrl }) {
    const buscaInput = document.querySelector(inputSelector);
    const resultadosDiv = document.querySelector(resultsSelector);
    const clientIdInput = document.querySelector(clientIdSelector);

    if (!buscaInput || !resultadosDiv || !clientIdInput) return;

    let timeoutId;
    let currentController;

    const limparSelecao = () => {
        clientIdInput.value = '';
    };

    const renderResultados = (pessoas) => {
        resultadosDiv.innerHTML = '';

        if (pessoas.length === 0) {
            resultadosDiv.classList.add('hidden');
            return;
        }

        pessoas.forEach((p) => {
            const item = document.createElement('div');
            item.className = 'resultado-item';
            item.dataset.id = p.id;
            item.dataset.nome = p.nome ?? '';
            item.dataset.cpf = p.cpf ?? '';
            item.dataset.endereco = p.endereco ?? '';
            item.dataset.profissao = p.profissao ?? '';
            item.dataset.estadoCivil = p.estado_civil ?? '';
            item.dataset.telefone = p.telefone ?? '';

            // textContent em vez de innerHTML: dado vem do cadastro, evita XSS.
            item.textContent = `${p.nome} — ${p.cpf}`;
            resultadosDiv.appendChild(item);
        });

        resultadosDiv.classList.remove('hidden');
    };

    buscaInput.addEventListener('input', () => {
        clearTimeout(timeoutId);
        limparSelecao(); // se o usuário editar a busca, invalida a seleção anterior

        const q = buscaInput.value.trim();
        if (q.length < 2) {
            resultadosDiv.classList.add('hidden');
            return;
        }

        timeoutId = setTimeout(async () => {
            currentController?.abort();
            currentController = new AbortController();

            try {
                const res = await fetch(`${searchUrl}?q=${encodeURIComponent(q)}`, {
                    signal: currentController.signal,
                    credentials: 'same-origin',
                });
                if (!res.ok) throw new Error('Falha na busca.');
                renderResultados(await res.json());
            } catch (err) {
                if (err.name !== 'AbortError') {
                    console.error('Erro ao buscar pessoa física:', err);
                }
            }
        }, 300);
    });

    resultadosDiv.addEventListener('click', (e) => {
        const item = e.target.closest('.resultado-item');
        if (!item) return;

        clientIdInput.value = item.dataset.id;

        const campos = {
            'contratante[nome]': item.dataset.nome,
            'contratante[cpf]': item.dataset.cpf,
            'contratante[endereco]': item.dataset.endereco,
            'contratante[profissao]': item.dataset.profissao,
            'contratante[estado_civil]': item.dataset.estadoCivil,
            'contratante[telefones]': item.dataset.telefone,
        };

        for (const [name, value] of Object.entries(campos)) {
            const campo = document.querySelector(`[name="${name}"]`);
            if (campo && value) campo.value = value;
            // dispara o listener 'input' pra reformatar o valor que acabou
            // de ser preenchido programaticamente (CPF, telefone, etc.)
            campo?.dispatchEvent(new Event('input'));
        }

        buscaInput.value = item.dataset.nome;
        resultadosDiv.classList.add('hidden');
    });

    // Fecha a lista de resultados ao clicar fora
    document.addEventListener('click', (e) => {
        if (!resultadosDiv.contains(e.target) && e.target !== buscaInput) {
            resultadosDiv.classList.add('hidden');
        }
    });
}

/**
 * Remove marcações de erro anteriores (classe de erro + texto de mensagem)
 * de todos os campos e elementos de editor do form.
 */
function limparErrosDeCampo(form) {
    form.querySelectorAll('.campo-invalido').forEach((el) =>
        el.classList.remove('campo-invalido', 'border-red-400')
    );
    form.querySelectorAll('.erro-campo-texto').forEach((el) => el.remove());
}

/**
 * Laravel devolve erros com chave em dot notation (ex: "contratante.nome"),
 * mas os inputs usam bracket notation (ex: name="contratante[nome]").
 * Essa função converte o name do input pro formato de chave do Laravel.
 */
function nomeInputParaChaveErro(nomeInput) {
    // "contratante[nome]" -> "contratante.nome"
    return nomeInput.replace(/\[([^\]]+)\]/g, '.$1');
}

/**
 * Insere a mensagem de erro logo abaixo do elemento e marca a borda.
 *
 * Importante: a mensagem é inserida DENTRO do wrapper do campo
 * (.field-wrapper), nunca como sibling direto do input no grid.
 * Se o erro virasse um item a mais no grid, ele empurraria todos os
 * campos seguintes uma célula pra frente, quebrando o alinhamento
 * das colunas. Inserindo dentro do wrapper, só a altura daquela
 * célula específica cresce.
 */
function marcarElementoComErro(elemento, mensagem) {
    elemento.classList.add('campo-invalido', 'border-red-400');

    const texto = document.createElement('p');
    texto.className = 'erro-campo-texto text-xs text-brand-red mt-1';
    texto.textContent = mensagem;

    const wrapper = elemento.closest('.field-wrapper');
    if (wrapper) {
        wrapper.appendChild(texto);
    } else {
        // fallback pra elementos fora de um field-wrapper (ex: containers
        // dos editores TipTap, que já ficam fora do grid)
        elemento.insertAdjacentElement('afterend', texto);
    }
}

/**
 * Recebe o objeto `errors` retornado pelo Laravel (422) e marca cada campo
 * correspondente, inserindo uma mensagem logo abaixo dele. Campos dos
 * editores TipTap são redirecionados pro container visível do editor,
 * já que o input real é hidden.
 */
function exibirErrosDeCampo(form, errors) {
    limparErrosDeCampo(form);

    const chavesTratadas = new Set();

    // Primeiro, trata os casos com mapeamento especial (editores TipTap)
    for (const [chave, elementoSelector] of Object.entries(MAPA_ERRO_PARA_ELEMENTO_VISIVEL)) {
        const mensagens = errors[chave];
        if (!mensagens?.length) continue;

        const elemento = document.querySelector(elementoSelector);
        if (elemento) {
            marcarElementoComErro(elemento, mensagens[0]);
            chavesTratadas.add(chave);
        }
    }

    // Depois, percorre os inputs normais do form
    const inputs = form.querySelectorAll('[name]');
    inputs.forEach((input) => {
        const chave = nomeInputParaChaveErro(input.name);
        if (chavesTratadas.has(chave)) return;

        const mensagens = errors[chave];
        if (!mensagens?.length) return;

        marcarElementoComErro(input, mensagens[0]);
    });
}

/**
 * Máscara de moeda (BRL) pro campo de preço de venda. Trata a entrada
 * como centavos e formata com toLocaleString — o valor enviado no form
 * já sai formatado (ex: "R$ 1.500,00").
 */
function mountCurrencyMask(inputSelector) {
    const input = document.querySelector(inputSelector);
    if (!input) return;

    const formatarMoeda = (valor) => {
        const digits = valor.replace(/\D/g, '');
        const numero = (Number(digits) / 100) || 0;

        return numero.toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL',
        });
    };

    if (input.value) {
        input.value = formatarMoeda(input.value);
    }

    input.addEventListener('input', (e) => {
        e.target.value = formatarMoeda(e.target.value);
    });
}

/**
 * Formata dígitos puros no padrão de CPF (000.000.000-00), progressivamente
 * conforme o usuário digita.
 */
function formatarCpf(digits) {
    return digits
        .slice(0, 11)
        .replace(/(\d{3})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
}

/**
 * Formata dígitos puros no padrão de CNPJ (00.000.000/0000-00).
 */
function formatarCnpj(digits) {
    return digits
        .slice(0, 14)
        .replace(/(\d{2})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d)/, '$1/$2')
        .replace(/(\d{4})(\d{1,2})$/, '$1-$2');
}

/**
 * Máscara de CPF pra um campo. Usado no contratante, que é sempre pessoa
 * física.
 */
function mountCpfMask(inputSelector) {
    const input = document.querySelector(inputSelector);
    if (!input) return;

    const aplicar = () => {
        input.value = formatarCpf(input.value.replace(/\D/g, ''));
    };

    if (input.value) aplicar();
    input.addEventListener('input', aplicar);
}

/**
 * Máscara combinada de CPF/CNPJ. Usada no campo do contratado, que aceita
 * PF ou PJ: até 11 dígitos formata como CPF, a partir do 12º dígito passa
 * a formatar como CNPJ automaticamente — sem precisar de um campo separado
 * pra escolher o tipo.
 */
function mountCpfCnpjMask(inputSelector) {
    const input = document.querySelector(inputSelector);
    if (!input) return;

    const aplicar = () => {
        const digits = input.value.replace(/\D/g, '').slice(0, 14);
        input.value = digits.length > 11 ? formatarCnpj(digits) : formatarCpf(digits);
    };

    if (input.value) aplicar();
    input.addEventListener('input', aplicar);
}

/**
 * Formata um único telefone: fixo (10 dígitos, "(XX) XXXX-XXXX") ou
 * celular (11 dígitos, "(XX) XXXXX-XXXX"), progressivamente conforme a
 * quantidade de dígitos já digitados.
 */
function formatarTelefoneUnico(valor) {
    const digits = valor.replace(/\D/g, '').slice(0, 11);

    if (digits.length <= 2) return digits.replace(/(\d{0,2})/, '($1');
    if (digits.length <= 6) return digits.replace(/(\d{2})(\d{0,4})/, '($1) $2');
    if (digits.length <= 10) return digits.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
    return digits.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
}

/**
 * Máscara pro campo "telefones", que aceita mais de um número separado
 * por "/" (ex: "(88) 99999-9999 / (88) 3333-4444"). Cada trecho entre
 * barras é formatado de forma independente, preservando a barra como
 * separador enquanto o usuário ainda está digitando o próximo número.
 */
function mountPhoneMask(inputSelector) {
    const input = document.querySelector(inputSelector);
    if (!input) return;

    const aplicar = () => {
        input.value = input.value
            .split('/')
            .map((parte) => formatarTelefoneUnico(parte))
            .join(' / ');
    };

    if (input.value) aplicar();
    input.addEventListener('input', aplicar);
}

document.addEventListener('DOMContentLoaded', () => {
    const config = window.contractFormConfig || {};

    mountEditor({
        editorSelector: '#editor-outros-dados',
        hiddenJsonSelector: '#input-outros-dados-json',
        hiddenHtmlSelector: '#input-outros-dados',
        initialContent: config.outrosDadosContent,
    });

    mountEditor({
        editorSelector: '#editor-condicoes',
        hiddenJsonSelector: '#input-condicoes-json',
        hiddenHtmlSelector: '#input-condicoes',
        initialContent: config.condicoesContent,
    });

    mountEditor({
        editorSelector: '#editor-clausulas',
        hiddenJsonSelector: '#input-clausulas-json',
        hiddenHtmlSelector: '#input-clausulas',
        initialContent: config.clausulasContent,
    });

    mountClientSearch({
        inputSelector: '#busca-cliente',
        resultsSelector: '#resultados-busca-cliente',
        clientIdSelector: '#input-client-id',
        searchUrl: '/contracts/pessoas-fisicas/search',
    });

    mountCurrencyMask('#input-preco-venda');

    // Contratante: sempre pessoa física.
    mountCpfMask('[name="contratante[cpf]"]');
    mountPhoneMask('[name="contratante[telefones]"]');

    // Contratado: pode ser PF ou PJ, então usa a máscara combinada.
    mountCpfCnpjMask('[name="contratado[cpf_cnpj]"]');
    mountPhoneMask('[name="contratado[telefones]"]');

    const form = document.getElementById('contract-form');
    if (!form) return;

    const submitBtn = document.getElementById('submit-btn');
    const resultado = document.getElementById('resultado');
    const linkDownload = document.getElementById('link-download');
    const erroBox = document.getElementById('erro');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        erroBox?.classList.add('hidden');
        resultado?.classList.add('hidden');
        limparErrosDeCampo(form);
        submitBtn.disabled = true;
        submitBtn.textContent = 'Gerando...';

        try {
            const payload = buildPayload(form);
            const method = form.dataset.method || 'POST';

            const response = await fetch(form.action, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                    Accept: 'application/json',
                },
                body: JSON.stringify(payload),
            });

            if (response.ok) {
                const data = await response.json();

                if (linkDownload) {
                    linkDownload.href = data.download_url;
                    linkDownload.onclick = (ev) => {
                        ev.preventDefault();
                        downloadContract(data.download_url, data.file_name);
                    };
                }
                resultado?.classList.remove('hidden');
                return;
            }

            // Qualquer status de erro (419, 422, 500...) cai aqui.
            let body = null;
            try {
                body = await response.json();
            } catch {
                // resposta não veio em JSON (ex: página de erro HTML do 419/500)
            }

            if (response.status === 422 && body?.errors) {
                exibirErrosDeCampo(form, body.errors);
            }

            if (erroBox) {
                erroBox.textContent = body?.message
                    || `Erro ao gerar contrato (HTTP ${response.status}). Confira os campos e tente novamente.`;
                erroBox.classList.remove('hidden');
            }

            form.querySelector('.campo-invalido')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } catch (err) {
            // erro de rede, não de resposta HTTP
            if (erroBox) {
                erroBox.textContent = 'Erro ao gerar contrato: ' + err.message;
                erroBox.classList.remove('hidden');
            }
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Gerar contrato';
        }
    });
});