const MAPA_ERRO_PARA_ELEMENTO_VISIVEL = {
    'imovel.outros_dados': '#editor-outros-dados',
    'imovel.condicoes_pagamento': '#editor-condicoes',
    'clausulas': '#editor-clausulas',
};

export function limparErrosDeCampo(form) {
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
export function exibirErrosDeCampo(form, errors) {
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
