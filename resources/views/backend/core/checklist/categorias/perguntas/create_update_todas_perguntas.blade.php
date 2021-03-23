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
                        <small class="text-muted my-auto mr-2">Perguntas cadastradas no sistema. Caso não encontre, cadastre uma nova. Perguntas com * possuem pontuação.</small>

                        {{ form_cancel($urlBack, 'Voltar', 'btn btn-outline-danger px-5') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <table id="table" class="table" style="width:100%">
                <thead>
                <tr>
                    <th width="60">#</th>
                    <th>Pergunta</th>
                    <th>Respostas</th>
                    <th>Tipo</th>
                    <th>Ação Recomendada</th>
                    <th>Palavras Chave</th>
                    <th width="60">Ações</th>
                </tr>
                </thead>
            </table>
        </div>

        <div class="card-footer button-group">
            <div class="row">
                <div class="col">
                    {{ form_cancel($urlBack, 'Voltar', 'btn btn-danger px-4') }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-scripts')
    <script>
        $(document).ready(function () {
            $('#table').DataTable({
                "dom": '<"top table-top"f>rt<"row table-bottom"<"col-sm-12 col-md-5"il><"col-sm-12 col-md-7"p>><"clear">',
                "processing": true,
                "serverSide": true,
                "lengthChange": false,
                "ajax": '{{ $urlDatatable }}',
                "language": {
                    "url": '{{ asset('js/datatables-pt-br.json')}}',
                    // "emptyTable": "My Custom Message On Empty Table",
                },
                "columns": [
                    {"data": "id"},
                    {"data": "pergunta"},
                    {"data": "respostas"},
                    {"data": "tipo_pergunta"},
                    {"data": "plano_acao_default"},
                    {"data": "tags"},
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
