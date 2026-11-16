@php
    $vendedor = old('vendedor', $contract?->contratante ?? []);
    $comprador = old('comprador', $contract?->content_json['compra_venda']['comprador'] ?? []);
    $negocio = old('negocio', $contract?->content_json['compra_venda']['negocio'] ?? []);
@endphp

@include('contracts.forms.compra-venda._pessoa', [
    'pessoa' => 'vendedor',
    'titulo' => 'I. Vendedor(a):',
    'tipoPessoa' => old('vendedor.tipo', $vendedor['tipo'] ?? ($clientType ?? 'pf')),
    'controladorCampos' => 'person-fields',
    'mostrarSeletorTipo' => false,
])

@include('contracts.forms.compra-venda._pessoa', [
    'pessoa' => 'comprador',
    'titulo' => 'II. Comprador(a):',
    'tipoPessoa' => old('comprador.tipo', $comprador['tipo'] ?? 'pf'),
    'controladorCampos' => 'comprador-fields',
    'mostrarSeletorTipo' => true,
])

@include('contracts.forms.compra-venda._corretor')
@include('contracts.forms.compra-venda._imovel')
@include('contracts.forms.compra-venda._negocio')
@include('contracts.forms.compra-venda._clausulas-assinaturas')
