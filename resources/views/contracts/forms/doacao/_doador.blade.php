{{-- I. CONTRATANTE — segue a ordem/pareamento de linhas do template .docx --}}
<div>
    <p class="font-bold text-sm uppercase mb-1">{{ $tipoContratoKey === 'doacao' ? 'I. Doador:' : 'I. Contratante:' }}</p>

    <div class="border border-black text-sm">
        <div data-person-fields="pj" class="divide-y divide-black" @if (!$isPj) hidden @endif>
            @include('contracts._contratante-pj')
        </div>
        <div data-person-fields="pf" class="divide-y divide-black" @if ($isPj) hidden @endif>
            {{-- linha 1: NOME --}}
            <div class="field-wrapper px-2 py-1.5">
                <div class="flex items-baseline gap-1">
                    <label class="font-bold whitespace-nowrap">NOME:</label>
                    <input name="contratante[nome]" value="{{ $contratante['nome'] ?? '' }}"
                           class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                </div>
            </div>

            {{-- linha 2: ESTADO CIVIL | PROFISSÃO --}}
            <div class="grid grid-cols-2 divide-x divide-black">
                <div class="field-wrapper px-2 py-1.5">
                    <div class="flex items-baseline gap-1">
                        <label class="font-bold whitespace-nowrap">ESTADO CIVIL:</label>
                        <input name="contratante[estado_civil]" value="{{ $contratante['estado_civil'] ?? '' }}"
                               class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                    </div>
                </div>
                <div class="field-wrapper px-2 py-1.5">
                    <div class="flex items-baseline gap-1">
                        <label class="font-bold whitespace-nowrap">PROFISSÃO:</label>
                        <input name="contratante[profissao]" value="{{ $contratante['profissao'] ?? '' }}"
                               class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                    </div>
                </div>
            </div>

            {{-- linha 3: CPF | IDENTIDADE --}}
            <div class="grid grid-cols-2 divide-x divide-black">
                <div class="field-wrapper px-2 py-1.5">
                    <div class="flex items-baseline gap-1">
                        <label class="font-bold whitespace-nowrap">CPF(MF) Nº:</label>
                        <input name="contratante[cpf]" value="{{ $contratante['cpf'] ?? '' }}"
                               class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                    </div>
                </div>
                <div class="field-wrapper px-2 py-1.5">
                    <div class="flex items-baseline gap-1">
                        <label class="font-bold whitespace-nowrap">IDENTIDADE Nº:</label>
                        <input name="contratante[identidade]" value="{{ $contratante['identidade'] ?? '' }}"
                               class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                    </div>
                </div>
            </div>

            {{-- linha 4: NOME DO CÔNJUGE --}}
            <div class="field-wrapper px-2 py-1.5">
                <div class="flex items-baseline gap-1">
                    <label class="font-bold whitespace-nowrap">NOME DO CÔNJUGE:</label>
                    <input name="contratante[conjuge_nome]" value="{{ $contratante['conjuge_nome'] ?? '' }}"
                           class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                </div>
            </div>

            {{-- linha 5: IDENTIDADE DO CÔNJUGE --}}
            <div class="field-wrapper px-2 py-1.5">
                <div class="flex items-baseline gap-1">
                    <label class="font-bold whitespace-nowrap">IDENTIDADE DO CÔNJUGE Nº:</label>
                    <input name="contratante[conjuge_identidade]" value="{{ $contratante['conjuge_identidade'] ?? '' }}"
                           class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                </div>
            </div>

            {{-- linha 6: ENDEREÇO --}}
            <div class="field-wrapper px-2 py-1.5">
                <div class="flex items-baseline gap-1">
                    <label class="font-bold whitespace-nowrap">ENDEREÇO:</label>
                    <input name="contratante[endereco]" value="{{ $contratante['endereco'] ?? '' }}"
                           class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                </div>
            </div>

            {{-- linha 7: TELEFONES --}}
            <div class="field-wrapper px-2 py-1.5">
                <div class="flex items-baseline gap-1">
                    <label class="font-bold whitespace-nowrap">TELEFONES:</label>
                    <input name="contratante[telefones]" value="{{ $contratante['telefones'] ?? '' }}"
                           class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                </div>
            </div>
        </div>
    </div>
</div>