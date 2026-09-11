<section>
    <header class="mb-6">
        <h2 class="text-lg font-semibold text-gray-800">
            Informações do Perfil
        </h2>
        <p class="mt-1 text-sm text-gray-500">
            Atualize seu nome e endereço de email.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Nome</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus
                   autocomplete="name"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required
                   autocomplete="username"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="text-sm text-gray-600">
                        Seu email ainda não foi verificado.
                        <button form="send-verification" class="text-blue-600 hover:underline">
                            Clique aqui para reenviar o email de verificação.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm font-medium text-green-600">
                            Um novo link de verificação foi enviado para o seu email.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition text-sm font-medium">
                Salvar
            </button>

            @if (session('status') === 'profile-updated')
                <p class="text-sm text-gray-500">Salvo.</p>
            @endif
        </div>
    </form>
</section>
