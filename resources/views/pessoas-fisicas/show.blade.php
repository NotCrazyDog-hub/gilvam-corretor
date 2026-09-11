<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detalhes da Pessoa Física
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <dl class="grid grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nome</dt>
                        <dd class="text-gray-800">{{ $pessoas_fisica->nome }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">CPF</dt>
                        <dd class="text-gray-800">{{ $pessoas_fisica->cpf_formatado }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">RG</dt>
                        <dd class="text-gray-800">{{ $pessoas_fisica->rg ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Data de Nascimento</dt>
                        <dd class="text-gray-800">{{ \Carbon\Carbon::parse($pessoas_fisica->data_nascimento)->format('d/m/Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Estado Civil</dt>
                        <dd class="text-gray-800">{{ $pessoas_fisica->estado_civil }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nacionalidade</dt>
                        <dd class="text-gray-800">{{ $pessoas_fisica->nacionalidade ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Profissão</dt>
                        <dd class="text-gray-800">{{ $pessoas_fisica->profissao ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Telefone</dt>
                        <dd class="text-gray-800">{{ $pessoas_fisica->telefone_formatado }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="text-gray-800">{{ $pessoas_fisica->email }}</dd>
                    </div>
                    <div class="col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Endereço</dt>
                        <dd class="text-gray-800">{{ $pessoas_fisica->endereco }}</dd>
                    </div>
                </dl>

                <div class="flex justify-end space-x-2 mt-6">
                    <a href="{{ route('pessoas-fisicas.index') }}"
                       class="px-4 py-2 rounded border border-gray-300 text-gray-700">
                        Voltar
                    </a>
                    <a href="{{ route('pessoas-fisicas.edit', $pessoas_fisica) }}"
                       class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">
                        Editar
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
