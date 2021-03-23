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


                <div class="col-sm-7 pull-right">
                    <div class="float-right">
                        <small class="text-muted my-auto mr-2">Perguntas com * possuem pontuação.</small>

                        @can('addCategorias', $checklist)
                            <a href="{{ route('admin.core.checklist.categorias.create',$checklist->id) }}"
                                class="btn btn-primary px-5"
                                title="Novo">Adicionar Categoria</a>
                        @endcan

                        @cannot('addCategorias', $checklist)
                            <small class="text-muted">Não é possível adicionar novas categorias, este formulário já foi aplicado em alguma unidade produtiva.</small>
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
                    <th>Categoria</th>
                    <th>Tipo de Pergunta</th>
                    <th>Perguntas</th>
                    <th>Ordem</th>
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
                "pageLength":100,
                "dom": '<"top table-top"f>rt<"row table-bottom"<"col-sm-12 col-md-5"il><"col-sm-12 col-md-7"p>><"clear">',
                "processing": true,
                "serverSide": true,
                "lengthChange": false,
                "ajax": '{{ route('admin.core.checklist.categorias.datatable',$checklist->id) }}',
                "language": {
                    "url": '{{ asset('js/datatables-pt-br.json')}}'
                },
                "columns": [
                    {"data": "id"},
                    {"data": "nome"},
                    {"data": "tipo_perguntas"},
                    {"data": "perguntas"},
                    {"data": "ordem"},
                    {
                        "data": "actions",
                        "searchable": false,
                        "orderable": false,
                        render: function (data) {
                            return htmlDecode(data);
                        }
                    }
                ],
                "order":[["4", 'asc']]
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
