<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Histórico do contrato</title>
    @vite(['resources/css/app.css', 'resources/css/contracts.css'])
</head>
<body>
    <div class="container">
        <h1>Histórico — {{ $group->slug }}</h1>
        <p><a href="{{ route('contracts.index') }}">&larr; Voltar</a> · <a href="{{ route('contracts.edit', $group) }}">Editar (nova versão)</a></p>

        <table class="contracts">
            <thead>
                <tr>
                    <th>Versão</th>
                    <th>Contratante</th>
                    <th>Gerado em</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($versions as $version)
                    <tr>
                        <td>v{{ $version->version }}</td>
                        <td>{{ $version->contratante['nome'] ?? '—' }}</td>
                        <td>{{ $version->generated_at?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td>
                            <a href="{{ route('contracts.download', ['group' => $group, 'version' => $version->version]) }}">
                                Baixar
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>