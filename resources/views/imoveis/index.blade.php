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

                    {{-- Busca com lupa --}}
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-gray-400">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z" />
                            </svg>
                        </span>
                        <input type="text" name="busca" value="{{ request('busca') }}"
                               placeholder="Buscar por endereço, bairro, matrícula, proprietário..."
                               class="w-full rounded-full border-gray-300 text-sm pl-12 pr-5 py-2.5 focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <select name="tipo"
                            class="rounded-full border-gray-300 text-sm pl-5 pr-10 py-2.5 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Todos os tipos</option>
                        @foreach ($tipos as $valor => $label)
                            <option value="{{ $valor }}" @selected(request('tipo') === $valor)>{{ $label }}</option>
                        @endforeach
                    </select>

                    <button type="submit"
                            class="bg-gray-800 text-white px-5 py-2.5 rounded-full text-sm font-medium hover:bg-gray-900 transition">
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
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Foto</th>
                                <th class="text-left px-4 py-3">Tipo</th>
                                <th class="text-left px-4 py-3">Endereço</th>
                                <th class="text-left px-4 py-3">Matrícula</th>
                                <th class="text-left px-4 py-3">Proprietário</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($imoveis as $imovel)
                                {{-- Linha inteira clicável: leva ao show do imóvel --}}
                                <tr class="hover:bg-gray-50 cursor-pointer"
                                    onclick="window.location='{{ route('imoveis.show', $imovel) }}'">
                                    <td class="px-4 py-3">
                                        @if ($imovel->imagemPrincipal)
                                            <img src="{{ $imovel->imagemPrincipal->url }}"
                                                 alt="Foto do imóvel"
                                                 class="w-28 h-20 object-cover rounded-lg border border-gray-200">
                                        @else
                                            <div class="w-28 h-20 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-400">
                                                <svg class="w-7 h-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.41a2.25 2.25 0 013.182 0l2.909 2.91M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-block px-2 py-1 rounded-full  text-gray-700 text-xs font-medium hover:underline">
                                            {{ $imovel->tipo_label }}
                                        </span>
                                    </td>
                                    {{-- Link real no endereço: teclado, leitor de tela e Ctrl+clique --}}
                                    <td class="px-4 py-3 text-gray-700">
                                        <a href="{{ route('imoveis.show', $imovel) }}" class="hover:underline">
                                            {{ $imovel->endereco_completo }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500">{{ $imovel->numero_matricula ?: '—' }}</td>
                                    <td class="px-4 py-3 text-gray-500">{{ $imovel->proprietario_nome ?: '—' }}</td>
                                    {{-- stopPropagation: cliques nos botões não disparam o clique da linha --}}
                                    <td class="px-4 py-3 text-right whitespace-nowrap" onclick="event.stopPropagation()">
                                        <span class="inline-flex items-center gap-1.5">
                                            {{-- Ver --}}
                                            <a href="{{ route('imoveis.show', $imovel) }}"
                                               title="Ver" aria-label="Ver"
                                               class="p-1.5 rounded-lg text-sky-600 bg-sky-50 hover:bg-sky-100 hover:text-sky-700 transition">
                                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>

                                            {{-- Editar (papel com lápis) --}}
                                            <a href="{{ route('imoveis.edit', $imovel) }}"
                                               title="Editar" aria-label="Editar"
                                               class="p-1.5 rounded-lg text-amber-600 bg-amber-50 hover:bg-amber-100 hover:text-amber-700 transition">
                                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                </svg>
                                            </a>

                                            {{-- Excluir --}}
                                            <form action="{{ route('imoveis.destroy', $imovel) }}" method="POST" class="inline-flex"
                                                  onsubmit="return confirm('Tem certeza que deseja remover este imóvel?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        title="Excluir" aria-label="Excluir"
                                                        class="p-1.5 rounded-lg text-rose-600 bg-rose-50 hover:bg-rose-100 hover:text-rose-700 transition">
                                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </span>
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
