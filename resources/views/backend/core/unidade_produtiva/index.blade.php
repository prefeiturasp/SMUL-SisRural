@extends('backend.layouts.app')

@section('title', app_name() . ' | Listagem de Unidades Produtivas')


@section('content')
    <div class="card card-ater">
        <div class="card-header">
            <div class="row">
                <div class="col-sm-5">
                    <h1 class="card-title mb-0 mt-1 h4">
                        Lista de Unidades Produtivas
                    </h1>
                </div>

                @can('create same operational units productive units')
                    <div class="col-sm-7 pull-right">
                        <div class="float-right">
                            <a href="{{ route('admin.core.unidade_produtiva.produtor') }}" class="btn btn-primary px-5"  aria-title="Adicionar nova Unidade Produtiva"
                            title="Novo">Adicionar</a>
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
                    <th>Nome</th>
                    <!--<th>Cidade</th>
                    <th>Estado</th>-->
                    <th>Distrito</th>
                    <th>Bairro</th>
                    <th>Produtores/as</th>
                    <!--<th>Coproprietários/as</th>-->
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
                    {"data": "nome"},
                    // {"data": "cidade.nome"},
                    // {"data": "estado.nome"},
                    {"data": "subprefeitura"},
                    {"data": "bairro"},
                    {"data": "produtores"},
                    // {"data": "socios"},
                    // {"data": "tags"},
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
