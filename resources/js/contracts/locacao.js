import { mountCurrencyMask, mountCpfMask, mountCnpjMask, mountCpfCnpjMask, mountPhoneMask } from './masks.js';

document.addEventListener('DOMContentLoaded', () => {
    mountCurrencyMask('[name="locacao[valor_aluguel]"]');
    mountCpfMask('[name="locador[cpf]"]');
    mountCnpjMask('[name="locador[cnpj]"]');
    mountCpfMask('[name="locador[representante_legal_cpf]"]');
    mountPhoneMask('[name="locador[telefones]"]');
    mountCpfCnpjMask('[name="locatario[cpf_cnpj]"]');
    mountPhoneMask('[name="locatario[telefones]"]');
});
