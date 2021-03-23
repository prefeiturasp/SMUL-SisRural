@extends('backend.layouts.app-template', ['iframe'=>true])

@section('body')
    <div class="card">
        <div class="card-body">
            @include('backend.components.title-form.index', ['title' => $title])

            {!! form($form) !!}
        </div>

        <div class="card-footer button-group">
            <div class="row">
                <div class="col">
                    {{ form_cancel($back, __('buttons.general.cancel'), 'btn btn-outline-danger px-5') }}
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
        $(document).ready(function() {
            $("#instalacao_tipo_id").change(function() {
                if ($(this).val() == 2 || $(this).val() == 3 ) {
                    $(".card-area").addClass("d-none");
                } else {
                    $(".card-area").removeClass("d-none");
                }
            }).change();
        })
    </script>
@endpush
