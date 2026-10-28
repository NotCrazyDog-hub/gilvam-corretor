<?php

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

// Tudo que as duas corretagens têm em comum (mesmo .docx, mesmo formulário).
$corretagemVenda = [
    'titulo'         => 'CONTRATO DE CORRRETAGEM DE VENDA DE IMÓVEIS',
    'base_legal'     => '(ARTS. 481 A 532 DO NOVO CÓDIGO CIVIL – LEI Nº 10.406/2002)',
    'form_supported' => true,
    'template'       => 'contrato-de-corretagem.docx',
];

// Só o que muda: subtítulo e arquivo com as cláusulas padrão.
$especificos = [
    'corretagem_venda_exclusividade' => $corretagemVenda + [
        'subtitulo'       => 'COM CLÁUSULA DE EXCLUSIVIDADES',
        'clausulas_padrao' => 'corretagem-venda-exclusividade.html',
    ],
    'corretagem_venda_sem_exclusividade' => $corretagemVenda + [
        'subtitulo'       => 'SEM CLÁUSULA DE EXCLUSIVIDADES',
        'clausulas_padrao' => 'corretagem-venda-sem-exclusividade.html',
    ],
];

$contractTypes = [];
foreach ($types as $key => $title) {
    $contractTypes[$key] = ($especificos[$key] ?? []) + [
        // Nome exibido no dropdown e na listagem de contratos.
        'nome'             => $title,
        // Título usado no contrato gerado.
        'titulo'           => $title,
        'subtitulo'        => null,
        'base_legal'       => null,
        // Os outros formulários precisam ser implementados antes de gerar.
        'form_supported'   => false,
        // O arquivo pode ser adicionado depois em storage/app/public/templates.
        'template'         => $key . '.docx',
        // Arquivo em resources/contracts/clausulas com o texto inicial do editor.
        'clausulas_padrao' => null,
    ];
}

return [
    'default' => 'corretagem_venda_exclusividade',
    'templates_disk' => 'public',
    'templates_dir' => 'templates',
    'clausulas_dir' => 'contracts/clausulas', // relativo a resources/
    'types' => $contractTypes,
];