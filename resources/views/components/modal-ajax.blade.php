{{--
    Modal AJAX. Escuta o evento "abrir-modal" com { url } e carrega o HTML da URL.
    Uso: $dispatch('abrir-modal', { url: '...' })
--}}
<div
    x-data="{
        aberto: false,
        carregando: false,
        html: '',
        async abrir(url) {
            this.aberto = true;
            this.carregando = true;
            this.html = '';
            try {
                const resp = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    }
                });
                if (!resp.ok) throw new Error(resp.status);
                this.html = await resp.text();
            } catch (e) {
                this.html = '<p class=\'text-red-600 text-sm\'>Erro ao carregar o conteúdo.</p>';
            } finally {
                this.carregando = false;
            }
        },
        fechar() {
            this.aberto = false;
            this.html = '';
        }
    }"
    x-on:abrir-modal.window="abrir($event.detail.url)"
    x-on:keydown.escape.window="fechar()"
    x-show="aberto"
    x-cloak
    style="display: none;"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    role="dialog"
    aria-modal="true"
>
    {{-- Fundo escuro --}}
    <div class="absolute inset-0 bg-gray-900/60" x-on:click="fechar()"></div>

    {{-- Caixa do modal --}}
    <div class="relative bg-gray-50 rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto p-6">
        <button type="button" x-on:click="fechar()"
                class="absolute top-3 right-3 text-gray-400 hover:text-gray-700 text-2xl leading-none"
                aria-label="Fechar">&times;</button>

        <div x-show="carregando" class="py-10 text-center text-sm text-gray-500">
            Carregando...
        </div>

        <div x-show="!carregando" x-html="html"></div>
    </div>
</div>
