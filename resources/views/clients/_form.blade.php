{{-- Variáveis: $client (Client|null), $action, $tipo (só no create) --}}
@php
    $tipoInicial = $client?->tipo ?? old('tipo', $tipo ?? 'pf');
@endphp

<form action="{{ $action }}" method="POST" id="form-client"
      x-data="{ tipo: @js($tipoInicial), estadoCivil: @js(old('estado_civil', $client?->estado_civil ?? '')) }">
    @csrf
    @if ($client)
        @method('PUT')
    @else
        <input type="hidden" name="tipo" :value="tipo">

        <div class="inline-flex items-center gap-1 bg-slate-100 rounded-lg p-1 mb-6">
            <button type="button" @click="tipo = 'pf'"
                    :class="tipo === 'pf' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                    class="px-4 py-2 text-sm font-medium rounded-md transition">
                Pessoa física
            </button>
            <button type="button" @click="tipo = 'pj'"
                    :class="tipo === 'pj' ? 'bg-white text-green-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                    class="px-4 py-2 text-sm font-medium rounded-md transition">
                Pessoa jurídica
            </button>
        </div>
    @endif

    {{-- Nome / Razão social --}}
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700"
               x-text="tipo === 'pf' ? 'Nome' : 'Razão Social'"></label>
        <input type="text" name="nome" value="{{ old('nome', $client?->nome) }}"
               class="mt-1 block w-full rounded border-gray-300">
    </div>

    {{-- ===================== PESSOA FÍSICA ===================== --}}
    <div x-show="tipo === 'pf'" x-cloak>
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">CPF</label>
                <input type="text" name="cpf" value="{{ old('cpf', $client?->cpf) }}" maxlength="14"
                       placeholder="000.000.000-00" data-mask="cpf"
                       class="mt-1 block w-full rounded border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">RG</label>
                <input type="text" name="rg" value="{{ old('rg', $client?->rg) }}"
                       class="mt-1 block w-full rounded border-gray-300">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Data de Nascimento</label>
                <input type="date" name="data_nascimento"
                       value="{{ old('data_nascimento', $client?->data_nascimento?->format('Y-m-d')) }}"
                       class="mt-1 block w-full rounded border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Estado Civil</label>
                <select name="estado_civil" x-model="estadoCivil"
                        class="mt-1 block w-full rounded border-gray-300">
                    <option value="">Selecione</option>
                    <option value="Solteiro(a)">Solteiro(a)</option>
                    <option value="Casado(a)">Casado(a)</option>
                    <option value="União estável">União estável</option>
                    <option value="Divorciado(a)">Divorciado(a)</option>
                    <option value="Viúvo(a)">Viúvo(a)</option>
                </select>
            </div>
        </div>

        <div x-show="['Casado(a)', 'União estável'].includes(estadoCivil)" x-cloak
             class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Nome do Cônjuge</label>
                <input type="text" name="conjuge_nome" value="{{ old('conjuge_nome', $client?->conjuge_nome) }}"
                       class="mt-1 block w-full rounded border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Identidade (RG) do Cônjuge</label>
                <input type="text" name="conjuge_rg" value="{{ old('conjuge_rg', $client?->conjuge_rg) }}"
                       class="mt-1 block w-full rounded border-gray-300">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Nacionalidade</label>
                <input type="text" name="nacionalidade" value="{{ old('nacionalidade', $client?->nacionalidade) }}"
                       class="mt-1 block w-full rounded border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Profissão</label>
                <input type="text" name="profissao" value="{{ old('profissao', $client?->profissao) }}"
                       class="mt-1 block w-full rounded border-gray-300">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Telefone</label>
                <input type="text" name="telefone" value="{{ old('telefone', $client?->telefone) }}" maxlength="15"
                       placeholder="(00) 00000-0000" data-mask="telefone"
                       class="mt-1 block w-full rounded border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Telefone Secundário</label>
                <input type="text" name="telefone_secundario"
                       value="{{ old('telefone_secundario', $client?->telefone_secundario) }}"
                       maxlength="15" placeholder="(00) 00000-0000" data-mask="telefone"
                       class="mt-1 block w-full rounded border-gray-300">
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email" value="{{ old('email', $client?->email) }}"
                   class="mt-1 block w-full rounded border-gray-300">
        </div>
    </div>

    {{-- ===================== PESSOA JURÍDICA ===================== --}}
    <div x-show="tipo === 'pj'" x-cloak>
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Nome Fantasia</label>
                <input type="text" name="nome_fantasia" value="{{ old('nome_fantasia', $client?->nome_fantasia) }}"
                       class="mt-1 block w-full rounded border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">CNPJ</label>
                <input type="text" name="cnpj" value="{{ old('cnpj', $client?->cnpj) }}" maxlength="18"
                       placeholder="00.000.000/0000-00" data-mask="cnpj"
                       class="mt-1 block w-full rounded border-gray-300">
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Inscrição Estadual</label>
            <input type="text" name="inscricao_estadual"
                   value="{{ old('inscricao_estadual', $client?->inscricao_estadual) }}"
                   class="mt-1 block w-full rounded border-gray-300">
        </div>
    </div>

    {{-- Endereço (comum) --}}
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700">Endereço</label>
        <input type="text" name="endereco" value="{{ old('endereco', $client?->endereco) }}"
               class="mt-1 block w-full rounded border-gray-300">
    </div>

    {{-- Representante legal (PJ) --}}
    <div x-show="tipo === 'pj'" x-cloak>
        <hr class="mb-6">
        <h3 class="text-sm font-semibold text-gray-600 uppercase mb-4">Representante Legal</h3>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Nome do Representante</label>
            <input type="text" name="representante_legal_nome"
                   value="{{ old('representante_legal_nome', $client?->representante_legal_nome) }}"
                   class="mt-1 block w-full rounded border-gray-300">
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">CPF do Representante</label>
                <input type="text" name="representante_legal_cpf"
                       value="{{ old('representante_legal_cpf', $client?->representante_legal_cpf) }}"
                       maxlength="14" placeholder="000.000.000-00" data-mask="cpf"
                       class="mt-1 block w-full rounded border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Cargo</label>
                <input type="text" name="representante_legal_cargo"
                       value="{{ old('representante_legal_cargo', $client?->representante_legal_cargo) }}"
                       class="mt-1 block w-full rounded border-gray-300">
            </div>
        </div>
    </div>

    <div class="flex justify-end space-x-2">
        <a href="{{ route('clients.index') }}"
           class="px-4 py-2 rounded border border-gray-300 text-gray-700">
            Cancelar
        </a>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            {{ $client ? 'Salvar Alterações' : 'Salvar' }}
        </button>
    </div>
