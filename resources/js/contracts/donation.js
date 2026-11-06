import { mountCurrencyMask, mountCpfMask, mountCnpjMask, mountPhoneMask } from './masks.js';

function atualizarCamposDonatario(tipo) {
    document.querySelectorAll('[data-donatario-fields]').forEach((bloco) => {
        const ativo = bloco.dataset.donatarioFields === tipo;
        bloco.hidden = !ativo;
        bloco.querySelectorAll('input').forEach((input) => {
            input.disabled = !ativo;
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    mountCurrencyMask('#input-valor-atribuido');
    mountCpfMask('[name="donatario[cpf]"]');
    mountCpfMask('[name="donatario[representante_legal_cpf]"]');
    mountCnpjMask('[name="donatario[cnpj]"]');
    mountPhoneMask('[name="donatario[telefones]"]');

    const tipoDonatario = document.getElementById('donatario-tipo');
    if (!tipoDonatario) {
        return;
    }

    atualizarCamposDonatario(tipoDonatario.value);
    tipoDonatario.addEventListener('change', () => {
        atualizarCamposDonatario(tipoDonatario.value);
    });
});
