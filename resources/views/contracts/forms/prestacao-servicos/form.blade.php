@php
    $servico = old('prestacao_servicos', $prestacaoServicosSalva ?? []);
@endphp

@include('contracts.forms.corretagem-venda._contratante')
@include('contracts.forms.prestacao-servicos._prestador')
@include('contracts.forms.prestacao-servicos._escopo')
@include('contracts.forms.prestacao-servicos._clausulas-assinaturas')
