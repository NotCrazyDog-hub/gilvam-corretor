<h3 class="text-lg font-semibold text-gray-800 mb-4">
    Detalhes da {{ $client->isPj() ? 'Pessoa Jurídica' : 'Pessoa Física' }}
</h3>


<dl class="grid grid-cols-2 gap-4">
    <dl class="grid grid-cols-2 gap-4">
                    @if ($client->isPf())
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nome</dt>
                            <dd class="text-gray-800">{{ $client->nome }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">CPF</dt>
                            <dd class="text-gray-800">{{ $client->cpf_formatado ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">RG</dt>
                            <dd class="text-gray-800">{{ $client->rg ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Data de Nascimento</dt>
                            <dd class="text-gray-800">{{ $client->data_nascimento?->format('d/m/Y') ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Estado Civil</dt>
                            <dd class="text-gray-800">{{ $client->estado_civil ?? '—' }}</dd>
                        </div>
                        @if ($client->conjuge_nome)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Cônjuge</dt>
                                <dd class="text-gray-800">{{ $client->conjuge_nome }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nacionalidade</dt>
                            <dd class="text-gray-800">{{ $client->nacionalidade ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Profissão</dt>
                            <dd class="text-gray-800">{{ $client->profissao ?? '—' }}</dd>
                        </div>
                        {{-- REMOVIDO daqui: Telefone, Telefone Secundário e Email (agora ficam no bloco comum abaixo) --}}
                    @else
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Razão Social</dt>
                            <dd class="text-gray-800">{{ $client->nome }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nome Fantasia</dt>
                            <dd class="text-gray-800">{{ $client->nome_fantasia ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">CNPJ</dt>
                            <dd class="text-gray-800">{{ $client->cnpj_formatado ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Inscrição Estadual</dt>
                            <dd class="text-gray-800">{{ $client->inscricao_estadual ?? '—' }}</dd>
                        </div>
                    @endif

                    <div class="col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Endereço</dt>
                        <dd class="text-gray-800">{{ $client->endereco }}</dd>
                    </div>

                    {{-- NOVO: contato comum a PF e PJ --}}
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Telefone</dt>
                        <dd class="text-gray-800">{{ $client->telefone_formatado ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Telefone Secundário</dt>
                        <dd class="text-gray-800">{{ $client->telefone_secundario_formatado ?? '—' }}</dd>
                    </div>
                    <div class="col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="text-gray-800">{{ $client->email ?? '—' }}</dd>
                    </div>

                    @if ($client->isPj())
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Representante Legal</dt>
                            <dd class="text-gray-800">{{ $client->representante_legal_nome }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">CPF do Representante</dt>
                            <dd class="text-gray-800">{{ $client->representante_legal_cpf_formatado ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Cargo</dt>
                            <dd class="text-gray-800">{{ $client->representante_legal_cargo }}</dd>
                        </div>
                    @endif
                </dl>
</dl>

<div class="flex justify-end mt-6">
    <a href="{{ route('clients.edit', $client) }}"
       class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">Editar</a>
</div>
