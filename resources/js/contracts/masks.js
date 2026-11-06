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

    if (digits.length === 0) return '';
    if (digits.length <= 2) return digits.replace(/(\d{0,2})/, '($1');
    if (digits.length <= 6) return digits.replace(/(\d{2})(\d{0,4})/, '($1) $2');
    if (digits.length <= 10) return digits.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
    return digits.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
}

function formatarListaTelefones(valor) {
    return String(valor ?? '')
        .split('/')
        .map((parte) => formatarTelefoneUnico(parte))
        .join(' / ');
}

/**
 * Máscara pro campo "telefones", que aceita mais de um número separado
 * por "/" (ex: "(88) 99999-9999 / (88) 3333-4444"). Cada trecho entre
 * barras é formatado de forma independente, preservando a barra como
 * separador enquanto o usuário ainda está digitando o próximo número.
 */
function mountPhoneMask(inputSelector) {
    document.querySelectorAll(inputSelector).forEach((input) => {
        const aplicar = () => {
            input.value = formatarListaTelefones(input.value);
        };

        if (input.value) aplicar();
        input.addEventListener('input', aplicar);
    });
}

export { mountCurrencyMask, mountCnpjMask, mountCpfMask, mountCpfCnpjMask, mountPhoneMask, formatarListaTelefones };
