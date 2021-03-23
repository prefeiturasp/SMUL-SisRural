@extends('backend.layouts.app')

@section('title', app_name() . ' | Produtor')

@section('content')
    <div class="card card-ater">
        <div class="card-header">
            <div class="row">
                <div class="col-10">
                    <h1 class="card-title mb-0 mt-1 h4">
                        Escolha formulário aplicado
                    </h1>
                </div>
            </div>
        </div>

        <div class="card-body">
            <table id="table" class="table table-ater">
                <thead>
                <tr>
                    <th width="60">#</th>
                    <th>Formulário</th>
                    <th>Produtor/a</th>
                    <th>Coproprietários</th>
                    <th>Unid. Produtiva</th>
                    <th>Técnico</th>
                    <th>Criado em</th>
                    <th>Status</th>
                    <th></th>
                    <th width="60">Ações</th>
                </tr>
                </thead>
            </table>
        </div>

        <div class="card-footer">
            <a href="{{ $urlBack }}" class="btn btn-danger px-5">Voltar</a>
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
                    {"data": "produtor.nome"},
                    {"data": "unidade_produtiva.socios"},
                    {"data": "unidade_produtiva.nome"},
                    {"data": "usuario.first_name"},
                    {"data": "created_at_formatted", "name": "created_at"},
                    {"data": "status"},
                    {"data": 'created_at_formatted', "name":"created_at_formatted", visible:false},
                    {
                        "data": "actions",
                        "searchable": false,
                        "orderable": false,
                        render: function (data) {
                            return htmlDecode(data);
                        }
                    }
                ],
                "order":[["7", 'asc'], ["6", 'desc']]
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
