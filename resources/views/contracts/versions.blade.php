@php
    $versaoAtual = $versions->max('version');
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Histórico — {{ $group->slug }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-6 shadow-sm">
                <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <a href="{{ route('contracts.index') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Voltar para a lista
                    </a>

                    <a href="{{ route('contracts.edit', $group) }}?from=versions"
                       class="inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-700 px-4 py-2 text-sm font-medium text-white transition hover:bg-emerald-800">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Editar (gerar nova versão)
                    </a>
                </div>

                <div class="overflow-hidden rounded-lg border border-gray-200">
                    <table class="w-full table-fixed border-collapse text-left">
                        <colgroup>
                            <col class="w-[16%]">
                            <col class="w-[42%]">
                            <col class="w-[25%]">
                            <col class="w-[17%]">
                        </colgroup>

                        <thead>
                            <tr class="divide-x divide-gray-200 border-b border-gray-200 bg-gray-50">
                                <th scope="col" class="px-3 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    Versão
                                </th>
                                <th scope="col" class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    Contratante
                                </th>
                                <th scope="col" class="px-3 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    Gerado em
                                </th>
                                <th scope="col" class="px-3 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    Ações
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @forelse ($versions->sortByDesc('version')->values() as $index => $version)
                                <tr class="divide-x divide-gray-100 border-b border-gray-100 transition hover:bg-blue-50
                                           {{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50/60' }}">
                                    <td class="px-3 py-3 text-center text-sm">
                                        <span class="font-semibold text-gray-900">
                                            Versão {{ $version->version }}
                                        </span>

                                        @if ($version->version === $versaoAtual)
                                            <span class="ml-1 text-xs font-semibold text-blue-700">Atual</span>
                                        @endif
                                    </td>

                                    <td class="break-words px-3 py-3 text-left text-sm font-medium text-gray-900"
                                        title="{{ $version->contratante['nome'] ?? '' }}">
                                        {{ $version->contratante['nome'] ?? '—' }}
                                    </td>

                                    <td class="px-3 py-3 text-center text-sm text-gray-700">
                                        @if ($version->created_at)
                                            <div>{{ $version->created_at->format('d/m/Y') }}</div>
                                            <div class="text-xs text-gray-500">
                                                às {{ $version->created_at->format('H:i') }}
                                            </div>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>

                                    <td class="px-3 py-3 text-center text-sm">
                                        <a href="{{ route('contracts.download', ['group' => $group, 'version' => $version->version]) }}"
                                           title="Baixar arquivo" aria-label="Baixar arquivo"
                                           class="inline-flex rounded-lg p-1.5 text-emerald-600 transition hover:bg-emerald-100 hover:text-emerald-700">
                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-10 text-center text-sm text-gray-500">
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