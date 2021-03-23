@push('after-styles')
    <link href="{{ asset('css/leaflet.css') }}" rel="stylesheet" />
@endpush

@push('after-scripts')
    <script type="text/javascript" src="{{ asset('js/leaflet.js') }}"></script>
@endpush

<div class="map-lat-lng">
    <div id="map-content"></div>
</div>
