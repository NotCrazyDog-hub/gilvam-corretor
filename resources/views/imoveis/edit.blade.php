<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Imóvel
        </h2>
    </x-slot>

    <div class="py-12 pb-32 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <a href="{{ route('imoveis.show', $imovel) }}"
               class="inline-flex items-center gap-2 text-sm font-medium text-slate-600 underline decoration-slate-300 underline-offset-4 transition hover:text-slate-900 hover:decoration-slate-600">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Voltar para o imóvel
            </a>

            <div class="bg-white shadow-md rounded-lg p-6">
                <form id="form-imovel" method="POST" action="{{ route('imoveis.update', $imovel) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    @include('imoveis.partials.form')

                </form>
            </div>

            <x-form-action-bar form-id="form-imovel" :cancel-url="route('imoveis.show', $imovel)"
                               submit-label="Salvar alterações" container-class="max-w-5xl" />

        </div>
    </div>
</x-app-layout>
