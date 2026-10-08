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

            <!-- Sair da conta -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <section>
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">
                            {{ __('Sair da conta') }}
                        </h2>

                        <p class="mt-1 text-sm text-gray-600">
                            {{ __('Encerre sua sessão neste dispositivo.') }}
                        </p>
                    </header>

                    <form method="POST" action="{{ route('logout') }}" class="mt-6">
                        @csrf

                        <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            {{ __('Sair') }}
                        </button>
                    </form>
                </section>
            </div>

            <div class="bg-white shadow-md rounded-lg p-6">
                @include('profile.partials.delete-user-form')
            </div>

        </div>
    </div>
</x-app-layout>
