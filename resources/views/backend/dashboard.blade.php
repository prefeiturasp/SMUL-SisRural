@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . __('strings.backend.dashboard.title'))

@section('content')
    @cannot('report restricted')
        <div class="container-fluid">
            <h1 class="mb-3">Página inicial</h1>

            <div class="row">
                <div class="col-sm-6 col-md-4 col-lg-4">
                    @cardaddview(['title'=>__('concepts.caderno_de_campo.label'), 'total'=>$totalCaderno, 'icon'=>'c-icon c-icon-lg cil-clipboard', 'labelAdd'=>__('concepts.caderno_de_campo.add'), 'linkAdd'=>route('admin.core.cadernos.produtor_unidade_produtiva'), 'labelView'=>'Visualizar', 'linkView'=>route('admin.core.cadernos.index'), 'permissionView'=>'view menu caderno', 'permissionAdd'=>'create caderno'])
                    @endcardaddview
                </div>

                <div class="col-sm-6 col-md-4 col-lg-4">
                    @cardaddview(['title'=>'Produtores/as', 'total'=>$totalProdutor, 'icon'=>'c-icon-lg cil-address-book', 'labelAdd'=>'Novo/a Produtor/a', 'linkAdd'=>route('admin.core.novo_produtor_unidade_produtiva.create'), 'labelView'=>'Visualizar', 'linkView'=>route('admin.core.produtor.index'), 'permissionView'=>'view menu farmers', 'permissionAdd'=>'create same operational units farmers'])
                    @endcardaddview
                </div>

                <div class="col-sm-6 col-md-4 col-lg-4">
                    @cardaddview(['title'=>'Unidades Produtivas', 'total'=>$totalUnidProdutiva, 'icon'=>'c-icon c-icon-lg cil-location-pin', 'labelAdd'=>'Nova Unidade Produtiva', 'linkAdd'=>route('admin.core.unidade_produtiva.produtor'), 'labelView'=>'Visualizar', 'linkView'=>route('admin.core.unidade_produtiva.index'), 'permissionView'=>'view menu productive units', 'permissionAdd'=>'create same operational units productive units'])
                    @endcardaddview
                </div>

                <div class="col-sm-6 col-md-4 col-lg-4">
                    @cardaddview(['title'=>'Formulários Aplicados', 'total'=>$totalFormulariosAplicados, 'icon'=>'c-icon c-icon-lg cil-clipboard', 'labelAdd'=>'Aplicar Formulário', 'linkAdd'=>route('admin.core.checklist_unidade_produtiva.template'), 'labelView'=>'Visualizar', 'linkView'=>route('admin.core.checklist_unidade_produtiva.index'), 'permissionView'=>'view menu checklist_unidade_produtiva', 'permissionAdd'=>'create checklist_unidade_produtiva'])
                    @endcardaddview
                </div>

                <div class="col-sm-6 col-md-4 col-lg-4">
                    @cardaddview(['title'=>'Plano de Ação', 'total'=>$totalPlanoAcao, 'icon'=>'c-icon c-icon-lg cil-clipboard', 'labelAdd'=>'Criar Plano de Ação - Individual', 'linkAdd'=>route('admin.core.plano_acao.produtor_unidade_produtiva'), 'labelAdd2'=>'Criar Plano de Ação - Formulário', 'linkAdd2'=>route('admin.core.plano_acao.checklist_unidade_produtiva'),  'labelView'=>'Visualizar', 'linkView'=>route('admin.core.plano_acao.index'), 'permissionView'=>'view menu plano_acao', 'permissionAdd'=>'create plano_acao'])
                    @endcardaddview
                </div>

                <div class="col-sm-6 col-md-4 col-lg-4">
                    @cardaddview(['title'=>'Plano de Ação Coletivo', 'total'=>$totalPlanoAcaoColetivo, 'icon'=>'c-icon c-icon-lg cil-clipboard', 'labelAdd'=>'Criar Plano de Ação', 'linkAdd'=>route('admin.core.plano_acao_coletivo.create'), 'labelView'=>'Visualizar', 'linkView'=>route('admin.core.plano_acao_coletivo.index'), 'permissionView'=>'view menu plano_acao', 'permissionAdd'=>'create plano_acao'])
                    @endcardaddview
                </div>
            </div>

            @include('backend.core.dashboard.buttons_mapa_report_bi')

            @can('view menu farmers')
                @cardater(['title'=> 'Produtor/a'])
                    @slot('body')
                        <table id="table" class="table table-ater">
                            <thead>
                            <tr>
                                <th width="60">#</th>
                                <th>Nome</th>
                                <th><abbr title="Cadastro de Pessoa Física / Cadastro Nacional de Pessoa Jurídica">CPF/CNPJ</abbr></th>
                                <th>Telefone</th>
                                <th>Coproprietários/as</th>
                                <th>Município</th>
                                <th>Estado</th>
                                <th>Palavras chave</th>
                                <th width="60">Ações</th>
                            </tr>
                            </thead>
                        </table>
                    @endslot
                @endcardater
            @endcan
        </div>
    @endcannot

    @can('report restricted')
        @include('backend.core.dashboard.buttons_mapa_report_bi')
    @endcan
@endsection

@push('after-scripts')
    @can('view menu farmers')
        <script>
            $(document).ready(function () {
                $('#table').DataTable({
                    "dom": '<"top table-top"f>rt<"row table-bottom"<"col-sm-12 col-md-5"il><"col-sm-12 col-md-7"p>><"clear">',
                    "processing": true,
                    "serverSide": true,
                    "lengthChange": false,
                    "ajax": '{{ route('admin.core.produtor.datatable', ['dashboard'=>true]) }}',
                    "language": {
                        "url": '{{ asset('js/datatables-pt-br.json')}}'
                    },
                    "columns": [
                        {"data": "uid"},
                        {"data": "nome"},
                        {"data": "cpf"},
                        {"data": "telefone_1"},
                        {"data": "unidades_produtivas[].socios", "name": "socios", orderable:false},
                        {"data": "cidade.nome"},
                        {"data": "estado.nome"},
                        {"data": "tags", orderable:false},
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
    @endcan
@endpush
