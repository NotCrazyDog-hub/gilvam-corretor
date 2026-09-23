<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Meu Perfil
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center gap-1 text-sm text-gray-600 hover:text-gray-900">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Voltar ao Dashboard
            </a>

            <div class="bg-white shadow-md rounded-lg p-6">
                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="bg-white shadow-md rounded-lg p-6">
                @include('profile.partials.update-password-form')
            </div>

            <div class="bg-white shadow-md rounded-lg p-6">
                @include('profile.partials.delete-user-form')
            </div>

        </div>
    </div>
</x-app-layout>
