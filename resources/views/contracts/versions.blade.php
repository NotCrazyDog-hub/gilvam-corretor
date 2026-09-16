<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Histórico — {{ $group->slug }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <div class="flex items-center justify-between mb-4">
                    <a href="{{ route('contracts.index') }}" class="text-sm text-slate-600 hover:underline">
                        &larr; Voltar para a lista
                    </a>

                    <a href="{{ route('contracts.edit', $group) }}?from=versions"
                       class="bg-brand-red hover:bg-brand-red-hover text-white px-4 py-2 rounded text-sm transition">
                        Editar (gerar nova versão)
                    </a>
                </div>

                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b">
                            <th class="py-2">Versão</th>
                            <th class="py-2">Contratante</th>
                            <th class="py-2">Gerado em</th>
                            <th class="py-2 text-right">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($versions as $version)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-2 font-medium">Versão {{ $version->version }}</td>
                                <td class="py-2">{{ $version->contratante['nome'] ?? '—' }}</td>
                                <td class="py-2">{{ $version->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
                                <td class="py-2 text-right">
                                    <a href="{{ route('contracts.download', ['group' => $group, 'version' => $version->version]) }}"
                                       class="text-blue-600 hover:underline">
                                        Baixar Arquivo
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-gray-500">
                                    Nenhuma versão encontrada.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</x-app-layout>