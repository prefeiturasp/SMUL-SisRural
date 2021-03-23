@extends('backend.layouts.app')

@section('content')
    {{-- @if (@$checklist)
        @cannot('editForm', $checklist)
            <div class="alert alert-warning">
                Algumas informações podem ficar bloqueadas para edição.
            </div>
        @endcannot
    @endif --}}

    <div class="card-ater">
        <div class="card-body-ater">
            {{-- {!! form($form) !!} --}}

            {!!form_start($form)!!}

            {!!form_until($form, 'card-end-pontuacao')!!}

            @if (@$checklist)
                <div id="categorias">
                    @include('backend.components.iframe.html', ["id"=>$checklistCategoriasId, "src"=>$checklistCategoriasSrc])
                </div>
            @endif

            @if (!@$checklist)
                @include('backend.components.card-iframe-add.html', ["title"=>"Definição de categorias e perguntas", "data"=>"categorias", "label"=>"Adicionar categoria"])
            @endif

            @if (@$checklist)
                <span class="categorias-need-save d-none">
                    @include('backend.components.card-iframe-add.html', ["title"=>"Para atualizar as categorias é preciso salvar as alterações até o momento.", "data"=>"categorias", "label"=>"Salvar"])
                </span>
            @endif

            {!!form_rest($form)!!}
        </div>

        <div class="card-footer-ater">
            <div class="row">
                <div class="col">
                    {{ form_cancel(route('admin.core.checklist.index'), __('buttons.general.cancel'), 'btn btn-danger px-4') }}
                </div>

                <div class="col text-right col-submit">
                    @if(@$checklist)
                        @include('backend.components.button-confirm-submit.index', ['id'=>'checklist', 'form' => $form->getFormOption('id')])
                    @else
                        <button type="submit" class="btn btn-primary px-5" form="form-builder">Salvar</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-scripts')
    @if (@$checklist)
        @include('backend.components.iframe.scripts', ["id"=>$checklistCategoriasId, "src"=>$checklistCategoriasSrc])
    @endif

    <script>
        var plano_acao_start;
        var tipo_pontuacao_start;

        function reloadIframeCategory() {
            var plano_acao = $("select[name='plano_acao']").val();
            var tipo_pontuacao = $("select[name='tipo_pontuacao']").val();

            if (plano_acao_start != plano_acao || tipo_pontuacao_start != tipo_pontuacao) {
                $("#categorias").addClass("d-none");
                $(".categorias-need-save").removeClass("d-none");
            } else {
                $("#categorias").removeClass("d-none");
                $(".categorias-need-save").addClass("d-none");
            }

            // var iframe = $("#iframeChecklistCategorias");
            // iframe.attr("src", iframe.attr("src"));
        }

        selectAutoYesNo("#fl_fluxo_aprovacao", '#card-fluxo-aprovacao');

        $(document).ready(function() {
            plano_acao_start = $("select[name='plano_acao']").val();
            tipo_pontuacao_start = $("select[name='tipo_pontuacao']").val();

            $("select[name='plano_acao']").change(function() {
                var element = $("#instrucoes_pda").parent().parent();
                if ($(this).val() == 'obrigatorio' || $(this).val() == 'opcional') {
                    element.removeClass("d-none");
                }  else {
                    element.addClass("d-none");
                }

                reloadIframeCategory();
            }).change();

            $("select[name='tipo_pontuacao']").change(function() {
                var element = $("#card-formula");
                if ($(this).val() == 'com_pontuacao_formula_personalizada') {
                    element.removeClass("d-none");
                }  else {
                    element.addClass("d-none");
                }

                var elementNormalizar = $("#card-calculo");
                if ($(this).val() == 'com_pontuacao') {
                    elementNormalizar.removeClass("d-none");
                }  else {
                    elementNormalizar.addClass("d-none");
                }

                reloadIframeCategory();
            }).change();


            // $("select[name='usuarios[]']").change(function() {
            //     var dominiosParent = $("select[name='dominios[]']").parent().parent();
            //     var unidadesParent = $("select[name='unidadesOperacionais[]']").parent().parent();

            //     if ($(this).val().length > 0) {
            //         dominiosParent.addClass("d-none");
            //         unidadesParent.addClass("d-none");
            //     }  else {
            //         dominiosParent.removeClass("d-none");
            //         unidadesParent.removeClass("d-none");
            //     }
            // }).change();

            $("#status").change(function() {
                var status = $(this).val();
                var text = '';

                if (status == 'rascunho') {
                    text = 'O formulário será <b>RASCUNHO</b> e não estará disponível para aplicação. Você confirma?';
                } else if (status == 'inativo') {
                    text = 'O formulário será <b>INATIVO</b> e não estará disponível para aplicação. Você confirma?';
                } else if (status == 'publicado') {
                    text = 'O formulário será <b>PUBLICADO</b> e estará disponível para aplicação. Você confirma?';
                } else {
                    text = 'Deseja salvar os dados?'
                }

                $("#confirm-submit-checklist .modal-body").html(text);
            }).change();

        });
        $("select[multiple='multiple']").select2();
    </script>
@endpush
