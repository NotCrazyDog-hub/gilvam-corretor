<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Nova Pessoa Física
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('pessoas-fisicas.store') }}" method="POST" id="form-pessoa-fisica"
                      x-data="{ estadoCivil: @js(old('estado_civil', '')) }">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Nome</label>
                        <input type="text" name="nome" value="{{ old('nome') }}"
                               class="mt-1 block w-full rounded border-gray-300">
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">CPF</label>
                            <input type="text" name="cpf" value="{{ old('cpf') }}" maxlength="14"
                                   placeholder="000.000.000-00" data-mask="cpf"
                                   class="mt-1 block w-full rounded border-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">RG</label>
                            <input type="text" name="rg" value="{{ old('rg') }}"
                                   class="mt-1 block w-full rounded border-gray-300">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Data de Nascimento</label>
                            <input type="date" name="data_nascimento" value="{{ old('data_nascimento') }}"
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

                    {{-- Dados do cônjuge: só aparecem se casado(a) ou união estável --}}
                    <div x-show="['Casado(a)', 'União estável'].includes(estadoCivil)" x-cloak
                         class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nome do Cônjuge</label>
                            <input type="text" name="conjuge_nome" value="{{ old('conjuge_nome') }}"
                                   class="mt-1 block w-full rounded border-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Identidade (RG) do Cônjuge</label>
                            <input type="text" name="conjuge_rg" value="{{ old('conjuge_rg') }}"
                                   class="mt-1 block w-full rounded border-gray-300">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nacionalidade</label>
                            <input type="text" name="nacionalidade" value="{{ old('nacionalidade') }}"
                                   class="mt-1 block w-full rounded border-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Profissão</label>
                            <input type="text" name="profissao" value="{{ old('profissao') }}"
                                   class="mt-1 block w-full rounded border-gray-300">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Telefone</label>
                            <input type="text" name="telefone" value="{{ old('telefone') }}" maxlength="15"
                                   placeholder="(00) 00000-0000" data-mask="telefone"
                                   class="mt-1 block w-full rounded border-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Telefone Secundário</label>
                            <input type="text" name="telefone_secundario" value="{{ old('telefone_secundario') }}"
                                   maxlength="15" placeholder="(00) 00000-0000" data-mask="telefone"
                                   class="mt-1 block w-full rounded border-gray-300">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="mt-1 block w-full rounded border-gray-300">
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700">Endereço</label>
                        <input type="text" name="endereco" value="{{ old('endereco') }}"
                               class="mt-1 block w-full rounded border-gray-300">
                    </div>

                    <div class="flex justify-end space-x-2">
                        <a href="{{ route('pessoas-fisicas.index') }}"
                           class="px-4 py-2 rounded border border-gray-300 text-gray-700">
                            Cancelar
                        </a>
                        <button type="submit"
                                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Salvar
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <script>
        function maskCPF(value) {
            value = value.replace(/\D/g, '').slice(0, 11);
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
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

        function unmask(value) {
            return value.replace(/\D/g, '');
        }

        document.querySelectorAll('[data-mask="cpf"]').forEach((input) => {
            input.addEventListener('input', (e) => {
                e.target.value = maskCPF(e.target.value);
            });
            input.value = maskCPF(input.value);
        });

        document.querySelectorAll('[data-mask="telefone"]').forEach((input) => {
            input.addEventListener('input', (e) => {
                e.target.value = maskTelefone(e.target.value);
            });
            input.value = maskTelefone(input.value);
        });

        document.getElementById('form-pessoa-fisica').addEventListener('submit', () => {
            document.querySelectorAll('[data-mask="cpf"], [data-mask="telefone"]').forEach((input) => {
                input.value = unmask(input.value);
            });
        });
    </script>
</x-app-layout>
