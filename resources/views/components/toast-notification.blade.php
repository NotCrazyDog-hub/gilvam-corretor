@php
    $errorMessage = session('error')
        ?? ($errors->any() ? 'Não foi possível concluir a ação. Confira os campos destacados e tente novamente.' : null);
    $message = $errorMessage ?? session('success') ?? session('sucesso');
    $type = $errorMessage ? 'error' : 'success';
@endphp

<div
    x-data="{
        visible: @js($message !== null),
        type: @js($type),
        message: @js($message ?? ''),
        timeout: null,
        showToast(type, message) {
            this.type = type;
            this.message = message;
            this.visible = true;
            window.clearTimeout(this.timeout);
            this.timeout = window.setTimeout(() => this.visible = false, 4500);
        }
    }"
    x-init="if (visible) timeout = window.setTimeout(() => visible = false, 4500)"
    x-on:app-toast.window="showToast($event.detail.type, $event.detail.message)"
    x-show="visible"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 translate-y-2"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-2"
    role="status"
    :aria-live="type === 'error' ? 'assertive' : 'polite'"
    class="fixed bottom-4 right-4 z-[100] flex w-[calc(100%-2rem)] max-w-sm items-start gap-3 rounded-lg border p-4 text-white shadow-lg"
    :class="type === 'error' ? 'border-red-700 bg-red-600' : 'border-green-700 bg-green-600'"
    x-cloak
>
    <span class="mt-0.5 inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-white/20 text-white">
        <svg x-show="type === 'success'" class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" />
        </svg>
        <svg x-show="type === 'error'" class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
        </svg>
    </span>

    <p class="flex-1 text-sm font-medium" x-text="message"></p>

    <button type="button" aria-label="Fechar notificação" @click="visible = false"
            class="-mr-1 -mt-1 rounded p-1 text-white/80 transition hover:bg-white/20 hover:text-white">
        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="m6 6 12 12M18 6 6 18" />
        </svg>
    </button>
</div>
