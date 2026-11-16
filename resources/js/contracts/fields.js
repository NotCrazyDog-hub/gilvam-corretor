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

const CAMPOS_VENDEDOR = {
    pf: {
        nome: 'vendedor[nome]', cpf: 'vendedor[cpf]', rg: 'vendedor[identidade]',
        estado_civil: 'vendedor[estado_civil]', profissao: 'vendedor[profissao]',
        nacionalidade: 'vendedor[nacionalidade]', email: 'vendedor[email]',
        conjuge_nome: 'vendedor[conjuge_nome]', conjuge_rg: 'vendedor[conjuge_identidade]',
        endereco: 'vendedor[endereco]', telefone: 'vendedor[telefones]',
    },
    pj: {
        razao_social: 'vendedor[razao_social]', nome_fantasia: 'vendedor[nome_fantasia]',
        cnpj: 'vendedor[cnpj]', inscricao_estadual: 'vendedor[inscricao_estadual]',
        endereco: 'vendedor[endereco]', email: 'vendedor[email]',
        representante_legal_nome: 'vendedor[representante_legal_nome]',
        representante_legal_cargo: 'vendedor[representante_legal_cargo]',
        representante_legal_cpf: 'vendedor[representante_legal_cpf]',
        telefones: 'vendedor[telefones]',
    },
};

const CAMPOS_LOCADOR = {
    pf: {
        nome: 'locador[nome]', cpf: 'locador[cpf]', rg: 'locador[identidade]',
        estado_civil: 'locador[estado_civil]', profissao: 'locador[profissao]',
        conjuge_nome: 'locador[conjuge_nome]', conjuge_rg: 'locador[conjuge_identidade]',
        endereco: 'locador[endereco]', telefone: 'locador[telefones]',
    },
    pj: {
        razao_social: 'locador[razao_social]', nome_fantasia: 'locador[nome_fantasia]',
        cnpj: 'locador[cnpj]', inscricao_estadual: 'locador[inscricao_estadual]',
        endereco: 'locador[endereco]', representante_legal_nome: 'locador[representante_legal_nome]',
        representante_legal_cargo: 'locador[representante_legal_cargo]',
        representante_legal_cpf: 'locador[representante_legal_cpf]', telefones: 'locador[telefones]',
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

export { preencherCampos, CAMPOS_IMOVEL, CAMPOS_CONTRATANTE, CAMPOS_VENDEDOR, CAMPOS_LOCADOR, atualizarCamposPessoa };
