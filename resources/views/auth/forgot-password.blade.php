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

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    {{-- Email --}}
                    <div class="mb-6">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                               autocomplete="username"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                            class="w-full bg-green-600 text-white py-2 rounded-md hover:bg-green-700 transition">
                        Enviar link de redefinição
                    </button>

                    <p class="text-center text-sm text-gray-600 mt-6">
                        Lembrou a senha?
                        <a href="{{ route('login') }}" class="text-green-600 hover:underline font-bold">Voltar para o login</a>
                    </p>

                </form>

            </div>
        </div>
    </div>
</x-guest-layout>