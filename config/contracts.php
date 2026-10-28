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

$contractTypes = [];
foreach ($types as $key => $title) {
    $contractTypes[$key] = [
        // Nome exibido no dropdown e na listagem de contratos.
        'nome' => $title,
        // Título usado no contrato gerado.
        'titulo' => $key === 'corretagem_venda_exclusividade'
            ? 'Contrato de Corretagem de Venda de Imóveis'
            : $title,
        'subtitulo' => $key === 'corretagem_venda_exclusividade'
            ? 'Com Cláusula de Exclusividade'
            : null,
        'base_legal' => $key === 'corretagem_venda_exclusividade'
            ? '(Arts. 722 a 729 do Novo Código Civil c/c Art. 20, III da Lei nº 6.530/78)'
            : null,
        // Os outros formulários precisam ser implementados antes de gerar.
        'form_supported' => in_array($key, ['corretagem_venda_exclusividade', 'doacao'], true),
        // O arquivo pode ser adicionado depois em storage/app/public/templates.
        'template' => match ($key) {
            'corretagem_venda_exclusividade' => 'contrato-de-corretagem.docx',
            'doacao' => 'contrato-de-doacao.docx',
            default => $key . '.docx',
        },
    ];
}

return [
    'default' => 'corretagem_venda_exclusividade',
    'templates_disk' => 'public',
    'templates_dir' => 'templates',
    'types' => $contractTypes,
];
