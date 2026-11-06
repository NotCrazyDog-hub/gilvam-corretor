@php
    $cores = [
        'Vigente'       => 'bg-green-100 text-green-700',
        'Vencendo'      => 'bg-yellow-100 text-yellow-700',
        'Vencido'       => 'bg-red-100 text-red-700',
        'Indeterminado' => 'bg-gray-100 text-gray-700',
    ];
    $filtroAtivo = in_array($clientType ?? null, ['pf', 'pj'], true) ? $clientType : null;
    $busca = $busca ?? '';
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Gestão de Contratos
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

                {{-- Abas + Busca + Botão Novo Contrato --}}
                <div class="flex items-center justify-between gap-4 mb-4">

                    {{-- Abas (mantêm o termo buscado) --}}
                    <div class="flex items-center gap-1 bg-slate-100 rounded-lg p-1">
                        <a href="{{ route('contracts.index', array_filter(['q' => $busca])) }}"
                           class="px-4 py-2 text-sm font-medium rounded-md transition
                                  {{ $filtroAtivo ? 'text-slate-500 hover:bg-slate-200 hover:text-slate-700' : 'bg-white text-blue-600 shadow-sm' }}">
                            Todos
                        </a>
                        <a href="{{ route('contracts.index', array_filter(['client_type' => 'pf', 'q' => $busca])) }}"
                           class="px-4 py-2 text-sm font-medium rounded-md transition
                                  {{ $filtroAtivo === 'pf' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:bg-slate-200 hover:text-slate-700' }}">
                            Pessoa física
                        </a>
                        <a href="{{ route('contracts.index', array_filter(['client_type' => 'pj', 'q' => $busca])) }}"
                           class="px-4 py-2 text-sm font-medium rounded-md transition
                                  {{ $filtroAtivo === 'pj' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:bg-slate-200 hover:text-slate-700' }}">
                            Pessoa jurídica
                        </a>
                    </div>

                    {{-- Busca --}}
                    <form method="GET" action="{{ route('contracts.index') }}" class="flex-1 max-w-2xl">
                        @if ($filtroAtivo)
                            <input type="hidden" name="client_type" value="{{ $filtroAtivo }}">
                        @endif
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-gray-400">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z" />
                                </svg>
                            </span>
                            <input type="search" name="q" value="{{ $busca }}" placeholder="Buscar por contratante, CPF ou CNPJ"
                                class="w-full rounded-full border-gray-300 text-sm pl-12 pr-5 py-2.5 focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </form>

                    {{-- Botão Novo Contrato: o menu lista os tipos de contrato. --}}
                    <div x-data="{ open: false }"
                         @click.outside="open = false"
                         class="relative py-1">
                            <button @click="open = !open"
                                    aria-haspopup="true" :aria-expanded="open"
                                    class="bg-emerald-700 hover:bg-emerald-800 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2 transition">
                                <svg class="w-4 h-4 block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                Novo contrato
                                <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="open" x-cloak x-transition
                                 class="absolute right-0 mt-2 w-80 max-h-[70vh] overflow-y-auto bg-white shadow-lg rounded-lg border border-slate-100 py-2 z-10">
                                @foreach (config('contracts.types') as $key => $tipoContrato)
                                    @php
                                        $pendente = empty($tipoContrato['form_supported'])
                                            || !\Illuminate\Support\Facades\Storage::disk(config('contracts.templates_disk'))
                                                ->exists(config('contracts.templates_dir') . '/' . $tipoContrato['template']);
                                    @endphp

                                    <a href="{{ route('contracts.create', ['contractType' => $key]) }}"
                                       class="group flex items-start gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-gray-100 transition">

                                        {{-- Ícone: documento com linhas de texto --}}
                                        <svg class="w-5 h-5 shrink-0 mt-0.5 {{ $pendente ? 'text-amber-500' : 'text-slate-400 group-hover:text-green-600' }} transition"
                                             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                        </svg>

                                        <span class="min-w-0">
                                            {{ $tipoContrato['nome'] }}
                                            @if ($pendente)
                                                <span class="block mt-0.5 text-xs text-amber-700">Modelo .docx ou formulário pendente</span>
                                            @endif
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                    </div>

                </div>

                {{-- Tabela --}}
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <table class="w-full text-center border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 divide-x divide-gray-200">
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase text-left">Contratante</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase text-center">Tipo de contrato</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase text-center">Versão vigente</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase text-center">Situação</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase text-center">Validade</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase text-center">Gerado em</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase text-center whitespace-nowrap">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($groups as $index => $group)
                                @php $v = $group->latestVersion; @endphp
                                <tr class="border-b border-gray-100 last:border-b-0 divide-x divide-gray-100 transition hover:bg-blue-50
                                           {{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50/60' }}">
                                    <td class="py-3 px-4 text-sm font-semibold text-gray-900 text-left truncate max-w-xs" title="{{ $group->client_display_name }}">
                                        {{ $group->client_display_name }}
                                    </td>
                                    <td class="py-3 px-4 text-sm text-center">
                                        {{ \App\Domains\Contracts\Services\ContractTypes::get($group->contract_type)['nome'] }}
                                    </td>
                                    <td class="py-3 px-4 text-sm text-center">
                                        Versão {{ $v?->version ?? '—' }}
                                    </td>
                                    <td class="py-3 px-4 text-sm text-center">
                                        @if ($v)
                                            <span class="inline-block px-2.5 py-0.5 text-xs font-bold rounded-full {{ $cores[$v->situacao] ?? 'bg-slate-100 text-slate-600' }}">
                                                {{ $v->situacao }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-sm text-center">
                                        @if ($v?->prazo_indeterminado)
                                            <span class="text-gray-700">Indeterminado</span>
                                        @elseif ($v?->data_termino)
                                            <div class="text-gray-700">{{ $v->data_termino->format('d/m/Y') }}</div>
                                            <div class="text-xs {{ str_contains($v->tempo_restante, 'vencido') ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                                                ({{ $v->tempo_restante }})
                                            </div>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-sm text-center whitespace-nowrap">
                                        {{ $v?->created_at?->format('d/m/Y\, à\s H:i') ?? '—' }}
                                    </td>
                                    <td class="py-3 px-4 text-sm text-center whitespace-nowrap">
                                        <span class="inline-flex items-center gap-3">
                                            {{-- Editar (papel com lápis) --}}
                                            <a href="{{ route('contracts.edit', $group) }}?from=index"
                                            title="Editar" aria-label="Editar"
                                            class="p-1.5 rounded-lg text-gray-600 hover:bg-gray-200 hover:text-gray-700 transition">
                                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                </svg>
                                            </a>

                                            {{-- Histórico (folha com relógio) --}}
                                            <a href="{{ route('contracts.versions', $group) }}"
                                            title="Histórico" aria-label="Histórico"
                                            class="p-1.5 rounded-lg text-green-600 hover:bg-green-100 hover:text-green-700 transition">
                                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 3H7a2 2 0 00-2 2v14a2 2 0 002 2h3M13 3l5 5v2M13 3v5h5" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 13a4 4 0 100 8 4 4 0 000-8zM17 15v2l1.5 1" />
                                                </svg>
                                            </a>
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-6 text-center text-gray-500">
                                        {{ $busca ? "Nenhum contrato encontrado para \"{$busca}\"." : 'Nenhum contrato encontrado.' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $groups->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
