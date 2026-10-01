<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Clientes</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <div class="flex items-center justify-between mb-4 gap-4">

                    {{-- Abas --}}
                    <div class="flex items-center gap-1 bg-slate-100 rounded-lg p-1">
                        <a href="{{ route('clients.index', array_filter(['q' => $busca])) }}"
                           class="px-4 py-2 text-sm font-medium rounded-md transition
                                  {{ $tipo ? 'text-slate-500 hover:text-slate-700' : 'bg-white text-blue-600 shadow-sm' }}">
                            Todos
                        </a>
                        <a href="{{ route('clients.index', array_filter(['tipo' => 'pf', 'q' => $busca])) }}"
                           class="px-4 py-2 text-sm font-medium rounded-md transition
                                  {{ $tipo === 'pf' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                            Pessoa física
                        </a>
                        <a href="{{ route('clients.index', array_filter(['tipo' => 'pj', 'q' => $busca])) }}"
                           class="px-4 py-2 text-sm font-medium rounded-md transition
                                  {{ $tipo === 'pj' ? 'bg-white text-green-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                            Pessoa jurídica
                        </a>
                    </div>

                    {{-- Busca --}}
                    <form method="GET" action="{{ route('clients.index') }}" class="flex-1 max-w-sm">
                        @if ($tipo) <input type="hidden" name="tipo" value="{{ $tipo }}"> @endif
                        <input type="search" name="q" value="{{ $busca }}" placeholder="Buscar por nome, CPF ou CNPJ"
                               class="w-full rounded border-gray-300 text-sm">
                    </form>

                    {{-- Novo cliente --}}
                    <div x-data="{ open: false }" @click.outside="open = false" class="relative">
                        <button @click="open = !open"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm flex items-center gap-2 transition">
                            + Novo cliente
                        </button>
                        <div x-show="open" x-cloak x-transition
                             class="absolute right-0 mt-2 w-52 bg-white shadow-lg rounded-lg border border-slate-100 py-2 z-10">
                            <a href="{{ route('clients.create', ['tipo' => 'pf']) }}"
                               class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Pessoa física</a>
                            <a href="{{ route('clients.create', ['tipo' => 'pj']) }}"
                               class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Pessoa jurídica</a>
                        </div>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <table class="w-full text-center border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Nome / Razão Social</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Tipo</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Documento</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Contato</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase whitespace-nowrap">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($clients as $index => $client)
                                <tr class="border-b border-gray-100 last:border-b-0 hover:bg-blue-50/40 transition
                                           {{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50/60' }}">
                                    <td class="py-3 px-4 text-sm font-semibold text-gray-900 truncate max-w-xs"
                                        title="{{ $client->nome }}">{{ $client->nome }}</td>
                                    <td class="py-3 px-4 text-sm">
                                        <span class="inline-block px-2.5 py-0.5 text-xs font-bold rounded-full
                                                     {{ $client->isPj() ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                            {{ $client->isPj() ? 'PJ' : 'PF' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-700">{{ $client->documento_formatado ?? '—' }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-700">
                                        {{ $client->isPj() ? ($client->representante_legal_nome ?? '—') : ($client->telefone_formatado ?? '—') }}
                                    </td>
                                    <td class="py-3 px-4 text-sm whitespace-nowrap">
                                        <span class="inline-flex items-center divide-x divide-gray-300">
                                            <a href="{{ route('clients.show', $client) }}" class="text-blue-600 hover:underline px-2">Ver</a>
                                            <a href="{{ route('clients.edit', $client) }}" class="text-yellow-600 hover:underline px-2">Editar</a>
                                            <form action="{{ route('clients.destroy', $client) }}" method="POST" class="inline px-2"
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
                                    <td colspan="5" class="py-6 text-center text-gray-500">Nenhum cliente encontrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $clients->links() }}</div>

            </div>
        </div>
    </div>
</x-app-layout>