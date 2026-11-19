<?php

namespace App\Domains\Contracts\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Domains\Clients\Models\Client;
use App\Domains\Imoveis\Models\Imovel;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\DB;

abstract class BaseContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'client_type'   => 'required|string|in:pf,pj',
            'client_id'     => 'required|integer',
            'contract_type' => ['nullable', \Illuminate\Validation\Rule::in(\App\Domains\Contracts\Services\ContractTypes::keys())],
            'imovel_id' => 'nullable|integer|exists:imoveis,id',

            'contratante'              => 'required|array',
            'contratante.nome'         => 'required_if:client_type,pf|string|max:255',
            'contratante.cpf'          => 'required_if:client_type,pf|string|max:20',
            'contratante.estado_civil' => 'required_if:client_type,pf|string|max:100',
            'contratante.conjuge_nome' => 'nullable|string|max:255',
            'contratante.conjuge_cpf'  => 'nullable|string|max:20',
            'contratante.endereco'     => 'required|string|max:255',
            'contratante.telefones'    => 'nullable|string|max:100',
            'contratante.profissao'    => 'required_if:client_type,pf|string|max:255',
            'contratante.identidade'   => 'required_if:client_type,pf|string|max:50',
            'contratante.conjuge_identidade' => 'nullable|string|max:50',

            'contratante.razao_social'                => 'required_if:client_type,pj|string|max:255',
            'contratante.nome_fantasia'               => 'nullable|string|max:255',
            'contratante.cnpj'                        => 'required_if:client_type,pj|string|max:20',
            'contratante.inscricao_estadual'          => 'nullable|string|max:100',
            'contratante.representante_legal_nome'    => 'required_if:client_type,pj|string|max:255',
            'contratante.representante_legal_cargo'   => 'required_if:client_type,pj|string|max:100',
            'contratante.representante_legal_cpf'     => 'required_if:client_type,pj|string|max:20',

            'contratado'          => 'required_unless:contract_type,doacao|array',
            'contratado.nome'     => 'required_unless:contract_type,doacao|string|max:255',
            'contratado.cpf_cnpj' => 'required_unless:contract_type,doacao|string|max:20',
            'contratado.endereco'  => 'required_unless:contract_type,doacao|string|max:255',
            'contratado.telefones' => 'nullable|string|max:100',
            'contratado.creci'     => 'required_unless:contract_type,doacao|string|max:50',

            'imovel'                          => 'required|array',
            'imovel.tipo'                      => 'required|string|max:255',
            'imovel.localizacao'               => 'required|string|max:255',
            'imovel.documento_propriedade'     => 'required|string|max:255',
            'imovel.matricula'                 => 'required|string|max:255',
            'imovel.inscricao_prefeitura'      => 'nullable|string|max:255',
            'imovel.preco_venda'               => 'required_unless:contract_type,doacao|string|max:100',
            'imovel.outros_dados'              => 'nullable|string',
            'imovel.condicoes_pagamento'       => 'nullable|string',
            'imovel.valor_atribuido'           => 'required_if:contract_type,doacao|string|max:100',
            'imovel.onus'                      => 'nullable|string|max:2000',

            'donatario' => 'required_if:contract_type,doacao|array',
            'donatario.tipo' => 'required_if:contract_type,doacao|in:pf,pj',
            'donatario.nome' => 'required_if:donatario.tipo,pf|string|max:255',
            'donatario.estado_civil' => 'required_if:donatario.tipo,pf|string|max:100',
            'donatario.profissao' => 'required_if:donatario.tipo,pf|string|max:255',
            'donatario.cpf' => 'required_if:donatario.tipo,pf|string|max:20',
            'donatario.identidade' => 'required_if:donatario.tipo,pf|string|max:50',
            'donatario.conjuge_nome' => 'nullable|string|max:255',
            'donatario.conjuge_identidade' => 'nullable|string|max:50',
            'donatario.razao_social' => 'required_if:donatario.tipo,pj|string|max:255',
            'donatario.nome_fantasia' => 'nullable|string|max:255',
            'donatario.inscricao_estadual' => 'nullable|string|max:100',
            'donatario.cnpj' => 'required_if:donatario.tipo,pj|string|max:20',
            'donatario.representante_legal_nome' => 'required_if:donatario.tipo,pj|string|max:255',
            'donatario.representante_legal_cargo' => 'required_if:donatario.tipo,pj|string|max:100',
            'donatario.representante_legal_cpf' => 'required_if:donatario.tipo,pj|string|max:20',
            'donatario.endereco' => 'required_if:contract_type,doacao|string|max:255',
            'donatario.telefones' => 'nullable|string|max:100',

            'content_json' => 'nullable|array',
            'content_html' => 'nullable|string',

            'clausulas' => 'nullable|string',

            'data_termino' => ['nullable', 'date', 'after:today', function ($attribute, $value, $fail) {
                $prazoIndeterminado = filter_var($this->input('prazo_indeterminado', false), FILTER_VALIDATE_BOOLEAN);

                if (!$prazoIndeterminado && !$value) {
                    $fail('Informe a data de término ou marque como prazo indeterminado.');
                }
            }],
            'prazo_indeterminado'  => 'nullable|boolean',
        ];

        if ($this->input('contract_type') === 'locacao') {
            foreach (array_keys($rules) as $key) {
                if (str_starts_with($key, 'contratante') || str_starts_with($key, 'contratado')
                    || in_array($key, ['imovel.preco_venda', 'imovel.condicoes_pagamento', 'data_termino'], true)) {
                    unset($rules[$key]);
                }
            }

            $rules += [
                'locador' => 'required|array',
                'locador.nome' => 'required_if:client_type,pf|string|max:255',
                'locador.estado_civil' => 'required_if:client_type,pf|string|max:100',
                'locador.profissao' => 'required_if:client_type,pf|string|max:255',
                'locador.cpf' => 'required_if:client_type,pf|string|max:20',
                'locador.identidade' => 'required_if:client_type,pf|string|max:50',
                'locador.conjuge_nome' => 'nullable|string|max:255',
                'locador.conjuge_identidade' => 'nullable|string|max:50',
                'locador.razao_social' => 'required_if:client_type,pj|string|max:255',
                'locador.nome_fantasia' => 'nullable|string|max:255',
                'locador.inscricao_estadual' => 'nullable|string|max:100',
                'locador.cnpj' => 'required_if:client_type,pj|string|max:20',
                'locador.representante_legal_nome' => 'required_if:client_type,pj|string|max:255',
                'locador.representante_legal_cargo' => 'required_if:client_type,pj|string|max:100',
                'locador.representante_legal_cpf' => 'required_if:client_type,pj|string|max:20',
                'locador.endereco' => 'required|string|max:255',
                'locador.telefones' => 'nullable|string|max:100',
                'locatario' => 'required|array',
                'locatario.nome' => 'required|string|max:255',
                'locatario.estado_civil' => 'nullable|string|max:100',
                'locatario.profissao' => 'nullable|string|max:255',
                'locatario.cpf_cnpj' => 'required|string|max:20',
                'locatario.identidade' => 'nullable|string|max:50',
                'locatario.endereco' => 'required|string|max:255',
                'locatario.telefones' => 'nullable|string|max:100',
                'locacao' => 'required|array',
                'locacao.finalidade' => 'required|string|max:255',
                'locacao.prazo' => 'required|string|max:100',
                'locacao.data_inicio' => 'required|date',
                'locacao.data_termino' => 'nullable|required_unless:prazo_indeterminado,1|date|after:locacao.data_inicio',
                'locacao.valor_aluguel' => 'required|string|max:100',
                'locacao.indice_reajuste' => 'required|string|max:100',
                'locacao.dia_vencimento' => 'required|string|max:20',
                'locacao.garantia' => 'required|string|max:255',
                'locacao.condicoes_pagamento' => 'nullable|string',
            ];
        }

        if ($this->input('contract_type') === 'compra_venda') {
            $rules['imovel_id'] = 'required|integer|exists:imoveis,id';

            foreach (array_keys($rules) as $key) {
                if (str_starts_with($key, 'contratante')) {
                    unset($rules[$key]);
                }
            }

            unset($rules['data_termino'], $rules['prazo_indeterminado']);

            $rules += [
                'vendedor' => 'required|array',
                'vendedor.tipo' => 'required|string|in:pf,pj|same:client_type',
                'vendedor.nome' => 'required_if:client_type,pf|string|max:255',
                'vendedor.estado_civil' => 'required_if:client_type,pf|string|max:100',
                'vendedor.profissao' => 'required_if:client_type,pf|string|max:255',
                'vendedor.nacionalidade' => 'nullable|string|max:255',
                'vendedor.email' => 'nullable|email|max:255',
                'vendedor.cpf' => 'required_if:client_type,pf|string|max:20',
                'vendedor.identidade' => 'required_if:client_type,pf|string|max:50',
                'vendedor.conjuge_nome' => 'nullable|string|max:255',
                'vendedor.conjuge_identidade' => 'nullable|string|max:50',
                'vendedor.conjuge_cpf' => 'nullable|string|max:20',
                'vendedor.regime_bens' => 'nullable|string|max:100',
                'vendedor.razao_social' => 'required_if:client_type,pj|string|max:255',
                'vendedor.nome_fantasia' => 'nullable|string|max:255',
                'vendedor.inscricao_estadual' => 'nullable|string|max:100',
                'vendedor.cnpj' => 'required_if:client_type,pj|string|max:20',
                'vendedor.representante_legal_nome' => 'required_if:client_type,pj|string|max:255',
                'vendedor.representante_legal_cargo' => 'required_if:client_type,pj|string|max:100',
                'vendedor.representante_legal_cpf' => 'required_if:client_type,pj|string|max:20',
                'vendedor.endereco' => 'required|string|max:255',
                'vendedor.telefones' => 'nullable|string|max:100',

                'comprador' => 'required|array',
                'comprador.tipo' => 'required|string|in:pf,pj',
                'comprador.nome' => 'required_if:comprador.tipo,pf|string|max:255',
                'comprador.estado_civil' => 'required_if:comprador.tipo,pf|string|max:100',
                'comprador.profissao' => 'required_if:comprador.tipo,pf|string|max:255',
                'comprador.nacionalidade' => 'nullable|string|max:255',
                'comprador.email' => 'nullable|email|max:255',
                'comprador.cpf' => 'required_if:comprador.tipo,pf|string|max:20',
                'comprador.identidade' => 'required_if:comprador.tipo,pf|string|max:50',
                'comprador.conjuge_nome' => 'nullable|string|max:255',
                'comprador.conjuge_identidade' => 'nullable|string|max:50',
                'comprador.conjuge_cpf' => 'nullable|string|max:20',
                'comprador.regime_bens' => 'nullable|string|max:100',
                'comprador.razao_social' => 'required_if:comprador.tipo,pj|string|max:255',
                'comprador.nome_fantasia' => 'nullable|string|max:255',
                'comprador.inscricao_estadual' => 'nullable|string|max:100',
                'comprador.cnpj' => 'required_if:comprador.tipo,pj|string|max:20',
                'comprador.representante_legal_nome' => 'required_if:comprador.tipo,pj|string|max:255',
                'comprador.representante_legal_cargo' => 'required_if:comprador.tipo,pj|string|max:100',
                'comprador.representante_legal_cpf' => 'required_if:comprador.tipo,pj|string|max:20',
                'comprador.endereco' => 'required|string|max:255',
                'comprador.telefones' => 'nullable|string|max:100',

                'negocio' => 'required|array',
                'negocio.sinal' => 'nullable|string|max:255',
                'negocio.prazo_escritura' => 'nullable|string|max:255',
                'negocio.data_posse' => 'nullable|date',
                'negocio.comissao' => 'nullable|string|max:255',
                'negocio.comissao_responsavel' => 'nullable|string|max:255',
            ];
        }

        if ($this->input('contract_type') === 'prestacao_servicos') {
            foreach (array_keys($rules) as $key) {
                if (str_starts_with($key, 'imovel') || str_starts_with($key, 'donatario')) {
                    unset($rules[$key]);
                }
            }

            unset(
                $rules['contratado.creci'],
                $rules['contratado.nome'],
                $rules['contratado.cpf_cnpj'],
            );

            $rules += [
                'contratante.email' => 'nullable|email|max:255',
                'contratado.tipo' => 'required|string|in:pf,pj',
                'contratado.nome' => 'required_if:contratado.tipo,pf|string|max:255',
                'contratado.estado_civil' => 'required_if:contratado.tipo,pf|string|max:100',
                'contratado.profissao' => 'required_if:contratado.tipo,pf|string|max:255',
                'contratado.cpf' => 'required_if:contratado.tipo,pf|string|max:20',
                'contratado.identidade' => 'required_if:contratado.tipo,pf|string|max:50',
                'contratado.razao_social' => 'required_if:contratado.tipo,pj|string|max:255',
                'contratado.nome_fantasia' => 'nullable|string|max:255',
                'contratado.inscricao_estadual' => 'nullable|string|max:100',
                'contratado.cnpj' => 'required_if:contratado.tipo,pj|string|max:20',
                'contratado.representante_legal_nome' => 'required_if:contratado.tipo,pj|string|max:255',
                'contratado.representante_legal_cargo' => 'required_if:contratado.tipo,pj|string|max:100',
                'contratado.representante_legal_cpf' => 'required_if:contratado.tipo,pj|string|max:20',
                'contratado.email' => 'nullable|email|max:255',
                'servico' => 'required|array',
                'servico.descricao' => 'required|string|max:10000',
                'servico.local' => 'nullable|string|max:5000',
                'servico.entregaveis' => 'nullable|string|max:5000',
                'servico.data_inicio' => 'required|date',
                'servico.prazo' => 'required|string|max:255',
                'servico.outros_dados' => 'nullable|string|max:5000',
                'servico.valor' => 'required|string|max:255',
                'servico.forma_pagamento' => 'required|string|max:1000',
                'servico.vencimento' => 'required|string|max:500',
                'servico.dados_pagamento' => 'nullable|string|max:1000',
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'client_type.required' => 'Selecione o tipo de cliente.',
            'client_type.in'       => 'Tipo de cliente inválido.',
            'client_id.required' => 'Selecione um cliente cadastrado antes de gerar o contrato.',
            'contratante.nome.required'     => 'O nome do contratante é obrigatório.',
            'contratante.cpf.required'      => 'O CPF do contratante é obrigatório.',
            'contratante.estado_civil.required' => 'O estado civil do contratante é obrigatório.',
            'contratante.endereco.required'   => 'O endereço do contratante é obrigatório.',
            'contratante.identidade.required' => 'A identidade do contratante é obrigatória.',
            'contratante.profissao.required'   => 'A profissão do contratante é obrigatória.',

            'contratante.razao_social.required_if' => 'A razão social é obrigatória.',
            'contratante.cnpj.required_if' => 'O CNPJ é obrigatório.',
            'contratante.representante_legal_nome.required_if' => 'O nome do representante legal é obrigatório.',
            'contratante.representante_legal_cargo.required_if' => 'O cargo do representante legal é obrigatório.',
            'contratante.representante_legal_cpf.required_if' => 'O CPF do representante legal é obrigatório.',

            'contratado.nome.required'        => 'O nome do contratado é obrigatório.',
            'contratado.cpf_cnpj.required'     => 'O CPF/CNPJ do contratado é obrigatório.',
            'contratado.endereco.required'     => 'O endereço do contratado é obrigatório.',
            'contratado.creci.required'        => 'O CRECI do contratado é obrigatório.',
            
            'imovel.tipo.required'             => 'O tipo do imóvel é obrigatório.',
            'imovel.localizacao.required'      => 'A localização do imóvel é obrigatória.',
            'imovel.documento_propriedade.required' => 'O documento de propriedade do imóvel é obrigatório.',
            'imovel.matricula.required'        => 'A matrícula do imóvel é obrigatória.',
            'imovel.preco_venda.required'      => 'O preço de venda do imóvel é obrigatório.',

            'vendedor.tipo.same' => 'O tipo do vendedor deve corresponder ao cliente selecionado para o imóvel.',
            'vendedor.endereco.required' => 'O endereço do vendedor é obrigatório.',
            'vendedor.nome.required_if' => 'O nome do vendedor é obrigatório.',
            'vendedor.estado_civil.required_if' => 'O estado civil do vendedor é obrigatório.',
            'vendedor.profissao.required_if' => 'A profissão do vendedor é obrigatória.',
            'vendedor.cpf.required_if' => 'O CPF do vendedor é obrigatório.',
            'vendedor.identidade.required_if' => 'A identidade do vendedor é obrigatória.',
            'vendedor.razao_social.required_if' => 'A razão social do vendedor é obrigatória.',
            'vendedor.cnpj.required_if' => 'O CNPJ do vendedor é obrigatório.',
            'vendedor.representante_legal_nome.required_if' => 'O representante legal do vendedor é obrigatório.',
            'vendedor.representante_legal_cargo.required_if' => 'O cargo do representante legal do vendedor é obrigatório.',
            'vendedor.representante_legal_cpf.required_if' => 'O CPF do representante legal do vendedor é obrigatório.',
            'comprador.nome.required_if' => 'O nome do comprador é obrigatório.',
            'comprador.tipo.required' => 'Selecione se o comprador é pessoa física ou jurídica.',
            'comprador.estado_civil.required_if' => 'O estado civil do comprador é obrigatório.',
            'comprador.profissao.required_if' => 'A profissão do comprador é obrigatória.',
            'comprador.cpf.required_if' => 'O CPF do comprador é obrigatório.',
            'comprador.identidade.required_if' => 'A identidade do comprador é obrigatória.',
            'comprador.razao_social.required_if' => 'A razão social do comprador é obrigatória.',
            'comprador.cnpj.required_if' => 'O CNPJ do comprador é obrigatório.',
            'comprador.representante_legal_nome.required_if' => 'O representante legal do comprador é obrigatório.',
            'comprador.representante_legal_cargo.required_if' => 'O cargo do representante legal do comprador é obrigatório.',
            'comprador.representante_legal_cpf.required_if' => 'O CPF do representante legal do comprador é obrigatório.',
            'comprador.endereco.required' => 'O endereço do comprador é obrigatório.',

            'contratado.tipo.required' => 'Selecione se o prestador é pessoa física ou jurídica.',
            'contratado.nome.required_if' => 'O nome do prestador é obrigatório.',
            'contratado.cpf.required_if' => 'O CPF do prestador é obrigatório.',
            'contratado.identidade.required_if' => 'A identidade do prestador é obrigatória.',
            'contratado.razao_social.required_if' => 'A razão social do prestador é obrigatória.',
            'contratado.cnpj.required_if' => 'O CNPJ do prestador é obrigatório.',
            'contratado.representante_legal_nome.required_if' => 'O representante legal do prestador é obrigatório.',
            'contratado.representante_legal_cargo.required_if' => 'O cargo do representante legal do prestador é obrigatório.',
            'contratado.representante_legal_cpf.required_if' => 'O CPF do representante legal do prestador é obrigatório.',
            'servico.descricao.required' => 'Descreva o serviço contratado.',
            'servico.data_inicio.required' => 'Informe a data de início dos serviços.',
            'servico.prazo.required' => 'Informe o prazo ou a vigência dos serviços.',
            'servico.valor.required' => 'Informe o valor dos serviços.',
            'servico.forma_pagamento.required' => 'Informe a forma de pagamento.',
            'servico.vencimento.required' => 'Informe o vencimento ou as datas de pagamento.',

            'data_termino.required_if' => 'Informe a data de término ou marque como prazo indeterminado.',
            'data_termino.after'       => 'A data de término deve ser posterior a hoje.',
            'locacao.data_termino.required_unless' => 'Informe o término da locação ou marque o prazo como indeterminado.',
            'locacao.data_termino.after' => 'O término da locação deve ser posterior ao início.',
        ];
    }

    /**
     * Confere se client_id realmente existe na tabela correspondente ao
     * client_type. Não dá pra fazer isso com Rule::exists() direto nas
     * rules() porque a tabela depende de outro campo do próprio request.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $clientType = $this->input('client_type');
            $clientId = $this->input('client_id');

            if (!$clientType || !$clientId) {
                return; // já cobertos pelas regras required acima
            }

            $client = Client::find($clientId);

            if (!$client) {
                $validator->errors()->add('client_id', 'Cliente selecionado não foi encontrado.');
            } elseif ($client->tipo !== $clientType) {
                $validator->errors()->add('client_id', 'O cliente selecionado não é do tipo informado.');
            }

            $imovelId = $this->input('imovel_id');
            if ($imovelId && $clientId) {
                $vinculado = DB::table('client_imovel')
                    ->where('imovel_id', $imovelId)
                    ->where('client_id', $clientId)
                    ->whereIn('papel', Imovel::PAPEIS_CONTRATANTE)
                    ->exists();

                if (!$vinculado) {
                    $validator->errors()->add('client_id', 'Este cliente não é proprietário nem vendedor do imóvel selecionado.');
                }
}
        });
    }
}
