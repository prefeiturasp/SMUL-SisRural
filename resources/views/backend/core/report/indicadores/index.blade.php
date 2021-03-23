@extends('backend.layouts.app')

@section('content')
    {!!@$viewFilter!!}

    <ul class="nav nav-tabs" id="tabIndicadores" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="gerencial-tab" data-action="{{$dataIndicadorGerencial}}" data-toggle="tab" href="#gerencial" type="button" role="tab" aria-controls="gerencial" aria-selected="true">Indicadores Gerenciais</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="cadastral-tab" data-action="{{$dataIndicadoresCadastrais}}" data-toggle="tab" href="#cadastral" type="button" role="tab" aria-controls="cadastral" aria-selected="false">Indicadores Cadastrais</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="formulario-tab" data-action="{{$dataIndicadoresFormularios}}" data-toggle="tab" href="#formulario" type="button" role="tab" aria-controls="formulario" aria-selected="false">Indicadores de Formulários</a>
        </li>
        {{-- <li class="nav-item" role="presentation">
            <a class="nav-link" id="plano-acao-tab" data-action="{{$dataIndicadoresPdas}}" data-toggle="tab" href="#plano-acao" type="button" role="tab" aria-controls="plano-acao" aria-selected="false">Indicadores de Plano de Ação</a>
        </li> --}}
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="caderno-tab" data-action="{{$dataIndicadoresCadernos}}" data-toggle="tab" href="#caderno" type="button" role="tab" aria-controls="caderno" aria-selected="false">Indicadores de Cadernos de Campo</a>
        </li>
    </ul>

    <div class="panel-indicadores">
        @loading
        @endloading
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="gerencial" role="tabpanel" aria-labelledby="gerencial-tab">
                @include('backend.core.report.indicadores.indicadores_gerenciais')
            </div>
            <div class="tab-pane fade" id="cadastral" role="tabpanel" aria-labelledby="cadastral-tab">
                @include('backend.core.report.indicadores.indicadores_cadastrais')
            </div>
            <div class="tab-pane fade" id="formulario" role="tabpanel" aria-labelledby="formulario-tab">
                @include('backend.core.report.indicadores.indicadores_formularios')
            </div>
            {{-- <div class="tab-pane fade" id="plano-acao" role="tabpanel" aria-labelledby="plano-acao-tab">
                @include('backend.core.report.indicadores.indicadores_pdas')
            </div> --}}
            <div class="tab-pane fade" id="caderno" role="tabpanel" aria-labelledby="caderno-tab">
                @include('backend.core.report.indicadores.indicadores_cadernos')
            </div>
        </div>
    </div>
@endsection

@push('after-styles')
    <style>
        svg > g > g:last-child { pointer-events: none }
        div.google-visualization-tooltip { pointer-events: none }

        .card-chart .chart path { cursor:pointer; }
        /* .card-chart .chart ellipse { cursor:pointer; } */
        .card-chart .chart  path ~ text { cursor:pointer; }

        .card-chart .chart-txt {
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            color: #56575a;
        }

        @media screen and (max-width: 991px)  {
            .card-count {
                margin-top:15px;
            }
        }

        .nav-tabs .nav-link {
            font-size:16px;
            border-color: #d8dbe0 #d8dbe0 #c4c9d0;
        }
        .nav-tabs .nav-link.active {
            color:rgb(86, 87, 90);
            font-weight: bold;
            background-color:#FFF;
        }

        .panel-indicadores {
            position: relative;
            min-height: 300px;
        }

        .panel-indicadores.loading .c-loading {
            opacity: 1;
            visibility: visible;
        }
    </style>
@endpush

@push('after-scripts')
    <script>
        $('a[data-toggle="tab"]').on('shown.coreui.tab', function (event) {
            submitFilter(true);
        });
    </script>
@endpush

@push('after-scripts')
    <script>
        //Draw
        function callbackIndicadores(data) {
            var list = Object.keys(data);
            for(var i =0; i<list.length; i++) {
                var key = list[i];

                if (typeof window[key] === "function") {
                    console.log("call fn "+key+", data:", data[key]);
                    window[key](data[key]);
                }
            }
        }
    </script>
@endpush

@push('after-scripts')
    <script>
        function getFilterParams() {
            var form = $("#form-filter");
            var list = form.serializeArray();

            var ret = {};
            for(var i=0; i < list.length;i++) {
                var item = list[i];
                if (item.name.indexOf("[]") > -1) {
                    if (!ret[item.name]) {
                        ret[item.name] = new Array();
                    }

                    ret[item.name].push(item.value);
                } else {
                    ret[item.name] = item.value;
                }
            }

            return ret;
        }

        //Data
        function submitFilter(ignoreExpand) {
            if (!ignoreExpand && $("#card-filter").hasClass('is-expand')) {
                $("#card-filter").addClass("hide");
            }

            var form = $("#form-filter");

            //Força validação p/ mostrar erros na tela
            if (form[0].checkValidity() == false) {
                form[0].dispatchEvent(new Event('submit'));
                return;
            }

            $(".panel-indicadores").addClass("loading");
            $("#form-submit").addClass("loading");

            var data = form.serializeArray();
            // data.push({name: 'fl_periodo', value: $(".fl_periodo").is(':checked')});
            data.push({name: 'fl_periodo', value: $("#fl_periodo input[type='radio']:checked").val()});

            //console.log('serializeArray', data);
            console.log('serialize', form.serialize());

            var url = $("#tabIndicadores .nav-link.active").data("action");

            fetchIndicadorAjax(url, data, function(rs) {
                callbackIndicadores(rs);
                $(".panel-indicadores").removeClass("loading");
                $("#form-submit").removeClass("loading");
            });
        }

        function fetchIndicadorAjax(action, data, success) {
            $.ajax({
                type: "POST",
                url: action,
                data: data,
                dataType: "json",
                xhrFields: {
                    withCredentials: true
                },
                success:success,
                error: function(err) {
                    $("#form-submit").removeClass("loading");
                    $(".panel-indicadores").removeClass("loading");

                    let message = "Erro ao enviar os dados, tente novamente.";
                    if (err && err.responseJSON && err.responseJSON.message) {
                        message = err.responseJSON.message;
                    }

                    toastr.error(message);
                }
            });
        }

        //Submit form
        $("#card-filter #form-submit").click(function(evt) {
            evt.preventDefault();
            submitFilter(false);
        });

        //FIX seleção caderno de campo
        $("select[name='template_caderno_id[]']").select2({width: 'style'});
    </script>

    {{-- Maps --}}
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script>
        google.charts.load('current', {'packages':['corechart', 'bar', 'line']});
        google.charts.setOnLoadCallback(start);

        function start() {
            setTimeout(submitFilter, 500, true);
        }
    </script>
@endpush
