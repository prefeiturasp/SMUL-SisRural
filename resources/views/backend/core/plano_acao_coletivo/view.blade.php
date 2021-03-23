@extends('backend.layouts.app')

@section('content')
    @cardater(['title' => 'Informações gerais'])
        @if (@$planoAcao->status == \App\Enums\PlanoAcaoStatusEnum::NaoIniciado || $planoAcao->status == \App\Enums\PlanoAcaoStatusEnum::EmAndamento)
            @can('update', @$planoAcao)
                @slot('headerRight')
                    <div class="float-right">
                        <a href="{{route('admin.core.plano_acao_coletivo.pdf', ['planoAcao'=>$planoAcao])}}" class="btn btn-primary px-5 mr-3" form="form-builder">Download do plano ação coletivo</a>

                        <a href="{{route('admin.core.plano_acao_coletivo.edit', ['planoAcao'=>$planoAcao])}}" class="btn btn-primary px-5" form="form-builder">Editar plano de ação</a>
                    </div>
                @endslot
            @endcan
        @endif

        @slot('body')
            <table class="table table-hover">
                <tr>
                    <th>Nome</th>
                    <td>{{$planoAcao->nome}}</td>
                </tr>

                <tr>
                    <th>Status</th>
                    <td>{{\App\Enums\PlanoAcaoStatusEnum::toSelectArray()[$planoAcao->status]}}</td>
                </tr>

                <tr>
                    <th>Prazo</th>
                    <td>{{$planoAcao->prazo_formatted}}</td>
                </tr>

                <tr>
                    <th>Criado por</th>
                    <td>{{@$planoAcao->usuario->fullname}}</td>
                </tr>

                <tr>
                    <th>Criado em</th>
                    <td>{{$planoAcao->created_at_formatted }}</td>
                </tr>

                <tr>
                    <th>Atualizado em</th>
                    <td>{{ $planoAcao->updated_at_formatted }}</td>
                </tr>
            </table>
        @endslot
    @endcardater

    @cardater(['title' => 'Unidades produtivas'])
        @slot('body')
            <table id="table-unidades-produtivas" class="table table-ater">
                <thead>
                    <tr>
                        <th>Produtor/a</th>
                        <th>Unid. Produtiva</th>
                        <th><img class="icon-status" src='img/backend/select/nao_iniciado.png'/> Não iniciado</th>
                        <th><img class="icon-status" src='img/backend/select/em_andamento.png'/> Em Andamento</th>
                        <th><img class="icon-status" src='img/backend/select/cancelado.png'/> Cancelado</th>
                        <th><img class="icon-status" src='img/backend/select/concluido.png'/> Concluído</th>
                        <th width="60">Ações</th>
                    </tr>
                </thead>
            </table>
        @endslot
    @endcardater

    @cardater(['title' => 'Ações coletivas cadastradas'])
        @slot('body')
            <table id="table-coletivas" class="table table-ater">
                <thead>
                    <tr>
                        <th width="80">#</th>
                        <th width="80">Prioridade</th>
                        <th>Ação</th>
                        <th>Prazo</th>
                        <th>Não Iniciado</th>
                        <th>Em Andamento</th>
                        <th>Cancelado</th>
                        <th>Concluído</th>
                        <th>Status Geral</th>
                        <th>Ações</th>
                    </tr>
                </thead>
            </table>
        @endslot
    @endcardater

    @cardater(['title' => 'Acompanhamento do plano de ação coletivo'])
        @slot('headerRight')
            <div class="float-right">
                @can('history', @$planoAcao)
                    <a href="{{ $addHistoricoUrl }}"
                        class="btn-create-historico-pda btn btn-primary px-5"
                        title="Novo">Adicionar novo acompanhamento</a>
                @endcan
            </div>
        @endslot

        @slot('body')
            <table id="table-acompanhamento" class="table table-ater">
                <thead>
                    <tr>
                        <th width="80">#</th>
                        <th>Acompanhamento</th>
                        <th width="250">Usuário</th>
                        <th width="200">Adicionado em</th>
                        <th></th>
                    </tr>
                </thead>

                {{-- @foreach ($planoAcao->historicos as $rowHistorico)
                    <tr>
                        <td>{{$rowHistorico->texto}}</td>
                        <td>{{$rowHistorico->usuario->fullname}}</td>
                        <td>{{$rowHistorico->created_at_formatted}}</td>
                    </tr>
                @endforeach --}}
            </table>
        @endslot
    @endcardater


    @foreach ($planoAcao->plano_acao_filhos_with_count_status()->with(['unidade_produtiva:id,uid,nome', 'produtor:id,uid,nome'/*, 'historicos'*/])->get() as $row)
        @cardater(['title' => 'Unidade Produtiva - '.$row->unidade_produtiva->nome])
            @slot('headerRight')
                <div class="float-right">
                    @can('history', @$row)
                        <a href="{{route('admin.core.plano_acao.historico.create_and_list', ['planoAcao'=>$row])}}" class="btn-create-historico-pda btn btn-primary px-5 mr-2" form="form-builder">Acompanhamentos</a>
                    @endcan

                    <a href="{{route('admin.core.plano_acao.pdf', ['planoAcao'=>$row])}}" class="btn btn-primary px-5" form="form-builder">Download do plano ação individual</a>
                </div>
            @endslot

            @slot('body')
                <table id="table-unidades-produtivas-{{$row->id}}" class="table table-ater">
                        <tr>
                            <th width="200">Produtor/a</th>
                            <td>{!!$row->produtor->nome. '<br>' . $row->unidade_produtiva->socios!!}</td>
                        </tr>
                        <tr>
                            <th>Unid. Produtiva</th>
                            <td>{{$row->unidade_produtiva->nome}}</td>
                        </tr>
                        <tr>
                            <th><img class="icon-status" src='img/backend/select/nao_iniciado.png'/> Não iniciado</th>
                            <td>{{$row->nao_iniciado . ' ' . App\Helpers\General\AppHelper::toPerc($row->nao_iniciado, $row->total)}}</td>
                        </tr>
                        <tr>
                            <th><img class="icon-status" src='img/backend/select/em_andamento.png'/> Em Andamento</th>
                            <td>{{$row->em_andamento . ' ' . App\Helpers\General\AppHelper::toPerc($row->em_andamento, $row->total)}}</td>
                        </tr>
                        <tr>
                            <th><img class="icon-status" src='img/backend/select/cancelado.png'/> Cancelado</th>
                            <td>{{$row->cancelado . ' ' . App\Helpers\General\AppHelper::toPerc($row->cancelado, $row->total)}}</td>
                        </tr>
                        <tr>
                            <th><img class="icon-status" src='img/backend/select/concluido.png'/> Concluído</th>
                            <td>{{$row->concluido . ' ' . App\Helpers\General\AppHelper::toPerc($row->concluido, $row->total)}}</td>
                        </tr>
                </table>

                <br/>

                @cardater(['title' => 'Açoes individuais', 'class'=> 'card-ater-black'])
                    @slot('body')
                        @php
                            $acoesIndividuais = App\Models\Core\PlanoAcaoItemModel::where("plano_acao_id", $row->id)->with([/*'historicos',*/ 'plano_acao:id,nome,unidade_produtiva_id'])->get();
                        @endphp
                        <table class="table table-ater">
                            <tr>
                                <th width="80">Prioridade</th>
                                <th>Ação</th>
                                <th>Prazo</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                            @foreach ($acoesIndividuais as $rowItem)
                                <tr>
                                    <td><img src="img/backend/select/{{$rowItem->prioridade}}.png" style="display:block; margin:auto; padding-right:30px;"/></td>
                                    <td>{{$rowItem->descricao}}</td>
                                    <td>{{$rowItem->prazo_formatted}}</td>
                                    <td><img style="width:14px; height:auto;" src="img/backend/select/{{$rowItem->status}}.png"/> <span class="ml-2">{{App\Enums\PlanoAcaoItemStatusEnum::toSelectArray()[$rowItem->status]}}</span></td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button id="userActions" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">
                                                @lang('labels.general.more')
                                            </button>

                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userActions"  style="min-width:215px;">
                                                <a href="{{route('admin.core.plano_acao_item.historico_item.create_and_list', ['planoAcaoItem' => $rowItem])}}" class="dropdown-item btn-create-historico btn-create-historico-item">Acompanhamentos</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                {{-- @if (count($rowItem->historicos) > 0)
                                    <tr>
                                        <td colspan="4" style="padding:0px;">
                                            <table class="table table-ater bg-white">
                                                <tr>
                                                    <th>Acompanhamento</th>
                                                    <th>Usuário</th>
                                                    <th>Adicionado em</th>
                                                </tr>
                                                @foreach ($rowItem->historicos as $rowItemHistorico)
                                                    <tr>
                                                        <td>{{$rowItemHistorico->texto}}</td>
                                                        <td>{{$rowItemHistorico->usuario->first_name}}</td>
                                                        <td>{{$rowItemHistorico->created_at_formatted}}</td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </td>
                                    </tr>
                                @endif --}}
                            @endforeach
                        </table>
                    @endslot
                @endcardater

                {{-- @if (count($row->historicos) > 0)
                    <br/>

                    @cardater(['title' => 'Acompanhamento do plano de ação individual', 'class'=> 'card-ater-black'])
                        @slot('body')
                            <table class="table table-ater">
                                <tr>
                                    <th>Acompanhamento</th>
                                    <th width="250">Usuário</th>
                                    <th width="200">Adicionado em</th>
                                </tr>
                                @foreach ($row->historicos as $rowHistorico)
                                    <tr>
                                        <td>{{$rowHistorico->texto}}</td>
                                        <td>{{$rowHistorico->usuario->fullname}}</td>
                                        <td>{{$rowHistorico->created_at_formatted}}</td>
                                    </tr>
                                @endforeach
                            </table>
                        @endslot
                    @endcardater
                @endif --}}
            @endslot
        @endcardater
    @endforeach


    <div class="row mb-4">
        <div class="col">
            {{ form_cancel($back, 'Voltar', 'btn btn-danger px-4') }}
        </div>

        <div class="col">
            @if (@$planoAcao->status == \App\Enums\PlanoAcaoStatusEnum::NaoIniciado || $planoAcao->status == \App\Enums\PlanoAcaoStatusEnum::EmAndamento)
                @can('update', @$planoAcao)
                    <div class="float-right">
                        <a href="{{route('admin.core.plano_acao_coletivo.edit', ['planoAcao'=>$planoAcao])}}" class="btn btn-primary px-5" form="form-builder">Editar plano de ação</a>
                    </div>
                @endcan
            @endif
        </div>
    </div>
