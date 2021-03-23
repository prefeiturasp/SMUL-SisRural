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
            </div>
        </div>

        <div class="card-header">
            <form method="POST" id="search-form" class="form-inline" role="form">
                <div class="form-group  mr-4">
                    <label class="mr-4 font-weight-bold">Filtrar por:</label>
                </div>

                <div class="form-group  mr-4">
                    <label for="name" class="font-weight-bold mr-2">Prioridade</label>
                    {!! Form::select('prioridade', ['Todas'] + App\Enums\PlanoAcaoPrioridadeEnum::toSelectArray(), null, ['class' => 'form-control pr-4']) !!}
                </div>
            </form>
        </div>

        <div class="card-body">
            <table id="table" class="table table-ater" style="width:100%">
                <thead>
                <tr>
                    <th width="60">#</th>
                    <th width="80">Prioridade</th>
                    <th>Pergunta</th>
                    <th>Resposta</th>
                    <th>Ação recomendada</th>
                    <th>Detalhamento da ação</th>
                    <th width="60">Ações</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

    <div id="modal-detalhar-acao" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl">
          <div class="modal-content">

            <div class="modal-header">
              <h4 class="modal-title">Detalhar ação</h4>

              <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                <span aria-hidden="true">×</span>
              </button>
            </div>

            <div class="modal-body">
                @include('backend.components.iframe.html', ["id"=>'iframe-detalhar-acao', "src"=>''])
            </div>
          </div>
        </div>
      </div>

@endsection

@push('after-scripts')
    @include('backend.components.iframe.scripts', ["id"=>'iframe-detalhar-acao'])

    <style>
        #search-form .select2-selection {
            background-color:#F5F5F5;
            border-radius:0px;
            border:0px;
            box-shadow:none;
        }
    </style>
    <script>
        //Tabela
        var oTable;
        $(document).ready(function () {
            oTable = $('#table').DataTable({
                "dom": '<"top table-top">rt<"row table-bottom"<"col-sm-12 col-md-5"il><"col-sm-12 col-md-7"p>><"clear">',
                // "dom": '<"top table-top"f>rt<"row table-bottom"<"col-sm-12 col-md-5"il><"col-sm-12 col-md-7"p>><"clear">',
                "pageLength": 100,
                "processing": true,
                "serverSide": true,
                "lengthChange": false,
                "ajax": {
                    "url" : '{{ $urlDatatable }}',
                    "data": function (d) {
                        d.filter_prioridade = $('#search-form select[name=prioridade]').val();
                    }
                },
                "language": {
                    "url": '{{ asset('js/datatables-pt-br.json')}}'
                },
                "columns": [
                    {"data": "uid", visible:false},
                    {"data": "prioridade"},
                    {"data": "pergunta"},
                    {"data": "resposta"},
                    {"data": "plano_acao_default"},
                    {"data": "descricao"},
                    {
                        "data": "actions",
                        "searchable": false,
                        "orderable": false,
                        render: function (data) {
                            return htmlDecode(data);
                        }
                    }
                ],
                "order": [[ 1, "asc" ]],
            }).on('draw', function () {
                initAutoLink($("#table"));
            });

            addAutoLink(function () {
                debounceSearch('#table');
            });

            $('#search-form select').on('change', function(e) {
                e.stopPropagation();
                e.preventDefault();

                oTable.draw();
            });

            /** Select Template */
            function formatStatePlanoAcaoItem (state) {
                if (!state.id || state.id == "0") {
                    return state.text;
                }

                var $state = $(
                    '<span class="select2-with-icon"><img src="' + base_url + 'img/backend/select/' + state.element.value.toLowerCase() + '.png" class="img-flag" /><span>' + state.text + '</span></span>'
                );

                return $state;
            };

            $("#search-form select").select2({
                templateResult: formatStatePlanoAcaoItem,
                templateSelection: formatStatePlanoAcaoItem
            });
            /** Fim Select Template */


            /** Modal Detalhar Acao */
            $(document).on('click', '.btn-detalhar-acao', function(evt) {
                var href = $(this).attr("href");

                evt.preventDefault();
                evt.stopPropagation();

                var modal = $('#modal-detalhar-acao');

                modal.find("iframe").attr("src", '');

                modal.modal({
                    backdrop:'static',
                })

                modal.on('hidden.coreui.modal', function (e) {
                    oTable.draw();
                });

                setTimeout(function() {
                    setScrollPositionToIframe();

                    modal.find("iframe").attr("src", href);
                }, 200);
            });
            /** Fim Detalhar Acao */

        });
    </script>
@endpush
