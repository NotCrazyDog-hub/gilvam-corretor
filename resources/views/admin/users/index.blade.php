<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Usuários do sistema
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <div class="flex items-center justify-between mb-4">
                    <p class="text-sm text-gray-500">Somente você pode cadastrar novos usuários.</p>

                    <a href="{{ route('admin.users.create') }}"
                       class="bg-emerald-700 hover:bg-emerald-800 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2 whitespace-nowrap transition">
                        <svg class="w-4 h-4 block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Novo usuário
                    </a>
                </div>

                {{-- Tabela --}}
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <table class="w-full text-center border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 divide-x divide-gray-200">
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase text-left">Nome</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase text-center">E-mail</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase text-center">Perfil</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase text-center">Criado em</th>
                                <th class="py-3 px-4 text-xs font-semibold text-gray-500 uppercase text-center whitespace-nowrap">Ações</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($users->where('id', '!=', auth()->id())->values() as $index => $user)
                                <tr class="border-b border-gray-100 last:border-b-0 divide-x divide-gray-100 transition hover:bg-blue-50
                                           {{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50/60' }}">
                                    <td class="py-3 px-4 text-sm font-semibold text-gray-900 text-left truncate max-w-xs" title="{{ $user->name }}">
                                        {{ $user->name }}
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-700 text-center">
                                        {{ $user->email }}
                                    </td>
                                    <td class="py-3 px-4 text-sm text-center">
                                        @if ($user->isAdmin())
                                            <span class="inline-block px-2.5 py-0.5 text-xs font-bold rounded-full bg-green-100 text-green-700">Administrador</span>
                                        @else
                                            <span class="inline-block px-2.5 py-0.5 text-xs font-bold rounded-full bg-gray-200 text-gray-600">Usuário</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-sm text-center whitespace-nowrap">
                                        {{ $user->created_at?->format('d/m/Y\, à\s H:i') ?? '—' }}
                                    </td>
                                    <td class="py-3 px-4 text-sm text-center whitespace-nowrap">
                                        <span class="inline-flex items-center gap-3">
                                            {{-- Editar --}}
                                            <a href="{{ route('admin.users.edit', $user) }}"
                                               title="Editar usuário" aria-label="Editar {{ $user->name }}"
                                               class="p-1.5 rounded-lg text-gray-600 hover:bg-gray-200 hover:text-gray-700 transition">
                                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                </svg>
                                            </a>

                                            {{-- Excluir --}}
                                            <form method="POST"
                                                  action="{{ route('admin.users.destroy', $user) }}"
                                                  onsubmit="return confirm('Excluir {{ addslashes($user->name) }}? Essa ação não pode ser desfeita.')">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        title="Excluir usuário" aria-label="Excluir {{ $user->name }}"
                                                        class="p-1.5 rounded-lg text-red-600 hover:bg-red-100 hover:text-red-800 transition">
                                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-gray-500">
                                        Nenhum usuário encontrado.
                                    </td>
                                </tr>
                            @endforelse
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
