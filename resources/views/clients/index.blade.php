@php
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
                                  {{ $filtroAtivo ? 'text-slate-500 hover:bg-slate-200 hover:text-slate-700' : 'bg-white text-green-600 shadow-sm' }}">
                            Todos
                        </a>
                        <a href="{{ route('clients.index', array_filter(['tipo' => 'pf', 'q' => $busca])) }}"
                           class="px-4 py-2 text-sm font-medium rounded-md transition
                                  {{ $filtroAtivo === 'pf' ? 'bg-white text-green-600 shadow-sm' : 'text-slate-500 hover:bg-slate-200 hover:text-slate-700' }}">
                            Pessoa física
                        </a>
                        <a href="{{ route('clients.index', array_filter(['tipo' => 'pj', 'q' => $busca])) }}"
                           class="px-4 py-2 text-sm font-medium rounded-md transition
                                  {{ $filtroAtivo === 'pj' ? 'bg-white text-green-600 shadow-sm' : 'text-slate-500 hover:bg-slate-200 hover:text-slate-700' }}">
                            Pessoa jurídica
                        </a>
                    </div>

                    {{-- Busca --}}
                    <form method="GET" action="{{ route('clients.index') }}" class="flex-1 max-w-2xl">
                        @if ($filtroAtivo)
                            <input type="hidden" name="tipo" value="{{ $filtroAtivo }}">
                        @endif
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-gray-400">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z" />
                                </svg>
                            </span>
                            <input type="search" name="q" value="{{ $busca }}" placeholder="Buscar por nome, CPF ou CNPJ"
                                class="w-full rounded-full border-gray-300 text-sm pl-12 pr-5 py-2.5 focus:border-green-500 focus:ring-green-500">
                        </div>
                    </form>
                    {{-- Botão Novo Cliente --}}
                    <div x-data="{ open: false }" @click.outside="open = false" class="relative py-1">
                        <button @click="open = !open"
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm flex items-center gap-2 transition">
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
                               class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-gray-100">
                                <svg class="w-4 h-4 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                                Cliente Pessoa física
                            </a>
                            <a href="{{ route('clients.create', ['tipo' => 'pj']) }}"
                               class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-gray-100">
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
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase text-center">Tipo de pessoa</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase text-center">Documento</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase text-center">Telefone</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase text-center">E-mail</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase text-center whitespace-nowrap">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($clients as $index => $client)
                                <tr class="border-b border-gray-100 last:border-b-0 divide-x divide-gray-100 transition hover:bg-gray-100
                                           {{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50/60' }}">
                                    <td class="py-3 px-4 text-sm font-semibold text-gray-900 text-center truncate max-w-xs" title="{{ $client->nome }}">
                                        {{ $client->nome }}
                                    </td>

                                    {{-- ALTERADO: agora mostra "Físico" / "Jurídico" (via accessor tipo_label no Client.php) --}}
                                    <td class="py-3 px-4 text-sm text-gray-700 text-center">
                                        {{ $client->tipo_label }}
                                    </td>

                                    <td class="py-3 px-4 text-sm text-center whitespace-nowrap">
                                        {{ $client->documento_formatado ?? '—' }}
                                    </td>

                                    {{-- ALTERADO: Telefone e E-mail em colunas separadas (PF e PJ) --}}
                                    <td class="py-3 px-4 text-sm text-gray-700 text-center whitespace-nowrap">
                                        {{ $client->telefone_formatado ?? '—' }}
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-700 text-center truncate max-w-xs" title="{{ $client->email }}">
                                        {{ $client->email ?? '—' }}
                                    </td>

                                    <td class="py-3 px-4 text-sm text-center whitespace-nowrap">
                                        <span class="inline-flex items-center gap-3">
                                            {{-- Ver --}}
                                            <a href="{{ route('clients.show', $client) }}"
                                            title="Ver" aria-label="Ver"
                                            class="p-1.5 rounded-lg text-green-600 bg-green-50 hover:bg-green-100 hover:text-green-700 transition">
                                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>

                                            {{-- Editar (papel com lápis) --}}
                                            <a href="{{ route('clients.edit', $client) }}"
                                            title="Editar" aria-label="Editar"
                                            class="p-1.5 rounded-lg text-gray-600 bg-gray-100 hover:bg-gray-200 hover:text-gray-700 transition">
                                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                </svg>
                                            </a>

                                            {{-- Excluir --}}
                                            <form action="{{ route('clients.destroy', $client) }}" method="POST" class="inline-flex"
                                                onsubmit="return confirm('Tem certeza que deseja remover?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        title="Excluir" aria-label="Excluir"
                                                        class="p-1.5 rounded-lg text-red-600 bg-red-50 hover:bg-red-100 hover:text-red-700 transition">
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
                                    <td colspan="6" class="py-6 text-center text-gray-500">
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