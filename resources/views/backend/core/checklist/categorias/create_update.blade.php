@extends('backend.layouts.app-template', ['iframe'=>true])

@section('body')
    <div class="card">
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

                        @can('addPerguntas', $checklist)
                            @if (@$urlAdd)
                                <a href="{{ $urlAdd }}" class="btn btn-primary px-5" title="Novo">Vincular Pergunta</a>
                            @endif
                        @endcan

                        @cannot('addPerguntas', $checklist)
                            <small class="text-muted">Não é possível vincular novas perguntas. O formulário já foi aplicado em alguma unidade produtiva.</small>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            {!! form($form) !!}

            @if (@$urlDatatable)
                <div>
                    <table id="table-perguntas" class="table" style="width:100%;">
                        <thead>
                            <tr>
                                <th width="90">#</th>
                                <th>Tipo</th>
                                <th>Pergunta</th>
                                <th>Respostas / Pesos</th>
                                <th>Prioridade</th>
                                <th>Plano de Ação</th>
                                <th>Ordem</th>
                                <th width="60">Ações</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            @endif
        </div>

        <div class="card-footer button-group">
            <div class="row">
                <div class="col">
                    {{ form_cancel($back, __('buttons.general.cancel'), 'btn btn-outline-danger px-5') }}
                </div>

                <div class="col text-right">
                    <button type="submit" class="btn btn-primary px-5" form="form-builder">Salvar categoria</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-scripts')
    @if (@$urlDatatable)
        <script>
            $(document).ready(function () {
                var tablePerguntas = $('#table-perguntas').DataTable({
                    "dom": '<"top table-top"f>rt<"row table-bottom"<"col-sm-12 col-md-5"il><"col-sm-12 col-md-7"p>><"clear">',
                    "bFilter": false,
                    "pageLength": 20,
                    "processing": true,
                    "serverSide": true,
                    "lengthChange": false,
                    "ajax": '{{ $urlDatatable }}',
                    "language": {
                        "url": '{{ asset('js/datatables-pt-br.json')}}'
                    },
                    "columns": [
                        {"data": "id"},
                        {"data": "tipoPergunta"},
                        {"data": "pergunta"},
                        {"data": "respostas"},
                        {"data": "plano_acao_prioridade"},
                        {"data": "fl_plano_acao"},
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
                    "order": [[ 6, "asc" ]]
                });

                tablePerguntas.on('draw', function () {
                    initAutoLink($("#table-perguntas"));
                });

                addAutoLink(function () {
                    debounceSearch('#table-perguntas');
                });
            });

        </script>
    @endif
@endpush
