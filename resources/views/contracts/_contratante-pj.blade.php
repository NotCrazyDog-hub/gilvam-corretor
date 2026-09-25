{{-- Linhas do bloco "I. CONTRATANTE" para PESSOA JURÍDICA.
     Vai dentro de <div class="border border-black divide-y divide-black text-sm">.
     Usa $contratante (array) definido no form.blade.php.
     Ordem igual à do .docx depois que as linhas de PF são removidas. --}}

{{-- linha 1: RAZÃO SOCIAL --}}
<div class="field-wrapper px-2 py-1.5">
    <div class="flex items-baseline gap-1">
        <label class="font-bold whitespace-nowrap">RAZÃO SOCIAL:</label>
        <input name="contratante[razao_social]" value="{{ $contratante['razao_social'] ?? '' }}"
               class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
    </div>
</div>

{{-- linha 2: NOME FANTASIA | INSCRIÇÃO ESTADUAL --}}
<div class="grid grid-cols-2 divide-x divide-black">
    <div class="field-wrapper px-2 py-1.5">
        <div class="flex items-baseline gap-1">
            <label class="font-bold whitespace-nowrap">NOME FANTASIA:</label>
            <input name="contratante[nome_fantasia]" value="{{ $contratante['nome_fantasia'] ?? '' }}"
                   class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
        </div>
    </div>
    <div class="field-wrapper px-2 py-1.5">
        <div class="flex items-baseline gap-1">
            <label class="font-bold whitespace-nowrap">INSCRIÇÃO ESTADUAL:</label>
            <input name="contratante[inscricao_estadual]" value="{{ $contratante['inscricao_estadual'] ?? '' }}"
                   class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
        </div>
    </div>
</div>

{{-- linha 3: CNPJ --}}
<div class="field-wrapper px-2 py-1.5">
    <div class="flex items-baseline gap-1">
        <label class="font-bold whitespace-nowrap">CNPJ(MF) Nº:</label>
        <input name="contratante[cnpj]" value="{{ $contratante['cnpj'] ?? '' }}"
               data-mask="cnpj" inputmode="numeric" maxlength="18" placeholder="00.000.000/0000-00"
               class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
    </div>
</div>

{{-- linha 4: ENDEREÇO (sede) --}}
<div class="field-wrapper px-2 py-1.5">
    <div class="flex items-baseline gap-1">
        <label class="font-bold whitespace-nowrap">ENDEREÇO:</label>
        <input name="contratante[endereco]" value="{{ $contratante['endereco'] ?? '' }}"
               class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
    </div>
</div>

{{-- linha 5: TELEFONES --}}
<div class="field-wrapper px-2 py-1.5">
    <div class="flex items-baseline gap-1">
        <label class="font-bold whitespace-nowrap">TELEFONES:</label>
        <input name="contratante[telefones]" value="{{ $contratante['telefones'] ?? '' }}"
               class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
    </div>
</div>

{{-- linha 6: REPRESENTANTE LEGAL --}}
<div class="field-wrapper px-2 py-1.5">
    <div class="flex items-baseline gap-1">
        <label class="font-bold whitespace-nowrap">REPRESENTANTE LEGAL:</label>
        <input name="contratante[representante_legal_nome]" value="{{ $contratante['representante_legal_nome'] ?? '' }}"
               class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
    </div>
</div>

{{-- linha 7: CARGO | CPF DO REPRESENTANTE --}}
<div class="grid grid-cols-2 divide-x divide-black">
    <div class="field-wrapper px-2 py-1.5">
        <div class="flex items-baseline gap-1">
            <label class="font-bold whitespace-nowrap">CARGO:</label>
            <input name="contratante[representante_legal_cargo]" value="{{ $contratante['representante_legal_cargo'] ?? '' }}"
                   class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
        </div>
    </div>
    <div class="field-wrapper px-2 py-1.5">
        <div class="flex items-baseline gap-1">
            <label class="font-bold whitespace-nowrap">CPF(MF) Nº DO REPRESENTANTE:</label>
            <input name="contratante[representante_legal_cpf]" value="{{ $contratante['representante_legal_cpf'] ?? '' }}"
                   data-mask="cpf" inputmode="numeric" maxlength="14" placeholder="000.000.000-00"
                   class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
        </div>
    </div>
</div>

{{-- As máscaras de CNPJ e do CPF do representante (campos data-mask acima)
     são aplicadas pelo contract-form.js, reaproveitando mountCnpjMask e
     mountCpfMask — não há script aqui para não duplicar a lógica. --}}