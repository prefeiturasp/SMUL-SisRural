@extends('backend.layouts.app')

@section('title', app_name() . ' | Biblioteca de Formulários')

@section('content')
    <div class="card card-ater">
        <div class="card-header">
            <div class="row">
                <div class="col-sm-4">
                    <h1 class="card-title mb-0 mt-1 h4">
                        Biblioteca de formulários
                    </h1>
                </div>

                <div class="col-sm-8 my-auto text-right">
                    <small class="text-muted">Aqui você encontra todos os formulários cadastrados pelos usuários do sistema. Caso não tenha acesso para aplicação, você poderá duplicá-lo e criar um novo a partir deste template.</small>
                </div><!--col-->
            </div>
        </div>

        <div class="card-body">
            <table id="table" class="table table-ater">
                <thead>
                <tr>
                    <th width="60">#</th>
                    <th>Proprietário</th>
                    <th>Nome</th>
                    <th>Perguntas</th>
                    <th>Domínio</th>
                    <th>Unid. Operacionais</th>
                    <th>Usuários/as</th>
                    <th>Status</th>
                    <th>Aprovação?</th>
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
                "ajax": '{{ route('admin.core.checklist.datatableBiblioteca') }}',
                "language": {
                    "url": '{{ asset('js/datatables-pt-br.json')}}'
                },
                "columns": [
                    {"data": "id"},
                    {"data": "dominio"},
                    {"data": "nome"},
                    {"data": "perguntas", "orderable":false},
                    {"data": "dominiosPermissao", "orderable":false},
                    {"data": "unidadesOperacionaisPermissao", "orderable":false},
                    {"data": "usuariosPermissao"},
                    {"data": "status"},
                    {"data": "fl_fluxo_aprovacao"},
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
