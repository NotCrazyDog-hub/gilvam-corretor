<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Histórico — {{ $group->slug }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                {{-- Topo: Voltar + Editar --}}
                <div class="flex items-center justify-between mb-4 gap-4">
                    <a href="{{ route('contracts.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 hover:text-slate-700 rounded transition">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Voltar para a lista
                    </a>

                    <a href="{{ route('contracts.edit', $group) }}?from=versions"
                       class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm flex items-center gap-2 transition">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Editar (gerar nova versão)
                    </a>
                </div>

                {{-- Tabela --}}
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <table class="w-full text-center border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 divide-x divide-gray-200">
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase text-center">Versão</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase text-center">Contratante</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase text-center">Gerado em</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase text-center whitespace-nowrap">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($versions->sortByDesc('version')->values() as $index => $version)
                                <tr class="border-b border-gray-100 last:border-b-0 divide-x divide-gray-100 transition hover:bg-blue-50
                                           {{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50/60' }}">
                                    <td class="py-3 px-4 text-sm font-semibold text-gray-900 text-center whitespace-nowrap">
                                        Versão {{ $version->version }}
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-700 text-center truncate max-w-xs"
                                        title="{{ $version->contratante['nome'] ?? '' }}">
                                        {{ $version->contratante['nome'] ?? '—' }}
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-700 text-center whitespace-nowrap">
                                        {{ $version->created_at?->format('d/m/Y\, à\s H:i') ?? '—' }}
                                    </td>
                                    <td class="py-3 px-4 text-sm text-center whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5">
                                            {{-- Baixar arquivo --}}
                                            <a href="{{ route('contracts.download', ['group' => $group, 'version' => $version->version]) }}"
                                               title="Baixar arquivo" aria-label="Baixar arquivo"
                                               class="p-1.5 rounded-lg text-green-600 bg-green-50 hover:bg-green-100 hover:text-green-700 transition">
                                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                                </svg>
                                            </a>
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-gray-500">
                                        Nenhuma versão encontrada.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>