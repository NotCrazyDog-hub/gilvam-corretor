<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Gestão de Contratos
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                {{-- Abas + Botão Novo Contrato --}}
                <div class="flex items-center justify-between mb-4">

                    {{-- Abas --}}
                    <div class="flex items-center gap-1 bg-slate-100 rounded-lg p-1">
                        <a href="{{ route('contracts.index') }}"
                           class="px-4 py-2 text-sm font-medium rounded-md transition
                                  {{ request('client_type') ? 'text-slate-500 hover:text-slate-700' : 'bg-white text-brand-red shadow-sm' }}">
                            Todos
                        </a>
                        <a href="{{ route('contracts.index', ['client_type' => 'pf']) }}"
                           class="px-4 py-2 text-sm font-medium rounded-md transition
                                  {{ request('client_type') === 'pf' ? 'bg-white text-brand-red shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                            Pessoa física
                        </a>
                        <a href="{{ route('contracts.index', ['client_type' => 'pj']) }}"
                           class="px-4 py-2 text-sm font-medium rounded-md transition
                                  {{ request('client_type') === 'pj' ? 'bg-white text-brand-red shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                            Pessoa jurídica
                        </a>
                    </div>

                    {{-- Botão Novo Contrato --}}
                    <div x-data="{ open: false }" @click.outside="open = false" class="relative py-1">
                        <button @click="open = !open"
                                class="bg-brand-red hover:bg-brand-red-hover text-white px-4 py-2 rounded text-sm flex items-center gap-2 transition">
                            <svg class="w-4 h-4 block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            Novo contrato
                            <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                <div x-show="open" x-cloak x-transition
                    class="absolute right-0 mt-2 w-56 bg-white shadow-lg rounded-lg border border-slate-100 py-2 z-10">
                    <a href="{{ route('contracts.create.pf') }}"
                    class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                        <svg class="w-4 h-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        Contrato Pessoa física
                    </a>
                    <a href="{{ route('contracts.create.pj') }}"
                    class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                        <svg class="w-4 h-4 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4M9 9h1m-1 4h1m-1 4h1" />
                        </svg>
                        Contrato Pessoa jurídica
                    </a>
                </div>
                    </div>

                </div>

                {{-- Tabela --}}
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b">
                            <th class="py-2">Contratante</th>
                            <th class="py-2">Tipo de contrato</th>
                            <th class="py-2">Versão vigente</th>
                            <th class="py-2">Gerado em</th>
                            <th class="py-2 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($groups as $group)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-2">{{ $group->client_display_name }}</td>
                                <td class="py-2">
                                    <span class="inline-block px-2.5 py-0.5 text-xs font-bold rounded-full bg-slate-100 text-slate-600">
                                        {{ \Illuminate\Support\Str::of($group->contract_type)->replace('_', ' ')->title() }}
                                    </span>
                                </td>
                                <td class="py-2">Versão {{ $group->latestVersion->version ?? '—' }}</td>
                                <td class="py-2">{{ $group->latestVersion?->created_at?->format('d/m/Y\, à\s H:i') ?? '—' }}</td>
                                <td class="py-2 text-right space-x-2">
                                    <a href="{{ route('contracts.edit', $group) }}?from=index"
                                       class="text-yellow-600 hover:underline">Editar</a>
                                    <a href="{{ route('contracts.versions', $group) }}"
                                       class="text-blue-600 hover:underline">Histórico</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 text-center text-gray-500">
                                    Nenhum contrato encontrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $groups->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
