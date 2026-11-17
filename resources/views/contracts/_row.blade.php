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
            @if ($version)
                <span class="font-medium text-blue-700">Versão atual: {{ $version->version }}</span>
            @endif
        </div>
    </td>

    <td class="py-3 px-4 text-sm text-center whitespace-nowrap">
        {{ $group->firstVersion?->creator?->name ?? '—' }}
    </td>

    <td class="py-3 px-4 text-sm text-center text-gray-900">
        {{ $typeName }}
    </td>

    <td class="py-3 px-4 text-sm min-w-56">
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

    <td class="py-3 px-4 text-sm text-center whitespace-nowrap">
        @if ($version)
            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusColors[$version->situacao] ?? 'bg-slate-100 text-slate-600' }}">
                {{ $version->situacao }}
            </span>
            @if ($version->data_termino && ! $version->prazo_indeterminado)
                <div class="mt-1 text-gray-700">Até {{ $version->data_termino->format('d/m/Y') }}</div>
                <div class="text-xs {{ str_contains($version->tempo_restante ?? '', 'vencido') ? 'font-semibold text-red-600' : 'text-gray-500' }}">
                    ({{ $version->tempo_restante }})
                </div>
            @elseif (! $version->prazo_indeterminado && ! $version->data_termino)
                <div class="mt-1 text-gray-400">—</div>
            @endif
        @else
            <span class="text-gray-400">—</span>
        @endif
    </td>

    <td class="py-3 px-4 text-sm text-left">
        @if ($version?->created_at)
            <div>{{ $version->created_at->format('d/m/Y') }}</div>
            <div class="text-xs text-gray-500">às {{ $version->created_at->format('H:i') }}</div>
        @else
            <span class="text-gray-400">—</span>
        @endif
    </td>

    <td class="py-3 px-4 text-sm text-center whitespace-nowrap">
        <span class="inline-flex items-center gap-3">
            <a href="{{ route('contracts.edit', $group) }}?from=index" title="Editar" aria-label="Editar"
               class="rounded-lg p-1.5 text-gray-600 transition hover:bg-gray-200 hover:text-gray-700">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                </svg>
            </a>
            <a href="{{ route('contracts.versions', $group) }}" title="Histórico" aria-label="Histórico"
               class="rounded-lg p-1.5 text-green-600 transition hover:bg-green-100 hover:text-green-700">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 3H7a2 2 0 00-2 2v14a2 2 0 002 2h3M13 3l5 5v2M13 3v5h5" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 13a4 4 0 100 8 4 4 0 000-8zM17 15v2l1.5 1" />
                </svg>
            </a>
        </span>
    </td>
</tr>
