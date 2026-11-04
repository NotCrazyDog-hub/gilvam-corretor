<x-guest-layout>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-50 px-4">

        <div class="w-full max-w-md">

            {{-- Logo --}}
            <div class="flex justify-center mb-8">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-16 w-auto">
            </div>

            <div class="bg-white shadow-md rounded-lg px-8 py-10">

                <h2 class="text-2xl font-semibold text-gray-800 text-center mb-6">
                    Bem-vindo(a) de volta!
                </h2>

                {{-- Session Status --}}
                @if (session('status'))
                    <div class="mb-4 text-sm text-green-600 text-center">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    {{-- Email --}}
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                               autocomplete="username"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-4" x-data="{ show: false }">
                        <label for="password" class="block text-sm font-medium text-gray-700">Senha</label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" id="password" name="password" required
                                   autocomplete="current-password"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 pr-10">
                            <button type="button" @click="show = !show"
                                    class="absolute inset-y-0 right-0 top-1 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                                <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Remember Me --}}
                    <div class="flex items-center justify-between mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember"
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-600">Lembre-me</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm text-green-600 hover:underline font-bold">
                                Esqueceu a senha?
                            </a>
                        @endif
                    </div>

                    <button type="submit"
                            class="w-full bg-green-600 text-white py-2 rounded-md hover:bg-green-700 transition">
                        Entrar
                    </button>

                </form>

            </div>
        </div>
    </div>
</x-guest-layout>
