@extends('backend.layouts.app')

@section('title', app_name())

@section('content')
    <div class="card card-ater">
        <div class="card-header">
            <div class="row">
                <div class="col-sm-3">
                    <h1 class="card-title mb-0 mt-1 h4">
                        {{$title}}
                    </h1>
                </div>

                <div class="col-sm-9 pull-right text-right">
                    <div class="float-right">
                        @if($showLinkExcluidos)
                            <a aria-label="Plano de Ação Excluídos" href="{{ route('admin.core.plano_acao.excluidos', ['produtor'=>$produtor]) }}" class="btn btn-outline-primary my-1 px-5 mx-2">Planos de ação excluídos</a>
                        @else
                            <a aria-label="Plano de Ação" href="{{ route('admin.core.plano_acao.index', ['produtor'=>$produtor]) }}" class="btn btn-outline-primary my-1 px-5 mx-2">Planos de ação ativos</a>
                        @endif

                        @can('create plano_acao')
                            <a href="{{ route('admin.core.plano_acao.checklist_unidade_produtiva', ['produtor'=>$produtor]) }}" class="btn btn-primary px-5 my-1 mx-2">Criar a partir de formulário aplicado</a>

                            <a href="{{ route('admin.core.plano_acao.produtor_unidade_produtiva', ['produtor'=>$produtor]) }}" class="btn btn-primary my-1 px-5 mx-2">Criar plano de ação</a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <table id="table" class="table table-ater">
                <thead>
                <tr>
                    <th width="60">#</th>
                    <th>Nome</th>
                    <th>Produtor/a</th>
                    <th>Unidade Produtiva</th>
                    <th>Vinculado a Formulário</th>
                    <th>Formulário</th>
                    <th>Atualizado em</th>
                    <th>Valido até</th>
                    <th>Status</th>
                    <th></th>
                    <th></th>
                    <th width="60">Ações</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('after-scripts')
    <script>
        $(document).ready(function () {
            var table = $('#table').DataTable({
                "dom": '<"top table-top"f>rt<"row table-bottom"<"col-sm-12 col-md-5"il><"col-sm-12 col-md-7"p>><"clear">',
                "pageLength": 30,
                "processing": true,
                "serverSide": true,
                "lengthChange": false,
                "ajax": '{{ $datatableUrl }}',
                "language": {
                    "url": '{{ asset('js/datatables-pt-br.json')}}'
                },
                "columns": [
                    {"data": "uid"},
                    {"data": "nome"},
                    {"data": "produtor.nome"},
                    {"data": "unidade_produtiva.nome"},
                    {"data": "checklist_unidade_produtiva_id"},
                    {"data": "checklist"},
                    {"data": "updated_at_formatted", "name": "updated_at"},
                    {"data": "prazo_formatted", "name": "prazo"},
                    {"data": 'status'},
                    {"data": 'updated_at_formatted', "name":"updated_at_formatted", visible:false},
                    {"data": 'prazo_formatted', "name":"prazo_formatted", visible:false},
                    {
                        "data": "actions",
                        "searchable": false,
                        "orderable": false,
                        render: function (data) {
                            return htmlDecode(data);
                        }
                    }
                ],
                "order":[['6', 'desc']]
            });

            table.on("draw", function () {
                initAutoLink($("#table"));
            });

            addAutoLink(function () {
                debounceSearch('#table');
            });
        });
    </script>
@endpush
