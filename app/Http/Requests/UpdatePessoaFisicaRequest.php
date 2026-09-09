<?php

namespace App\Http\Requests;

use App\Models\PessoaFisica;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePessoaFisicaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:255'],
            'cpf' => ['required', 'string', 'size:11'],
            'rg' => ['nullable', 'string', 'max:20'],
            'data_nascimento' => ['required', 'date'],
            'estado_civil' => ['required', 'string', 'max:255'],
            'nacionalidade' => ['nullable', 'string', 'max:255'],
            'profissao' => ['nullable', 'string', 'max:255'],
            'telefone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255'],
            'endereco' => ['required', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $hash = hash('sha256', $this->cpf);
            $id = $this->route('pessoas_fisica')->id;

            $exists = PessoaFisica::where('cpf_hash', $hash)
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                $validator->errors()->add('cpf', 'Este CPF já está cadastrado.');
            }
        });
    }
}