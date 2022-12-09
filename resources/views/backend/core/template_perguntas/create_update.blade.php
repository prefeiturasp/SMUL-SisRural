@extends('backend.layouts.app')

@section('content')
    <div class="card-ater">
        <div class="card-body-ater">
            {{-- @include('backend.components.title-form.index', ['title' => $title]) --}}

            {!! form($form) !!}

            @include('backend.components.card-iframe-add.html', ["title"=>"Respostas", "data"=>"cadastrar-respostas", "label"=>"Visualizar/Cadastrar Respostas", "class"=>"card-respostas"])
        </div>

        <div class="card-footer-ater">
            <div class="row">
                <div class="col">
                    {{ form_cancel(route('admin.core.template_perguntas.index'), __('buttons.general.cancel'), 'btn btn-danger px-4') }}
                </div>

                <div class="col text-right">
                    <button type="submit" class="btn btn-primary px-5" form="form-builder">Salvar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-scripts')
    <script>
        $(document).ready(function () {
            $("#tipo").change(function() {
                $(".card-respostas").addClass('d-none');

                if (($(this).val() != 'text') && ($(this).val() != 'data')) {
                    $(".card-respostas").removeClass('d-none');
                }
            }).change();
        });
    </script>
@endpush
