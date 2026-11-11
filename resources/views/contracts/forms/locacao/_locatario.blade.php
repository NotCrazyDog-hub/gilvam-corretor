<div>
    <p class="font-bold text-sm uppercase mb-1">II. Locatário(a):</p>
    <div class="border border-black divide-y divide-black text-sm">
        @foreach ([
            'nome' => 'NOME', 'estado_civil' => 'ESTADO CIVIL', 'profissao' => 'PROFISSÃO',
            'cpf_cnpj' => 'CPF(MF) ou CNPJ(MF)', 'identidade' => 'IDENTIDADE Nº',
            'endereco' => 'ENDEREÇO', 'telefones' => 'TELEFONES',
        ] as $field => $label)
            <div class="field-wrapper px-2 py-1.5"><div class="flex items-baseline gap-1">
                <label class="font-bold whitespace-nowrap">{{ $label }}:</label>
                <input name="locatario[{{ $field }}]" value="{{ $locatario[$field] ?? '' }}" class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
            </div></div>
        @endforeach
    </div>
</div>
