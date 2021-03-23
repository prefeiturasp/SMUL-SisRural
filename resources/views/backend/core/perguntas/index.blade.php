@extends('backend.layouts.app')

@section('title', app_name() . ' | Perguntas')

@section('content')
<div class="card card-ater">
    <div class="card-header">
        <div class="row">
            <div class="col-sm-5">
                <h1 class="card-title mb-0 mt-1 h4">
                    Perguntas cadastradas no sistema
                </h1>
            </div>

            @can('create pergunta checklist')
            <div class="col-sm-7 pull-right">
                <div class="float-right">
                    <small class="text-muted my-auto mr-2">Perguntas com * possuem pontuação.</small>
                    <a aria-label="Adicionar nova Pergunta" href="{{ route('admin.core.perguntas.create') }}"
                        class="btn btn-primary px-5">Adicionar pergunta</a>
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
                    <th>Tipo</th>
                    <th>Pergunta</th>
                    <th>Respostas</th>
                    <th>Palavras chave</th>
                    <th>Situação</th>
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
                "ajax": '{{ route('admin.core.perguntas.datatable') }}',
                "language": {
                    "url": '{{ asset('js/datatables-pt-br.json')}}'
                },
                "columns": [
                    {"data": "id"},
                    {"data": "tipo_pergunta"},
                    {"data": "pergunta"},
                    {"data": "respostas"},
                    {"data": "tags"},
                    {"data": "fl_arquivada"},
                    {
                        "data": "actions",
                        "searchable": false,
                        "orderable": false,
                        render: function (data) {
                            return htmlDecode(data);
                        }
                    }
                ],
                "order":[["5", "asc"], ["0", "asc"]]
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
