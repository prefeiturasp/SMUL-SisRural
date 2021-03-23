@extends('backend.layouts.app-template', ['iframe'=>true])

@section('body')
    <div class="card card-ater">
        <div class="card-header">
            <div class="row">
                <div class="col-5">
                    <h1 class="card-title mb-0 mt-1 h4">
                        Unidades produtivas
                    </h1>
                </div>

                @can('createItem', $planoAcao)
                    <div class="col-sm-7 pull-right">
                        <div class="float-right">
                            <a href="{{ $urlAdd }}"
                            class="btn btn-primary px-5"
                            title="Novo">Adicionar unidade produtiva</a>
                        </div>
                    </div>
                @endcan
            </div>
        </div>

        <div class="card-body">
                <table id="table" class="table table-ater">
                    <thead>
                    <tr>
                        <th>Produtor/a</th>
                        <th>Unid. Produtiva</th>
                        <th><img class="icon-status" src='img/backend/select/nao_iniciado.png'/> Não iniciado</th>
                        <th><img class="icon-status" src='img/backend/select/em_andamento.png'/> Em Andamento</th>
                        <th><img class="icon-status" src='img/backend/select/cancelado.png'/> Cancelado</th>
                        <th><img class="icon-status" src='img/backend/select/concluido.png'/> Concluído</th>
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
    <script>
        $(document).ready(function () {
            var table = $('#table').DataTable({
                "dom": '<"top table-top"f>rt<"row table-bottom"<"col-sm-12 col-md-5"il><"col-sm-12 col-md-7"p>><"clear">',
                "processing": true,
                "serverSide": true,
                "lengthChange": false,
                "ajax": '{{ $urlDatatable }}',
                "language": {
                    "url": '{{ asset('js/datatables-pt-br.json')}}'
                },
                "columns": [
                    {"data": "produtor.nome"},
                    {"data": "unidade_produtiva.nome"},
                    {"data": "nao_iniciado", "searchable":false},
                    {"data": "em_andamento", "searchable":false},
                    {"data": "cancelado", "searchable":false},
                    {"data": "concluido", "searchable":false},
                    {
                        "className": 'details-control',
                        "orderable": false,
                        "searchable":false,
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
                ]
            }).on('draw', function () {
                initAutoLink($("#table"));

                $(".details-control .btn").on("click", function() {
                    if (parent){
                        var tr = $(this).closest('tr');
                        var row = table.row( tr );

                        var unidade_produtiva_id = row.data().unidade_produtiva_id

                        var ob = { type:'update_iframe', data: { unidade_produtiva_id:unidade_produtiva_id, item_id:null } };

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
