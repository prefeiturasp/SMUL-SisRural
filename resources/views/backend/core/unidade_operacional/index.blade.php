@extends('backend.layouts.app')

@section('title', app_name() . ' | Unidades Operacionais')

@section('content')
    <div class="card card-ater">
        <div class="card-header">
            <div class="row">
                <div class="col-sm-5">
                    <h1 class="card-title mb-0 mt-1 h4">
                        Unidades Operacionais
                    </h1>
                </div>

                <div class="col-sm-7 pull-right">
                    <div class="float-right">
                        @can('create same domain operational units')
                        <a href="{{ route('admin.core.unidade_operacional.create') }}" class="btn btn-primary px-5"
                           title="Novo">Adicionar</a>
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
                    <th>Domínio</th>
                    <th>Nome</th>
                    <th>Endereço</th>
                    <th>Telefone</th>
                    <th>Abrangência (Estadual)</th>
                    <th>Abrangência (Municipal)</th>
                    <th>Abrangência (Regiões)</th>
                    <th>Abrangência (Unidades Produtivas)</th>
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
                "ajax": '{{ route('admin.core.unidade_operacional.datatable') }}',
                "language": {
                    "url": '{{ asset('js/datatables-pt-br.json')}}'
                },
                "columns": [
                    {"data": "id"},
                    {"data": "dominio.nome"},
                    {"data": "nome"},
                    {"data": "endereco"},
                    {"data": "telefone"},
                    {"data": "abrangenciaEstadual"},
                    {"data": "abrangenciaMunicipal"},
                    {"data": "abrangenciaRegiao"},
                    {"data": "unidadesProdutivasManuais"},
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
