@extends('backend.layouts.app-template', ['iframe'=>true])

@section('body')
    <div class="card card-ater">
        <div class="card-header">
            <div class="row">
                <div class="col-sm-5">
                    <h1 class="card-title mb-0 mt-1 h4">
                        {{$title}}
                    </h1>
                </div>

                @can('createItem', $planoAcao)
                    <div class="col-sm-7 pull-right">
                        <div class="float-right">
                            <a href="{{ $urlAdd }}"
                            class="btn btn-primary px-5"
                            title="Novo">Adicionar nova ação coletiva</a>
                        </div>
                    </div>
                @endcan
            </div>
        </div>

        <div class="card-body">
            <table id="table" class="table table-ater" style="width:100%">
                <thead>
                <tr>
                    <th width="60">#</th>
                    <th width="80">Prioridade</th>
                    <th>Ação</th>
                    <th>Prazo</th>

                    <th><img class="icon-status" src='img/backend/select/nao_iniciado.png'/> Não iniciado</th>
                    <th><img class="icon-status" src='img/backend/select/em_andamento.png'/> Em Andamento</th>
                    <th><img class="icon-status" src='img/backend/select/cancelado.png'/> Cancelado</th>
                    <th><img class="icon-status" src='img/backend/select/concluido.png'/> Concluído</th>

                    <th>Status Geral</th>
                    {{-- <th></th> --}}
                    {{-- <th></th> --}}
                    <th></th>
                    <th></th>
                    <th width="60">Ações</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

    @include('backend.components.modal-iframe.html', ["id"=>"modal-create-historico", "iframe"=>"iframe-create-historico", "btnClass"=>"btn-create-historico", "title"=>"Acompanhamentos"])
@endsection

@push('after-scripts')
    <style>
        #table_filter {
            display: none;
        }
    </style>

    <script>
        //Tabela
        var versaoSimples = {{ $versaoSimples }};
        var table;

        $(document).ready(function () {
            table = $('#table').DataTable({
                "dom": '<"top table-top"f>rt<"row table-bottom"<"col-sm-12 col-md-5"il><"col-sm-12 col-md-7"p>><"clear">',
                // "dom": '<"top table-top"f>rt<"row table-bottom"<"col-sm-12 col-md-5"il><"col-sm-12 col-md-7"p>><"clear">',
                "pageLength": 30,
                "processing": true,
                "serverSide": true,
                "lengthChange": false,
                "ajax": {
                    "url" : '{{ $urlDatatable }}',
                },
                "language": {
                    "url": '{{ asset('js/datatables-pt-br.json')}}'
                },
                "columns": [
                    {"data": "uid", visible:false},
                    {"data": "prioridade"},
                    {"data": "descricao"},
                    {"data": "prazo_formatted", "name": "prazo"},

                    {"data": "nao_iniciado", visible:!versaoSimples},
                    {"data": "em_andamento", visible:!versaoSimples},
                    {"data": "cancelado", visible:!versaoSimples},
                    {"data": "concluido", visible:!versaoSimples},
                    // {"data": "ultima_observacao", visible:!versaoSimples},
                    // {"data": "ultima_observacao_data_formatted", "name":"ultima_observacao_data", visible:!versaoSimples},
                    {"data": "status", visible:!versaoSimples},
                    {"data": 'prazo_formatted', "name":"prazo_formatted", visible:false},
                    // {"data": 'ultima_observacao_data_formatted', "name":"ultima_observacao_data_formatted", visible:false},
                    {
                        "className": 'details-control',
                        "orderable": false,
                        "data": null,
                        "defaultContent": '<div class="text-primary btn btn-sm">Expandir</div>'
                    },
                    {
                        "data": "actions",
                        "searchable": false,
                        "orderable": false,
                        render: function (data) {
                            return htmlDecode(data);
                        }
                    }
                ],
                "order": [[ 1, "asc" ]],
            }).on('draw', function () {
                initAutoLink($("#table"));

                $(".details-control .btn").on("click", function() {
                    if (parent){
                        var tr = $(this).closest('tr');
                        var row = table.row( tr );

                        var item_id = row.data().id

                        var ob = { type:'update_iframe', data: { unidade_produtiva_id:null, item_id:item_id } };

                        parent.postMessage(JSON.stringify(ob));
                    }
                });
            });

            addAutoLink(function () {
                debounceSearch('#table');
            });

            //Atualiza os outros iframes da tela
            if (parent) {
                parent.postMessage(JSON.stringify({ type:'refresh', data: { } }));
            }
        });
    </script>
@endpush
