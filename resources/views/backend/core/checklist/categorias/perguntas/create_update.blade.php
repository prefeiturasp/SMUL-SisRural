@extends('backend.layouts.app-template', ['iframe'=>true])

@section('body')
    @if (@$checklistPergunta)
        @cannot('editForm', $checklistPergunta)
            <div class="alert alert-warning">
                Algumas informações podem ficar bloqueadas para edição. Esta pergunta já foi utilizada em uma aplicação de formulário.
            </div>
        @endcannot
    @endif

    @cardater(['title'=> $title,'titleTag'=>'h1'])
        @slot('body')
            {!! form($form) !!}
        @endslot

        @slot('footer')
            <div class="row">
                <div class="col">
                    {{ form_cancel($back, __('buttons.general.cancel'), 'btn btn-outline-danger px-5') }}
                </div>

                <div class="col text-right">
                    <button type="submit" class="btn btn-primary px-5" form="form-builder">Salvar</button>
                </div>
            </div>
        @endslot
    @endcardater
@endsection

@push('after-scripts')
    <script>
        $(function () {
            selectAutoYesNo("#fl_plano_acao", '#card-plano-acao');
        });
    </script>
@endpush
