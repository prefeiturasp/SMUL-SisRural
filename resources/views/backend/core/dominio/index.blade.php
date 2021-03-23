@extends('backend.layouts.app')

@section('title', app_name() . ' | Domínios')

@section('content')
    <div class="card card-ater">
        <div class="card-header">
            <div class="row">
                <div class="col-10">
                    <h1 class="card-title h4 mb-0 mt-1">
                       Lista de Domínios
                    </h1>
                </div>

                <div class="col-2 pull-right">
                    <div class="float-right">
                        @can('create domains')
                            <a href="{{ route('admin.core.dominio.create') }}" class="btn btn-primary px-5">Adicionar</a>
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
                    <th>Unidades Operacionais</th>
                    <th>Abrangência (Estadual)</th>
                    <th>Abrangência (Municipal)</th>
                    <th>Abrangência (Regiões)</th>
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
            var dataTable = $('#table').DataTable({
                "dom": '<"top table-top"f>rt<"row table-bottom"<"col-sm-12 col-md-5"il><"col-sm-12 col-md-7"p>><"clear">',
                "processing": true,
                "serverSide": true,
                "lengthChange": false,
                "ajax": '{{ route('admin.core.dominio.datatable') }}',
                "language": {
                    "url": '{{ asset('js/datatables-pt-br.json')}}'
                },
                "columns": [
                    {"data": "id"},
                    {"data": "nome"},
                    {"data": "unidadesOperacionais", "orderable": false},
                    {"data": "abrangenciaEstadual", "orderable": false},
                    {"data": "abrangenciaMunicipal", "orderable": false},
                    {"data": "abrangenciaRegiao", "orderable": false},
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
            });

            addAutoLink(function () {
                debounceSearch('#table');
            });
        });
    </script>
@endpush
