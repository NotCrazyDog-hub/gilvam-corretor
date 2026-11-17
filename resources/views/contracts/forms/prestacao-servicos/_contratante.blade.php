<div>
    <p class="mb-1 text-sm font-bold uppercase">I. Contratante:</p>
    <div class="divide-y divide-black border border-black text-sm">
        <div data-person-fields="pf" class="divide-y divide-black" @if ($isPj) hidden @endif>
            <div class="field-wrapper px-2 py-1.5">
                <label for="contratante-nome" class="mr-2 whitespace-nowrap font-bold">NOME:</label>
                <input id="contratante-nome" name="contratante[nome]" value="{{ $contratante['nome'] ?? '' }}"
                       class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
            </div>
            <div class="grid grid-cols-2 divide-x divide-black">
                <div class="field-wrapper px-2 py-1.5">
                    <label for="contratante-estado-civil" class="mr-2 whitespace-nowrap font-bold">ESTADO CIVIL:</label>
                    <input id="contratante-estado-civil" name="contratante[estado_civil]" value="{{ $contratante['estado_civil'] ?? '' }}"
                           class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
                </div>
                <div class="field-wrapper px-2 py-1.5">
                    <label for="contratante-profissao" class="mr-2 whitespace-nowrap font-bold">PROFISSÃO:</label>
                    <input id="contratante-profissao" name="contratante[profissao]" value="{{ $contratante['profissao'] ?? '' }}"
                           class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
                </div>
            </div>
            <div class="grid grid-cols-2 divide-x divide-black">
                <div class="field-wrapper px-2 py-1.5">
                    <label for="contratante-cpf" class="mr-2 whitespace-nowrap font-bold">CPF(MF) Nº:</label>
                    <input id="contratante-cpf" name="contratante[cpf]" value="{{ $contratante['cpf'] ?? '' }}"
                           data-mask="cpf" inputmode="numeric" maxlength="14" placeholder="000.000.000-00"
                           class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
                </div>
                <div class="field-wrapper px-2 py-1.5">
                    <label for="contratante-identidade" class="mr-2 whitespace-nowrap font-bold">IDENTIDADE Nº:</label>
                    <input id="contratante-identidade" name="contratante[identidade]" value="{{ $contratante['identidade'] ?? '' }}"
                           class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
                </div>
            </div>
        </div>

        <div class="field-wrapper px-2 py-1.5">
            <label for="contratante-email" class="mr-2 whitespace-nowrap font-bold">E-MAIL:</label>
            <input id="contratante-email" type="email" name="contratante[email]" value="{{ old('contratante.email', $contratante['email'] ?? '') }}"
                   class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
        </div>

        <div data-person-fields="pj" class="divide-y divide-black" @unless ($isPj) hidden @endunless>
            <div class="field-wrapper px-2 py-1.5">
                <label for="contratante-razao-social" class="mr-2 whitespace-nowrap font-bold">RAZÃO SOCIAL:</label>
                <input id="contratante-razao-social" name="contratante[razao_social]" value="{{ $contratante['razao_social'] ?? '' }}"
                       class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
            </div>
            <div class="grid grid-cols-2 divide-x divide-black">
                <div class="field-wrapper px-2 py-1.5">
                    <label for="contratante-nome-fantasia" class="mr-2 whitespace-nowrap font-bold">NOME FANTASIA:</label>
                    <input id="contratante-nome-fantasia" name="contratante[nome_fantasia]" value="{{ $contratante['nome_fantasia'] ?? '' }}"
                           class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
                </div>
                <div class="field-wrapper px-2 py-1.5">
                    <label for="contratante-inscricao-estadual" class="mr-2 whitespace-nowrap font-bold">INSCRIÇÃO ESTADUAL:</label>
                    <input id="contratante-inscricao-estadual" name="contratante[inscricao_estadual]" value="{{ $contratante['inscricao_estadual'] ?? '' }}"
                           class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
                </div>
            </div>
            <div class="field-wrapper px-2 py-1.5">
                <label for="contratante-cnpj" class="mr-2 whitespace-nowrap font-bold">CNPJ(MF) Nº:</label>
                <input id="contratante-cnpj" name="contratante[cnpj]" value="{{ $contratante['cnpj'] ?? '' }}"
                       data-mask="cnpj" inputmode="numeric" maxlength="18" placeholder="00.000.000/0000-00"
                       class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
            </div>
        </div>

        <div class="field-wrapper px-2 py-1.5">
            <label for="contratante-endereco" class="mr-2 whitespace-nowrap font-bold">ENDEREÇO:</label>
            <input id="contratante-endereco" name="contratante[endereco]" value="{{ $contratante['endereco'] ?? '' }}"
                   class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
        </div>
        <div class="field-wrapper px-2 py-1.5">
            <label for="contratante-telefones" class="mr-2 whitespace-nowrap font-bold">TELEFONES:</label>
            <input id="contratante-telefones" name="contratante[telefones]" value="{{ $contratante['telefones'] ?? '' }}"
                   class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
        </div>

        <div data-person-fields="pj" class="divide-y divide-black" @unless ($isPj) hidden @endunless>
            <div class="field-wrapper px-2 py-1.5">
                <label for="contratante-representante" class="mr-2 whitespace-nowrap font-bold">REPRESENTANTE LEGAL:</label>
                <input id="contratante-representante" name="contratante[representante_legal_nome]" value="{{ $contratante['representante_legal_nome'] ?? '' }}"
                       class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
            </div>
            <div class="grid grid-cols-2 divide-x divide-black">
                <div class="field-wrapper px-2 py-1.5">
                    <label for="contratante-cargo" class="mr-2 whitespace-nowrap font-bold">CARGO:</label>
                    <input id="contratante-cargo" name="contratante[representante_legal_cargo]" value="{{ $contratante['representante_legal_cargo'] ?? '' }}"
                           class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
                </div>
                <div class="field-wrapper px-2 py-1.5">
                    <label for="contratante-cpf-representante" class="mr-2 whitespace-nowrap font-bold">CPF(MF) Nº DO REPRESENTANTE:</label>
                    <input id="contratante-cpf-representante" name="contratante[representante_legal_cpf]" value="{{ $contratante['representante_legal_cpf'] ?? '' }}"
                           data-mask="cpf" inputmode="numeric" maxlength="14" placeholder="000.000.000-00"
                           class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
                </div>
            </div>
        </div>
    </div>
</div>
