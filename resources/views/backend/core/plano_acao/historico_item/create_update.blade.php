@extends('backend.layouts.app-template', ['iframe'=>true])

@section('body')
    <style>
        body {
            background: transparent;
        }
    </style>

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
                    <button type="submit" class="btn btn-primary px-5" form="form-builder">Salvar acompanhamento</button>
                </div>
            </div>
        @endslot
    @endcardater
@endsection
