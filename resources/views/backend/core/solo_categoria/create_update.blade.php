@extends('backend.layouts.app')

@section('content')
    <div class="card-ater">
        <div class="card-body-ater">
            {!! form($form) !!}
        </div>

        <div class="card-footer-ater">
            <div class="row">
                <div class="col">
                    {{ form_cancel(route('admin.core.solo_categoria.index'), __('buttons.general.cancel'), 'btn btn-danger px-4') }}
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
        $(function() {
            $("#tipo").change(function() {
                var v = $(this).val();
                if (v == 'geral') {
                    $("#tipo_form").parent().parent().removeClass("d-none");
                    $("#min").parent().parent().removeClass("d-none");
                    $("#max").parent().parent().removeClass("d-none");
                } else {
                    $("#tipo_form").parent().parent().addClass("d-none");
                    $("#min").parent().parent().addClass("d-none");
                    $("#max").parent().parent().addClass("d-none");
                }
            })
        });
    </script>
@endpush
