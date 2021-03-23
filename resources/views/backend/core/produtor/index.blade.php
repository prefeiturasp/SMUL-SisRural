@extends('backend.layouts.app')

@section('title', app_name() . ' | Produtor/a')

@section('content')
    <div class="card card-ater">
        <div class="card-header">
            <div class="row">
                <div class="col-6">
                    <h1 class="card-title mb-0 mt-1 h4">
                        Lista de Produtores
                    </h1>
                </div>

                @can('create same operational units farmers')
                    <div class="col-6 pull-right">
                        <div class="float-right">
                            <a aria-label="Produtor/a sem unidade" href="{{ route('admin.core.produtor.index_sem_unidade') }}"
                                class="btn btn-outline-primary px-5 mr-4">
                                Produtor/a sem unidade
                            </a>

                            <a aria-label="Adicionar novo/a produtor/a"
                                href="{{ route('admin.core.novo_produtor_unidade_produtiva.create') }}"
                                class="btn btn-primary px-5">
                                Adicionar
                            </a>
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
                        <th><abbr title="Cadastro de Pessoa Física / Cadastro Nacional de Pessoa Jurídica">CPF/CNPJ</abbr>
                        </th>
                        <th>Telefone</th>
                        <th>Coproprietários</th>
                        <th>Município</th>
                        <th>Estado</th>
                        <th>Palavras chave</th>
                        <th width="60">Ações</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('after-scripts')
    <script>
        $(document).ready(function() {
            $('#table').DataTable({
                "dom": '<"top table-top"f>rt<"row table-bottom"<"col-sm-12 col-md-5"il><"col-sm-12 col-md-7"p>><"clear">',
                "processing": true,
                "serverSide": true,
                "lengthChange": false,
                "ajax": '{{ route('admin.core.produtor.datatable') }}',
                "language": {
                    "url": '{{ asset('js/datatables-pt-br.json') }}'
                },
                "columns": [{
                        "data": "uid"
                    },
                    {
                        "data": "nome"
                    },
                    {
                        "data": "cpf"
                    },
                    {
                        "data": "telefone_1"
                    },
                    {
                        "data": "unidades_produtivas[].socios",
                        "name": "socios",
                        orderable: false
                    },
                    {
                        "data": "cidade.nome"
                    },
                    {
                        "data": "estado.nome"
                    },
                    {
                        "data": "tags",
                        orderable: false
                    },
                    {
                        "data": "actions",
                        "searchable": false,
                        "orderable": false,
                        render: function(data) {
                            return htmlDecode(data);
                        }
                    }
                ]
            }).on('draw', function() {
                initAutoLink($("#table"));
            });

            addAutoLink(function() {
                debounceSearch('#table');
            });
        });

    </script>
@endpush
