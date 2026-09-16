<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Histórico — {{ $group->slug }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-[1000px] mx-auto bg-white p-8 rounded-lg shadow-sm">

            <p class="mb-6 text-sm text-slate-600">
                <a href="{{ route('contracts.index') }}" class="text-brand-green font-semibold hover:underline">
                    &larr; Voltar para a lista
                </a>
                <span class="mx-1">·</span>
                <a href="{{ route('contracts.edit', $group) }}?from=versions" class="text-brand-green font-semibold hover:underline">
                    Editar (gerar nova versão)
                </a>
            </p>

            <table class="w-full border-collapse rounded-lg overflow-hidden">
                <thead>
                    <tr>
                        <th class="bg-slate-800 text-white text-left text-sm font-semibold p-3">Versão</th>
                        <th class="bg-slate-800 text-white text-left text-sm font-semibold p-3">Contratante</th>
                        <th class="bg-slate-800 text-white text-left text-sm font-semibold p-3">Gerado em</th>
                        <th class="bg-slate-800 text-white text-left text-sm font-semibold p-3">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($versions as $version)
                        <tr class="hover:bg-slate-50">
                            <td class="p-3 border-b border-slate-200 text-sm font-medium">
                                Versão {{ $version->version }}
                            </td>
                            <td class="p-3 border-b border-slate-200 text-sm">
                                {{ $version->contratante['nome'] ?? '—' }}
                            </td>
                            <td class="p-3 border-b border-slate-200 text-sm text-slate-600">
                                {{ $version->created_at?->format('d/m/Y H:i') ?? '—' }}
                            </td>
                            <td class="p-3 border-b border-slate-200 text-sm">
                                <a href="{{ route('contracts.download', ['group' => $group, 'version' => $version->version]) }}"
                                   class="text-brand-green font-semibold hover:underline">
                                    Baixar Arquivo
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-6 text-center text-slate-400">Nenhuma versão encontrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>
</x-app-layout>