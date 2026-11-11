@php
    $locador = old('locador', $contract?->contratante ?? []);
    $locatario = old('locatario', $contract?->contratado ?? []);
    $imovel = old('imovel', $contract?->imovel ?? []);
    $locacao = old('locacao', $contract?->content_json['locacao'] ?? []);
@endphp

@include('contracts.forms.locacao._locador')
@include('contracts.forms.locacao._locatario')
@include('contracts.forms.locacao._imovel')
@include('contracts.forms.locacao._clausulas-assinaturas')
