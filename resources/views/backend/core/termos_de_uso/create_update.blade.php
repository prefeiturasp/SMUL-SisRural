@extends('backend.layouts.app')

@section('content')
    <div class="card-ater">
        <div class="card-body-ater">
            {{-- @include('backend.components.title-form.index', ['title' => $title]) --}}

            <div class="card pb-3">
                <div class="card-header">
                    <h5 class="card-title">Informações Gerais</h5>
                </div>

                <div class="card-body">
                    {!! form($form) !!}

                    <div id="editor">
                        {!!$form->texto->getValue()!!}
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer-ater">
            <div class="row">
                <div class="col">
                    {{ form_cancel(route('admin.core.termos_de_uso.index'), __('buttons.general.cancel'), 'btn btn-danger px-4') }}
                </div>

                <div class="col text-right">
                    <button type="submit" class="btn btn-primary px-5" form="form-builder">Salvar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-styles')
    <link href="{{ asset('css/quill.snow.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/quill.core.css') }}" rel="stylesheet" />
@endpush

@push('after-scripts')
    <script type="text/javascript" src="{{ asset('js/quill.min.js') }}"></script>

    <script>
        /**
         * Quill fix getHtml
         */
        Quill.prototype.getHtml = function() {
            return this.container.querySelector(".ql-editor").innerHTML;
        };

        $(document).ready(function() {
            var quill = new Quill('#editor', {
                modules: {
                    //toolbar:true,
                    toolbar: [['bold', 'italic', 'underline']]
                },
                theme: 'snow'
            });

            $(".card-footer-ater button[type='submit']").on("click", function (e) {
                $("input[name='texto']").val(quill.getHtml());
            });
        })
    </script>
@endpush
