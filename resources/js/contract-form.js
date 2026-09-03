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