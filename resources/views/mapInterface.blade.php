{{-- resources/views/mapInterface.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Localização do Imóvel</h2>

    <div class="mb-3">
        <label>Latitude</label>
        <input type="number" step="any" id="lat-input" 
               value="{{ old('latitude', $imovel->latitude) }}">

        <label>Longitude</label>
        <input type="number" step="any" id="lng-input" 
               value="{{ old('longitude', $imovel->longitude) }}">

        <button type="button" id="btn-localizar">Localizar no mapa</button>
    </div>

    <div id="map" style="height: 400px;"></div>
    <p id="endereco-resultado"></p>

    {{-- campos ocultos pra enviar junto do form de cadastro do imóvel --}}
    <input type="hidden" name="latitude" id="latitude-hidden" value="{{ $imovel->latitude }}">
    <input type="hidden" name="longitude" id="longitude-hidden" value="{{ $imovel->longitude }}">
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const map = L.map('map').setView([
            {{ $imovel->latitude ?? -4.9394 }},
            {{ $imovel->longitude ?? -37.9758 }}
        ], 13);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            attribution: '© OpenStreetMap contributors © CARTO'
        }).addTo(map);

        let marker = null;

        // se o imóvel já tiver coordenadas salvas, mostra o marcador de cara
        @if($imovel->latitude && $imovel->longitude)
            marker = L.marker([{{ $imovel->latitude }}, {{ $imovel->longitude }}], { draggable: true }).addTo(map);
        @endif

        document.getElementById('btn-localizar').addEventListener('click', function () {
            const lat = parseFloat(document.getElementById('lat-input').value);
            const lng = parseFloat(document.getElementById('lng-input').value);

            if (isNaN(lat) || isNaN(lng)) {
                alert('Digite latitude e longitude válidas.');
                return;
            }

            map.setView([lat, lng], 17);
            if (marker) {
                marker.setLatLng([lat, lng]);
            } else {
                marker = L.marker([lat, lng], { draggable: true }).addTo(map);
            }

            document.getElementById('latitude-hidden').value = lat;
            document.getElementById('longitude-hidden').value = lng;
        });
    });
</script>
@endpush