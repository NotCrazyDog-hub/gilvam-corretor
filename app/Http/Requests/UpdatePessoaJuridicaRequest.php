<?php

namespace App\Http\Requests;

use App\Models\PessoaJuridica;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePessoaJuridicaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('pessoas_juridica')->id;

        return [
            'razao_social' => ['required', 'string', 'max:255'],
            'nome_fantasia' => ['nullable', 'string', 'max:255'],
            'cnpj' => ['required', 'string', 'size:14', 'unique:pessoas_juridicas,cnpj,' . $id],
            'inscricao_estadual' => ['nullable', 'string', 'max:255'],
            'endereco' => ['required', 'string', 'max:255'],
            'representante_legal_nome' => ['required', 'string', 'max:255'],
            'representante_legal_cpf' => ['required', 'string', 'size:11'],
            'representante_legal_cargo' => ['required', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $hash = hash('sha256', $this->representante_legal_cpf);
            $id = $this->route('pessoas_juridica')->id;

            $exists = PessoaJuridica::where('representante_legal_cpf_hash', $hash)
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                $validator->errors()->add(
                    'representante_legal_cpf',
                    'Este CPF já está cadastrado como representante legal.'
                );
            }
        });
    }
}