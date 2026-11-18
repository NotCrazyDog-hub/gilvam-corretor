<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Novo Cliente</h2>
    </x-slot>

    <div class="py-12 pb-32">
        <div class="max-w-3xl mx-auto space-y-6 sm:px-6 lg:px-8">
            <a href="{{ route('clients.index') }}"
               class="inline-flex items-center gap-2 text-sm font-medium text-slate-600 underline decoration-slate-300 underline-offset-4 transition hover:text-slate-900 hover:decoration-slate-600">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Voltar para clientes
            </a>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @include('clients._form', [
                    'client' => null,
                    'action' => route('clients.store'),
                    'tipo'   => $tipo,
                ])

            </div>

            <x-form-action-bar form-id="form-client" :cancel-url="route('clients.index')"
                               submit-label="Salvar" container-class="max-w-3xl" />
        </div>
    </div>
</x-app-layout>
