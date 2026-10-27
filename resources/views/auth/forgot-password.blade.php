<x-guest-layout>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-50 px-4">

        <div class="w-full max-w-md">
            <div class="bg-white shadow-md rounded-lg px-8 py-10">

                <h2 class="text-2xl font-semibold text-gray-800 text-center mb-2">
                    Esqueceu a senha?
                </h2>

                <p class="text-sm text-gray-600 text-center mb-6">
                    Sem problemas. Informe seu email e enviaremos um link para você criar uma nova senha.
                </p>

                {{-- Session Status --}}
                @if (session('status'))
                    <div class="mb-4 text-sm text-green-600 text-center">
                        {{ session('status') }}
                    </div>
                @endif
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4">
        <div class="w-full max-w-md">

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">

                {{-- Cabeçalho com ícone --}}
                <div class="px-8 pt-8 pb-2 text-center">
                    <div class="mx-auto flex items-center justify-center w-14 h-14 rounded-lg bg-blue-50 mb-4">
                        <svg class="w-7 h-7 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 10-8 0v4h8z" />
                        </svg>
                    </div>

                    <h1 class="text-lg font-semibold text-gray-800">
                        {{ __('Esqueceu sua senha?') }}
                    </h1>
                    <p class="mt-2 text-sm text-gray-500">
                        {{ __('Sem problemas. Informe seu e-mail e enviaremos um link para você redefinir sua senha.') }}
                    </p>
                </div>

                <div class="px-8 pb-8 pt-4">

                    {{-- Status da sessão --}}
                    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>
    </div>
</x-guest-layout>
