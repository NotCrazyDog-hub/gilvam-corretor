<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Histórico do contrato - Gilvam Vieira</title>
    @vite(['resources/css/app.css', 'resources/css/contracts.css'])
</head>
<body>
    <div class="container">
        <header class="header-brand">
            <img src="{{ asset('images/logo.png') }}" alt="Gilvam Vieira Corretor de Imóveis">
            <h1>Histórico — {{ $group->slug }}</h1>
        </header>

        <p class="nav-links">
            <a href="{{ route('contracts.index') }}">&larr; Voltar para a lista</a> · 
            <a href="{{ route('contracts.edit', $group) }}">Editar (gerar nova versão)</a>
        </p>

        <table class="contracts">
            <thead>
                <tr>
                    <th>Versão:</th>
                    <th>Contratante:</th>
                    <th>Gerado em:</th>
                    <th>Ação:</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($versions as $version)
                    <tr>
                        <td><strong>Versão {{ $version->version }}</strong></td>
                        <td>{{ $version->contratante['nome'] ?? '—' }}</td>
                        <td>{{ $version->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td>
                            <a href="{{ route('contracts.download', ['group' => $group, 'version' => $version->version]) }}">
                                Baixar Arquivo
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>