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
            $("#relacao_id").change(function() {
                if (!$(this).val() || $(this).val() > 2) {
                    $("#card-cpf").addClass("d-none");
                } else {
                    $("#card-cpf").removeClass("d-none");
                }
            }).change();
        })
    </script>
@endpush
