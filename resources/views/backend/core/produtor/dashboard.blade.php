@extends('backend.layouts.app')

@section('title', 'Dashboard do/a Produtor/a - '.$produtor->nome)

@section('content')
<h1 class="mb-4">Dashboard do/a Produtor/a: {{$produtor->nome}}</h1>
    <div class="row">
        <div class="col col-12 col-md-6">
            @cardater(['title'=>'Dados do/a Produtor/a'])
                @slot('body')
                    <div class="row ml-1">
                        <div class="col col-sm-6 ">
                            <div class="row">
                                <div class="avatar-rounded">
                                    <span>{{substr($produtor->nome, 0, 2)}}</span>
                                </div>

                                <div class="ml-2 my-auto">
                                    <div>{{$produtor->nome}}</div>
                                    <div>{{$produtor->telefone_1}}</div>
                                    <div>{{$produtor->telefone_2}}</div>

                                    @php
                                        // $unidadeProdutiva = $produtor->unidadesProdutivas()->with('colaboradoresSocios')->get();
                                        // $colaboradores = $unidadeProdutiva->map(function ($item, $key) {
                                        //     return $item->colaboradoresSocios->pluck('nome');
                                        // })->toArray();
                                        $socios = join(", ", @$produtor->unidadesProdutivas()->pluck('socios')->toArray());
                                    @endphp

                                    @if ($socios)
                                        <div class="text-black-50">
                                            Coproprietários/as: <span>{{$socios}}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col col-sm-6  pull-right">

                        </div>
                    </div>
                @endslot
            @endcardater

            @cardater(['title'=>'Unidades Produtivas'])
                @slot('body')
                    @foreach ($produtor->unidadesProdutivas as $k=>$v)
                        <a href={{route('admin.core.unidade_produtiva.view', ['unidadeProdutiva'=>$v])}} style='color:inherit; text-decoration:none;'>
                            <h5>{{$v->nome}}</h5>
                            <div>{{$v->endereco}} {{$v->bairro}}</div>
                            <div>{{$v->cidade->nome}} - {{$v->estado->uf}}</div>
                        </a>
                        <hr/>
                    @endforeach
                @endslot
            @endcardater
        </div>

        <div class="col col-12 col-md-6">
            @cardater(['title'=>'Mapa - Unidades Produtivas', 'class'=>'card-ater-mapa'])
                @slot('body')
                    @include('backend.components.unidades-produtivas-latlng.html')
                @endslot
            @endcardater
        </div>
    </div>

    <div>
        <div class="row">
            @can('view menu caderno')
                @php
                    $totalCaderno = count($produtor->cadernos);
                @endphp

                <div class="col-sm-6 col-md-4 col-lg-4">
                    @cardaddview(['title'=>'Caderno de Campo', 'total'=>$totalCaderno, 'icon'=>'c-icon c-icon-lg cil-clipboard', 'labelAdd'=>'Novo Caderno', 'linkAdd'=>route('admin.core.cadernos.produtor_unidade_produtiva', ['produtor'=>$produtor]), 'labelView'=>'Visualizar', 'linkView'=>route('admin.core.cadernos.index', ['produtor'=>$produtor]), 'permissionView'=>'view menu caderno', 'permissionAdd'=>'create caderno'])
                    @endcardaddview
                </div>
            @endcan

            @can('view menu farmers')
                <div class="col-sm-6 col-md-4 col-lg-4">
                    @cardaddview(['title'=>'Produtor/a', 'total'=>'', 'icon'=>'c-icon-lg cil-address-book', 'labelAdd'=>'Editar Produtor/a', 'linkAdd'=>route('admin.core.produtor.edit', ['produtor'=>$produtor]), 'labelView'=>'Visualizar', 'linkView'=>route('admin.core.produtor.view', ['produtor'=>$produtor]), 'permissionView'=>'view menu farmers', 'permissionAdd'=>'edit same operational units farmers'])
                    @endcardaddview
                </div>
            @endcan

            @can('view menu productive units')
                @php
                    $totalUnidadesProdutivas = count($produtor->unidadesProdutivas);

                    if (@count($produtor->unidadesProdutivas) == 0)
                        $linkUnidadesProdutivas = '';
                    else if (count($produtor->unidadesProdutivas) == 1)
                        $linkUnidadesProdutivas = route('admin.core.unidade_produtiva.view', ['unidadeProdutiva'=> @$produtor->unidadesProdutivas[0]]);
                    else
                        $linkUnidadesProdutivas = route('admin.core.unidade_produtiva.index', ['produtor'=>$produtor]);
                @endphp

                <div class="col-sm-6 col-md-4 col-lg-4">
                    @cardaddview(['title'=>'Unidades Produtivas', 'total'=>$totalUnidadesProdutivas, 'icon'=>'c-icon c-icon-lg cil-location-pin', 'labelAdd'=>'Nova Unidade Produtiva', 'linkAdd'=>route('admin.core.unidade_produtiva.create', ['produtor'=>$produtor]), 'labelView'=>'Visualizar', 'linkView'=>$linkUnidadesProdutivas, 'permissionView'=>'view menu productive units', 'permissionAdd'=>'create same operational units productive units'])
                    @endcardaddview
                </div>
            @endcan

            @can('view menu checklist_unidade_produtiva')
                @php
                    $totalFormulariosAplicados = count($produtor->checklists);
                @endphp
                <div class="col-sm-6 col-md-4 col-lg-4">
                    @cardaddview(['title'=>'Formulários Aplicados', 'total'=>$totalFormulariosAplicados, 'icon'=>'c-icon c-icon-lg cil-clipboard', 'labelAdd'=>'Aplicar Formulário', 'linkAdd'=>route('admin.core.checklist_unidade_produtiva.template', ['produtor'=>$produtor]), 'labelView'=>'Visualizar', 'linkView'=>route('admin.core.checklist_unidade_produtiva.index', ['produtor'=>$produtor]), 'permissionView'=>'view menu checklist_unidade_produtiva', 'permissionAdd'=>'create checklist_unidade_produtiva'])
                    @endcardaddview
                </div>
            @endcan

           @can('view menu plano_acao')
                @php
                    $totalPlanoAcao = count($produtor->plano_acao);
                    $totalPlanoAcaoColetivo = count($produtor->plano_acao_coletivo);
                @endphp

                @php
                    if (@count($produtor->unidadesProdutivas) == 0)
                        $linkCriarPdaIndividual = '';
                    else if (count($produtor->unidadesProdutivas) == 1)
                        $linkCriarPdaIndividual = route('admin.core.plano_acao.create', ['produtor' => $produtor, 'unidadeProdutiva' => $produtor->unidadesProdutivas[0]]);
                    else
                        $linkCriarPdaIndividual = route('admin.core.plano_acao.produtor_unidade_produtiva', ['produtor'=>$produtor]);
                @endphp

                @php
                    $linkCriarPdaFormulario = route('admin.core.plano_acao.checklist_unidade_produtiva', ['produtor'=>$produtor]);
                @endphp

                @php
                    if (@$totalPlanoAcao == 1)
                        $linkViewPdaIndividual = route('admin.core.plano_acao.view', ['planoAcao'=>$produtor->plano_acao[0]]);
                    else
                        $linkViewPdaIndividual = route('admin.core.plano_acao.index', ['produtor'=>$produtor]);
                @endphp

                <div class="col-sm-6 col-md-4 col-lg-4">
                    @cardaddview(['title'=>'Plano de Ação', 'total'=>$totalPlanoAcao, 'icon'=>'c-icon c-icon-lg cil-clipboard', 'labelAdd'=>'Criar Plano de Ação - Individual', 'linkAdd'=>$linkCriarPdaIndividual, 'labelAdd2'=>'Criar Plano de Ação - Formulário', 'linkAdd2'=>$linkCriarPdaFormulario, 'labelView'=>'Visualizar', 'linkView'=>$linkViewPdaIndividual, 'permissionView'=>'view menu plano_acao', 'permissionAdd'=>'create plano_acao'])
                    @endcardaddview
                </div>

                @php
                    if (@$totalPlanoAcaoColetivo == 1)
                        $linkViewPdaColetivo = route('admin.core.plano_acao_coletivo.view', ['planoAcao'=>$produtor->plano_acao_coletivo[0]->plano_acao_coletivo_id]);
                    else
                        $linkViewPdaColetivo = route('admin.core.plano_acao_coletivo.index', ['produtor'=>$produtor]);
                @endphp

                <div class="col-sm-6 col-md-4 col-lg-4">
                    @cardaddview(['title'=>'Plano de Ação Coletivo', 'total'=>$totalPlanoAcaoColetivo, 'icon'=>'c-icon c-icon-lg cil-clipboard', 'labelAdd'=>'Criar Plano de Ação', 'linkAdd'=>'', 'labelView'=>'Visualizar', 'linkView'=>$linkViewPdaColetivo, 'permissionView'=>'view menu plano_acao', 'permissionAdd'=>'create plano_acao'])
                    @endcardaddview
                </div>
            @endcan
        </div>
    </div>
@endsection

@push('before-scripts')
    <style>
        .card-ater-mapa {
            height:calc(100% - 24px);
            min-height:300px;
        }

        .card-ater-mapa .card-body, .card-ater-mapa .map-lat-lng, .card-ater-mapa .map-lat-lng #map-content {
            height:100%;
        }

        .avatar-rounded {
            width: 60px;
            height: 60px;
            background-color: rgb(235, 237, 239);
            border-radius:50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .avatar-rounded span {
            font-size:22px;
            text-align: center;
            text-transform: uppercase;
        }
    </style>
@endpush

@push('after-scripts')
    @include('backend.components.unidades-produtivas-latlng.scripts', ["produtor"=>$produtor])
@endpush
