<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Pessoas Físicas
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
                <p>
                    Gilvam Vieira Corretoria

                </p>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <div class="flex justify-end mb-4">
                    <a href="{{ route('pessoas-fisicas.create') }}"
                       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        + Nova Pessoa Física
                    </a>
                </div>

                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b">
                            <th class="py-2">Nome</th>
                            <th class="py-2">CPF</th>
                            <th class="py-2">Telefone</th>
                            <th class="py-2">Email</th>
                            <th class="py-2 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pessoasFisicas as $pessoa)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-2">{{ $pessoa->nome }}</td>
                                <td class="py-2">{{ $pessoa->cpf_formatado }}</td>
                                <td class="py-2">{{ $pessoa->telefone_formatado }}</td>
                                <td class="py-2">{{ $pessoa->email }}</td>
                                <td class="py-2 text-right space-x-2">
                                    <a href="{{ route('pessoas-fisicas.show', $pessoa) }}"
                                       class="text-blue-600 hover:underline">Ver</a>
                                    <a href="{{ route('pessoas-fisicas.edit', $pessoa) }}"
                                       class="text-yellow-600 hover:underline">Editar</a>
                                    <form action="{{ route('pessoas-fisicas.destroy', $pessoa) }}"
                                          method="POST" class="inline"
                                          onsubmit="return confirm('Tem certeza que deseja remover?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">
                                            Excluir
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 text-center text-gray-500">
                                    Nenhuma pessoa física cadastrada.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $pessoasFisicas->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
