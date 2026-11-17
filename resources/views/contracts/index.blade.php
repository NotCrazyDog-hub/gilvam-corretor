@php
    $queryState = $queryState ?? [];
    $sortUrl = function (string $column) use ($queryState, $sort, $direction): string {
        $nextDirection = $sort === $column && $direction === 'asc' ? 'desc' : 'asc';

        return route('contracts.index', array_filter(
            array_merge($queryState, ['sort' => $column, 'direction' => $nextDirection]),
            fn ($value) => $value !== null && $value !== '',
        ));
    };
    $sortIndicator = fn (string $column) => $sort === $column ? ($direction === 'asc' ? '↑' : '↓') : '';
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
                <div class="mb-4 rounded bg-green-100 p-4 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="overflow-hidden rounded-lg bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-3 xl:flex-row xl:items-center">
                    <form method="GET" action="{{ route('contracts.index') }}"
                          class="flex min-w-0 flex-1 flex-col gap-3 lg:flex-row lg:items-center">
                        <input type="hidden" name="sort" value="{{ $sort }}">
                        <input type="hidden" name="direction" value="{{ $direction }}">

                        <label class="relative block min-w-0 flex-1">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z" />
                                </svg>
                            </span>
                            <input type="search" name="q" value="{{ $search }}" placeholder="Contratante, CPF/CNPJ, código ou endereço do imóvel"
                                   aria-label="Buscar contratos" x-on:input.debounce.400ms="$el.form.requestSubmit()"
                                   class="w-full rounded-full border-gray-300 py-2.5 pl-12 pr-5 text-sm focus:border-blue-500 focus:ring-blue-500">
                        </label>

                        <select name="contract_type" aria-label="Filtrar por tipo de contrato" onchange="this.form.submit()"
                                class="w-full shrink-0 rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500 lg:w-56">
                            <option value="">Todos os tipos</option>
                            @foreach (config('contracts.types') as $key => $tipoContrato)
                                <option value="{{ $key }}" @selected($contractType === $key)>{{ $tipoContrato['nome'] }}</option>
                            @endforeach
                        </select>

                        <select name="status" aria-label="Filtrar por situação" onchange="this.form.submit()"
                                class="w-full shrink-0 rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500 lg:w-52">
                            <option value="">Todas as situações</option>
                            @foreach ($situacoes as $opcaoSituacao)
                                <option value="{{ $opcaoSituacao }}" @selected($situacao === $opcaoSituacao)>{{ $opcaoSituacao }}</option>
                            @endforeach
                        </select>
                    </form>

                    <div x-data="{ open: false }" @click.outside="open = false" class="relative shrink-0 self-start xl:self-auto">
                        <button @click="open = !open" type="button" aria-haspopup="true" :aria-expanded="open"
                                class="flex items-center gap-2 rounded-lg bg-emerald-700 px-4 py-2 text-sm text-white transition hover:bg-emerald-800">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            Novo contrato
                            <svg :class="open ? 'rotate-180' : ''" class="h-4 w-4 transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" x-cloak x-transition class="absolute right-0 z-20 mt-2 max-h-[70vh] w-80 overflow-y-auto rounded-lg border border-slate-100 bg-white py-2 shadow-lg">
                            @foreach (config('contracts.types') as $key => $tipoContrato)
                                @php
                                    $pendente = empty($tipoContrato['form_supported'])
                                        || !\Illuminate\Support\Facades\Storage::disk(config('contracts.templates_disk'))
                                            ->exists(config('contracts.templates_dir') . '/' . $tipoContrato['template']);
                                @endphp

                                <a href="{{ route('contracts.create', ['contractType' => $key]) }}" class="group flex items-start gap-3 px-4 py-2 text-sm text-slate-700 transition hover:bg-gray-100">
                                    <svg class="mt-0.5 h-5 w-5 shrink-0 {{ $pendente ? 'text-amber-500' : 'text-slate-400 group-hover:text-green-600' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                    </svg>
                                    <span class="min-w-0">
                                        {{ $tipoContrato['nome'] }}
                                        @if ($pendente)
                                            <span class="mt-0.5 block text-xs text-amber-700">Modelo .docx ou formulário pendente</span>
                                        @endif
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                    <p class="text-sm text-slate-500">
                        {{ $groups->total() }} {{ \Illuminate\Support\Str::plural('contrato', $groups->total()) }}
                    </p>
                </div>

                <div class="mt-3 overflow-hidden rounded-lg border border-gray-200">
                    <table class="w-full table-fixed border-collapse text-left">
                        <colgroup>
                            <col class="w-[18%]">
                            <col class="w-[12%]">
                            <col class="w-[13%]">
                            <col class="w-[22%]">
                            <col class="w-[16%]">
                            <col class="w-[11%]">
                            <col class="w-[8%]">
                        </colgroup>
                        <thead>
                            <tr class="divide-x divide-gray-200 border-b border-gray-200 bg-gray-50">
                                @foreach ([
                                    'contract' => 'Contrato',
                                    'type' => 'Tipo',
                                    'property' => 'Imóvel',
                                    'status' => 'Situação / Validade',
                                    'generated' => 'Gerado em',
                                ] as $sortColumn => $label)
                                    <th scope="col" class="px-2 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 {{ $sortColumn === 'contract' ? 'text-left' : '' }}">
                                        <a href="{{ $sortUrl($sortColumn) }}" class="inline-flex items-center gap-1.5 hover:text-slate-900">
                                            {{ $label }}
                                            @if ($sortIndicator($sortColumn) !== '')
                                                <span aria-hidden="true">{{ $sortIndicator($sortColumn) }}</span>
                                            @endif
                                        </a>
                                    </th>
                                    @if ($sortColumn === 'contract')
                                        <th scope="col" class="px-2 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">Responsável</th>
                                    @endif
                                @endforeach
                                <th scope="col" class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">Ações</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @forelse ($groups as $rowIndex => $group)
                                @include('contracts._row', ['group' => $group, 'rowIndex' => $rowIndex])
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-10 text-center text-sm text-gray-500">
                                        @if ($search || $contractType || $situacao)
                                            Nenhum contrato corresponde aos filtros aplicados.
                                        @else
                                            Nenhum contrato encontrado.
                                        @endif
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
