@can('view menu report')
<div class="row mb-4">
    <div class="col-4">
        @cardaddview(['title'=>'Mapa', 'icon'=>'c-icon c-icon-lg cil-location-pin', 'noPadding' => true])
            @slot('body')
                <a href="{{route('admin.core.mapa.index', ['dt_ini'=>date('Y-m-d', strtotime("-1 year")), 'dt_end'=>date('Y-m-d')])}}">
                    <img class="img" src="/img/backend/dashboard/mapa.jpg"/>
                </a>
            @endslot
        @endcardaddview
    </div>

    <div class="col-4">
        @cardaddview(['title'=>'Painéis de indicadores', 'icon'=>'c-icon c-icon-lg cil-bar-chart', 'noPadding' => true])
            @slot('body')
                <a href="{{route('admin.core.indicadores.index', ['dt_ini'=>date('Y-m-d', strtotime("-1 year")), 'dt_end'=>date('Y-m-d')])}}">
                    <img class="img" src="/img/backend/dashboard/indicadores.jpg"/>
                </a>
            @endslot
        @endcardaddview
    </div>

    <div class="col-4">
        @cardaddview(['title'=>'Download de planilhas', 'icon'=>'c-icon c-icon-lg cil-notes', 'noPadding' => true])
            @slot('body')
                <a href="{{route('admin.core.report.index', ['dt_ini'=>date('Y-m-d', strtotime("-1 year")), 'dt_end'=>date('Y-m-d')])}}">
                    <img class="img" src="/img/backend/dashboard/report.jpg"/>
                </a>
            @endslot
        @endcardaddview
    </div>
</div>
@endcan
