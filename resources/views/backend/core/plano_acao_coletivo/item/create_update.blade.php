@extends('backend.layouts.app-template', ['iframe'=>true])

@section('body')
    @cardater(['title'=> $title,'titleTag'=>'h1'])
        @slot('body')
            {!! form($form) !!}

            @can('view menu plano_acao')
                @if (@$item)
                    <div class="mt-5">
                        <div id="historico-item" class="card-item">
                            @include('backend.components.iframe.html', ["id"=>$historicoId, "src"=>$historicoSrc])
                        </div>
                    </div>
                @endif
            @endcan
        @endslot

        @slot('footer')
            <div class="row">
                <div class="col">
                    {{ form_cancel($back, __('buttons.general.cancel'), 'btn btn-outline-danger px-5') }}
                </div>

                <div class="col text-right">
                    @if (@$item && !$flIndividual)
                        @include('backend.components.button-confirm-submit.index', ['id'=>'plano_acao_coletivo_item', 'form' => $form->getFormOption('id'), 'label'=>'Salvar Ação', 'message'=>'Ao salvar a ação coletiva, os dados informados nos campos <u>descrição</u>, <u>status</u>, <u>prioridade</u> e <u>prazo</u> irão sobrepor as ações individuais de todas as UPAs.'])
                    @else
                        <button type="submit" class="btn btn-primary px-5" form="form-builder">Salvar Ação</button>
                    @endif
                </div>
            </div>
        @endslot
    @endcardater
@endsection

@push('after-scripts')
    @can('view menu plano_acao')
        @if (@$item)
            @include('backend.components.iframe.scripts', ["id"=>$historicoId, "src"=>$historicoSrc])
        @endif
    @endcan

    <script>
        function formatStatePlanoAcaoItem (state) {
            if (!state.id || state.id == "0") {
                return state.text;
            }

            var $state = $(
                '<span class="select2-with-icon"><img src="' + base_url + 'img/backend/select/' + state.element.value.toLowerCase() + '.png" class="img-flag" /><span>' + state.text + '</span></span>'
            );

            return $state;
        };

        $("select").select2({
            templateResult: formatStatePlanoAcaoItem,
            templateSelection: formatStatePlanoAcaoItem
        });
    </script>
@endpush
