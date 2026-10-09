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
 * Preenche (ou limpa, se o dado não existir) os campos do form a partir
 * de um mapa { propriedade: 'name-do-input' }. Dispara 'input' em cada
 * campo para reaplicar as máscaras.
 */
function preencherCampos(mapa, dados) {
    for (const [prop, inputName] of Object.entries(mapa)) {
        const campo = document.querySelector(`[name="${inputName}"]`);
        if (!campo) continue;
        campo.value = dados?.[prop] ?? '';
        campo.dispatchEvent(new Event('input'));
    }
}

const CAMPOS_IMOVEL = {
    tipo: 'imovel[tipo]',
    localizacao: 'imovel[localizacao]',
    documento_propriedade: 'imovel[documento_propriedade]',
    matricula: 'imovel[matricula]',
    inscricao_prefeitura: 'imovel[inscricao_prefeitura]',
};

const CAMPOS_CONTRATANTE = {
    pf: {
        nome: 'contratante[nome]', cpf: 'contratante[cpf]', rg: 'contratante[identidade]',
        estado_civil: 'contratante[estado_civil]', profissao: 'contratante[profissao]',
        conjuge_nome: 'contratante[conjuge_nome]', conjuge_rg: 'contratante[conjuge_identidade]',
        endereco: 'contratante[endereco]', telefone: 'contratante[telefones]',
    },
    pj: {
        razao_social: 'contratante[razao_social]', nome_fantasia: 'contratante[nome_fantasia]',
        cnpj: 'contratante[cnpj]', inscricao_estadual: 'contratante[inscricao_estadual]',
        endereco: 'contratante[endereco]',
        representante_legal_nome: 'contratante[representante_legal_nome]',
        representante_legal_cargo: 'contratante[representante_legal_cargo]',
        representante_legal_cpf: 'contratante[representante_legal_cpf]',
    },
};

/**
 * Busca de imóvel. Ao escolher um imóvel, preenche os dados do imóvel e
 * lista as pessoas vinculadas a ele como proprietário/vendedor para o
 * usuário escolher quem será o contratante. Só seleciona sozinho quando
 * há exatamente uma pessoa (e do tipo PF/PJ do contrato em andamento).
 */
function mountImovelSearch() {
    const bloco = document.querySelector('#bloco-busca-imovel');
    if (!bloco) return;

    const tipoContrato = bloco.dataset.tipo; // 'pf' | 'pj'
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
        preencherCampos(CAMPOS_CONTRATANTE[tipoContrato], c.dados);
    };

    const limparContratante = () => {
        clientIdInput.value = '';
        preencherCampos(CAMPOS_CONTRATANTE[tipoContrato], {});
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
            const compativel = c.tipo === tipoContrato;
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
            papeis.textContent = c.papeis_label + (compativel ? '' : ` (pessoa ${c.tipo === 'pj' ? 'jurídica' : 'física'} — use o contrato de outro tipo)`);

            label.append(radio, texto, papeis);
            lista.appendChild(label);

            // Só marca sozinho quando não há dúvida: exatamente uma pessoa vinculada e do tipo certo.
            if (candidatos.length === 1 && compativel) {
                radio.checked = true;
                escolherContratante(c);
            }
        });

        mostrarAviso(
            candidatos.length > 1
                ? 'Este imóvel tem mais de uma pessoa vinculada. Escolha quem será o contratante.'
                : ''
        );
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
 * Máscara de CNPJ pra um campo (contratante pessoa jurídica).
 */
function mountCnpjMask(inputSelector) {
    const input = document.querySelector(inputSelector);
    if (!input) return;

    const aplicar = () => {
        input.value = formatarCnpj(input.value.replace(/\D/g, ''));
    };

    if (input.value) aplicar();
    input.addEventListener('input', aplicar);
}

/**
 * Máscara de CPF pra um campo (contratante pessoa física e CPF do
 * representante legal da pessoa jurídica).
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

    mountCurrencyMask('#input-preco-venda');

    // Contratante pessoa física (os campos só existem na página de PF).
    mountCpfMask('[name="contratante[cpf]"]');
    mountPhoneMask('[name="contratante[telefones]"]');

    // Contratante pessoa jurídica (os campos só existem na página de PJ).
    mountCnpjMask('[name="contratante[cnpj]"]');
    mountCpfMask('[name="contratante[representante_legal_cpf]"]');

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

    // Busca de imóvel (só monta se #bloco-busca-imovel existir na página).
    mountImovelSearch();
});