<?php

// Tipos disponíveis (chave => nome exibido no dropdown e na listagem).
$types = [
    'compra_venda' => 'Contrato de compra e venda',
    'locacao' => 'Contrato de locação (aluguel)',
    'comodato' => 'Contrato de comodato (empréstimo gratuito)',
    'prestacao_servicos' => 'Contrato de prestação de serviços',
    'trabalho' => 'Contrato de trabalho',
    'empreitada' => 'Contrato de empreitada',
    'parceria' => 'Contrato de parceria',
    'sociedade' => 'Contrato de sociedade',
    'doacao' => 'Contrato de doação',
    'permuta' => 'Contrato de permuta',
    'intermediacao_exclusividade' => 'Contrato de intermediação (com exclusividade)',
    'intermediacao_sem_exclusividade' => 'Contrato de intermediação (sem exclusividade)',
    'corretagem_venda_exclusividade' => 'Contrato de corretagem de venda de imóveis (com exclusividade)',
    'corretagem_venda_sem_exclusividade' => 'Contrato de corretagem de venda de imóveis (sem exclusividade)',
    'mandato_procuracao' => 'Contrato de mandato/procuração',
    'administracao' => 'Contrato de administração',
    'arrendamento' => 'Contrato de arrendamento',
    'cessao_direitos' => 'Contrato de cessão de direitos',
    'confissao_divida' => 'Contrato de confissão de dívida',
    'financiamento' => 'Contrato de financiamento',
    'emprestimo' => 'Contrato de empréstimo',
    'franquia' => 'Contrato de franquia',
    'representacao_comercial' => 'Contrato de representação comercial',
    'distribuicao' => 'Contrato de distribuição',
    'fornecimento' => 'Contrato de fornecimento',
    'transporte' => 'Contrato de transporte',
    'seguro' => 'Contrato de seguro',
    'agencia' => 'Contrato de agência',
    'concessao' => 'Contrato de concessão',
    'licenca_uso' => 'Contrato de licença de uso',
    'aditivo' => 'Aditivo contratual',
    'rescisao_distrato' => 'Rescisão/distrato',
];

// Valores padrão: valem para qualquer tipo que não os sobrescreva.
// Tipos sem formulário implementado ficam com form_supported = false.
$defaults = [
    'subtitulo'        => null,
    'base_legal'       => null,
    'form_supported'   => false,
    'form_view'        => null,
    'clausulas_padrao' => null,
    // O arquivo pode ser adicionado depois em storage/app/public/templates.
    'template'         => null, // null = "{chave}.docx" (resolvido no loop abaixo)
];

// Base compartilhada pelas duas corretagens (mesmo .docx, mesmo formulário).
$corretagemVenda = [
    'titulo'         => 'CONTRATO DE CORRETAGEM DE VENDA DE IMÓVEIS',
    'base_legal'     => '(ARTS. 722 A 729 DO NOVO CÓDIGO CIVIL)',
    'form_supported' => true,
    'template'       => 'contrato-de-corretagem.docx',
    'form_view'      => 'contracts.forms.corretagem-venda.form',
];

// Apenas o que difere do padrão em cada tipo.
$especificos = [
    'corretagem_venda_exclusividade' => $corretagemVenda + [
        'subtitulo'        => 'COM CLÁUSULA DE EXCLUSIVIDADE',
        'base_legal'       => '(ARTS. 722 A 729 DO NOVO CÓDIGO CIVIL C/C ART. 20, III DA LEI Nº 6.530/78)',
        'clausulas_padrao' => 'corretagem-venda-exclusividade.html',
    ],

    'corretagem_venda_sem_exclusividade' => $corretagemVenda + [
        'subtitulo'        => 'SEM CLÁUSULA DE EXCLUSIVIDADE',
        'clausulas_padrao' => 'corretagem-venda-sem-exclusividade.html',
    ],

    'locacao' => [
        'titulo'         => 'CONTRATO DE LOCAÇÃO DE IMÓVEL',
        'base_legal'     => '(LEI Nº 8.245/91)',
        'form_supported' => true,
        'template'       => 'contrato-de-locacao.docx',
        'form_view'      => 'contracts.forms.locacao.form',
    ],

    'doacao' => [
        'titulo'         => 'CONTRATO DE DOAÇÃO DE IMÓVEL',
        'base_legal'     => '(ARTS. 538 A 564 DO NOVO CÓDIGO CIVIL)',
        'form_supported' => true,
        'template'       => 'contrato-de-doacao.docx',
        'form_view'      => 'contracts.forms.doacao.form',
    ],
];

$contractTypes = [];

foreach ($types as $key => $nome) {
    $config = ($especificos[$key] ?? []) + $defaults;

    $contractTypes[$key] = $config + [
        'nome'   => $nome,                       // exibido no dropdown e na listagem
        'titulo' => $nome,                       // título do contrato gerado (se não sobrescrito)
    ];

    $contractTypes[$key]['template'] ??= $key . '.docx';
}

return [
    'default' => 'corretagem_venda_exclusividade',
    'templates_disk' => 'public',
    'templates_dir' => 'templates',
    'clausulas_dir' => 'contracts/clausulas', // relativo a resources/
    'types' => $contractTypes,
];