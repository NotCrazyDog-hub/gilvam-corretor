@php($tipoDonatario = old('donatario.tipo', $contratado['tipo'] ?? 'pf'))

<div>
    <p class="font-bold text-sm uppercase mb-1">II. Donatário(a):</p>

    <div class="border border-black divide-y divide-black text-sm">
        <div class="field-wrapper px-2 py-1.5">
            <label for="donatario-tipo" class="font-bold mr-2">TIPO DE PESSOA:</label>
            <select id="donatario-tipo" name="donatario[tipo]" class="border border-slate-300 text-sm px-2 py-1">
                <option value="pf" @selected($tipoDonatario === 'pf')>Pessoa física</option>
                <option value="pj" @selected($tipoDonatario === 'pj')>Pessoa jurídica</option>
            </select>
        </div>

        <div data-donatario-fields="pf" class="divide-y divide-black" @if ($tipoDonatario !== 'pf') hidden @endif>
            {{-- linha 1: NOME --}}
            <div class="field-wrapper px-2 py-1.5">
                <div class="flex items-baseline gap-1">
                    <label class="font-bold whitespace-nowrap">NOME:</label>
                    <input name="donatario[nome]" value="{{ old('donatario.nome', $contratado['nome'] ?? '') }}"
                           class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                </div>
            </div>

            {{-- linha 2: ESTADO CIVIL | PROFISSÃO --}}
            <div class="grid grid-cols-2 divide-x divide-black">
                <div class="field-wrapper px-2 py-1.5">
                    <div class="flex items-baseline gap-1">
                        <label class="font-bold whitespace-nowrap">ESTADO CIVIL:</label>
                        <input name="donatario[estado_civil]" value="{{ old('donatario.estado_civil', $contratado['estado_civil'] ?? '') }}"
                               class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                    </div>
                </div>
                <div class="field-wrapper px-2 py-1.5">
                    <div class="flex items-baseline gap-1">
                        <label class="font-bold whitespace-nowrap">PROFISSÃO:</label>
                        <input name="donatario[profissao]" value="{{ old('donatario.profissao', $contratado['profissao'] ?? '') }}"
                               class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                    </div>
                </div>
            </div>

            {{-- linha 3: CPF | IDENTIDADE --}}
            <div class="grid grid-cols-2 divide-x divide-black">
                <div class="field-wrapper px-2 py-1.5">
                    <div class="flex items-baseline gap-1">
                        <label class="font-bold whitespace-nowrap">CPF(MF) Nº:</label>
                        <input name="donatario[cpf]" value="{{ old('donatario.cpf', $contratado['cpf'] ?? '') }}"
                               data-mask="cpf" inputmode="numeric" maxlength="14" placeholder="000.000.000-00"
                               class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                    </div>
                </div>
                <div class="field-wrapper px-2 py-1.5">
                    <div class="flex items-baseline gap-1">
                        <label class="font-bold whitespace-nowrap">IDENTIDADE Nº:</label>
                        <input name="donatario[identidade]" value="{{ old('donatario.identidade', $contratado['identidade'] ?? '') }}"
                               class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                    </div>
                </div>
            </div>

            {{-- linha 4: NOME DO CÔNJUGE --}}
            <div class="field-wrapper px-2 py-1.5">
                <div class="flex items-baseline gap-1">
                    <label class="font-bold whitespace-nowrap">NOME DO CÔNJUGE:</label>
                    <input name="donatario[conjuge_nome]" value="{{ old('donatario.conjuge_nome', $contratado['conjuge_nome'] ?? '') }}"
                           class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                </div>
            </div>

            {{-- linha 5: IDENTIDADE DO CÔNJUGE --}}
            <div class="field-wrapper px-2 py-1.5">
                <div class="flex items-baseline gap-1">
                    <label class="font-bold whitespace-nowrap">IDENTIDADE DO CÔNJUGE Nº:</label>
                    <input name="donatario[conjuge_identidade]" value="{{ old('donatario.conjuge_identidade', $contratado['conjuge_identidade'] ?? '') }}"
                           class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                </div>
            </div>

            {{-- linha 6: ENDEREÇO --}}
            <div class="field-wrapper px-2 py-1.5">
                <div class="flex items-baseline gap-1">
                    <label class="font-bold whitespace-nowrap">ENDEREÇO:</label>
                    <input name="donatario[endereco]" value="{{ old('donatario.endereco', $contratado['endereco'] ?? '') }}"
                           class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                </div>
            </div>

            {{-- linha 7: TELEFONES --}}
            <div class="field-wrapper px-2 py-1.5">
                <div class="flex items-baseline gap-1">
                    <label class="font-bold whitespace-nowrap">TELEFONES:</label>
                    <input name="donatario[telefones]" value="{{ old('donatario.telefones', $contratado['telefones'] ?? '') }}"
                           class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                </div>
            </div>
        </div>

        <div data-donatario-fields="pj" class="divide-y divide-black" @if ($tipoDonatario !== 'pj') hidden @endif>
            {{-- linha 1: RAZÃO SOCIAL --}}
            <div class="field-wrapper px-2 py-1.5">
                <div class="flex items-baseline gap-1">
                    <label class="font-bold whitespace-nowrap">RAZÃO SOCIAL:</label>
                    <input name="donatario[razao_social]" value="{{ old('donatario.razao_social', $contratado['razao_social'] ?? '') }}"
                           class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                </div>
            </div>

            {{-- linha 2: NOME FANTASIA | INSCRIÇÃO ESTADUAL --}}
            <div class="grid grid-cols-2 divide-x divide-black">
                <div class="field-wrapper px-2 py-1.5">
                    <div class="flex items-baseline gap-1">
                        <label class="font-bold whitespace-nowrap">NOME FANTASIA:</label>
                        <input name="donatario[nome_fantasia]" value="{{ old('donatario.nome_fantasia', $contratado['nome_fantasia'] ?? '') }}"
                               class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                    </div>
                </div>
                <div class="field-wrapper px-2 py-1.5">
                    <div class="flex items-baseline gap-1">
                        <label class="font-bold whitespace-nowrap">INSCRIÇÃO ESTADUAL:</label>
                        <input name="donatario[inscricao_estadual]" value="{{ old('donatario.inscricao_estadual', $contratado['inscricao_estadual'] ?? '') }}"
                               class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                    </div>
                </div>
            </div>

            {{-- linha 3: CNPJ --}}
            <div class="field-wrapper px-2 py-1.5">
                <div class="flex items-baseline gap-1">
                    <label class="font-bold whitespace-nowrap">CNPJ(MF) Nº:</label>
                    <input name="donatario[cnpj]" value="{{ old('donatario.cnpj', $contratado['cnpj'] ?? '') }}"
                           data-mask="cnpj" inputmode="numeric" maxlength="18" placeholder="00.000.000/0000-00"
                           class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                </div>
            </div>

            {{-- linha 4: ENDEREÇO --}}
            <div class="field-wrapper px-2 py-1.5">
                <div class="flex items-baseline gap-1">
                    <label class="font-bold whitespace-nowrap">ENDEREÇO:</label>
                    <input name="donatario[endereco]" value="{{ old('donatario.endereco', $contratado['endereco'] ?? '') }}"
                           class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                </div>
            </div>

            {{-- linha 5: TELEFONES --}}
            <div class="field-wrapper px-2 py-1.5">
                <div class="flex items-baseline gap-1">
                    <label class="font-bold whitespace-nowrap">TELEFONES:</label>
                    <input name="donatario[telefones]" value="{{ old('donatario.telefones', $contratado['telefones'] ?? '') }}"
                           class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                </div>
            </div>

            {{-- linha 6: REPRESENTANTE LEGAL --}}
            <div class="field-wrapper px-2 py-1.5">
                <div class="flex items-baseline gap-1">
                    <label class="font-bold whitespace-nowrap">REPRESENTANTE LEGAL:</label>
                    <input name="donatario[representante_legal_nome]" value="{{ old('donatario.representante_legal_nome', $contratado['representante_legal_nome'] ?? '') }}"
                           class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                </div>
            </div>

            {{-- linha 7: CARGO | CPF DO REPRESENTANTE --}}
            <div class="grid grid-cols-2 divide-x divide-black">
                <div class="field-wrapper px-2 py-1.5">
                    <div class="flex items-baseline gap-1">
                        <label class="font-bold whitespace-nowrap">CARGO:</label>
                        <input name="donatario[representante_legal_cargo]" value="{{ old('donatario.representante_legal_cargo', $contratado['representante_legal_cargo'] ?? '') }}"
                               class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                    </div>
                </div>
                <div class="field-wrapper px-2 py-1.5">
                    <div class="flex items-baseline gap-1">
                        <label class="font-bold whitespace-nowrap">CPF(MF) Nº DO REPRESENTANTE:</label>
                        <input name="donatario[representante_legal_cpf]" value="{{ old('donatario.representante_legal_cpf', $contratado['representante_legal_cpf'] ?? '') }}"
                               data-mask="cpf" inputmode="numeric" maxlength="14" placeholder="000.000.000-00"
                               class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>