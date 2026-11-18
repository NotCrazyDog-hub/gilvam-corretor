@props([
    'formId',
    'cancelUrl',
    'submitLabel' => 'Salvar',
    'containerClass' => 'max-w-5xl',
    'disabled' => false,
    'buttonId' => null,
])

<div class="fixed inset-x-0 bottom-0 z-40 border-t border-slate-200 bg-white/95 py-3 shadow-[0_-4px_12px_rgba(15,23,42,0.08)] backdrop-blur">
    <div class="mx-auto flex w-full {{ $containerClass }} items-center justify-center gap-2 px-4 sm:gap-3 sm:px-6 lg:px-8">
        <a href="{{ $cancelUrl }}"
           onclick="return window.confirm('Tem certeza de que deseja cancelar? As alterações não salvas serão perdidas.')"
           class="inline-flex items-center justify-center whitespace-nowrap rounded-lg px-4 py-3.5 text-base font-bold text-slate-600 underline decoration-slate-300 underline-offset-4 transition hover:text-slate-900 hover:decoration-slate-600 sm:px-8">
            Cancelar
        </a>

        <button type="submit" form="{{ $formId }}" data-default-label="{{ $submitLabel }}" @if ($buttonId) id="{{ $buttonId }}" @endif
                @disabled($disabled)
                class="whitespace-nowrap rounded-lg bg-blue-600 px-4 py-3.5 text-base font-bold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 sm:px-8">
            {{ $submitLabel }}
        </button>
    </div>
</div>
