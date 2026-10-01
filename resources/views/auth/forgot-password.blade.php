<x-guest-layout>
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

                    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                        @csrf

                        {{-- E-mail --}}
                        <div>
                            <x-input-label for="email" :value="__('E-mail')" class="text-gray-700" />
                            <div class="relative mt-1">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </span>
                                <x-text-input
                                    id="email"
                                    class="block w-full pl-10 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    type="email"
                                    name="email"
                                    :value="old('email')"
                                    required
                                    autofocus
                                    placeholder="seuemail@exemplo.com"
                                />
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        {{-- Botão --}}
                        <div class="pt-2">
                            <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 bg-blue-600 text-white shadow-sm rounded-lg px-4 py-2.5 text-sm font-medium hover:bg-blue-700 transition">
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                {{ __('Enviar link de redefinição') }}
                            </button>
                        </div>
                    </form>

                    {{-- Voltar ao login --}}
                    <div class="mt-6 text-center">
                        <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:underline inline-flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            {{ __('Voltar para o login') }}
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-guest-layout>
