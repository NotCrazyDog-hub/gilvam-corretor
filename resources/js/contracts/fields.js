import { formatarListaTelefones } from './masks.js';

function preencherCampos(mapa, dados, container = null) {
    for (const [prop, inputName] of Object.entries(mapa)) {
        const campo = (container ?? document).querySelector(`[name="${inputName}"]`);
        if (!campo) continue;
        campo.value = dados?.[prop] ?? '';
        if (inputName === 'contratante[telefones]' || inputName === 'contratado[telefones]') {
            campo.value = formatarListaTelefones(campo.value);
        }
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
        telefones: 'contratante[telefones]',
    },
};

function atualizarCamposPessoa(tipo) {
    document.querySelectorAll('[data-person-fields]').forEach((bloco) => {
        const ativo = bloco.dataset.personFields === tipo;
        bloco.hidden = !ativo;
        bloco.querySelectorAll('input').forEach((input) => { input.disabled = !ativo; });
    });

    const rotuloAssinatura = document.querySelector('[data-assinatura-segundo]');
    if (rotuloAssinatura) {
        rotuloAssinatura.textContent = tipo === 'pj' ? 'REPRESENTANTE LEGAL' : 'CÔNJUGE';
    }
}

export { preencherCampos, CAMPOS_IMOVEL, CAMPOS_CONTRATANTE, atualizarCamposPessoa };
