@extends('backend.layouts.app')

@section('title', app_name() . ' | Cadernos de Campo')

@section('content')
    <div class="card card-ater">
        <div class="card-header">
            <div class="row">
                <div class="col-sm-5">
                    <h1 class="card-title mb-0 mt-1 h4">
                        @if (\Route::is('admin.core.cadernos.excluidos'))
                            Lista de Cadernos de Campo Excluídos
                        @else
                            Lista de Cadernos de Campo
                        @endif
                    </h1>
                </div>

                <div class="col-sm-7 pull-right">
                    <div class="float-right">
                        @if($showLinkExcluidos)
                            <a aria-label="Cadernos de Campo Excluídos" href="{{ route('admin.core.cadernos.excluidos') }}" class="btn btn-outline-primary px-5">Cadernos de Campo Excluídos</a>
                        @else
                            <a aria-label="Cadernos de Campo Aplicados" href="{{ route('admin.core.cadernos.index') }}" class="btn btn-outline-primary px-5">Cadernos de Campo Aplicados</a>
                        @endif

                        @can('create caderno')
                            <a aria-label="Adicionar novo Caderno de Campo" href="{{ route('admin.core.cadernos.produtor_unidade_produtiva') }}" class="btn btn-primary px-5 ml-4">Adicionar</a>
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
                    <th>Protocolo</th>
                    <th>Modelo</th>
                    <th>Produtor/a</th>
                    <th>Unidade Produtiva</th>
                    <th>Status</th>
                    <th>Técnico/a</th>
                    <th>Criado em</th>
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
                // "ajax": '{{ route('admin.core.cadernos.datatable') }}',
                "ajax": '{{ $datatableUrl }}',
                "language": {
                    "url": '{{ asset('js/datatables-pt-br.json')}}'
                },
                "columns": [
                    {"data": "uid"},
                    {"data": "protocolo"},
                    {"data": "template.nome"},
                    {"data": "produtor.nome"},
                    {"data": "datatable_unidade_produtiva.nome"},
                    {"data": "status"},
                    {"data": "tecnicas[].first_name"},
                    {"data": "created_at_formatted", "name": "created_at"},
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
                ],
                "order":[["7", "desc"]]
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
