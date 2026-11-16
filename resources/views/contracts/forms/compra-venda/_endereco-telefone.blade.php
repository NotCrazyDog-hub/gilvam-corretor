<div class="field-wrapper px-2 py-1.5">
    <div class="flex items-baseline gap-1">
        <label class="whitespace-nowrap font-bold">ENDEREÇO:</label>
        <input name="{{ $pessoa }}[endereco]" value="{{ $dadosPessoa['endereco'] ?? '' }}"
               class="min-w-0 flex-1 border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
    </div>
</div>
<div class="field-wrapper px-2 py-1.5">
    <div class="flex items-baseline gap-1">
        <label class="whitespace-nowrap font-bold">TELEFONES:</label>
        <input name="{{ $pessoa }}[telefones]" value="{{ $dadosPessoa['telefones'] ?? $dadosPessoa['telefone'] ?? '' }}"
               class="min-w-0 flex-1 border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
    </div>
</div>
