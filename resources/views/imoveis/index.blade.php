<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Imóveis
        </h2>
    </x-slot>

    <div class="py-12 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                {{-- Busca + filtro + Novo imóvel --}}
                <form method="GET" action="{{ route('imoveis.index') }}"
                      class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 mb-4">

                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-gray-400">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z" />
                            </svg>
                        </span>
                        <input type="search" name="busca" value="{{ request('busca') }}"
                               placeholder="Buscar por endereço, bairro, matrícula ou proprietário"
                               class="w-full rounded-full border-gray-300 text-sm pl-12 pr-5 py-2.5 focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <select name="tipo" onchange="this.form.submit()"
                            class="rounded-full border-gray-300 text-sm pl-5 pr-10 py-2.5 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Todos os tipos</option>
                        @foreach ($tipos as $valor => $label)
                            <option value="{{ $valor }}" @selected(request('tipo') === $valor)>{{ $label }}</option>
                        @endforeach
                    </select>

                    @if (request('busca') || request('tipo'))
                        <a href="{{ route('imoveis.index') }}"
                           class="text-sm text-gray-500 hover:text-gray-800 whitespace-nowrap">
                            Limpar
                        </a>
                    @endif

                    <a href="{{ route('imoveis.create') }}"
                       class="bg-emerald-700 hover:bg-emerald-800 text-white px-4 py-2.5 rounded-lg text-sm flex items-center justify-center gap-2 transition whitespace-nowrap">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Novo imóvel
                    </a>
                </form>

                {{-- Tabela --}}
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <table class="w-full text-center border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 divide-x divide-gray-200">
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Foto</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Tipo</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase text-left">Endereço</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Matrícula</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase text-left">Proprietário</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($imoveis as $index => $imovel)
                                {{-- Linha inteira clicável: leva ao show do imóvel --}}
                                <tr class="border-b border-gray-100 last:border-b-0 divide-x divide-gray-100 transition hover:bg-blue-50 cursor-pointer
                                           {{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50/60' }}"
                                    onclick="window.location='{{ route('imoveis.show', $imovel) }}'">

                                    <td class="py-3 px-4">
                                        <div class="flex justify-center">
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
                                        </div>
                                    </td>

                                    <td class="py-3 px-4 text-sm text-gray-700">
                                        {{ $imovel->tipo_label }}
                                    </td>

                                    <td class="py-3 px-4 text-sm font-semibold text-gray-900 text-left">
                                        <a href="{{ route('imoveis.show', $imovel) }}" class="hover:underline">
                                            {{ $imovel->endereco_completo }}
                                        </a>
                                    </td>

                                    <td class="py-3 px-4 text-sm text-gray-700 whitespace-nowrap">
                                        {{ $imovel->numero_matricula ?: '—' }}
                                    </td>

                                    <td class="py-3 px-4 text-sm text-gray-700 text-left">
                                        {{ $imovel->proprietario_nome ?: '—' }}
                                    </td>

                                    {{-- stopPropagation: cliques nos botões não disparam o clique da linha --}}
                                    <td class="py-3 px-4 whitespace-nowrap" onclick="event.stopPropagation()">
                                        <span class="inline-flex items-center gap-3">
                                            <button type="button" x-data
                                                x-on:click="$dispatch('abrir-modal', { url: '{{ route('imoveis.show', $imovel) }}' })"
                                                title="Ver" aria-label="Ver"
                                                class="p-1.5 rounded-lg text-blue-600 hover:bg-blue-100 hover:text-blue-700 transition">
                                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>

                                            <a href="{{ route('imoveis.pdf', $imovel) }}"
                                               title="Baixar PDF" aria-label="Baixar PDF"
                                               style="color: #047857;"
                                               class="p-1.5 rounded-lg hover:bg-green-100 transition">
                                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4" />
                                                </svg>
                                            </a>

                                            <a href="{{ route('imoveis.edit', $imovel) }}"
                                               title="Editar" aria-label="Editar"
                                               class="p-1.5 rounded-lg text-gray-600 hover:bg-gray-200 hover:text-gray-700 transition">
                                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                </svg>
                                            </a>

                                            <form action="{{ route('imoveis.destroy', $imovel) }}" method="POST" class="inline-flex"
                                                  onsubmit="return confirm('Tem certeza que deseja remover este imóvel?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        title="Excluir" aria-label="Excluir"
                                                        class="p-1.5 rounded-lg text-red-600 hover:bg-red-100 hover:text-red-700 transition">
                                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-6 text-center text-sm text-gray-500">
                                        Nenhum imóvel encontrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $imoveis->links() }}
                </div>

            </div>
        </div>
    </div>

    <x-modal-ajax />
</x-app-layout>
