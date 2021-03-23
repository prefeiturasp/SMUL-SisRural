@extends('backend.layouts.app')

@section('content')
    <div class="card-ater">
        <div class="card-body-ater">
            {{-- @include('backend.components.title-form.index', ['title' => $title]) --}}

            {!! form($form) !!}

            @if (@$template)
                <div class="mt-5">
                    <div id="a-arquivos">
                        @include('backend.components.iframe.html', ["id"=>$perguntasId, "src"=>$perguntasSrc])
                    </div>
                </div>
            @endif

            @if (!@$template)
                @include('backend.components.card-iframe-add.html', ["title"=>"Perguntas", "data"=>"vincular-perguntas", "label"=>"Visualizar/Vincular Perguntas"])
            @endif
        </div>

        <div class="card-footer-ater">
            <div class="row">
                <div class="col">
                    {{ form_cancel(route('admin.core.templates_caderno.index'), __('buttons.general.cancel'), 'btn btn-danger px-4') }}
                </div>

                <div class="col text-right">
                    <button type="submit" class="btn btn-primary px-5" form="form-builder">Salvar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-scripts')
    @if (@$template)
        @include('backend.components.iframe.scripts', ["id"=>$perguntasId, "src"=>$perguntasSrc])
    @endif
@endpush
