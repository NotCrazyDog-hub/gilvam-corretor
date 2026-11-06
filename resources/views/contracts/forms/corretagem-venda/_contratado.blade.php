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

