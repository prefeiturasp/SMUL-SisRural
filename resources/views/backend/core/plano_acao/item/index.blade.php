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
                            title="Novo">Adicionar nova ação</a>
                        </div>
                    </div>
                @endcan
            </div>
        </div>

        <div class="card-header">
            <form method="POST" id="search-form" class="form-inline" role="form">
                <div class="form-group  mr-4">
                    <label class="mr-4 font-weight-bold">Filtrar por:</label>
                </div>

                <div class="form-group  mr-4">
                    <label for="name" class="font-weight-bold mr-2">Prioridade</label>
                    {!! Form::select('prioridade', ['Todas'] + App\Enums\PlanoAcaoPrioridadeEnum::toSelectArray(), null, ['class' => 'form-control pr-4']) !!}
                </div>

                <div class="form-group">
                    <label for="email" class="font-weight-bold mr-2">Status</label>
                    {!! Form::select('status', ['Todos'] + App\Enums\PlanoAcaoItemStatusEnum::toSelectArray(), null, ['class' => 'form-control pr-4']) !!}
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
                    <th>Prazo</th>
                    <th>Último acompanhamento</th>
                    <th>Data último acomp.</th>
                    <th>Status</th>
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
        #search-form .select2-selection {
            background-color:#F5F5F5;
            border-radius:0px;
            border:0px;
            box-shadow:none;
        }
    </style>

    <script>
        //Tabela
        var oTable;
        $(document).ready(function () {
            oTable = $('#table').DataTable({
                "dom": '<"top table-top">rt<"row table-bottom"<"col-sm-12 col-md-5"il><"col-sm-12 col-md-7"p>><"clear">',
                // "dom": '<"top table-top"f>rt<"row table-bottom"<"col-sm-12 col-md-5"il><"col-sm-12 col-md-7"p>><"clear">',
                "pageLength": 100,
                "processing": true,
                "serverSide": true,
                "lengthChange": false,
                "ajax": {
                    "url" : '{{ $urlDatatable }}',
                    "data": function (d) {
                        d.filter_prioridade = $('#search-form select[name=prioridade]').val();
                        d.filter_status = $('#search-form select[name=status]').val();
                    }
                },
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
                    // {"data": 'action_custom', "name": "action_custom", "searchable":false, "orderable":false },
                    {"data": 'prazo_formatted', "name":"prazo_formatted", visible:false},
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


            /** Select Template */
            function formatStatePlanoAcaoItem (state) {
                if (!state.id || state.id == "0") {
                    return state.text;
                }

                var $state = $(
                    '<span class="select2-with-icon"><img src="' + base_url + 'img/backend/select/' + state.element.value.toLowerCase() + '.png" class="img-flag" /><span>' + state.text + '</span></span>'
                );

                return $state;
            };

            $("#search-form select").select2({
                templateResult: formatStatePlanoAcaoItem,
                templateSelection: formatStatePlanoAcaoItem
            });
            /** Fim Select Template */
        });
    </script>
@endpush
