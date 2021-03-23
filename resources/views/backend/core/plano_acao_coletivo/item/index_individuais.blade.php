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
                <div class="col-sm-7 pull-right">
                    <div class="float-right">
                        <div class="btn btn-primary px-5 btn-update">Atualizar</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-header">
            <form method="POST" id="search-form" class="form-inline" role="form">
                <div class="form-group mr-4 mw-100">
                    <div class="form-group mr-4">
                        <label class="mr-4 font-weight-bold">Filtrar por:</label>
                    </div>

                    <label class="mr-4 font-weight-bold">Unidade(s) produtiva(s)</label>
                    {!! Form::select('unidade_produtiva', ['Todas'] + $unidadesProdutivas, $selectUnidadeProdutiva ? $selectUnidadeProdutiva : null, ['class' => 'form-control pr-4', 'style' => 'max-width:200px;']) !!}

                    <label class="ml-4 mr-4 font-weight-bold">Ações</label>
                    {!! Form::select('item', ['Todas'] + $itens, $selectItem ? $selectItem : null, ['class' => 'form-control pr-4', 'style' => 'max-width:200px;']) !!}

                    <label class="ml-4 mr-4 font-weight-bold">Prioridade</label>
                    {!! Form::select('prioridade', ['Todas'] + App\Enums\PlanoAcaoPrioridadeEnum::toSelectArray(), $selectPrioridade ? $selectPrioridade : null, ['class' => 'form-control pr-4']) !!}

                    <label class="ml-4 mr-4 font-weight-bold">Status</label>
                    {!! Form::select('status', ['Todos'] + App\Enums\PlanoAcaoItemStatusEnum::toSelectArray(), $selectStatus ? $selectStatus : null, ['class' => 'form-control pr-4']) !!}
                </div>
            </form>
        </div>

        <div class="card-body">
            <table id="table" class="table table-ater" style="width:100%">
                <thead>
                <tr>
                    <th width="60">#</th>
                    <th width="80">Prioridade</th>
                    <th>Ação</th>
                    <th>Unidade Produtiva</th>
                    {{-- <th>Prazo</th> --}}
                    <th>Último acompanhamento</th>
                    <th>Data último acomp.</th>
                    <th>Status</th>
                    <th></th>
                    {{-- <th></th> --}}
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
        #search-form .select2-selection {
            background-color:#F5F5F5;
            border-radius:0px;
            border:0px;
            box-shadow:none;
        }
        #table_filter {
            display: none;
        }
    </style>

    <script>
        //Tabela
        var oTable;
        $(document).ready(function () {
            oTable = $('#table').DataTable({
                "dom": '<"top table-top"f>rt<"row table-bottom"<"col-sm-12 col-md-5"il><"col-sm-12 col-md-7"p>><"clear">',
                // "dom": '<"top table-top"f>rt<"row table-bottom"<"col-sm-12 col-md-5"il><"col-sm-12 col-md-7"p>><"clear">',
                "pageLength": 30,
                "processing": true,
                "serverSide": true,
                "lengthChange": false,
                "ajax": {
                    "url" : '{{ $urlDatatable }}',
                    "data": function (d) {
                        if ($('#search-form select[name=unidade_produtiva]').length > 0) {
                            d.filter_unidade_produtiva = $('#search-form select[name=unidade_produtiva]').val();
                        }
                        if ($('#search-form select[name=item]').length > 0) {
                            d.filter_item = $('#search-form select[name=item]').val();
                        }
                        if ($('#search-form select[name=prioridade]').length > 0) {
                            d.filter_prioridade = $('#search-form select[name=prioridade]').val();
                        }
                        if ($('#search-form select[name=status]').length > 0) {
                            d.filter_status = $('#search-form select[name=status]').val();
                        }
                    }
                },
                "language": {
                    "url": '{{ asset('js/datatables-pt-br.json')}}'
                },
                "columns": [
                    {"data": "uid", visible:false},
                    {"data": "prioridade"},
                    {"data": "descricao"},
                    {"data": "unidade_produtiva"},
                    // {"data": "prazo_formatted", "name": "prazo"},
                    {"data": "ultima_observacao"},
                    {"data": "ultima_observacao_data_formatted", "name":"ultima_observacao_data"},
                    {"data": "status"},
                    // {"data": 'prazo_formatted', "name":"prazo_formatted", visible:false},
                    {"data": 'ultima_observacao_data_formatted', "name":"ultima_observacao_data_formatted", visible:false},
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
            });

            addAutoLink(function () {
                debounceSearch('#table');
            });

            $('#search-form select').on('change', function(e) {
                e.stopPropagation();
                e.preventDefault();

                oTable.draw();
            });

            $(".btn-update").click(function() {
                document.location.reload(true);
            });

            //Atualiza os outros iframes da tela
            if (parent) {
                parent.postMessage(JSON.stringify({ type:'refresh', data: { } }));
            }
        });
    </script>
@endpush