</form>

<script>
    function maskCPF(value) {
        value = value.replace(/\D/g, '').slice(0, 11);
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        return value;
    }

    function maskCNPJ(value) {
        value = value.replace(/\D/g, '').slice(0, 14);
        value = value.replace(/^(\d{2})(\d)/, '$1.$2');
        value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
        value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
        value = value.replace(/(\d{4})(\d)/, '$1-$2');
        return value;
    }

    function maskTelefone(value) {
        value = value.replace(/\D/g, '').slice(0, 11);
        value = value.replace(/^(\d{2})(\d)/, '($1) $2');
        if (value.length > 10) {
            value = value.replace(/(\d{5})(\d{1,4})$/, '$1-$2');
        } else {
            value = value.replace(/(\d{4})(\d{1,4})$/, '$1-$2');
        }
        return value;
    }

    const masks = { cpf: maskCPF, cnpj: maskCNPJ, telefone: maskTelefone };

    document.querySelectorAll('[data-mask]').forEach((input) => {
        const fn = masks[input.dataset.mask];
        input.addEventListener('input', (e) => { e.target.value = fn(e.target.value); });
        input.value = fn(input.value);
    });

    document.getElementById('form-client').addEventListener('submit', () => {
        document.querySelectorAll('[data-mask]').forEach((input) => {
            input.value = input.value.replace(/\D/g, '');
        });
    });
</script>