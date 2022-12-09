@extends('backend.layouts.app')

@section('title', app_name() . ' | Formulários para Aplicação')

@section('content')
    <div class="card card-ater">
        <div class="card-header">
            <div class="row">
                <div class="col-sm-5">
                    <h1 class="card-title mb-0 mt-1 h4">
                        Formulários para aplicação
                    </h1>
                </div>

                @can('create checklist base')
                    <div class="col-sm-7 pull-right">
                        <div class="float-right">
                            <a href="{{ route('admin.core.checklist.create') }}" class="btn btn-primary px-5"
                               aria-label="Criar formulário" title="Novo">Criar formulário</a>
                        </div>
                    </div>
                @endcan
            </div>
        </div>

        <div class="card-body">
            <table id="table" class="table table-ater">
                <thead>
                <tr>
                    <th width="60">#</th>
                    <th>Proprietário</th>
                    <th>Nome</th>
                    <th>Perguntas</th>
                    <th>Domínio</th>
                    <th>Unid. Operacionais</th>
                    <th>Usuários/as</th>
                    <th>Status</th>
                    <th>Aprovação?</th>
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
                "ajax": '{{ route('admin.core.checklist.datatable') }}',
                "language": {
                    "url": '{{ asset('js/datatables-pt-br.json')}}'
                },
                "columns": [
                    {"data": "id"},
                    {"data": "dominio"},
                    {"data": "nome"},
                    {"data": "perguntas", "orderable":false},
                    {"data": "dominiosPermissao", "orderable":false},
                    {"data": "unidadesOperacionaisPermissao", "orderable":false},
                    {"data": "usuariosPermissao"},
                    {"data": "status"},
                    {"data": "fl_fluxo_aprovacao"},
                    {
                        "data": "actions",
                        "searchable": false,
                        "orderable": false,
                        render: function (data) {
                            return htmlDecode(data);
                        }
                    }
                ]
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
