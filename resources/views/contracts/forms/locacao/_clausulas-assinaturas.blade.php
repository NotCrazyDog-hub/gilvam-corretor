<div class="border border-black text-sm p-4 space-y-6">
    <p>Pelo presente instrumento particular, LOCADOR(A) e LOCATÁRIO(A), têm justo e contratado, a presente locação, que será regida pelas cláusulas e condições a seguir:</p>
    <div class="field-wrapper">
        <div id="editor-clausulas" class="min-h-[220px] border border-slate-300 p-2 text-sm"></div>
        <input type="hidden" name="clausulas" id="input-clausulas">
        <input type="hidden" name="content_json[clausulas]" id="input-clausulas-json">
    </div>
    <div class="max-w-md mx-auto space-y-10 text-center pt-6">
        <div class="border-t border-black pt-1"><p class="font-bold">LOCADOR(A)</p></div>
        <div class="border-t border-black pt-1"><p class="font-bold" data-assinatura-segundo>{{ $isPj ? 'REPRESENTANTE LEGAL' : 'CÔNJUGE' }}</p></div>
        <div class="border-t border-black pt-1"><p class="font-bold">LOCATÁRIO(A)</p></div>
    </div>
    <div>
        <p class="font-bold mb-4">TESTEMUNHAS:</p>
        <div class="grid grid-cols-2 gap-8 text-center text-xs">
            <div class="border-t border-black pt-1"><p>CPF(MF):</p></div>
            <div class="border-t border-black pt-1"><p>CPF(MF):</p></div>
        </div>
    </div>
</div>
