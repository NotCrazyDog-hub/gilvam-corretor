<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detalhes da Pessoa Jurídica
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <dl class="grid grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Razão Social</dt>
                        <dd class="text-gray-800">{{ $pessoas_juridica->razao_social }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nome Fantasia</dt>
                        <dd class="text-gray-800">{{ $pessoas_juridica->nome_fantasia ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">CNPJ</dt>
                        <dd class="text-gray-800">{{ $pessoas_juridica->cnpj_formatado }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Inscrição Estadual</dt>
                        <dd class="text-gray-800">{{ $pessoas_juridica->inscricao_estadual ?? '—' }}</dd>
                    </div>
                    <div class="col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Endereço</dt>
                        <dd class="text-gray-800">{{ $pessoas_juridica->endereco }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Representante Legal</dt>
                        <dd class="text-gray-800">{{ $pessoas_juridica->representante_legal_nome }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">CPF do Representante</dt>
                        <dd class="text-gray-800">{{ $pessoas_juridica->representante_legal_cpf_formatado }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Cargo</dt>
                        <dd class="text-gray-800">{{ $pessoas_juridica->representante_legal_cargo }}</dd>
                    </div>
                </dl>

                <div class="flex justify-end space-x-2 mt-6">
                    <a href="{{ route('pessoas-juridicas.index') }}"
                       class="px-4 py-2 rounded border border-gray-300 text-gray-700">
                        Voltar
                    </a>
                    <a href="{{ route('pessoas-juridicas.edit', $pessoas_juridica) }}"
                       class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">
                        Editar
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
