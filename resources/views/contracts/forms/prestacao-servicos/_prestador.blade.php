@php($tipoPrestador = old('contratado.tipo', $contratado['tipo'] ?? 'pf'))

<div>
    <p class="mb-1 text-sm font-bold uppercase">II. Contratado(a):</p>
    <div class="mb-2 border border-black px-2 py-1.5 text-sm">
        <label for="prestador-tipo" class="mr-2 font-bold">TIPO DE PESSOA:</label>
        <select id="prestador-tipo" name="contratado[tipo]" class="border border-slate-300 px-2 py-1 text-sm">
            <option value="pf" @selected($tipoPrestador === 'pf')>Pessoa física</option>
            <option value="pj" @selected($tipoPrestador === 'pj')>Pessoa jurídica</option>
        </select>
    </div>

    <div class="divide-y divide-black border border-black text-sm">
        <div data-servico-contratado="pf" class="divide-y divide-black" @if ($tipoPrestador !== 'pf') hidden @endif>
            <div class="field-wrapper px-2 py-1.5">
                <label for="prestador-nome" class="mr-2 whitespace-nowrap font-bold">NOME:</label>
                <input id="prestador-nome" name="contratado[nome]" value="{{ old('contratado.nome', $contratado['nome'] ?? '') }}"
                       class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
            </div>
            <div class="grid grid-cols-2 divide-x divide-black">
                <div class="field-wrapper px-2 py-1.5">
                    <label for="prestador-estado-civil" class="mr-2 whitespace-nowrap font-bold">ESTADO CIVIL:</label>
                    <input id="prestador-estado-civil" name="contratado[estado_civil]" value="{{ old('contratado.estado_civil', $contratado['estado_civil'] ?? '') }}"
                           class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
                </div>
                <div class="field-wrapper px-2 py-1.5">
                    <label for="prestador-profissao" class="mr-2 whitespace-nowrap font-bold">PROFISSÃO:</label>
                    <input id="prestador-profissao" name="contratado[profissao]" value="{{ old('contratado.profissao', $contratado['profissao'] ?? '') }}"
                           class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
                </div>
            </div>
            <div class="grid grid-cols-2 divide-x divide-black">
                <div class="field-wrapper px-2 py-1.5">
                    <label for="prestador-cpf" class="mr-2 whitespace-nowrap font-bold">CPF(MF) Nº:</label>
                    <input id="prestador-cpf" name="contratado[cpf]" value="{{ old('contratado.cpf', $contratado['cpf'] ?? '') }}"
                           data-mask="cpf" inputmode="numeric" maxlength="14" placeholder="000.000.000-00"
                           class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
                </div>
                <div class="field-wrapper px-2 py-1.5">
                    <label for="prestador-identidade" class="mr-2 whitespace-nowrap font-bold">IDENTIDADE Nº:</label>
                    <input id="prestador-identidade" name="contratado[identidade]" value="{{ old('contratado.identidade', $contratado['identidade'] ?? '') }}"
                           class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
                </div>
            </div>
        </div>

        <div data-servico-contratado="pj" class="divide-y divide-black" @if ($tipoPrestador !== 'pj') hidden @endif>
            <div class="field-wrapper px-2 py-1.5">
                <label for="prestador-razao-social" class="mr-2 whitespace-nowrap font-bold">RAZÃO SOCIAL:</label>
                <input id="prestador-razao-social" name="contratado[razao_social]" value="{{ old('contratado.razao_social', $contratado['razao_social'] ?? '') }}"
                       class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
            </div>
            <div class="grid grid-cols-2 divide-x divide-black">
                <div class="field-wrapper px-2 py-1.5">
                    <label for="prestador-nome-fantasia" class="mr-2 whitespace-nowrap font-bold">NOME FANTASIA:</label>
                    <input id="prestador-nome-fantasia" name="contratado[nome_fantasia]" value="{{ old('contratado.nome_fantasia', $contratado['nome_fantasia'] ?? '') }}"
                           class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
                </div>
                <div class="field-wrapper px-2 py-1.5">
                    <label for="prestador-inscricao-estadual" class="mr-2 whitespace-nowrap font-bold">INSCRIÇÃO ESTADUAL:</label>
                    <input id="prestador-inscricao-estadual" name="contratado[inscricao_estadual]" value="{{ old('contratado.inscricao_estadual', $contratado['inscricao_estadual'] ?? '') }}"
                           class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
                </div>
            </div>
            <div class="field-wrapper px-2 py-1.5">
                <label for="prestador-cnpj" class="mr-2 whitespace-nowrap font-bold">CNPJ(MF) Nº:</label>
                <input id="prestador-cnpj" name="contratado[cnpj]" value="{{ old('contratado.cnpj', $contratado['cnpj'] ?? '') }}"
                       data-mask="cnpj" inputmode="numeric" maxlength="18" placeholder="00.000.000/0000-00"
                       class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
            </div>
            <div class="field-wrapper px-2 py-1.5">
                <label for="prestador-representante" class="mr-2 whitespace-nowrap font-bold">REPRESENTANTE LEGAL:</label>
                <input id="prestador-representante" name="contratado[representante_legal_nome]" value="{{ old('contratado.representante_legal_nome', $contratado['representante_legal_nome'] ?? '') }}"
                       class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
            </div>
            <div class="grid grid-cols-2 divide-x divide-black">
                <div class="field-wrapper px-2 py-1.5">
                    <label for="prestador-cargo" class="mr-2 whitespace-nowrap font-bold">CARGO:</label>
                    <input id="prestador-cargo" name="contratado[representante_legal_cargo]" value="{{ old('contratado.representante_legal_cargo', $contratado['representante_legal_cargo'] ?? '') }}"
                           class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
                </div>
                <div class="field-wrapper px-2 py-1.5">
                    <label for="prestador-representante-cpf" class="mr-2 whitespace-nowrap font-bold">CPF DO REPRESENTANTE:</label>
                    <input id="prestador-representante-cpf" name="contratado[representante_legal_cpf]" value="{{ old('contratado.representante_legal_cpf', $contratado['representante_legal_cpf'] ?? '') }}"
                           data-mask="cpf" inputmode="numeric" maxlength="14" placeholder="000.000.000-00"
                           class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
                </div>
            </div>
        </div>

        <div class="field-wrapper px-2 py-1.5">
            <label for="prestador-email" class="mr-2 whitespace-nowrap font-bold">E-MAIL:</label>
            <input id="prestador-email" type="email" name="contratado[email]" value="{{ old('contratado.email', $contratado['email'] ?? '') }}"
                   class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
        </div>
        <div class="field-wrapper px-2 py-1.5">
            <label for="prestador-endereco" class="mr-2 whitespace-nowrap font-bold">ENDEREÇO:</label>
            <input id="prestador-endereco" name="contratado[endereco]" value="{{ old('contratado.endereco', $contratado['endereco'] ?? '') }}"
                   class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
        </div>
        <div class="field-wrapper px-2 py-1.5">
            <label for="prestador-telefones" class="mr-2 whitespace-nowrap font-bold">TELEFONES:</label>
            <input id="prestador-telefones" name="contratado[telefones]" value="{{ old('contratado.telefones', $contratado['telefones'] ?? '') }}"
                   class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
        </div>
    </div>
</div>
