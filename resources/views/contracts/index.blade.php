<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Gestão de Contratos
            </h2>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-[1000px] mx-auto bg-white p-8 rounded-lg shadow-sm">

            {{-- Abas + Botão Novo Contrato --}}
            <div class="flex items-center justify-between mb-6">

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
                <div x-data="{ open: false }" @click.outside="open = false" class="relative">
                    <button @click="open = !open"
                            class="bg-brand-red hover:bg-brand-red-hover text-white shadow-sm rounded-lg px-4 py-2.5 flex items-center gap-2 text-sm font-bold transition">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
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
                           class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                            Contrato Pessoa física
                        </a>
                        <a href="{{ route('contracts.create.pj') }}"
                           class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                            Contrato Pessoa jurídica
                        </a>
                    </div>
                </div>

            </div>

            {{-- Tabela --}}
            <table class="w-full border-collapse rounded-lg overflow-hidden">
                <thead>
                    <tr>
                        <th class="bg-slate-800 text-white text-left text-sm font-semibold p-3">Contratante</th>
                        <th class="bg-slate-800 text-white text-left text-sm font-semibold p-3">Tipo</th>
                        <th class="bg-slate-800 text-white text-left text-sm font-semibold p-3">Versão vigente</th>
                        <th class="bg-slate-800 text-white text-left text-sm font-semibold p-3">Gerado em</th>
                        <th class="bg-slate-800 text-white text-left text-sm font-semibold p-3">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($groups as $group)
                        <tr class="hover:bg-slate-50">
                            <td class="p-3 border-b border-slate-200 text-sm font-medium">
                                {{ $group->client_display_name }}
                            </td>
                            <td class="p-3 border-b border-slate-200">
                                <span class="inline-block px-2.5 py-0.5 text-xs font-bold rounded-full bg-slate-100 text-slate-600">
                                    {{ \Illuminate\Support\Str::of($group->contract_type)->replace('_', ' ')->title() }}
                                </span>
                            </td>
                            <td class="p-3 border-b border-slate-200 text-sm text-slate-600">
                                Versão {{ $group->latestVersion->version ?? '—' }}
                            </td>
                            <td class="p-3 border-b border-slate-200 text-sm text-slate-600">
                                {{ $group->latestVersion?->created_at?->format('d/m/Y\, à\s H:i') ?? '—' }}
                            </td>
                            <td class="p-3 border-b border-slate-200 text-sm">
                                <a href="{{ route('contracts.edit', $group) }}" class="text-brand-green font-semibold hover:underline">Editar</a> ·
                                <a href="{{ route('contracts.versions', $group) }}" class="text-brand-green font-semibold hover:underline ml-1">Histórico</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-slate-400">Nenhum contrato encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-5">
                {{ $groups->links() }}
            </div>

        </div>
    </div>
</body>
</html>