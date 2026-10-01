<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Client;
use Illuminate\Validation\Validator;

abstract class BaseContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_type'   => 'required|string|in:pf,pj',
            'client_id'     => 'required|integer',
            'contract_type' => 'nullable|string|max:255',

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

            'contratado'          => 'required|array',
            'contratado.nome'     => 'required|string|max:255',
            'contratado.cpf_cnpj' => 'required|string|max:20',
            'contratado.endereco'  => 'required|string|max:255',
            'contratado.telefones' => 'nullable|string|max:100',
            'contratado.creci'     => 'required|string|max:50',

            'imovel'                          => 'required|array',
            'imovel.tipo'                      => 'required|string|max:255',
            'imovel.localizacao'               => 'required|string|max:255',
            'imovel.documento_propriedade'     => 'required|string|max:255',
            'imovel.matricula'                 => 'required|string|max:255',
            'imovel.inscricao_prefeitura'      => 'nullable|string|max:255',
            'imovel.preco_venda'               => 'required|string|max:100',
            'imovel.outros_dados'              => 'nullable|string',
            'imovel.condicoes_pagamento'       => 'nullable|string',

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

            'data_termino.required_if' => 'Informe a data de término ou marque como prazo indeterminado.',
            'data_termino.after'       => 'A data de término deve ser posterior a hoje.',
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
        });
    }
}