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
        $(function () {
            $("#solo_categoria_id").change(function() {
                $(".todos").addClass("d-none");
                $(".hectares").addClass("d-none");

                if (!$(this).val()) {
                    return;
                }

                $.ajax({
                    url:base_url+'api/unidades_produtivas/soloCategorias',
                    method:"GET",
                    data:{
                        id: $(this).val(),
                    }
                }).done((response)=>{
                    var categoria = response.solo_categoria;

                    if (categoria.tipo_form) {
                        $("."+categoria.tipo_form).removeClass("d-none");
                    }
                });
            }).change();
        });
    </script>
@endpush
