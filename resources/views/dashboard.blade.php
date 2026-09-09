<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Mensagem de boas-vindas (temporária, some após a próxima navegação) --}}
            @if (session('login_success'))
                <div class="mb-6 p-4 bg-green-100 text-green-800 rounded border border-green-300">
                    {{ session('login_success') }}
                </div>
            @endif

            {{-- Cards de resumo --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-600">
                    <h3 class="text-sm font-medium text-gray-500 uppercase mb-1">Pessoas Físicas</h3>
                    <p class="text-3xl font-bold text-gray-800">{{ $totalPessoasFisicas }}</p>
                    <a href="{{ route('pessoas-fisicas.index') }}"
                       class="text-sm text-blue-600 hover:underline mt-2 inline-block">
                        Ver todas →
                    </a>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-600">
                    <h3 class="text-sm font-medium text-gray-500 uppercase mb-1">Pessoas Jurídicas</h3>
                    <p class="text-3xl font-bold text-gray-800">{{ $totalPessoasJuridicas }}</p>
                    <a href="{{ route('pessoas-juridicas.index') }}"
                       class="text-sm text-blue-600 hover:underline mt-2 inline-block">
                        Ver todas →
                    </a>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-gray-400">
                    <h3 class="text-sm font-medium text-gray-500 uppercase mb-1">Total de Cadastros</h3>
                    <p class="text-3xl font-bold text-gray-800">{{ $totalPessoasFisicas + $totalPessoasJuridicas }}</p>
                    <span class="text-sm text-gray-400 mt-2 inline-block">Físicas + Jurídicas</span>
                </div>

            </div>

            {{-- Atalhos rápidos --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Ações rápidas</h3>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('pessoas-fisicas.create') }}"
                       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                        + Nova Pessoa Física
                    </a>
                    <a href="{{ route('pessoas-juridicas.create') }}"
                       class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm">
                        + Nova Pessoa Jurídica
                    </a>
                </div>
            </div>

            {{-- Últimos cadastros --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Últimas Pessoas Físicas</h3>

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
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Últimas Pessoas Jurídicas</h3>

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

        </div>
    </div>
</x-app-layout>
