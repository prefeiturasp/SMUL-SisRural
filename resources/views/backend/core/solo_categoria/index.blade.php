@extends('backend.layouts.app')

@section('title', app_name() . ' | Uso do Solo - Categorias')

@section('content')
    <div class="card card-ater">
        <div class="card-header">
            <div class="row">
                <div class="col-sm-5">
                    <h1 class="card-title mb-0 mt-1 h4">
                        Uso do Solo - Categorias
                    </h1>
                </div>

                <div class="col-sm-7 pull-right">
                    <div class="float-right">
                        <a href="{{ route('admin.core.solo_categoria.create') }}" class="btn btn-primary px-5" title="Novo">Adicionar</a>
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
                    <th>Tipo</th>
                    <th>Tipo - Formulário</th>
                    <th>Agrobiodiversidade - Baixo</th>
                    <th>Agrobiodiversidade - Alto</th>
                    <th width="60">Ações</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            var table = $('#table').DataTable({
                "dom": '<"top table-top"f>rt<"row table-bottom"<"col-sm-12 col-md-5"il><"col-sm-12 col-md-7"p>><"clear">',
                "processing": true,
                "serverSide": true,
                "lengthChange": false,
                "ajax": '{{ route('admin.core.solo_categoria.datatable') }}',
                "language": {
                    "url": '{{ asset('js/datatables-pt-br.json')}}'
                },
                "columns": [
                    {"data": "id"},
                    {"data": "nome"},
                    {"data": "tipo"},
                    {"data": "tipo_form"},
                    {"data": "min"},
                    {"data": "max"},
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
@endsection

