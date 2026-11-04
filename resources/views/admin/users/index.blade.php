<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Usuários do sistema
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <div class="flex items-center justify-between mb-4">
                    <p class="text-sm text-gray-500">Somente você pode cadastrar novos usuários.</p>

                    <a href="{{ route('admin.users.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm inline-flex items-center gap-2 transition">
                        <svg class="w-4 h-4 block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Novo usuário
                    </a>
                </div>

                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase text-left">Nome</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase text-left">E-mail</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase text-center">Perfil</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase text-center">Criado em</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase text-center">Ações</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($users as $user)
                                @continue($user->id === auth()->id())

                                <tr class="border-b border-gray-100 last:border-b-0 hover:bg-blue-50">
                                    <td class="py-3 px-4 text-sm font-semibold text-gray-900">{{ $user->name }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-700">{{ $user->email }}</td>
                                    <td class="py-3 px-4 text-center">
                                        @if ($user->isAdmin())
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Administrador</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600">Usuário</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-500 text-center">
                                        {{ $user->created_at?->format('d/m/Y') }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <div class="inline-flex items-center gap-1">
                                            <a href="{{ route('admin.users.edit', $user) }}"
                                                title="Editar usuário"
                                                aria-label="Editar {{ $user->name }}"
                                                class="inline-flex items-center justify-center p-2 rounded-md text-blue-600 hover:text-blue-800 hover:bg-blue-50 transition">
                                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                </svg>
                                            </a>

                                            <form method="POST"
                                                action="{{ route('admin.users.destroy', $user) }}"
                                                onsubmit="return confirm('Excluir {{ addslashes($user->name) }}? Essa ação não pode ser desfeita.')">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                    title="Excluir usuário"
                                                    aria-label="Excluir {{ $user->name }}"
                                                    class="inline-flex items-center justify-center p-2 rounded-md text-red-600 hover:text-red-800 hover:bg-red-50 transition">
                                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>