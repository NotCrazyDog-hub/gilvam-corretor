<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Pessoa Jurídica
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

                <form action="{{ route('pessoas-juridicas.update', $pessoas_juridica) }}" method="POST" id="form-pessoa-juridica">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Razão Social</label>
                        <input type="text" name="razao_social"
                               value="{{ old('razao_social', $pessoas_juridica->razao_social) }}"
                               class="mt-1 block w-full rounded border-gray-300">
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nome Fantasia</label>
                            <input type="text" name="nome_fantasia"
                                   value="{{ old('nome_fantasia', $pessoas_juridica->nome_fantasia) }}"
                                   class="mt-1 block w-full rounded border-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">CNPJ</label>
                            <input type="text" name="cnpj"
                                   value="{{ old('cnpj', $pessoas_juridica->cnpj) }}" maxlength="18"
                                   placeholder="00.000.000/0000-00" data-mask="cnpj"
                                   class="mt-1 block w-full rounded border-gray-300">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Inscrição Estadual</label>
                        <input type="text" name="inscricao_estadual"
                               value="{{ old('inscricao_estadual', $pessoas_juridica->inscricao_estadual) }}"
                               class="mt-1 block w-full rounded border-gray-300">
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700">Endereço</label>
                        <input type="text" name="endereco"
                               value="{{ old('endereco', $pessoas_juridica->endereco) }}"
                               class="mt-1 block w-full rounded border-gray-300">
                    </div>

                    <hr class="mb-6">

                    <h3 class="text-sm font-semibold text-gray-600 uppercase mb-4">Representante Legal</h3>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Nome do Representante</label>
                        <input type="text" name="representante_legal_nome"
                               value="{{ old('representante_legal_nome', $pessoas_juridica->representante_legal_nome) }}"
                               class="mt-1 block w-full rounded border-gray-300">
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">CPF do Representante</label>
                            <input type="text" name="representante_legal_cpf"
                                   value="{{ old('representante_legal_cpf', $pessoas_juridica->representante_legal_cpf) }}"
                                   maxlength="14" placeholder="000.000.000-00" data-mask="cpf"
                                   class="mt-1 block w-full rounded border-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Cargo</label>
                            <input type="text" name="representante_legal_cargo"
                                   value="{{ old('representante_legal_cargo', $pessoas_juridica->representante_legal_cargo) }}"
                                   class="mt-1 block w-full rounded border-gray-300">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <a href="{{ route('pessoas-juridicas.index') }}"
                           class="px-4 py-2 rounded border border-gray-300 text-gray-700">
                            Cancelar
                        </a>
                        <button type="submit"
                                class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                            Salvar Alterações
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

        function maskCNPJ(value) {
            value = value.replace(/\D/g, '').slice(0, 14);
            value = value.replace(/^(\d{2})(\d)/, '$1.$2');
            value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
            value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
            value = value.replace(/(\d{4})(\d)/, '$1-$2');
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

        document.querySelectorAll('[data-mask="cnpj"]').forEach((input) => {
            input.addEventListener('input', (e) => {
                e.target.value = maskCNPJ(e.target.value);
            });
            input.value = maskCNPJ(input.value);
        });

        document.getElementById('form-pessoa-juridica').addEventListener('submit', () => {
            document.querySelectorAll('[data-mask="cpf"], [data-mask="cnpj"]').forEach((input) => {
                input.value = unmask(input.value);
            });
        });
    </script>
</x-app-layout>
