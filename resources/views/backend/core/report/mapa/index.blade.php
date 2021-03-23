@extends('backend.layouts.app')

@section('content')
    {!!@$viewFilter!!}

    <div class="map-lat-lng">
        <div class="loading">
            <div class="spinner-border text-light" role="status"></div>
        </div>

        <div id="map-content"></div>
    </div>

    {{-- @cardater(['title'=> 'Mapa','titleTag'=>'h1'])
        @slot('body')
            <div class="map-lat-lng">
                <div id="map-content"></div>
            </div>
        @endslot
    @endcardater --}}
@endsection

@push('after-scripts')
    <script type="text/javascript" src="{{ asset('js/leaflet.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/leaflet.markercluster.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/leaflet-omnivore.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/leaflet.fullscreen.min.js') }}"></script>

    @include('backend.core.report.mapa.scripts')
@endpush

@push('after-styles')
    <link href="{{ asset('css/leaflet.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/MarkerCluster.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/MarkerCluster.Default.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/leaflet.fullscreen.css') }}" rel="stylesheet" />

    @include('backend.core.report.mapa.styles')
@endpush
