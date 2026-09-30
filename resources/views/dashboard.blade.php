<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>

            {{-- Ações Rápidas --}}
            <div x-data="{ open: false }" @click.outside="open = false" class="relative">
                <button @click="open = !open"
                        class="bg-blue-600 text-white shadow-sm rounded-lg px-4 py-2 flex items-center gap-2 text-sm font-medium hover:bg-blue-700 transition">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Ações Rápidas
                    <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="open" x-cloak x-transition
                     class="absolute right-0 mt-2 w-60 bg-white shadow-lg rounded-lg border border-gray-100 py-2 z-10">
                    <a href="{{ route('pessoas-fisicas.create') }}"
                       class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <svg class="w-4 h-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        Nova Pessoa Física
                    </a>
                    <a href="{{ route('pessoas-juridicas.create') }}"
                    class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                    <svg class="w-4 h-4 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4M9 9h1m-1 4h1m-1 4h1" />
                        </svg>
                        Nova Pessoa Jurídica
                    </a>
                    <a href="{{ route('imoveis.create') }}"
                       class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <svg class="w-4 h-4 text-orange-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7m-9-7v10a1 1 0 001 1h3m6-9l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Novo Imóvel
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Mensagem de boas-vindas --}}
            @if (session('login_success'))
                <div class="mb-6 p-4 bg-green-100 text-green-800 rounded border border-green-300">
                    {{ session('login_success') }}
                </div>
            @endif

            {{-- Cards de resumo --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">

                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-4 border-l-4 border-blue-600 flex items-start gap-4">
                    <div class="bg-blue-50 rounded-lg p-2">
                        <svg class="w-6 h-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xs font-medium text-gray-500 uppercase mb-1">Pessoas Físicas</h3>
                        <p class="text-2xl font-bold text-gray-800">{{ $totalPessoasFisicas }}</p>
                        <a href="{{ route('pessoas-fisicas.index') }}"
                           class="text-xs text-blue-600 hover:underline mt-1 inline-block">
                            Ver todas →
                        </a>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-4 border-l-4 border-green-600 flex items-start gap-4">
                    <div class="bg-green-50 rounded-lg p-2">
                        <svg class="w-6 h-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4M9 9h1m-1 4h1m-1 4h1" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xs font-medium text-gray-500 uppercase mb-1">Pessoas Jurídicas</h3>
                        <p class="text-2xl font-bold text-gray-800">{{ $totalPessoasJuridicas }}</p>
                        <a href="{{ route('pessoas-juridicas.index') }}"
                           class="text-xs text-blue-600 hover:underline mt-1 inline-block">
                            Ver todas →
                        </a>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-4 border-l-4 border-gray-400 flex items-start gap-4">
                    <div class="bg-gray-100 rounded-lg p-2">
                        <svg class="w-6 h-6 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l7 7-7 7z M3 5h4v14H3zM17 9h4v10h-4z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xs font-medium text-gray-500 uppercase mb-1">Total de Cadastros</h3>
                        <p class="text-2xl font-bold text-gray-800">{{ $totalPessoasFisicas + $totalPessoasJuridicas }}</p>
                        <span class="text-xs text-gray-400 mt-1 inline-block">Físicas + Jurídicas</span>
                    </div>
                </div>

            </div>

            {{-- Últimos cadastros --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Últimas Pessoas Físicas
                    </h3>

                    @if ($ultimasPessoasFisicas->isEmpty())
                        <p class="text-sm text-gray-400">Nenhum cadastro ainda.</p>
                    @else
                        <ul class="divide-y divide-gray-100">
                            @foreach ($ultimasPessoasFisicas as $pessoa)
                                <li class="py-2 flex justify-between items-center">
                                    <span class="text-sm text-gray-700">{{ $pessoa->nome }}</span>
                                    <a href="{{ route('pessoas-fisicas.show', $pessoa) }}"
                                       class="text-xs text-blue-600 hover:underline">
                                        Ver
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4M9 9h1m-1 4h1m-1 4h1" />
                        </svg>
                        Últimas Pessoas Jurídicas
                    </h3>

                    @if ($ultimasPessoasJuridicas->isEmpty())
                        <p class="text-sm text-gray-400">Nenhum cadastro ainda.</p>
                    @else
                        <ul class="divide-y divide-gray-100">
                            @foreach ($ultimasPessoasJuridicas as $pessoa)
                                <li class="py-2 flex justify-between items-center">
                                    <span class="text-sm text-gray-700">{{ $pessoa->razao_social }}</span>
                                    <a href="{{ route('pessoas-juridicas.show', $pessoa) }}"
                                       class="text-xs text-blue-600 hover:underline">
                                        Ver
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

            </div>

            {{-- Seção de Imóveis --}}
            <div class="mt-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-orange-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7m-9-7v10a1 1 0 001 1h3m6-9l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Imóveis
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    <div class="bg-white overflow-hidden shadow-sm rounded-lg p-4 border-l-4 border-orange-500 flex items-start gap-4">
                        <div class="bg-orange-50 rounded-lg p-2">
                            <svg class="w-6 h-6 text-orange-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7m-9-7v10a1 1 0 001 1h3m6-9l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xs font-medium text-gray-500 uppercase mb-1">Total de Imóveis</h3>
                            <p class="text-2xl font-bold text-gray-800">{{ $totalImoveis }}</p>
                            <a href="{{ route('imoveis.index') }}"
                               class="text-xs text-blue-600 hover:underline mt-1 inline-block">
                                Ver todos →
                            </a>
                        </div>
                    </div>

                    <div class="md:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Últimos Imóveis</h3>

                        @if ($ultimosImoveis->isEmpty())
                            <p class="text-sm text-gray-400">Nenhum cadastro ainda.</p>
                        @else
                            <ul class="divide-y divide-gray-100">
                                @foreach ($ultimosImoveis as $imovel)
                                    <li class="py-2 flex justify-between items-center">
                                        <span class="text-sm text-gray-700">{{ $imovel->tipo_label }} — {{ $imovel->bairro }}</span>
                                        <a href="{{ route('imoveis.show', $imovel) }}"
                                           class="text-xs text-blue-600 hover:underline">
                                            Ver
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
