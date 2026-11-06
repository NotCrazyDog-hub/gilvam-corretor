                {{-- CLÁUSULAS + ASSINATURAS (caixa única, sem os rótulos IV/V) --}}
                <div class="border border-black text-sm p-4 space-y-6">
                    <p>
                        @if ($tipoContratoKey === 'doacao')
                            Pelo presente instrumento, DOADOR e DONATÁRIO têm justo e contratado a presente doação, que será regida pelas cláusulas e condições a seguir:
                        @else
                        Pelo presente instrumento particular, CONTRATANTE e CONTRATADO, têm justo e contratado,
                        a presente contratação, que será regida pelas cláusulas e condições a seguir:
                        @endif
                    </p>

                    <div class="field-wrapper">
                        <div id="editor-clausulas"
                            class="min-h-[220px] border border-slate-300 p-2 text-sm focus-within:border-black"></div>
                        <input type="hidden" name="clausulas" id="input-clausulas">
                        <input type="hidden" name="content_json[clausulas]" id="input-clausulas-json">
                    </div>

                    <div class="field-wrapper flex items-baseline gap-1">
                        <label class="font-bold whitespace-nowrap">LOCAL E DATA:</label>
                        <p class="text-sm">
                            <span class="inline-block border-b border-black w-48 align-bottom">&nbsp;</span>,
                            <span class="inline-block border-b border-black w-10 align-bottom mx-1">&nbsp;</span>
                            de
                            <span class="inline-block border-b border-black w-40 align-bottom mx-1">&nbsp;</span>
                            de
                            <span class="inline-block border-b border-black w-20 align-bottom mx-1">&nbsp;</span>
                        </p>
                    </div>

                    <div class="max-w-md mx-auto space-y-10 text-center pt-6">
                        <div class="border-t border-black pt-1">
                            <p class="font-bold">{{ $tipoContratoKey === 'doacao' ? 'DOADOR' : 'CONTRATANTE' }}</p>
                        </div>
                        <div class="border-t border-black pt-1">
                            <p class="font-bold" data-assinatura-segundo>{{ $isPj ? 'REPRESENTANTE LEGAL' : 'CÔNJUGE' }}</p>
                        </div>
                        <div class="border-t border-black pt-1">
                            <p class="font-bold">{{ $tipoContratoKey === 'doacao' ? 'DONATÁRIO' : 'CONTRATADO' }}</p>
                        </div>
                    </div>

                    <div>
                        <p class="font-bold mb-4">TESTEMUNHAS:</p>
                        <div class="grid grid-cols-2 gap-8 text-center text-xs">
                            <div class="border-t border-black pt-1">
                                <p>CPF(MF):</p>
                            </div>
                            <div class="border-t border-black pt-1">
                                <p>CPF(MF):</p>
                            </div>
                        </div>
                    </div>
                </div>

