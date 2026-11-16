import { mountCnpjMask, mountCpfMask, mountPhoneMask } from './masks.js';

function atualizarCamposComprador(tipo) {
    document.querySelectorAll('[data-comprador-fields]').forEach((bloco) => {
        const ativo = bloco.dataset.compradorFields === tipo;
        bloco.hidden = !ativo;
        bloco.querySelectorAll('input').forEach((input) => {
            input.disabled = !ativo;
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    mountCpfMask('[name="vendedor[cpf]"]');
    mountCpfMask('[name="vendedor[conjuge_cpf]"]');
    mountCpfMask('[name="vendedor[representante_legal_cpf]"]');
    mountCnpjMask('[name="vendedor[cnpj]"]');
    mountPhoneMask('[name="vendedor[telefones]"]');

    mountCpfMask('[name="comprador[cpf]"]');
    mountCpfMask('[name="comprador[conjuge_cpf]"]');
    mountCpfMask('[name="comprador[representante_legal_cpf]"]');
    mountCnpjMask('[name="comprador[cnpj]"]');
    mountPhoneMask('[name="comprador[telefones]"]');
    const compradorTipo = document.getElementById('comprador-tipo');
    if (!compradorTipo) {
        return;
    }

    atualizarCamposComprador(compradorTipo.value);
    compradorTipo.addEventListener('change', () => {
        atualizarCamposComprador(compradorTipo.value);
    });
});
