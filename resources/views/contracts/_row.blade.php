@php
    $version = $group->latestVersion;
    $document = $group->client?->documento_formatado;
    $typeName = \App\Domains\Contracts\Services\ContractTypes::get($group->contract_type)['nome'];
    $statusColors = [
        'Vigente' => 'bg-green-100 text-green-700',
        'Vencendo' => 'bg-yellow-100 text-yellow-700',
        'Vencido' => 'bg-red-100 text-red-700',
        'Indeterminado' => 'bg-gray-200 text-gray-700',
    ];
@endphp

<tr
    @if ($rowHidden ?? false) x-show="expanded" x-cloak @endif
    class="border-b border-gray-100 last:border-b-0 divide-x divide-gray-100 transition hover:bg-blue-50 {{ ($rowIndex ?? 0) % 2 === 0 ? 'bg-white' : 'bg-gray-50/60' }}"
>
    <td class="py-3 px-4 text-sm text-left min-w-52">
        <div class="font-semibold text-gray-900" title="{{ $group->client_display_name }}">
            {{ $group->client_display_name }}
        </div>
        <div class="mt-1 flex flex-wrap items-center gap-x-1.5 gap-y-1 text-xs text-gray-500">
            <span class="font-mono font-medium text-slate-600">{{ $group->reference_code }}</span>
            @if ($document)
                <span aria-hidden="true">·</span>
                <span>{{ $document }}</span>
            @endif
            @if ($version && $version->version > 1)
                <span class="rounded-full bg-blue-100 px-1.5 py-0.5 font-semibold text-blue-700">v{{ $version->version }}</span>
            @endif
        </div>
    </td>

    <td class="py-3 px-4 text-sm text-left">
        <span class="inline-flex max-w-56 rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-medium leading-4 text-indigo-700">
            {{ $typeName }}
        </span>
    </td>

    <td class="py-3 px-4 text-sm text-left min-w-56">
        <div class="font-medium text-gray-800">{{ $group->imovel_display_name }}</div>
        @if ($group->imovel_display_address !== '')
            <div class="mt-0.5 max-w-sm truncate text-xs text-gray-500" title="{{ $group->imovel_display_address }}">
                {{ $group->imovel_display_address }}
            </div>
        @endif
        @if ($group->imovel_display_reference)
            <div class="mt-0.5 text-xs text-gray-500">{{ $group->imovel_display_reference }}</div>
        @endif
    </td>

    <td class="py-3 px-4 text-sm text-left whitespace-nowrap">
        @if ($version)
            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusColors[$version->situacao] ?? 'bg-slate-100 text-slate-600' }}">
                {{ $version->situacao }}
            </span>
        @else
            <span class="text-gray-400">—</span>
        @endif
    </td>

    <td class="py-3 px-4 text-sm text-left whitespace-nowrap">
        @if ($version?->prazo_indeterminado)
            <span class="text-gray-700">Indeterminado</span>
        @elseif ($version?->data_termino)
            <div class="text-gray-700">{{ $version->data_termino->format('d/m/Y') }}</div>
            <div class="text-xs {{ str_contains($version->tempo_restante ?? '', 'vencido') ? 'font-semibold text-red-600' : 'text-gray-500' }}">
                ({{ $version->tempo_restante }})
            </div>
        @else
            <span class="text-gray-400">—</span>
        @endif
    </td>

    <td class="py-3 px-4 text-sm text-left whitespace-nowrap">
        {{ $version?->created_at?->format('d/m/Y, H:i') ?? '—' }}
    </td>

    <td class="py-3 px-4 text-sm text-left whitespace-nowrap">
        <div x-data="{ open: false }" @click.outside="open = false" class="relative inline-block">
            <button
                type="button"
                @click="open = !open"
                :aria-expanded="open"
                aria-haspopup="true"
                aria-label="Ações do contrato {{ $group->reference_code }}"
                class="rounded-lg p-2 text-gray-600 transition hover:bg-gray-100 hover:text-gray-900"
            >
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                </svg>
            </button>

            <div x-show="open" x-cloak x-transition class="absolute right-0 z-20 mt-1 w-44 rounded-lg border border-gray-200 bg-white py-1 text-sm shadow-lg">
                <a href="{{ route('contracts.edit', $group) }}?from=index" class="block px-3 py-2 text-gray-700 hover:bg-gray-50">Editar contrato</a>
                <a href="{{ route('contracts.versions', $group) }}" class="block px-3 py-2 text-gray-700 hover:bg-gray-50">Ver versões</a>
                @if ($version)
                    <a href="{{ route('contracts.download', ['group' => $group->uuid, 'version' => $version->version]) }}" class="block px-3 py-2 text-gray-700 hover:bg-gray-50">Baixar .docx</a>
                @endif
            </div>
        </div>
    </td>
</tr>
