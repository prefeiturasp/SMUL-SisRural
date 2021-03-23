@extends('backend.layouts.app')

@section('title', app_name())

@section('content')
    <div class="card card-ater">
        <div class="card-header">
            <div class="row">
                <div class="col-sm-5">
                    <h1 class="card-title mb-0 mt-1 h4">
                        Análise de Formulários
                    </h1>
                </div>
            </div>
        </div>

        <div class="card-body">
            <table id="table" class="table table-ater">
                <thead>
                <tr>
                    <th width="60">#</th>
                    <th>Formulário/Plano de ação</th>
                    <th>Unid. Produtiva</th>
                    <th>Técnico</th>
                    <th>Criado em</th>
                    <th>Status</th>
                    <th>Status Fluxo Aprov.</th>
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
                "processing": true,
                "serverSide": true,
                "lengthChange": false,
                "ajax": '{{ $datatableUrl }}',
                "language": {
                    "url": '{{ asset('js/datatables-pt-br.json')}}'
                },
                "columns": [
                    {"data": "uid"},
                    {"data": "checklist.nome"},
                    {"data": "unidade_produtiva.nome"},
                    {"data": "usuario.first_name"},
                    {"data": "created_at_formatted", "name": "created_at"},
                    {"data": "status"},
                    {"data": "status_flow"},
                    {"data": 'created_at_formatted', "name":"created_at_formatted", visible:false},
                    {"data": "usuario.last_name", visible:false},
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
