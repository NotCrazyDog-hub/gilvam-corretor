<div>
    <p class="mb-1 text-sm font-bold uppercase">II. Contratado(a) / prestador(a) de serviços:</p>
    <div class="divide-y divide-black border border-black text-sm">
        <div class="field-wrapper px-2 py-1.5">
            <label for="prestador-nome" class="mr-2 whitespace-nowrap font-bold">NOME / RAZÃO SOCIAL:</label>
            <input id="prestador-nome" name="contratado[nome]" value="{{ old('contratado.nome', $contratado['nome'] ?? '') }}"
                   class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
        </div>
        <div class="grid grid-cols-2 divide-x divide-black">
            <div class="field-wrapper px-2 py-1.5">
                <label for="prestador-documento" class="mr-2 whitespace-nowrap font-bold">CPF / CNPJ:</label>
                <input id="prestador-documento" name="contratado[cpf_cnpj]" value="{{ old('contratado.cpf_cnpj', $contratado['cpf_cnpj'] ?? '') }}"
                       data-mask="cpf-cnpj" inputmode="numeric" maxlength="18" placeholder="CPF ou CNPJ"
                       class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
            </div>
            <div class="field-wrapper px-2 py-1.5">
                <label for="prestador-email" class="mr-2 whitespace-nowrap font-bold">E-MAIL:</label>
                <input id="prestador-email" type="email" name="contratado[email]" value="{{ old('contratado.email', $contratado['email'] ?? '') }}"
                       class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
            </div>
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
