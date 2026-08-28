<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Painel — Sistema Imobiliária
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <p>Bem-vindo(a), {{ $user->name }}!</p>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ url('/imoveis') }}" class="block p-4 bg-gray-50 rounded-lg hover:bg-gray-100">
                        Imóveis
                    </a>
                    <a href="{{ url('/pessoas') }}" class="block p-4 bg-gray-50 rounded-lg hover:bg-gray-100">
                        Pessoas
                    </a>
                    <a href="{{ url('/negociacoes') }}" class="block p-4 bg-gray-50 rounded-lg hover:bg-gray-100">
                        Negociações
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>