@endsection

@push('after-scripts')
    @include('backend.components.modal-iframe.html', ["id"=>"modal-create-historico-item", "iframe"=>"iframe-create-historico-item", "btnClass"=>"btn-create-historico-item", "title"=>"Acompanhamentos"])

    @include('backend.components.modal-iframe.html', ["id"=>"modal-create-historico-pda", "iframe"=>"iframe-create-historico-pda", "btnClass"=>"btn-create-historico-pda", "title"=>"Acompanhamentos"])

    <script>
        $(document).ready(function () {
            $('#table-coletivas').DataTable({
                "dom": '<"top table-top">rt<"row table-bottom"<"col-sm-12 col-md-5"><"col-sm-12 col-md-7">><"clear">',
                // "dom": '<"top table-top"f>rt<"row table-bottom"<"col-sm-12 col-md-5"il><"col-sm-12 col-md-7"p>><"clear">',
                "pageLength": 5000,
                "processing": true,
                "serverSide": true,
                "lengthChange": false,
                "ajax": {
                    "url" : '{{ $datatableAcoesColetivas }}',
                },
                "language": {
                    "url": '{{ asset('js/datatables-pt-br.json')}}'
                },
                "columns": [
                    {"data": "uid", "orderable":false, "visible":false},
                    {"data": "prioridade", "orderable":false},
                    {"data": "descricao", "orderable":false},
                    {"data": "prazo_formatted", "name": "prazo", "orderable":false},

                    {"data": "nao_iniciado", "orderable":false},
                    {"data": "em_andamento", "orderable":false},
                    {"data": "cancelado", "orderable":false},
                    {"data": "concluido", "orderable":false},
                    {"data": "status", "orderable":false},
                    {"data": "actionsView", "orderable":false},
                ],
            }).on('draw', function () {
                initAutoLink($("#table-coletivas"));
            });

            addAutoLink(function () {
                debounceSearch('#table-coletivas');
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            $('#table-unidades-produtivas').DataTable({
                "dom": '<"top table-top">rt<"row table-bottom"<"col-sm-12 col-md-5"><"col-sm-12 col-md-7"p>><"clear">',
                "pageLength": 50,
                "processing": true,
                "serverSide": true,
                "lengthChange": false,
                "ajax": '{{ $datatableUnidadesProdutivas }}',
                "language": {
                    "url": '{{ asset('js/datatables-pt-br.json')}}'
                },
                "columns": [
                    {"data": "produtor.nome", "orderable":false},
                    {"data": "unidade_produtiva.nome", "orderable":false},
                    {"data": "nao_iniciado", "orderable":false},
                    {"data": "em_andamento", "orderable":false},
                    {"data": "cancelado", "orderable":false},
                    {"data": "concluido", "orderable":false},
                    {"data": "actionsView", "orderable":false},
                ]
            }).on('draw', function () {
                initAutoLink($("#table-unidades-produtivas"));
            });

            addAutoLink(function () {
                debounceSearch('#table-unidades-produtivas');
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            var tableAcompanhamento = $('#table-acompanhamento').DataTable({
                "dom": '<"top table-top"f>rt<"row table-bottom"<"col-sm-12 col-md-5"il><"col-sm-12 col-md-7"p>><"clear">',
                "pageLength": 300,
                "processing": true,
                "serverSide": true,
                "lengthChange": false,
                "ajax": '{{ $datatableAcompanhamentoUrl }}',
                "language": {
                    "url": '{{ asset('js/datatables-pt-br.json')}}'
                },
                "columns": [
                    {"data": "uid"},
                    {"data": "texto"},
                    {"data": "usuario"},
                    {"data": "created_at_formatted", "name": "created_at"},
                    {"data": 'created_at_formatted', "name": "created_at_formatted", visible:false},
                ],
                "order": [[ 4, "asc" ]]
            }).on('draw', function () {
                initAutoLink($("#table-acompanhamento"));
            });

            addAutoLink(function () {
                debounceSearch('#table-acompanhamento');
            });

            $("#modal-create-historico-pda").on('hidden.coreui.modal', function (e) {
                tableAcompanhamento.draw();
            });
        });
    </script>

    <style>
        .content-table-expand {
            background-color:#F8F8F8;
        }
    </style>
@endpush
