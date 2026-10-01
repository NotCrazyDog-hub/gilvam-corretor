@php
    $coresTipo = [
        'pf' => 'bg-blue-100 text-blue-700',
        'pj' => 'bg-green-100 text-green-700',
    ];
    $filtroAtivo = in_array($tipo ?? null, ['pf', 'pj'], true) ? $tipo : null;
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Gestão de Clientes
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                {{-- Abas + Busca + Botão Novo Cliente --}}
                <div class="flex items-center justify-between mb-4 gap-4">

                    {{-- Abas --}}
                    <div class="flex items-center gap-1 bg-slate-100 rounded-lg p-1">
                        <a href="{{ route('clients.index', array_filter(['q' => $busca])) }}"
                           class="px-4 py-2 text-sm font-medium rounded-md transition
                                  {{ $filtroAtivo ? 'text-slate-500 hover:text-slate-700' : 'bg-white text-brand-blue shadow-sm' }}">
                            Todos
                        </a>
                        <a href="{{ route('clients.index', array_filter(['tipo' => 'pf', 'q' => $busca])) }}"
                           class="px-4 py-2 text-sm font-medium rounded-md transition
                                  {{ $filtroAtivo === 'pf' ? 'bg-white text-brand-red shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                            Pessoa física
                        </a>
                        <a href="{{ route('clients.index', array_filter(['tipo' => 'pj', 'q' => $busca])) }}"
                           class="px-4 py-2 text-sm font-medium rounded-md transition
                                  {{ $filtroAtivo === 'pj' ? 'bg-white text-brand-red shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                            Pessoa jurídica
                        </a>
                    </div>

                    {{-- Busca --}}
                    <form method="GET" action="{{ route('clients.index') }}" class="flex-1 max-w-sm">
                        @if ($filtroAtivo)
                            <input type="hidden" name="tipo" value="{{ $filtroAtivo }}">
                        @endif
                        <input type="search" name="q" value="{{ $busca }}" placeholder="Buscar por nome, CPF ou CNPJ"
                               class="w-full rounded border-gray-300 text-sm">
                    </form>

                    {{-- Botão Novo Cliente --}}
                    <div x-data="{ open: false }" @click.outside="open = false" class="relative py-1">
                        <button @click="open = !open"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm flex items-center gap-2 transition">
                            <svg class="w-4 h-4 block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            Novo cliente
                            <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" x-cloak x-transition
                            class="absolute right-0 mt-2 w-56 bg-white shadow-lg rounded-lg border border-slate-100 py-2 z-10">
                            <a href="{{ route('clients.create', ['tipo' => 'pf']) }}"
                               class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                <svg class="w-4 h-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                                Cliente Pessoa física
                            </a>
                            <a href="{{ route('clients.create', ['tipo' => 'pj']) }}"
                               class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                <svg class="w-4 h-4 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4M9 9h1m-1 4h1m-1 4h1" />
                                </svg>
                                Cliente Pessoa jurídica
                            </a>
                        </div>
                    </div>

                </div>

                {{-- Tabela --}}
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <table class="w-full text-center border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 divide-x divide-gray-200">
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase text-center">Nome / Razão Social</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase text-center">Tipo</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase text-center">Documento</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase text-center">Contato</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase text-center whitespace-nowrap">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($clients as $index => $client)
                                <tr class="border-b border-gray-100 last:border-b-0 divide-x divide-gray-100 hover:bg-blue-50/40 transition
                                           {{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50/60' }}">
                                    <td class="py-3 px-4 text-sm font-semibold text-gray-900 text-center truncate max-w-xs" title="{{ $client->nome }}">
                                        {{ $client->nome }}
                                    </td>
                                    <td class="py-3 px-4 text-sm text-center">
                                        <span class="inline-block px-2.5 py-0.5 text-xs font-bold rounded-full {{ $coresTipo[$client->tipo] ?? 'bg-slate-100 text-slate-600' }}">
                                            {{ $client->isPj() ? 'PJ' : 'PF' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-sm text-center whitespace-nowrap">
                                        {{ $client->documento_formatado ?? '—' }}
                                    </td>
                                    <td class="py-3 px-4 text-sm text-center">
                                        {{ $client->isPj() ? ($client->representante_legal_nome ?? '—') : ($client->telefone_formatado ?? '—') }}
                                    </td>
                                    <td class="py-3 px-4 text-sm text-center whitespace-nowrap">
                                        <span class="inline-flex items-center divide-x divide-gray-300">
                                            <a href="{{ route('clients.show', $client) }}"
                                               class="text-blue-600 hover:underline px-2">Ver</a>
                                            <a href="{{ route('clients.edit', $client) }}"
                                               class="text-yellow-600 hover:underline px-2">Editar</a>
                                            <form action="{{ route('clients.destroy', $client) }}" method="POST" class="inline-flex px-2"
                                                  onsubmit="return confirm('Tem certeza que deseja remover?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline">Excluir</button>
                                            </form>
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-gray-500">
                                        Nenhum cliente encontrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $clients->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>