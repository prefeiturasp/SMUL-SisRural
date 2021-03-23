@extends('backend.layouts.app')

@section('title', app_name() . ' | Unidade Produtiva')

@section('content')
    <h1 class="mb-4">{{ !@$unidadeProdutiva?"Criar Unidade Produtiva":"Editar Unidade Produtiva: ".$unidadeProdutiva->nome}}</h1>

    @if(@$produtor->id)
        @cardater(['class'=>''])
            @slot('body')
                <div class="row align-items-center">
                    <div class="col col-lg-10">
                        <h4 class="mb-0 text-white2">Complete agora os dados da Unidade Produtiva ou pule para próximo passo.</h4>
                    </div>

                    <div class="col col-lg-2 text-right">
                        <a href="{{route('admin.core.produtor.dashboard', ['produtor'=>$produtor])}}" class="btn btn-primary px-5" form="form-builder">PULAR</a>
                    </div>
                </div>
            @endslot
        @endcardater
    @endif

    <div class="card-ater">
        <div class="card-body-ater">
            {{-- @include('backend.components.title-form.index', ['title' => $title]) --}}

            {!!form_start($form)!!}

            {!!form_until($form, 'lng')!!}

            @include('backend.core.unidade_produtiva.lat_lng.index', ['lat' => @$form->lat->getValue(), 'lng'=> @$form->lng->getValue()])

            {!!form_until($form, 'card-solo-end')!!}

            @if (@$unidadeProdutiva)
                <div class="mt-5">
                    <div id="a-uso-do-solo">
                        @include('backend.components.iframe.html', ["id"=>$caracterizacoesId, "src"=>$caracterizacoesSrc])
                    </div>
                </div>
            @else
                @include('backend.components.card-iframe-add.html', ["title"=>"Uso do Solo", "data"=>"a-uso-do-solo", "label"=>"Cadastrar Uso do Solo"])
            @endif

            {!!form_until($form, 'card-agua-end')!!}

            @if (@$unidadeProdutiva)
                <div class="mt-5">
                    <div id="a-pessoas">
                        @include('backend.components.iframe.html', ["id"=>$colaboradoresId, "src"=>$colaboradoresSrc])
                    </div>

                    <div id="a-infra-estrutura">
                        @include('backend.components.iframe.html', ["id"=>$instalacoesId, "src"=>$instalacoesSrc])
                    </div>
                </div>
            @else
                @include('backend.components.card-iframe-add.html', ["title"=>"Pessoas", "data"=>"a-pessoas", "label"=>"Cadastrar Pessoa"])

                @include('backend.components.card-iframe-add.html', ["title"=>"Infra-estrutura", "data"=>"a-infra-estrutura", "label"=>"Cadastrar Infra-estrutura"])
            @endif

            {!!form_rest($form)!!}

            @if (@$unidadeProdutiva)
                <div class="mt-5">
                    <div id="a-arquivos">
                        @include('backend.components.iframe.html', ["id"=>$arquivosId, "src"=>$arquivosSrc])
                    </div>
                </div>
            @else
                @include('backend.components.card-iframe-add.html', ["title"=>"Arquivos", "data"=>"a-arquivos", "label"=>"Cadastrar Arquivo"])
            @endif
        </div>

        <div class="card-footer-ater">
            <div class="row">
                <div class="col">
                    {{ form_cancel(App\Helpers\General\AppHelper::prevUrl(route('admin.core.unidade_produtiva.index')), __('buttons.general.cancel'), 'btn btn-danger px-4') }}
                </div>

                <div class="col text-right">
                    <button type="submit" class="btn btn-primary px-5" form="form-builder">Salvar</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('after-scripts')
    @if (@$unidadeProdutiva)
        @include('backend.components.iframe.scripts', ["id"=>$colaboradoresId, "src"=>$colaboradoresSrc])
        @include('backend.components.iframe.scripts', ["id"=>$instalacoesId, "src"=>$instalacoesSrc])
        @include('backend.components.iframe.scripts', ["id"=>$caracterizacoesId, "src"=>$caracterizacoesSrc])
        @include('backend.components.iframe.scripts', ["id"=>$arquivosId, "src"=>$arquivosSrc])
    @endif

    @include('backend.core.unidade_produtiva.lat_lng.scripts', ['lat' => @$form->lat->getValue(), 'lng'=> @$form->lng->getValue()])

    @include('backend.scripts.estado-cidade-select2')

    <script>
        $(function() {
            $("select[name='canaisComercializacao[]']").select2();
            $("select[name='riscosContaminacaoAgua[]']").select2();
            $("select[name='tiposFonteAgua[]']").select2();
            $("select[name='solosCategoria[]']").select2();
            $("select[name='certificacoes[]']").select2();
            $("select[name='pressaoSociais[]']").select2();
            $("select[name='residuoSolidos[]']").select2();
            $("select[name='esgotamentoSanitarios[]']").select2();

            selectAutoComboSim('#fl_car', '#card-car');

            selectAutoYesNo("#fl_risco_contaminacao", '.card-risco-contaminacao');
            selectAutoYesNo("#fl_certificacoes", '#card-certificacoes');
            selectAutoYesNo("#fl_comercializacao", '#card-comercializacao');
            selectAutoYesNo("#fl_pressao_social", '#card-pressao-social');

            selectAutoComboSim('#fl_producao_processa', '#card-producao-processa');

            selectAutoYesNo("#solosCategoria input", '#card-outros-usos');

        });
    </script>
@endpush
