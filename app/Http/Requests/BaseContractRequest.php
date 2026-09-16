<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
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
            'contratante.nome'         => 'required|string|max:255',
            'contratante.cpf'          => 'required|string|max:20',
            'contratante.estado_civil' => 'nullable|string|max:100',
            'contratante.conjuge_nome' => 'nullable|string|max:255',
            'contratante.conjuge_cpf'  => 'nullable|string|max:20',
            'contratante.endereco'     => 'nullable|string|max:255',
            'contratante.telefones'    => 'nullable|string|max:100',
            'contratante.profissao'    => 'nullable|string|max:255',
            'contratante.identidade'   => 'nullable|string|max:50',
            'contratante.conjuge_identidade' => 'nullable|string|max:50',

            'contratado'          => 'required|array',
            'contratado.nome'     => 'required|string|max:255',
            'contratado.cpf_cnpj' => 'required|string|max:20',
            'contratado.endereco'  => 'nullable|string|max:255',
            'contratado.telefones' => 'nullable|string|max:100',
            'contratado.creci'     => 'nullable|string|max:50',

            'imovel'                          => 'required|array',
            'imovel.tipo'                      => 'nullable|string|max:255',
            'imovel.localizacao'               => 'nullable|string|max:255',
            'imovel.documento_propriedade'     => 'nullable|string|max:255',
            'imovel.matricula'                 => 'nullable|string|max:255',
            'imovel.inscricao_prefeitura'      => 'nullable|string|max:255',
            'imovel.preco_venda'               => 'nullable|string|max:100',
            'imovel.outros_dados'              => 'nullable|string',
            'imovel.condicoes_pagamento'       => 'nullable|string',

            'content_json' => 'nullable|array',
            'content_html' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'client_type.required' => 'Selecione o tipo de cliente.',
            'client_type.in'       => 'Tipo de cliente inválido.',
            'client_id.required'   => 'Selecione um cliente cadastrado.',
            'contratante.nome.required'     => 'O nome do contratante é obrigatório.',
            'contratante.cpf.required'      => 'O CPF do contratante é obrigatório.',
            'contratado.nome.required'      => 'O nome do contratado é obrigatório.',
            'contratado.cpf_cnpj.required'  => 'O CPF/CNPJ do contratado é obrigatório.',
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
                return; // já barrado pelas rules 'required' acima
            }

            $table = $clientType === 'pf' ? 'pessoas_fisicas' : 'pessoas_juridicas';

            if (!DB::table($table)->where('id', $clientId)->exists()) {
                $validator->errors()->add('client_id', 'Cliente selecionado não foi encontrado.');
            }
        });
    }
}