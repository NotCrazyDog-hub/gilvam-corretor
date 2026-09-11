<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Contratos - Gilvam Vieira</title>
    @vite(['resources/css/app.css', 'resources/css/contracts.css'])
</head>
<body>
    <div class="container">
        <header class="header-brand">
            <img src="{{ asset('images/logo.png') }}" alt="Gilvam Vieira Corretor de Imóveis">
            <h1>Gestão de Contratos</h1>
        </header>

        <p class="nav-links">
            <a href="{{ route('contracts.index') }}">Todos</a> ·
            <a href="{{ route('contracts.index', ['client_type' => 'pf']) }}">Pessoa física</a> ·
            <a href="{{ route('contracts.index', ['client_type' => 'pj']) }}">Pessoa jurídica</a> ·
            <a href="{{ route('contracts.create') }}">+ Novo contrato</a>
        </p>

        <table class="contracts">
            <thead>
                <tr>
                    <th>Contratante</th>
                    <th>Tipo</th>
                    <th>Versão vigente</th>
                    <th>Gerado em</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($groups as $group)
                    <tr>
                        <td>{{ $group->latestVersion->contratante['nome'] ?? '—' }}</td>
                        <td>{{ $group->contract_type }}</td>
                        <td>Versão {{ $group->latestVersion->version ?? '—' }}</td>
                        <td>{{ $group->latestVersion?->created_at?->format('d/m/Y\, à\s H:i') ?? '—' }}</td>
                        <td>
                            <a href="{{ route('contracts.edit', $group) }}">Editar</a> ·
                            <a href="{{ route('contracts.versions', $group) }}">Histórico</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">Nenhum contrato encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $groups->links() }}
        </div>
    </div>
</body>
</html>