<?php

return [
    'default' => 'corretagem_venda_exclusividade',

    // Templates em storage/app/public/templates
    'templates_disk' => 'public',
    'templates_dir'  => 'templates',

    'types' => [
        'corretagem_venda_exclusividade' => [
            'titulo'     => 'Contrato de Corretagem de Venda de Imóveis',
            'subtitulo'  => 'Com Cláusula de Exclusividade',          // pode ser null
            'base_legal' => '(Arts. 722 a 729 do Novo Código Civil c/c Art. 20, III da Lei nº 6.530/78)',
            'template'   => 'contrato-de-corretagem.docx',
        ],
        // 'corretagem_locacao' => [ 'titulo' => 'Contrato de Corretagem de Locação', 'subtitulo' => null, ... ],
    ],
];