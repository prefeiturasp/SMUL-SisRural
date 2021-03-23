@extends('backend.layouts.app')

@section('content')
    <div class="card-ater">
        <div class="card-body-ater">

            {!!form_start($form)!!}

            {!!form_until($form, 'card-gallery')!!}

            @if (@$checklist->fl_gallery)
                @if (@$checklistUnidadeProdutiva)
                    <div id="a-arquivos">
                        @include('backend.components.iframe.html', ["id"=>$arquivosId, "src"=>$arquivosSrc])
                    </div>
                @endif

                @if (@!$checklistUnidadeProdutiva)
                    @include('backend.components.card-iframe-add.html', ["title"=>"Arquivos", "data"=>"a-arquivos", "label"=>"Cadastrar Arquivo"])
                @endif
            @endif

            {!!form_rest($form)!!}

            @if (@$analises)
                @component('backend.core.checklist_unidade_produtiva.components.analises-only-view', ['analises'=>$analises])
                @endcomponent
            @endif
        </div>

        <div class="card-footer-ater">
            <div class="row">
                <div class="col">
                    {{ form_cancel($back, __('buttons.general.cancel'), 'btn btn-danger px-4') }}
                </div>

                <div class="col text-right">
                    @if ($checklist->plano_acao == App\Enums\PlanoAcaoEnum::Obrigatorio || $checklist->plano_acao == App\Enums\PlanoAcaoEnum::Opcional)
                        <div id="submit-pda-obrigatorio" class="btn btn-primary px-5 btn-submit-pda" form="form-builder" data-toggle="modal" data-target="#modal-pda-obrigatorio">Salvar</div>
                    @endif

                    <button type="submit" class="btn btn-primary px-5 btn-submit" form="form-builder">Salvar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-pda-obrigatorio">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold">Aviso</h5>
                </div>

                <div class="modal-body text-left">
                    Este formulário possui um plano de ação. Você deseja ir para o plano de ação?
                </div>

                <div class="modal-footer">
                    <button class="btn btn-no btn-secondary" data-dismiss="modal">Não, apenas salvar</button>
                    <button class="btn btn-yes btn-primary" data-dismiss="modal">Sim, salvar e ir para o plano de ação</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('after-scripts')
    @if (@$checklistUnidadeProdutiva)
        @include('backend.components.iframe.scripts', ["id"=>$arquivosId, "src"=>$arquivosSrc])
    @endif

    <script>
        @if ($checklist->plano_acao == App\Enums\PlanoAcaoEnum::Obrigatorio || $checklist->plano_acao == App\Enums\PlanoAcaoEnum::Opcional)
            $("#status").change(function() {
                $(".btn-submit-pda").addClass("d-none");
                $(".btn-submit").addClass("d-none");

                if ($(this).val() != 'finalizado') {
                    $(".btn-submit").removeClass("d-none");
                } else {
                    $(".btn-submit-pda").removeClass("d-none");
                }
            }).change();
        @endif

        $("#modal-pda-obrigatorio .btn-no").click(function() {
            $("#form-builder")[0].dispatchEvent(new Event('submit'));
        });

        $("#modal-pda-obrigatorio .btn-yes").click(function() {
            $("input[name='redirect_pda']").val("pda");
            $("#form-builder")[0].dispatchEvent(new Event('submit'));
        });

        $("select[multiple='multiple']").select2();
    </script>
@endpush
