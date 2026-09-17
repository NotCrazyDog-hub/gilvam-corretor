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
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <div class="flex justify-end mb-4">
                    <a href="{{ route('pessoas-fisicas.create') }}"
                    class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700 transition">
                        + Nova Pessoa Física
                    </a>
                </div>

                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <table class="w-full text-center border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 divide-x divide-gray-200">
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase text-center">Nome</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase text-center">CPF</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase text-center">Telefone</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase text-center">Email</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase text-center whitespace-nowrap">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pessoasFisicas as $index => $pessoa)
                                <tr class="border-b border-gray-100 last:border-b-0 divide-x-20 divide-gray-100 hover:bg-blue-50/40 transition
                                           {{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50/60' }}">
                                    <td class="py-3 px-4 text-sm font-semibold text-gray-900 text-center">{{ $pessoa->nome }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-700 text-center">{{ $pessoa->cpf_formatado }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-700 text-center">{{ $pessoa->telefone_formatado }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-700 text-center">{{ $pessoa->email }}</td>
                                    <td class="py-3 px-4 text-sm text-center whitespace-nowrap">
                                        <span class="inline-flex items-center divide-x divide-gray-300">
                                            <a href="{{ route('pessoas-fisicas.show', $pessoa) }}"
                                               class="text-blue-600 hover:underline px-2">Ver</a>
                                            <a href="{{ route('pessoas-fisicas.edit', $pessoa) }}"
                                               class="text-yellow-600 hover:underline px-2">Editar</a>
                                            <form action="{{ route('pessoas-fisicas.destroy', $pessoa) }}"
                                                  method="POST" class="inline px-2"
                                                  onsubmit="return confirm('Tem certeza que deseja remover?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline">
                                                    Excluir
                                                </button>
                                            </form>
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-gray-500">
                                        Nenhuma pessoa física cadastrada.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $pessoasFisicas->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
