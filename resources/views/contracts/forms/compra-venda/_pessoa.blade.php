<div>
    <p class="mb-1 text-sm font-bold uppercase">{{ $titulo }}</p>

    @if ($mostrarSeletorTipo)
        <div class="field-wrapper mb-2 border border-black px-2 py-1.5 text-sm">
            <label for="comprador-tipo" class="mr-2 font-bold">TIPO DE PESSOA:</label>
            <select id="comprador-tipo" name="comprador[tipo]" class="border border-slate-300 px-2 py-1 text-sm">
                <option value="pf" @selected($tipoPessoa === 'pf')>Pessoa física</option>
                <option value="pj" @selected($tipoPessoa === 'pj')>Pessoa jurídica</option>
            </select>
        </div>
    @else
        <input type="hidden" name="vendedor[tipo]" value="{{ $tipoPessoa }}">
    @endif

    <div class="border border-black text-sm">
        <div data-{{ $controladorCampos }}="pf" class="divide-y divide-black" @if ($tipoPessoa !== 'pf') hidden @endif>
            <div class="field-wrapper px-2 py-1.5">
                <div class="flex items-baseline gap-1">
                    <label class="whitespace-nowrap font-bold">NOME:</label>
                    <input name="{{ $pessoa }}[nome]" value="{{ $pessoa === 'vendedor' ? ($vendedor['nome'] ?? '') : ($comprador['nome'] ?? '') }}"
                           class="min-w-0 flex-1 border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
                </div>
            </div>
            <div class="grid grid-cols-2 divide-x divide-black">
                <div class="field-wrapper px-2 py-1.5">
                    <div class="flex items-baseline gap-1">
                        <label class="whitespace-nowrap font-bold">ESTADO CIVIL:</label>
                        <input name="{{ $pessoa }}[estado_civil]" value="{{ $pessoa === 'vendedor' ? ($vendedor['estado_civil'] ?? '') : ($comprador['estado_civil'] ?? '') }}"
                               class="min-w-0 flex-1 border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
                    </div>
                </div>
                <div class="field-wrapper px-2 py-1.5">
                    <div class="flex items-baseline gap-1">
                        <label class="whitespace-nowrap font-bold">PROFISSÃO:</label>
                        <input name="{{ $pessoa }}[profissao]" value="{{ $pessoa === 'vendedor' ? ($vendedor['profissao'] ?? '') : ($comprador['profissao'] ?? '') }}"
                               class="min-w-0 flex-1 border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-2 divide-x divide-black">
                <div class="field-wrapper px-2 py-1.5">
                    <div class="flex items-baseline gap-1">
                        <label class="whitespace-nowrap font-bold">NACIONALIDADE:</label>
                        <input name="{{ $pessoa }}[nacionalidade]" value="{{ $pessoa === 'vendedor' ? ($vendedor['nacionalidade'] ?? '') : ($comprador['nacionalidade'] ?? '') }}"
                               class="min-w-0 flex-1 border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
                    </div>
                </div>
                <div class="field-wrapper px-2 py-1.5">
                    <div class="flex items-baseline gap-1">
                        <label class="whitespace-nowrap font-bold">E-MAIL:</label>
                        <input type="email" name="{{ $pessoa }}[email]" value="{{ $pessoa === 'vendedor' ? ($vendedor['email'] ?? '') : ($comprador['email'] ?? '') }}"
                               class="min-w-0 flex-1 border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-2 divide-x divide-black">
                <div class="field-wrapper px-2 py-1.5">
                    <div class="flex items-baseline gap-1">
                        <label class="whitespace-nowrap font-bold">CPF(MF) Nº:</label>
                        <input name="{{ $pessoa }}[cpf]" value="{{ $pessoa === 'vendedor' ? ($vendedor['cpf'] ?? '') : ($comprador['cpf'] ?? '') }}"
                               data-mask="cpf" inputmode="numeric" maxlength="14" placeholder="000.000.000-00"
                               class="min-w-0 flex-1 border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
                    </div>
                </div>
                <div class="field-wrapper px-2 py-1.5">
                    <div class="flex items-baseline gap-1">
                        <label class="whitespace-nowrap font-bold">IDENTIDADE Nº:</label>
                        <input name="{{ $pessoa }}[identidade]" value="{{ $pessoa === 'vendedor' ? ($vendedor['identidade'] ?? '') : ($comprador['identidade'] ?? '') }}"
                               class="min-w-0 flex-1 border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
                    </div>
                </div>
            </div>
            <div class="field-wrapper px-2 py-1.5">
                <div class="flex items-baseline gap-1">
                    <label class="whitespace-nowrap font-bold">NOME DO CÔNJUGE:</label>
                    <input name="{{ $pessoa }}[conjuge_nome]" value="{{ $pessoa === 'vendedor' ? ($vendedor['conjuge_nome'] ?? '') : ($comprador['conjuge_nome'] ?? '') }}"
                           class="min-w-0 flex-1 border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
                </div>
            </div>
            <div class="grid grid-cols-2 divide-x divide-black">
                <div class="field-wrapper px-2 py-1.5">
                    <div class="flex items-baseline gap-1">
                        <label class="whitespace-nowrap font-bold">IDENTIDADE DO CÔNJUGE Nº:</label>
                        <input name="{{ $pessoa }}[conjuge_identidade]" value="{{ $pessoa === 'vendedor' ? ($vendedor['conjuge_identidade'] ?? '') : ($comprador['conjuge_identidade'] ?? '') }}"
                               class="min-w-0 flex-1 border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
                    </div>
                </div>
                <div class="field-wrapper px-2 py-1.5">
                    <div class="flex items-baseline gap-1">
                        <label class="whitespace-nowrap font-bold">CPF DO CÔNJUGE:</label>
                        <input name="{{ $pessoa }}[conjuge_cpf]" value="{{ $pessoa === 'vendedor' ? ($vendedor['conjuge_cpf'] ?? '') : ($comprador['conjuge_cpf'] ?? '') }}"
                               data-mask="cpf" inputmode="numeric" maxlength="14" placeholder="000.000.000-00"
                               class="min-w-0 flex-1 border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
                    </div>
                </div>
            </div>
            <div class="field-wrapper px-2 py-1.5">
                <div class="flex items-baseline gap-1">
                    <label class="whitespace-nowrap font-bold">REGIME DE BENS:</label>
                    <input name="{{ $pessoa }}[regime_bens]" value="{{ $pessoa === 'vendedor' ? ($vendedor['regime_bens'] ?? '') : ($comprador['regime_bens'] ?? '') }}"
                           class="min-w-0 flex-1 border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
                </div>
            </div>
            @include('contracts.forms.compra-venda._endereco-telefone', ['pessoa' => $pessoa, 'dadosPessoa' => $pessoa === 'vendedor' ? $vendedor : $comprador])
        </div>

        <div data-{{ $controladorCampos }}="pj" class="divide-y divide-black" @if ($tipoPessoa !== 'pj') hidden @endif>
            <div class="field-wrapper px-2 py-1.5">
                <div class="flex items-baseline gap-1">
                    <label class="whitespace-nowrap font-bold">RAZÃO SOCIAL:</label>
                    <input name="{{ $pessoa }}[razao_social]" value="{{ $pessoa === 'vendedor' ? ($vendedor['razao_social'] ?? '') : ($comprador['razao_social'] ?? '') }}"
                           class="min-w-0 flex-1 border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
                </div>
            </div>
            <div class="grid grid-cols-2 divide-x divide-black">
                <div class="field-wrapper px-2 py-1.5">
                    <div class="flex items-baseline gap-1">
                        <label class="whitespace-nowrap font-bold">NOME FANTASIA:</label>
                        <input name="{{ $pessoa }}[nome_fantasia]" value="{{ $pessoa === 'vendedor' ? ($vendedor['nome_fantasia'] ?? '') : ($comprador['nome_fantasia'] ?? '') }}"
                               class="min-w-0 flex-1 border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
                    </div>
                </div>
                <div class="field-wrapper px-2 py-1.5">
                    <div class="flex items-baseline gap-1">
                        <label class="whitespace-nowrap font-bold">INSCRIÇÃO ESTADUAL:</label>
                        <input name="{{ $pessoa }}[inscricao_estadual]" value="{{ $pessoa === 'vendedor' ? ($vendedor['inscricao_estadual'] ?? '') : ($comprador['inscricao_estadual'] ?? '') }}"
                               class="min-w-0 flex-1 border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
                    </div>
                </div>
            </div>
            <div class="field-wrapper px-2 py-1.5">
                <div class="flex items-baseline gap-1">
                    <label class="whitespace-nowrap font-bold">CNPJ(MF) Nº:</label>
                    <input name="{{ $pessoa }}[cnpj]" value="{{ $pessoa === 'vendedor' ? ($vendedor['cnpj'] ?? '') : ($comprador['cnpj'] ?? '') }}"
                           data-mask="cnpj" inputmode="numeric" maxlength="18" placeholder="00.000.000/0000-00"
                           class="min-w-0 flex-1 border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
                </div>
            </div>
            <div class="field-wrapper px-2 py-1.5">
                <div class="flex items-baseline gap-1">
                    <label class="whitespace-nowrap font-bold">E-MAIL:</label>
                    <input type="email" name="{{ $pessoa }}[email]" value="{{ $pessoa === 'vendedor' ? ($vendedor['email'] ?? '') : ($comprador['email'] ?? '') }}"
                           class="min-w-0 flex-1 border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
                </div>
            </div>
            @include('contracts.forms.compra-venda._endereco-telefone', ['pessoa' => $pessoa, 'dadosPessoa' => $pessoa === 'vendedor' ? $vendedor : $comprador])
            <div class="field-wrapper px-2 py-1.5">
                <div class="flex items-baseline gap-1">
                    <label class="whitespace-nowrap font-bold">REPRESENTANTE LEGAL:</label>
                    <input name="{{ $pessoa }}[representante_legal_nome]" value="{{ $pessoa === 'vendedor' ? ($vendedor['representante_legal_nome'] ?? '') : ($comprador['representante_legal_nome'] ?? '') }}"
                           class="min-w-0 flex-1 border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
                </div>
            </div>
            <div class="grid grid-cols-2 divide-x divide-black">
                <div class="field-wrapper px-2 py-1.5">
                    <div class="flex items-baseline gap-1">
                        <label class="whitespace-nowrap font-bold">CARGO:</label>
                        <input name="{{ $pessoa }}[representante_legal_cargo]" value="{{ $pessoa === 'vendedor' ? ($vendedor['representante_legal_cargo'] ?? '') : ($comprador['representante_legal_cargo'] ?? '') }}"
                               class="min-w-0 flex-1 border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
                    </div>
                </div>
                <div class="field-wrapper px-2 py-1.5">
                    <div class="flex items-baseline gap-1">
                        <label class="whitespace-nowrap font-bold">CPF DO REPRESENTANTE:</label>
                        <input name="{{ $pessoa }}[representante_legal_cpf]" value="{{ $pessoa === 'vendedor' ? ($vendedor['representante_legal_cpf'] ?? '') : ($comprador['representante_legal_cpf'] ?? '') }}"
                               data-mask="cpf" inputmode="numeric" maxlength="14" placeholder="000.000.000-00"
                               class="min-w-0 flex-1 border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
