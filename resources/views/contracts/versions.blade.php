@php
    $versaoAtual = $versions->max('version');
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Histórico — {{ $group->slug }}
        </h2>
    </x-slot>

    <div class="py-12"
         x-data="{
            previewOpen: false,
            previewLoading: false,
            previewHtml: '',
            previewError: '',
            previewVersion: '',
            async openPreview(url, version) {
                this.previewOpen = true;
                this.previewLoading = true;
                this.previewHtml = '';
                this.previewError = '';
                this.previewVersion = version;
                try {
                    const response = await fetch(url, {
                        credentials: 'same-origin',
                        headers: { Accept: 'text/html', 'X-Requested-With': 'XMLHttpRequest' },
                    });
                    if (!response.ok) throw new Error();
                    this.previewHtml = await response.text();
                } catch (error) {
                    this.previewError = 'Não foi possível carregar a pré-visualização deste contrato.';
                } finally {
                    this.previewLoading = false;
                }
            },
            closePreview() {
                this.previewOpen = false;
                this.previewHtml = '';
                this.previewError = '';
            }
         }"
         @keydown.escape.window="closePreview()"
         x-effect="document.body.classList.toggle('overflow-hidden', previewOpen)">
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
                                        <button type="button"
                                                title="Visualizar contrato"
                                                aria-label="Visualizar contrato, versão {{ $version->version }}"
                                                data-preview-url="{{ route('contracts.show', ['group' => $group, 'version' => $version->version]) }}"
                                                data-preview-version="{{ $version->version }}"
                                                @click="openPreview($el.dataset.previewUrl, $el.dataset.previewVersion)"
                                                class="inline-flex rounded-lg p-1.5 text-blue-600 transition hover:bg-blue-100 hover:text-blue-700">
                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M2.25 12s3.5-7 9.75-7 9.75 7 9.75 7-3.5 7-9.75 7-9.75-7-9.75-7z" />
                                                <circle cx="12" cy="12" r="3" />
                                            </svg>
                                        </button>
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

        <div x-cloak x-show="previewOpen" x-transition.opacity
             class="fixed inset-0 z-[80] flex items-center justify-center bg-slate-900/60 p-3 sm:p-6"
             @click.self="closePreview()" role="presentation">
            <section x-show="previewOpen" x-transition
                     class="relative flex max-h-[92vh] w-full max-w-5xl flex-col overflow-hidden rounded-xl bg-white shadow-2xl"
                     role="dialog" aria-modal="true" aria-labelledby="contract-preview-title">
                <header class="flex shrink-0 items-center justify-between border-b border-slate-200 px-5 py-4 sm:px-7">
                    <div>
                        <h2 id="contract-preview-title" class="text-lg font-semibold text-slate-900">Pré-visualização do contrato</h2>
                        <p class="mt-0.5 text-sm text-slate-500">Versão <span x-text="previewVersion"></span></p>
                    </div>
                    <button type="button" @click="closePreview()" title="Fechar" aria-label="Fechar pré-visualização"
                            class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </header>
                <div class="min-h-0 overflow-y-auto bg-slate-100 p-3 sm:p-7">
                    <p x-show="previewLoading" class="py-16 text-center text-sm text-slate-600">Carregando contrato…</p>
                    <p x-show="previewError" x-text="previewError" class="py-16 text-center text-sm text-red-700"></p>
                    <div x-show="!previewLoading && !previewError" x-html="previewHtml"></div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
