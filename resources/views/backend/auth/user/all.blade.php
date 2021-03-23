@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('labels.backend.access.users.management'))

@section('content')
<div class="card card-ater">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-5">
                <h1 class="card-title mb-0 h4">
                    {{ __('labels.backend.access.users.management') }} <small class="text-muted">Todos Usuários</small>
                </h1>
            </div><!--col-->

            <div class="col-sm-7">
                @include('backend.auth.user.includes.header-buttons', ['showAll'=>false, 'showAdd'=>false, 'showActivated'=>true, 'showDesactivated'=>false, 'showDeleted'=>false])
            </div><!--col-->
        </div><!--row-->

        <div class="row mt-4">
            <div class="col">
                <div class="table-responsive">
                    <table id="table" class="table table-ater">
                        <thead>
                        <tr>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th><abbr title="Cadastro de Pessoa Física">CPF</abbr></th>
                            <th>Telefone</th>
                            <th>Papéis</th>
                            <th>Domínio</th>
                            <th>Unidades Operacionais</th>
                            <th>Ativo?</th>
                            <th>Ações</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div><!--col-->
        </div><!--row-->
    </div><!--card-body-->
</div><!--card-->
@endsection


@push('after-scripts')
    <script>
        $(document).ready(function () {
            var table = $('#table').DataTable({
                "dom": '<"top table-top"f>rt<"row table-bottom"<"col-sm-12 col-md-5"il><"col-sm-12 col-md-7"p>><"clear">',
                "processing": true,
                "serverSide": true,
                "lengthChange": false,
                "ajax": '{{ route('admin.auth.user.datatableAll') }}',
                "language": {
                    "url": '{{ asset('js/datatables-pt-br.json')}}'
                },
                "columns": [
                    {"data": "first_name"},
                    {"data": "email"},
                    {"data": "document", "orderable":true},
                    {"data": "phone"},
                    {"data": "roles_label", "orderable":true},
                    {"data": "dominio", "orderable":true},
                    {"data": "unidades_operacionais", "orderable":true},
                    {"data": "active", "orderable":true},
                    {
                        "data": "actions",
                        "searchable": false,
                        orderable: false,
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
