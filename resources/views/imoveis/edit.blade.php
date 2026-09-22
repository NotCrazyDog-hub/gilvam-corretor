<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Imóvel
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <a href="{{ route('imoveis.show', $imovel) }}"
               class="inline-flex items-center gap-1 text-sm text-gray-600 hover:text-gray-900">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Voltar para o imóvel
            </a>

            <div class="bg-white shadow-md rounded-lg p-6">
                <form method="POST" action="{{ route('imoveis.update', $imovel) }}">
                    @csrf
                    @method('PUT')

                    @include('imoveis.partials.form')

                    <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-gray-100">
                        <a href="{{ route('imoveis.show', $imovel) }}"
                           class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">
                            Cancelar
                        </a>
                        <button type="submit"
                                class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                            Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
