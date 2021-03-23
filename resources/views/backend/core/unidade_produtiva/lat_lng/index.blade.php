    @push('after-styles')
        <link href="{{ asset('css/leaflet.css') }}" rel="stylesheet" />
    @endpush

    @push('after-scripts')
        <script type="text/javascript" src="{{ asset('js/leaflet.js') }}"></script>
    @endpush

    <style>
        .map-lat-lng #map-content {
            height:300px;
        }
        .map-lat-lng input {
            margin-right: 15px;
            margin-left: 15px;
        }
        .map-lat-lng button {
            margin-right: 15px;
            margin-left: 15px;
        }

        @media (max-width: 767px) {
            .map-lat-lng button {
                margin-top:10px;
            }
        }
    </style>

    <div class="map-lat-lng">
        <div class="form-group row" style="margin-bottom:0px;">
            <div class="col-12">
                <label for="address" class="form-control-label d-block" >Buscar Coordenada</label>
            </div>

            <input name="address" aria-label="Endereço completo"  type="text" class="input-lat-lng col-md-4 form-control" placeholder="Digite o endereço completo">

            <button type="button" class="btn-search btn btn-primary col-md-2" disabled>Buscar no mapa</button>
        </div>

        <small class="pb-3 d-block">Informe o endereço, número, bairro e cidade na hora de buscar o local.<br/>Ex: Viaduto do Chá, 15, São Paulo</small>

        <div id="map-content"></div>
    </div>
