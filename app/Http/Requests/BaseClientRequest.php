<?php

namespace App\Http\Requests;

use App\Models\Client;
use App\Support\DocumentHash;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

abstract class BaseClientRequest extends FormRequest
{
    abstract protected function tipo(): ?string;

    abstract protected function ignoredClient(): ?Client;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];

        foreach (['cpf', 'cnpj', 'representante_legal_cpf'] as $campo) {
            if ($this->filled($campo)) {
                $merge[$campo] = preg_replace('/\D/', '', (string) $this->input($campo));
            }
        }

        // Sem cônjuge se não for casado(a) ou em união estável
        if ($this->tipo() === Client::TIPO_PF
            && !in_array($this->input('estado_civil'), ['Casado(a)', 'União estável'])) {
            $merge['conjuge_nome'] = null;
            $merge['conjuge_rg']   = null;
        }

        $this->merge($merge);
    }

    public function rules(): array
    {
        $ignoreId = $this->ignoredClient()?->id;

        return match ($this->tipo()) {
            Client::TIPO_PF => [
                'nome'                => ['required', 'string', 'max:255'],
                'cpf'                 => ['required', 'string', 'size:11'],
                'rg'                  => ['nullable', 'string', 'max:20'],
                'data_nascimento'     => ['required', 'date'],
                'estado_civil'        => ['required', 'string', 'max:255'],
                'conjuge_nome'        => ['nullable', 'string', 'max:255'],
                'conjuge_rg'          => ['nullable', 'string', 'max:20'],
                'nacionalidade'       => ['nullable', 'string', 'max:255'],
                'profissao'           => ['nullable', 'string', 'max:255'],
                'telefone'            => ['required', 'string', 'max:20'],
                'telefone_secundario' => ['nullable', 'string', 'max:20'],
                'email'               => ['required', 'email', 'max:255'],
                'endereco'            => ['required', 'string', 'max:255'],
            ],
            Client::TIPO_PJ => [
                'nome'                      => ['required', 'string', 'max:255'], // razão social
                'nome_fantasia'             => ['nullable', 'string', 'max:255'],
                'cnpj'                      => ['required', 'string', 'size:14',
                                                Rule::unique('clients', 'cnpj')->ignore($ignoreId)],
                'inscricao_estadual'        => ['nullable', 'string', 'max:255'],
                'endereco'                  => ['required', 'string', 'max:255'],
                'representante_legal_nome'  => ['required', 'string', 'max:255'],
                'representante_legal_cpf'   => ['required', 'string', 'size:11'],
                'representante_legal_cargo' => ['required', 'string', 'max:255'],

                'telefone'                  => ['nullable', 'string', 'max:20'],
                'telefone_secundario'       => ['nullable', 'string', 'max:20'],
                'email'                     => ['nullable', 'email', 'max:255'],
            ],
            default => [],
        };
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $ignoreId = $this->ignoredClient()?->id;

            if ($this->tipo() === Client::TIPO_PF && !$validator->errors()->has('cpf')) {
                $exists = Client::where('cpf_hash', DocumentHash::make($this->input('cpf')))
                    ->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))
                    ->exists();

                if ($exists) {
                    $validator->errors()->add('cpf', 'Este CPF já está cadastrado.');
                }
            }

            // Regra herdada do sistema antigo: o CPF do representante não pode repetir.
            if ($this->tipo() === Client::TIPO_PJ && !$validator->errors()->has('representante_legal_cpf')) {
                $exists = Client::where('representante_legal_cpf_hash', DocumentHash::make($this->input('representante_legal_cpf')))
                    ->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))
                    ->exists();

                if ($exists) {
                    $validator->errors()->add(
                        'representante_legal_cpf',
                        'Este CPF já está cadastrado como representante legal.'
                    );
                }
            }
        });
    }
}