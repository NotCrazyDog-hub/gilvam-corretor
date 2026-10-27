<section x-data="{ confirmingDeletion: false }">
    <header class="mb-6">
        <h2 class="text-lg font-semibold text-gray-800">
            Excluir Conta
        </h2>
        <p class="mt-1 text-sm text-gray-500">
            Depois que sua conta for excluída, todos os seus dados serão apagados permanentemente. Antes de excluir, baixe qualquer informação que deseje manter.
        </p>
    </header>

    <button @click="confirmingDeletion = true"
            class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition text-sm font-medium">
        Excluir Conta
    </button>

    {{-- Modal de confirmação --}}
    <div x-show="confirmingDeletion" x-cloak
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4">
        <div @click.outside="confirmingDeletion = false"
             class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">

            <h3 class="text-lg font-semibold text-gray-800 mb-2">
                Tem certeza que deseja excluir sua conta?
            </h3>
            <p class="text-sm text-gray-500 mb-4">
                Essa ação não pode ser desfeita. Digite sua senha para confirmar que deseja excluir sua conta permanentemente.
            </p>

            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')

                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">Senha</label>
                    <input id="password" name="password" type="password"
                           placeholder="Senha"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    @error('password', 'userDeletion')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" @click="confirmingDeletion = false"
                            class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 text-sm">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition text-sm font-medium">
                        Excluir Conta
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
