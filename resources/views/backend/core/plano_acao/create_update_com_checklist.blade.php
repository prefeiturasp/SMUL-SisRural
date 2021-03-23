@extends('backend.layouts.app')

@section('content')
    <div class="card-ater">
        <div class="card-body-ater">
            {!!form_start($form)!!}

            @can('view menu plano_acao')
                @if (@$planoAcao)
                    {!!form_until($form, 'card-inf-pda-end')!!}

                    <div class="mt-5">
                        <div id="cadastrar-item" class="card-item">
                            @include('backend.components.iframe.html', ["id"=>$itemId, "src"=>$itemSrc])
                        </div>
                    </div>
                @endif
            @endcan

            {!!form_rest($form)!!}
        </div>

        <div class="card-footer-ater">
            <div class="row">
                <div class="col">
                    {{ form_cancel(route('admin.core.plano_acao.index'), __('buttons.general.cancel'), 'btn btn-danger px-4') }}
                </div>

                <div class="col text-right">
                    @if (@$planoAcao && $planoAcao->checklist_unidade_produtiva->checklist->plano_acao == App\Enums\PlanoAcaoEnum::Obrigatorio && $planoAcao->checklist_unidade_produtiva->checklist->fl_fluxo_aprovacao)
                        <div class="btn-submit-pda-com-alerta">
                            @include('backend.components.button-confirm-submit.index', ['id'=>'plano_acao_com_checklist', 'form' => $form->getFormOption('id'), 'label'=>'Salvar', 'message'=>'Este Plano de Ação será enviado para análise. Deseja enviar?'])
                        </div>
                    @endif

                    <div class="btn-submit-pda">
                        <button type="submit" class="btn btn-primary px-5" form="form-builder">Salvar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-scripts')
    @can('view menu plano_acao')
        @if (@$planoAcao)
            @include('backend.components.iframe.scripts', ["id"=>$itemId, "src"=>$itemSrc])
        @endif
    @endcan

    @if (@$planoAcao && $planoAcao->checklist_unidade_produtiva->checklist->plano_acao == App\Enums\PlanoAcaoEnum::Obrigatorio && $planoAcao->checklist_unidade_produtiva->checklist->fl_fluxo_aprovacao)
        <script>
            $(document).ready(function() {
                $("#status").change(function() {
                    if ($(this).val() == 'nao_iniciado') {
                        $(".btn-submit-pda").addClass("d-none");
                        $(".btn-submit-pda-com-alerta").removeClass("d-none");
                    } else {
                        $(".btn-submit-pda").removeClass("d-none");
                        $(".btn-submit-pda-com-alerta").addClass("d-none");
                    }
                }).change();
            });
        </script>
    @endif
@endpush
