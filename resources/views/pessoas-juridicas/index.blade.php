<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Pessoas Jurídicas
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Botão sempre visível, independente de ter registros --}}
                <div class="flex justify-end mb-4">
                    <a href="{{ route('pessoas-juridicas.create') }}"
                       class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm">
                        + Nova Pessoa Jurídica
                    </a>
                </div>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Razão Social</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nome Fantasia</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">CNPJ</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($pessoasJuridicas as $pessoa)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $pessoa->razao_social }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $pessoa->nome_fantasia ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $pessoa->cnpj_formatado }}</td>
                                <td class="px-4 py-3 text-sm text-right space-x-2">
                                    <a href="{{ route('pessoas-juridicas.show', $pessoa) }}"
                                       class="text-blue-600 hover:underline">Ver</a>
                                    <a href="{{ route('pessoas-juridicas.edit', $pessoa) }}"
                                       class="text-yellow-600 hover:underline">Editar</a>
                                    <form action="{{ route('pessoas-juridicas.destroy', $pessoa) }}"
                                          method="POST" class="inline"
                                          onsubmit="return confirm('Tem certeza que deseja excluir?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-400">
                                    Nenhuma pessoa jurídica cadastrada ainda.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $pessoasJuridicas->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
