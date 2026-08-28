import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';

function mountEditor(editorSelector, hiddenInputSelector) {
    const hiddenInput = document.querySelector(hiddenInputSelector);

    return new Editor({
        element: document.querySelector(editorSelector),
        extensions: [StarterKit],
        content: '',
        onUpdate: ({ editor }) => {
            // por enquanto, texto puro (o backend ainda não converte HTML -> docx)
            hiddenInput.value = editor.getText();
        },
    });
}

document.addEventListener('DOMContentLoaded', () => {
    mountEditor('#editor-outros-dados', '#input-outros-dados');
    mountEditor('#editor-condicoes', '#input-condicoes');

    const form = document.getElementById('contract-form');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(form);
        const payload = formDataToNestedObject(formData);

        const response = await fetch(form.getAttribute('action') || '/contracts', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Accept': 'application/json',
            },
            body: JSON.stringify(payload),
        });

        if (!response.ok) {
            alert('Erro ao gerar contrato. Confira os campos obrigatórios.');
            return;
        }

        const data = await response.json();
        const resultado = document.getElementById('resultado');
        const link = document.getElementById('link-download');

        link.href = data.download_url;
        resultado.classList.remove('hidden');
    });
});

// Converte "contratante[nome]" -> { contratante: { nome: ... } }
function formDataToNestedObject(formData) {
    const result = {};

    for (const [key, value] of formData.entries()) {
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