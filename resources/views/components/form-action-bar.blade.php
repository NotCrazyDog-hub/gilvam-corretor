@props([
    'formId',
    'cancelUrl',
    'submitLabel' => 'Salvar',
    'containerClass' => 'max-w-5xl',
    'disabled' => false,
    'buttonId' => null,
])

<div class="pointer-events-none fixed inset-x-0 bottom-0 z-40 py-3">
    <div class="pointer-events-auto mx-auto flex w-full {{ $containerClass }} flex-col items-stretch justify-center gap-8 px-4 sm:flex-row sm:items-center sm:px-6 lg:px-8">
        <a href="{{ $cancelUrl }}"
           onclick="return window.confirm('Tem certeza de que deseja cancelar? As alterações não salvas serão perdidas.')"
           class="inline-flex h-14 w-full items-center justify-center whitespace-nowrap rounded-lg border border-gray-300 bg-white px-6 text-base font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 sm:w-56 sm:px-8">
            Cancelar
        </a>

        <button type="submit" form="{{ $formId }}" data-default-label="{{ $submitLabel }}" @if ($buttonId) id="{{ $buttonId }}" @endif
                @disabled($disabled)
                class="h-14 w-full whitespace-nowrap rounded-lg bg-green-600 px-6 text-base font-semibold text-white shadow-sm transition hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 sm:w-56 sm:px-8">
            {{ $submitLabel }}
        </button>
    </div>
</div>
