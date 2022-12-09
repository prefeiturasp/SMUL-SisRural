@extends('backend.layouts.app')

@section('content')
    @cardater(['title' => 'Informações gerais'])
        @slot('headerRight')
            <div class="float-right">
                {{-- Edição do PDA --}}
                @if (@$planoAcao->status == \App\Enums\PlanoAcaoStatusEnum::NaoIniciado || $planoAcao->status == \App\Enums\PlanoAcaoStatusEnum::EmAndamento)
                    @can('update', @$planoAcao)
                        <a href="{{route('admin.core.plano_acao.edit', ['planoAcao'=>$planoAcao])}}" class="btn btn-primary px-5" form="form-builder">Editar plano de ação</a>
                    @endcan
                @endif

                {{-- Detalhamento do PDA --}}
                @if (@$planoAcao->checklist_unidade_produtiva_id && @$planoAcao->status == \App\Enums\PlanoAcaoStatusEnum::Rascunho)
                    @can('update', @$planoAcao)
                        <a href="{{route('admin.core.plano_acao.edit_com_checklist', ['planoAcao'=>$planoAcao])}}" class="btn btn-primary px-5" form="form-builder">Editar plano de ação</a>
                    @endcan
                @endif

                {{-- Visualização do formulário atrelado ao PDA com formulário --}}
                @if (@$planoAcao->checklist_unidade_produtiva_id)
                    <a href="{{route('admin.core.checklist_unidade_produtiva.view', $planoAcao->checklist_unidade_produtiva_id)}}" class="btn btn-primary px-5 ml-3">Ver formulário</a>
                @endif
            </div>
        @endslot



        @slot('body')
            <table class="table table-hover">
                @if ($planoAcao->checklist_unidade_produtiva_id)
                    <tr>
                        <th width="30%">Formulário</th>
                        <td>{{$planoAcao->checklist_unidade_produtiva->checklist->nome}}</td>
                    </tr>
                @endif

                @if ($planoAcao->nome)
                    <tr>
                        <th width="30%">Título</th>
                        <td>{{$planoAcao->nome}}</td>
                    </tr>
                @endif

                <tr>
                    <th>Produtor/a</th>
                    <td>{{$planoAcao->produtor->nome}}</td>
                </tr>

                @if (@$planoAcao->unidade_produtiva->socios)
                    <tr>
                        <th>Coproprietários/as</th>
                        <td>{{$planoAcao->unidade_produtiva->socios}}</td>
                    </tr>
                @endif

                <tr>
                    <th>Unidade Produtiva</th>
                    <td>{{$planoAcao->unidade_produtiva->nome}}</td>
                </tr>
            </table>
        @endslot
    @endcardater

    @cardater(['title' => 'Ações'])
        @slot('body')
            <table id="table" class="table table-ater">
                <thead>
                    <tr>
                        <th width="60">#</th>
                        <th width="80">Prioridade</th>
                        <th>Ação</th>
                        <th>Prazo</th>
                        <th>Último acompanhamento</th>
                        <th>Data último acomp.</th>
                        <th>Status</th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
            </table>
        @endslot
    @endcardater

    @cardater(['title' => 'Detalhes'])
        @slot('body')
            <table class="table table-hover">
                <tr>
                    <th>Status</th>
                    <td>{{\App\Enums\PlanoAcaoStatusEnum::toSelectArray()[$planoAcao->status]}}</td>
                </tr>

                <tr>
                    <th>Prazo</th>
                    <td>{{$planoAcao->prazo_formatted}}</td>
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

    @cardater(['title' => 'Acompanhamento do plano de ação'])
        @slot('headerRight')
            @can('create plano_acao_historico')
                <div class="float-right">
                    <a href="{{ $addHistoricoUrl }}"
                        class="btn-create-historico-pda btn btn-primary px-5"
                        title="Novo">Adicionar novo acompanhamento</a>
                </div>
            @endcan
        @endslot

        @slot('body')
            <table id="table-acompanhamento" class="table table-ater">
                <thead>
                    <tr>
                        <th width="60">#</th>
                        <th>Acompanhamento</th>
                        <th width="250">Usuário</th>
                        <th width="200">Adicionado em</th>
                        <th></th>
                    </tr>
                </thead>
            </table>
        @endslot
    @endcardater

    <div class="row mb-4">
        <div class="col">
            {{ form_cancel($back, 'Voltar', 'btn btn-danger px-4') }}
        </div>

        @if (@$planoAcao->status == \App\Enums\PlanoAcaoStatusEnum::NaoIniciado || $planoAcao->status == \App\Enums\PlanoAcaoStatusEnum::EmAndamento)
            @can('update', @$planoAcao)
                <div class="float-right">
                    <a href="{{route('admin.core.plano_acao.edit', ['planoAcao'=>$planoAcao])}}" class="btn btn-primary px-5" form="form-builder">Editar plano de ação</a>
                </div>
            @endcan
        @endif
    </div>

    @include('backend.components.modal-iframe.html', ["id"=>"modal-create-historico", "iframe"=>"iframe-create-historico", "btnClass"=>"btn-create-historico", "title"=>"Acompanhamentos"])

    @include('backend.components.modal-iframe.html', ["id"=>"modal-create-historico-pda", "iframe"=>"iframe-create-historico-pda", "btnClass"=>"btn-create-historico-pda", "title"=>"Acompanhamentos"])
@endsection

@push('after-scripts')
    <script>
        $(document).ready(function () {
            var table = $('#table').DataTable({
                 select: {
                    selector:'td:not(:eq(7))',
                 },

                "dom": '<"top table-top"f>rt<"row table-bottom"<"col-sm-12 col-md-5"il><"col-sm-12 col-md-7"p>><"clear">',
                "pageLength": 300,
                "processing": true,
                "serverSide": true,
                "lengthChange": false,
                "ajax": '{{ $datatableUrl }}',
                "language": {
                    "url": '{{ asset('js/datatables-pt-br.json')}}'
                },
                "columns": [
                    {"data": "uid", visible:false},
                    {"data": "prioridade"},
                    {"data": "descricao"},
                    {"data": "prazo_formatted", "name": "prazo"},
                    {"data": "ultima_observacao"},
                    {"data": "ultima_observacao_data_formatted", "name":"ultima_observacao_data"},
                    {"data": "status"},
                    {"data": "actionsView", "orderable": false},
                    // {
                    //     "className": 'details-control',
                    //     "orderable": false,
                    //     "data": null,
                    //     "defaultContent": '<div class="text-primary btn btn-sm">Expandir</div>'
                    // },
                    {"data": 'prazo_formatted', "name":"prazo_formatted", visible:false},
                    {"data": 'ultima_observacao_data_formatted', "name":"ultima_observacao_data_formatted", visible:false},
                ],
                "order": [[ 1, "asc" ]],
            });

            table.on("draw", function () {
                initAutoLink($("#table"));
            });

            addAutoLink(function () {
                debounceSearch('#table');
            });

            // $('#table tbody').on('click', 'td.details-control', function () {
            //     var tr = $(this).closest('tr');
            //     var row = table.row( tr );

            //     if (row.child.isShown() ) {
            //         row.child.hide();
            //         tr.removeClass('shown');
            //     } else {
            //         row.child( format(row.data()), 'content-table-expand').show();
            //         tr.addClass('shown');
            //     }
            // });
        });

        // function format (d) {
        //     return d.historicos;
        // }

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
