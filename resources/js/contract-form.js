import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';

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

    mountClientSearch({
        inputSelector: '#busca-cliente',
        resultsSelector: '#resultados-busca-cliente',
        clientIdSelector: '#input-client-id',
        searchUrl: '/contracts/pessoas-fisicas/search',
    });

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
        submitBtn.disabled = true;
        submitBtn.textContent = 'Gerando...';

        try {
            const payload = buildPayload(form);
            const method = form.dataset.method || 'POST';

            const response = await fetch(form.action, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    Accept: 'application/json',
                },
                body: JSON.stringify(payload),
            });

            if (!response.ok) {
                const body = await response.json().catch(() => null);
                const msg = body?.message || 'Confira os campos obrigatórios.';
                throw new Error(msg);
            }

            const data = await response.json();

            if (linkDownload) {
                linkDownload.href = data.download_url;
                linkDownload.onclick = (ev) => {
                    ev.preventDefault();
                    downloadContract(data.download_url, `contrato-v${data.version}.docx`);
                };
            }
            resultado?.classList.remove('hidden');
        } catch (err) {
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