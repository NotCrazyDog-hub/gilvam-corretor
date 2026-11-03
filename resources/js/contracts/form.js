import { mountEditor, buildPayload, downloadContract } from './editors.js';
import { atualizarCamposPessoa } from './fields.js';
import { mountImovelSearch } from './property-search.js';
import { limparErrosDeCampo, exibirErrosDeCampo } from './validation.js';
import { mountCurrencyMask, mountCnpjMask, mountCpfMask, mountCpfCnpjMask, mountPhoneMask } from './masks.js';

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

    // Campos de PF/PJ existem na mesma página; o documento selecionado define qual bloco usar.
    mountCpfMask('[name="contratante[cpf]"]');
    mountPhoneMask('[name="contratante[telefones]"]');

    mountCnpjMask('[name="contratante[cnpj]"]');
    mountCpfMask('[name="contratante[representante_legal_cpf]"]');

    // Contratado: pode ser PF ou PJ, então usa a máscara combinada.
    mountCpfCnpjMask('[name="contratado[cpf_cnpj]"]');
    mountPhoneMask('[name="contratado[telefones]"]');

    const form = document.getElementById('contract-form');
    if (!form) return;

    const initialClientType = document.querySelector('#input-client-type')?.value || '';
    atualizarCamposPessoa(initialClientType);

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
