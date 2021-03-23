@extends('backend.layouts.app')

@section('content')
    @if(!$planoAcao)
        @cardater(['class'=>'card-info'])
            @slot('body')
                <div class="row align-items-center">
                    <div class="col col-lg-10">
                        <h4 class="mb-0 text-white2">Preencha os dados abaixos para prosseguir.</h4>
                    </div>

                    <div class="col col-lg-2 text-right">
                        <button type="submit" class="btn btn-primary px-5" form="form-builder">Prosseguir</button>
                    </div>
                </div>
            @endslot
        @endcardater
    @endif

    <div class="card-ater">
        <div class="card-body-ater">
            {!!form_start($form)!!}

            @can('view menu plano_acao')
                @if (@$planoAcao)
                    {!!form_until($form, 'card-inf-pda-end')!!}

                    <div class="mt-5">
                        <div id="cadastrar-unidade-produtiva" class="card-item">
                            @include('backend.components.iframe.html', ["id"=>$unidadeProdutivaId, "src"=>$unidadeProdutivaSrc])
                        </div>
                    </div>

                    <div class="mt-5">
                        <div id="cadastrar-item" class="card-item">
                            @include('backend.components.iframe.html', ["id"=>$itemId, "src"=>$itemSrc])
                        </div>
                    </div>

                    @if ($individuaisSrc)
                        <div class="mt-5">
                            <div id="lista-individuais" class="card-item">
                                @include('backend.components.iframe.html', ["id"=>$individuaisId, "src"=>$individuaisSrc])
                            </div>
                        </div>
                    @endif
                @endif
            @endcan

            {!!form_rest($form)!!}

            @if (@$planoAcao && @$historicoSrc)
                <div class="mt-5">
                    <div id="cadastrar-historico" class="card-historico">
                        @include('backend.components.iframe.html', ["id"=>$historicoId, "src"=>$historicoSrc])
                    </div>
                </div>
            @endif
        </div>

        <br/><br/><br/>

        <div class="card-footer-ater">
            <div class="row">
                <div class="col">
                    {{ form_cancel(route('admin.core.plano_acao.index'), __('buttons.general.cancel'), 'btn btn-danger px-4') }}
                </div>

                <div class="col text-right">
                    <button type="submit" class="btn btn-primary px-5" form="form-builder">
                            @if (@$planoAcao)
                                Salvar
                            @else
                                Prosseguir
                            @endif
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-scripts')
    @if (@$individuaisSrc)
        <script>
            //Utilizado para liberar o refresh do iframe depois do primeiro loading.
            var flTimerRefresh = false;
            setTimeout(function() {
                flTimerRefresh = true;
            }, 4000);

            function handleIframeMessage(evt) {
                try {
                    var data = $.parseJSON(evt.data);
                    console.log("handleIframeMessage", data, flTimerRefresh);

                    if (data.type == 'update_iframe') {
                        var unidade_produtiva_id = data.data.unidade_produtiva_id ? data.data.unidade_produtiva_id : '0';
                        var item_id = data.data.item_id ? data.data.item_id : '0';

                        $("html, body").animate({ scrollTop: $("#lista-individuais").offset().top - 150 }, 600);

                        $("#lista-individuais iframe").attr("src", "{{ @$iframeIndividuaisUrl }}/"+unidade_produtiva_id+"/"+item_id);
                    } else if (data.type == 'refresh' && flTimerRefresh == true) {
                        //refresh do bloco "Ações individuais" conforme as ações nos blocos "Ações coletivas cadastradas" e "Unidades produtivas"
                        var iframeIndividuais = $("#{{@$individuaisId}}");
                        iframeIndividuais.contents().find("#table_filter input").click();

                        var iframeUnidade = $("#{{@$unidadeProdutivaId}}");
                        iframeUnidade.contents().find("#table_filter input").click();

                        var iframeItem = $("#{{@$itemId}}");
                        iframeItem.contents().find("#table_filter input").click();
                    }
                } catch(e){
                    console.log("handleIframeMessage", e);
                }
            }

            window.addEventListener('message', handleIframeMessage, false);
        </script>
    @endif

    @can('view menu plano_acao')
        @if (@$planoAcao)
            @include('backend.components.iframe.scripts', ["id"=>$itemId, "src"=>$itemSrc])
            @include('backend.components.iframe.scripts', ["id"=>$unidadeProdutivaId, "src"=>$unidadeProdutivaSrc])

            @if ($individuaisSrc)
               @include('backend.components.iframe.scripts', ["id"=>$individuaisId, "src"=>$individuaisSrc])
            @endif

            @if (@$historicoId)
                @include('backend.components.iframe.scripts', ["id"=>$historicoId, "src"=>$historicoSrc])
            @endif
        @endif
    @endcan
@endpush
