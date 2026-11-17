<div class="field-wrapper border border-black px-2 py-1.5 text-sm">
    <label for="contratante-email" class="mr-2 whitespace-nowrap font-bold">E-MAIL DO(A) CONTRATANTE:</label>
    <input id="contratante-email" type="email" name="contratante[email]"
           value="{{ old('contratante.email', $contratante['email'] ?? '') }}"
           class="w-full border-0 bg-transparent p-0 focus:outline-none focus:ring-0">
</div>
