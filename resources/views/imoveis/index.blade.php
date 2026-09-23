<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Imóveis
            </h2>
            <a href="{{ route('imoveis.create') }}"
               class="bg-blue-600 text-white shadow-sm rounded-lg px-4 py-2 flex items-center gap-2 text-sm font-medium hover:bg-blue-700 transition">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Novo Imóvel
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('sucesso'))
                <div class="p-4 bg-green-100 text-green-800 rounded border border-green-300 text-sm">
                    {{ session('sucesso') }}
                </div>
            @endif

            {{-- Busca e filtro --}}
            <div class="bg-white shadow-sm rounded-lg p-4">
                <form method="GET" action="{{ route('imoveis.index') }}" class="flex flex-col sm:flex-row gap-3">
                    <input type="text" name="busca" value="{{ request('busca') }}"
                           placeholder="Buscar por endereço, bairro, matrícula, proprietário..."
                           class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">

                    <select name="tipo" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">Todos os tipos</option>
                        @foreach ($tipos as $valor => $label)
                            <option value="{{ $valor }}" @selected(request('tipo') === $valor)>{{ $label }}</option>
                        @endforeach
                    </select>

                    <button type="submit"
                            class="bg-gray-800 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-900 transition">
                        Filtrar
                    </button>

                    @if (request('busca') || request('tipo'))
                        <a href="{{ route('imoveis.index') }}"
                           class="px-4 py-2 text-sm text-gray-500 hover:text-gray-800 flex items-center">
                            Limpar
                        </a>
                    @endif
                </form>
            </div>

            {{-- Listagem --}}
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                @if ($imoveis->isEmpty())
                    <p class="p-6 text-sm text-gray-400">Nenhum imóvel cadastrado ainda.</p>
                @else
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                            <tr>
                                <th class="text-left px-4 py-3">Tipo</th>
                                <th class="text-left px-4 py-3">Endereço</th>
                                <th class="text-left px-4 py-3">Matrícula</th>
                                <th class="text-left px-4 py-3">Proprietário</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($imoveis as $imovel)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <span class="inline-block px-2 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-medium">
                                            {{ $imovel->tipo_label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">{{ $imovel->endereco_completo }}</td>
                                    <td class="px-4 py-3 text-gray-500">{{ $imovel->numero_matricula ?: '—' }}</td>
                                    <td class="px-4 py-3 text-gray-500">{{ $imovel->proprietario_nome ?: '—' }}</td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <a href="{{ route('imoveis.show', $imovel) }}"
                                           class="text-xs text-blue-600 hover:underline mr-3">Ver</a>
                                        <a href="{{ route('imoveis.edit', $imovel) }}"
                                           class="text-xs text-gray-600 hover:underline mr-3">Editar</a>
                                        <form action="{{ route('imoveis.destroy', $imovel) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Tem certeza que deseja remover este imóvel?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-red-600 hover:underline">Excluir</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="p-4 border-t border-gray-100">
                        {{ $imoveis->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
