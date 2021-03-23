@extends('backend.layouts.app')

@section('title', app_name() . ' | Novo Produtor / Unidade Produtiva')

@section('content')
<div class="card-ater">
    <h1 class="mb-4">Novo Produtor / Nova Unidade Produtiva</h1>
    <div class="card-body-ater">
        {!!form_start($form)!!}

        {!!form_until($form, 'lng')!!}

        @include('backend.core.unidade_produtiva.lat_lng.index', ['lat' => @$form->lat->getValue(), 'lng'=> @$form->lng->getValue()])

        {!!form_rest($form)!!}
    </div>

    <div class="card-footer-ater">
        <div class="row">
            <div class="col">
                {{ form_cancel(route('admin.dashboard'), __('buttons.general.cancel'), 'btn btn-danger px-4') }}
            </div>

            <div class="col text-right">
                <button type="submit" class="btn btn-primary px-5" form="form-builder">Salvar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('after-scripts')
    @include('backend.core.unidade_produtiva.lat_lng.scripts', ['lat' => @$form->lat->getValue(), 'lng'=> @$form->lng->getValue()])

    @include('backend.scripts.estado-cidade-select2')

    <script>
        $(function() {
            $("select[name='unidade_produtiva_id']").select2();

            $("#fl_unidade_produtiva").change(function() {
                $("#card-coordenadas").removeClass('d-none');
                $("#sem-unidade-produtiva").removeClass('d-none');
                $("#com-unidade-produtiva").addClass('d-none');

                $("#nome_unidade_produtiva").attr("required", "required");
                $("#estado_id").attr("required", "required");
                $("#cidade_id").attr("required", "required");
                $("#endereco").attr("required", "required");
                $("#nome_unidade_produtiva").attr("required", "required");
                $("#unidade_produtiva_id").removeAttr("required");

                if ($(this).prop("checked")) {
                    $("#card-coordenadas").addClass('d-none');
                    $("#sem-unidade-produtiva").addClass('d-none');
                    $("#com-unidade-produtiva").removeClass('d-none');

                    $("#nome_unidade_produtiva").removeAttr("required");
                    $("#estado_id").removeAttr("required");
                    $("#cidade_id").removeAttr("required");
                    $("#endereco").removeAttr("required");
                    $("#nome_unidade_produtiva").removeAttr("required");
                    $("#unidade_produtiva_id").attr("required", "required");
                }
            }).change();
            // selectAutoYesNo("#fl_exist_unidade_produtiva", '.card-exist-unidade-produtiva');
            // selectAutoYesNoNone("#fl_exist_unidade_produtiva", '.card-unidade-produtiva');
        });
    </script>
@endpush
