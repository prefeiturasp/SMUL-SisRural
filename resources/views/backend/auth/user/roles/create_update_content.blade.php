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

@push('after-scripts')
    <style>
        .app-ater {
            background-color:white;
        }
    </style>

    <script>
        $(document).ready(function() {
             $("select[name='role']").change(function() {
                $("select[name='dominio']").attr("required", true);
                $("select[name='dominio']").parent().parent().addClass("d-none");
                $("select[name='unidades_operacionais[]']").parent().parent().addClass("d-none");

                var v = $(this).val();

                if (v === 'Unidade Operacional' || v === 'Tecnico') {
                    $("select[name='dominio']").parent().parent().removeClass("d-none");
                    $("select[name='unidades_operacionais[]']").parent().parent().removeClass("d-none");
                } else if (v == 'Dominio') {
                    $("select[name='dominio']").parent().parent().removeClass("d-none");
                } else if (v == 'Administrator'){
                    $("select[name='dominio']").attr("required", false);
                }
            }).change();

            $("select[multiple='multiple']").select2();
        });
    </script>
@endpush
