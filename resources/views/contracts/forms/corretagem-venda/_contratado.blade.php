                @if ($tipoContratoKey === 'doacao')
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

                            <div data-donatario-fields="pf" @if ($tipoDonatario !== 'pf') hidden @endif>
                                <div class="field-wrapper px-2 py-1.5"><label class="font-bold">NOME:</label>
                                    <input name="donatario[nome]" value="{{ old('donatario.nome', $contratado['nome'] ?? '') }}" class="w-full border-0 bg-transparent focus:ring-0 p-0"></div>
                                <div class="grid grid-cols-2 divide-x divide-black">
                                    <div class="field-wrapper px-2 py-1.5"><label class="font-bold">ESTADO CIVIL:</label><input name="donatario[estado_civil]" value="{{ old('donatario.estado_civil', $contratado['estado_civil'] ?? '') }}" class="w-full border-0 bg-transparent focus:ring-0 p-0"></div>
                                    <div class="field-wrapper px-2 py-1.5"><label class="font-bold">PROFISSÃO:</label><input name="donatario[profissao]" value="{{ old('donatario.profissao', $contratado['profissao'] ?? '') }}" class="w-full border-0 bg-transparent focus:ring-0 p-0"></div>
                                </div>
                                <div class="grid grid-cols-2 divide-x divide-black">
                                    <div class="field-wrapper px-2 py-1.5"><label class="font-bold">CPF:</label><input name="donatario[cpf]" value="{{ old('donatario.cpf', $contratado['cpf'] ?? '') }}" class="w-full border-0 bg-transparent focus:ring-0 p-0" data-mask="cpf"></div>
                                    <div class="field-wrapper px-2 py-1.5"><label class="font-bold">IDENTIDADE:</label><input name="donatario[identidade]" value="{{ old('donatario.identidade', $contratado['identidade'] ?? '') }}" class="w-full border-0 bg-transparent focus:ring-0 p-0"></div>
                                </div>
                                <div class="grid grid-cols-2 divide-x divide-black">
                                    <div class="field-wrapper px-2 py-1.5"><label class="font-bold">NOME DO CÔNJUGE:</label><input name="donatario[conjuge_nome]" value="{{ old('donatario.conjuge_nome', $contratado['conjuge_nome'] ?? '') }}" class="w-full border-0 bg-transparent focus:ring-0 p-0"></div>
                                    <div class="field-wrapper px-2 py-1.5"><label class="font-bold">IDENTIDADE DO CÔNJUGE:</label><input name="donatario[conjuge_identidade]" value="{{ old('donatario.conjuge_identidade', $contratado['conjuge_identidade'] ?? '') }}" class="w-full border-0 bg-transparent focus:ring-0 p-0"></div>
                                </div>
                            </div>

                            <div data-donatario-fields="pj" @if ($tipoDonatario !== 'pj') hidden @endif>
                                <div class="field-wrapper px-2 py-1.5"><label class="font-bold">RAZÃO SOCIAL:</label><input name="donatario[razao_social]" value="{{ old('donatario.razao_social', $contratado['razao_social'] ?? '') }}" class="w-full border-0 bg-transparent focus:ring-0 p-0"></div>
                                <div class="grid grid-cols-2 divide-x divide-black">
                                    <div class="field-wrapper px-2 py-1.5"><label class="font-bold">NOME FANTASIA:</label><input name="donatario[nome_fantasia]" value="{{ old('donatario.nome_fantasia', $contratado['nome_fantasia'] ?? '') }}" class="w-full border-0 bg-transparent focus:ring-0 p-0"></div>
                                    <div class="field-wrapper px-2 py-1.5"><label class="font-bold">INSCRIÇÃO ESTADUAL:</label><input name="donatario[inscricao_estadual]" value="{{ old('donatario.inscricao_estadual', $contratado['inscricao_estadual'] ?? '') }}" class="w-full border-0 bg-transparent focus:ring-0 p-0"></div>
                                </div>
                                <div class="field-wrapper px-2 py-1.5"><label class="font-bold">CNPJ:</label><input name="donatario[cnpj]" value="{{ old('donatario.cnpj', $contratado['cnpj'] ?? '') }}" class="w-full border-0 bg-transparent focus:ring-0 p-0" data-mask="cnpj"></div>
                                <div class="field-wrapper px-2 py-1.5"><label class="font-bold">REPRESENTANTE LEGAL:</label><input name="donatario[representante_legal_nome]" value="{{ old('donatario.representante_legal_nome', $contratado['representante_legal_nome'] ?? '') }}" class="w-full border-0 bg-transparent focus:ring-0 p-0"></div>
                                <div class="grid grid-cols-2 divide-x divide-black">
                                    <div class="field-wrapper px-2 py-1.5"><label class="font-bold">CARGO:</label><input name="donatario[representante_legal_cargo]" value="{{ old('donatario.representante_legal_cargo', $contratado['representante_legal_cargo'] ?? '') }}" class="w-full border-0 bg-transparent focus:ring-0 p-0"></div>
                                    <div class="field-wrapper px-2 py-1.5"><label class="font-bold">CPF DO REPRESENTANTE:</label><input name="donatario[representante_legal_cpf]" value="{{ old('donatario.representante_legal_cpf', $contratado['representante_legal_cpf'] ?? '') }}" class="w-full border-0 bg-transparent focus:ring-0 p-0" data-mask="cpf"></div>
                                </div>
                            </div>

                            <div class="field-wrapper px-2 py-1.5"><label class="font-bold">ENDEREÇO:</label><input name="donatario[endereco]" value="{{ old('donatario.endereco', $contratado['endereco'] ?? '') }}" class="w-full border-0 bg-transparent focus:ring-0 p-0"></div>
                            <div class="field-wrapper px-2 py-1.5"><label class="font-bold">TELEFONES:</label><input name="donatario[telefones]" value="{{ old('donatario.telefones', $contratado['telefones'] ?? '') }}" class="w-full border-0 bg-transparent focus:ring-0 p-0"></div>
                        </div>
                    </div>
                @else
                {{-- II. CONTRATADO --}}
                <div>
                    <p class="font-bold text-sm uppercase mb-1">II. Contratado(a):</p>

                    <div class="border border-black divide-y divide-black text-sm">
                        {{-- linha 1: NOME --}}
                        <div class="field-wrapper px-2 py-1.5">
                            <div class="flex items-baseline gap-1">
                                <label class="font-bold whitespace-nowrap">NOME:</label>
                                <input name="contratado[nome]" value="{{ $contratado['nome'] ?? 'Gilvam Vieira' }}"
                                       class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                            </div>
                        </div>

                        {{-- linha 2: CPF/CNPJ | CRECI --}}
                        <div class="grid grid-cols-2 divide-x divide-black">
                            <div class="field-wrapper px-2 py-1.5">
                                <div class="flex items-baseline gap-1">
                                    <label class="font-bold whitespace-nowrap">CPF(MF) ou CNPJ(MF):</label>
                                    <input name="contratado[cpf_cnpj]" value="{{ $contratado['cpf_cnpj'] ?? '28851441000190' }}"
                                           class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                                </div>
                            </div>
                            <div class="field-wrapper px-2 py-1.5">
                                <div class="flex items-baseline gap-1">
                                    <label class="font-bold whitespace-nowrap">CRECI - 15ª REGIÃO Nº:</label>
                                    <input name="contratado[creci]" value="{{ $contratado['creci'] ?? '11564' }}"
                                           class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                                </div>
                            </div>
                        </div>

                        {{-- linha 3: ENDEREÇO --}}
                        <div class="field-wrapper px-2 py-1.5">
                            <div class="flex items-baseline gap-1">
                                <label class="font-bold whitespace-nowrap">ENDEREÇO:</label>
                                <input name="contratado[endereco]" value="{{ $contratado['endereco'] ?? 'Rua Cônego Agostinho, 1597 - Centro, Russas - CE, 62900-000, Brasil' }}"
                                       class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                            </div>
                        </div>

                        {{-- linha 4: TELEFONES --}}
                        <div class="field-wrapper px-2 py-1.5">
                            <div class="flex items-baseline gap-1">
                                <label class="font-bold whitespace-nowrap">TELEFONES:</label>
                                <input name="contratado[telefones]" value="{{ $contratado['telefones'] ?? '8834110124' }}"
                                       class="flex-1 border-0 bg-transparent focus:ring-0 focus:outline-none p-0 min-w-0">
                            </div>
                        </div>
                    </div>
                </div>
                @endif

