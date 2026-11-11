<div>
    <p class="font-bold text-sm uppercase mb-1">I. Locador(a):</p>
    <div class="border border-black text-sm">
        <div data-person-fields="pj" class="divide-y divide-black" @if (!$isPj) hidden @endif>
            <div class="field-wrapper px-2 py-1.5"><div class="flex items-baseline gap-1">
                <label class="font-bold whitespace-nowrap">RAZÃO SOCIAL:</label>
                <input name="locador[razao_social]" value="{{ $locador['razao_social'] ?? '' }}" class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
            </div></div>
            <div class="grid grid-cols-2 divide-x divide-black">
                <div class="field-wrapper px-2 py-1.5"><div class="flex items-baseline gap-1">
                    <label class="font-bold whitespace-nowrap">NOME FANTASIA:</label>
                    <input name="locador[nome_fantasia]" value="{{ $locador['nome_fantasia'] ?? '' }}" class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                </div></div>
                <div class="field-wrapper px-2 py-1.5"><div class="flex items-baseline gap-1">
                    <label class="font-bold whitespace-nowrap">INSCRIÇÃO ESTADUAL:</label>
                    <input name="locador[inscricao_estadual]" value="{{ $locador['inscricao_estadual'] ?? '' }}" class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                </div></div>
            </div>
            <div class="field-wrapper px-2 py-1.5"><div class="flex items-baseline gap-1">
                <label class="font-bold whitespace-nowrap">CNPJ(MF) Nº:</label>
                <input name="locador[cnpj]" value="{{ $locador['cnpj'] ?? '' }}" data-mask="cnpj" inputmode="numeric" maxlength="18" placeholder="00.000.000/0000-00" class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
            </div></div>
            <div class="field-wrapper px-2 py-1.5"><div class="flex items-baseline gap-1">
                <label class="font-bold whitespace-nowrap">REPRESENTANTE LEGAL:</label>
                <input name="locador[representante_legal_nome]" value="{{ $locador['representante_legal_nome'] ?? '' }}" class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
            </div></div>
            <div class="grid grid-cols-2 divide-x divide-black">
                <div class="field-wrapper px-2 py-1.5"><div class="flex items-baseline gap-1">
                    <label class="font-bold whitespace-nowrap">CARGO:</label>
                    <input name="locador[representante_legal_cargo]" value="{{ $locador['representante_legal_cargo'] ?? '' }}" class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                </div></div>
                <div class="field-wrapper px-2 py-1.5"><div class="flex items-baseline gap-1">
                    <label class="font-bold whitespace-nowrap">CPF(MF) Nº DO REPRESENTANTE:</label>
                    <input name="locador[representante_legal_cpf]" value="{{ $locador['representante_legal_cpf'] ?? '' }}" data-mask="cpf" inputmode="numeric" maxlength="14" placeholder="000.000.000-00" class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                </div></div>
            </div>
            @foreach (['endereco' => 'ENDEREÇO', 'telefones' => 'TELEFONES'] as $field => $label)
                <div class="field-wrapper px-2 py-1.5"><div class="flex items-baseline gap-1">
                    <label class="font-bold whitespace-nowrap">{{ $label }}:</label>
                    <input name="locador[{{ $field }}]" value="{{ $locador[$field] ?? '' }}" class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                </div></div>
            @endforeach
        </div>
        <div data-person-fields="pf" class="divide-y divide-black" @if ($isPj) hidden @endif>
            @foreach ([
                'nome' => 'NOME', 'estado_civil' => 'ESTADO CIVIL', 'profissao' => 'PROFISSÃO',
                'cpf' => 'CPF(MF) Nº', 'identidade' => 'IDENTIDADE Nº', 'conjuge_nome' => 'NOME DO CÔNJUGE',
                'conjuge_identidade' => 'IDENTIDADE DO CÔNJUGE Nº', 'endereco' => 'ENDEREÇO', 'telefones' => 'TELEFONES',
            ] as $field => $label)
                <div class="field-wrapper px-2 py-1.5"><div class="flex items-baseline gap-1">
                    <label class="font-bold whitespace-nowrap">{{ $label }}:</label>
                    <input name="locador[{{ $field }}]" value="{{ $locador[$field] ?? '' }}" class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                </div></div>
            @endforeach
        </div>
    </div>
</div>
