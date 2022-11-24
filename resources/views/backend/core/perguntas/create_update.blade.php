@extends('backend.layouts.app')

@section('content')
    @if (@$pergunta)
        @cannot('editForm', $pergunta)
            <div class="alert alert-warning">
                Algumas informações podem ficar bloqueadas para edição. Esta pergunta já foi utilizada em uma aplicação de formulário.
            </div>
        @endcannot
    @endif

    <div class="card-ater">
        <div class="card-body-ater">
            {!! form($form) !!}

            @can('view menu resposta checklist')
                @if (@$pergunta)
                    <div class="mt-5">
                        <div id="cadastrar-respostas" class="card-respostas">
                            @include('backend.components.iframe.html', ["id"=>$respostasId, "src"=>$respostasSrc])
                        </div>
                    </div>
                @endif

                @if (!@$pergunta)
                    @include('backend.components.card-iframe-add.html', ["title"=>"Respostas", "data"=>"cadastrar-respostas", "label"=>"Adicionar Respostas", "class"=>"card-respostas"])
                @endif
            @endcan
        </div>

        <div class="card-footer-ater">
            <div class="row">
                <div class="col">
                    {{ form_cancel(route('admin.core.perguntas.index'), __('buttons.general.cancel'), 'btn btn-danger px-4') }}
                </div>

                <div class="col text-right">
                    <button type="submit" class="btn btn-primary px-5" form="form-builder">Salvar</button>
                </div>
            </div>
        </div>

        <div class="modal" id="modal-tabela" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document" >
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Visualização da Tabela</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>

                  <div class="modal-body">

                  </div>
                </div>
              </div>
        </div>
    </div>
@endsection

@push('after-scripts')
    @can('view menu resposta checklist')
        @if (@$pergunta)
            @include('backend.components.iframe.scripts', ["id"=>$respostasId, "src"=>$respostasSrc])
        @endif
    @endcan

    <script>
        $(document).ready(function() {
            selectAutoYesNo("#fl_plano_acao", '#fieldset-plano-acao');

            $("#tabela_preview").click(function() {
                if ($("#tabela_colunas").val().length == 0) {
                    alert("Cadastre as colunas para conseguir visualizar a tabela.");
                    return;
                }

                var inputModal = $("<input/>").data("colunas", $("#tabela_colunas").val()).data("linhas", $("#tabela_linhas").val());
                $("#modal-tabela .modal-body").html(inputModal);
                inputTabela(inputModal[0]);

                $('#modal-tabela').modal()
            });

            $("#tipo_pergunta").change(function() {
                // if ($(this).val() == 'numerica-pontuacao') {
                //     $("#card-peso").removeClass("d-none");
                // } else {
                //     $("#card-peso").addClass("d-none");
                // }

                if ($(this).val() == 'tabela') {
                    $("#card-tabela").removeClass("d-none");
                } else {
                    $("#card-tabela").addClass("d-none");
                }

                if ($(this).val() == 'semaforica' || $(this).val() == 'semaforica-cinza' || $(this).val() == 'binaria'|| $(this).val() == 'binaria-cinza'|| $(this).val() == 'multipla-escolha' || $(this).val() == 'escolha-simples'  || $(this).val() == 'escolha-simples-pontuacao' || $(this).val() == 'escolha-simples-pontuacao-cinza') {
                    $(".card-respostas").removeClass("d-none");
                } else {
                    $(".card-respostas").addClass("d-none");
                }
            }).change();
        })
    </script>
@endpush
