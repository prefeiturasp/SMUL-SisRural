@extends('backend.layouts.app-template', ['iframe'=>true])

@section('body')
    <div class="card card-ater">
        <div class="card-header">
            <div class="row">
                <div class="col-5">
                    <h1 class="card-title mb-0 mt-1 h4">
                        Escolha o/a produtor/a e unidade produtiva
                    </h1>
                </div>
            </div>
        </div>

        <div class="card-body">
            <table id="table" class="table table-ater">
                <thead>
                    <tr>
                        <th width="60">#</th>
                        <th>Produtor/a</th>
                        <th><abbr title="Cadastro de Pessoa Física">CPF</abbr></th>
                        <th>Coproprietários/as</th>
                        <th>Unid. Produtiva</th>
                        <th>Cidade</th>
                        <th>Estado</th>
                        <th width="60">Ações</th>
                    </tr>
                </thead>
            </table>
        </div>

        <div class="card-footer">
            <div class="row">
                <div class="col">
                    &nbsp;
                </div>

                <div class="col text-right">
                    <a href="{{ $urlBack }}" class="btn btn-primary px-5">Concluído</a>
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
                "ajax": '{{ $datatableUrl }}',
                "language": {
                    "url": '{{ asset('js/datatables-pt-br.json')}}'
                },
                "columns": [
                    {"data": "uid"},
                    {"data": "nome"},
                    {"data": "cpf"},
                    {"data": "socios"},
                    {"data": "unidade_produtiva"},
                    {"data": "cidade.nome"},
                    {"data": "estado.nome"},
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
