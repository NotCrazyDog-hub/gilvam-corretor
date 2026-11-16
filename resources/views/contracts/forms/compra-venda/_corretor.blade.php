<div>
    <p class="mb-1 text-sm font-bold uppercase">III. Intermediação — Corretor(a) interveniente:</p>

    <div class="border border-black divide-y divide-black text-sm">
        <div class="field-wrapper px-2 py-1.5">
            <div class="flex items-baseline gap-1">
                <label class="whitespace-nowrap font-bold">NOME:</label>
                <input name="contratado[nome]" value="{{ $contratado['nome'] ?? 'Gilvam Vieira' }}"
                       class="min-w-0 flex-1 border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
            </div>
        </div>
        <div class="grid grid-cols-2 divide-x divide-black">
            <div class="field-wrapper px-2 py-1.5">
                <div class="flex items-baseline gap-1">
                    <label class="whitespace-nowrap font-bold">CPF(MF) OU CNPJ(MF):</label>
                    <input name="contratado[cpf_cnpj]" value="{{ $contratado['cpf_cnpj'] ?? '28851441000190' }}"
                           class="min-w-0 flex-1 border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
                </div>
            </div>
            <div class="field-wrapper px-2 py-1.5">
                <div class="flex items-baseline gap-1">
                    <label class="whitespace-nowrap font-bold">CRECI - 15ª REGIÃO Nº:</label>
                    <input name="contratado[creci]" value="{{ $contratado['creci'] ?? '11564' }}"
                           class="min-w-0 flex-1 border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
                </div>
            </div>
        </div>
        <div class="field-wrapper px-2 py-1.5">
            <div class="flex items-baseline gap-1">
                <label class="whitespace-nowrap font-bold">ENDEREÇO:</label>
                <input name="contratado[endereco]" value="{{ $contratado['endereco'] ?? 'Rua Cônego Agostinho, 1597 - Centro, Russas - CE, 62900-000, Brasil' }}"
                       class="min-w-0 flex-1 border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
            </div>
        </div>
        <div class="field-wrapper px-2 py-1.5">
            <div class="flex items-baseline gap-1">
                <label class="whitespace-nowrap font-bold">TELEFONES:</label>
                <input name="contratado[telefones]" value="{{ $contratado['telefones'] ?? '8834110124' }}"
                       class="min-w-0 flex-1 border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
            </div>
        </div>
    </div>
</div>
