@extends('backend.layouts.app')

@section('content')
    <div class="card-ater">
        <div class="card-body-ater">
            {!! form($form) !!}
        </div>

        <div class="card-footer-ater">
            <div class="row">
                <div class="col">
                    {{ form_cancel(route('admin.core.dado.index'), __('buttons.general.cancel'), 'btn btn-danger px-4') }}
                </div>

                <div class="col text-right">
                    <button type="submit" class="btn btn-primary px-5" form="form-builder">Salvar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function () {
            $("select[name='abrangenciaEstadual[]']").select2();

            $("select[name='abrangenciaMunicipal[]']").select2({
                    ajax: {
                        url: base_url+'api/estados/cidades/busca',
                        data: function (params) {
                            var query = {
                                termo: params.term
                            }
                            return query;
                        },
                        processResults: function (data) {
                            return {
                                results: data.cidades.map(
                                    function(v) {
                                        return {id:v.id, text:v.nome_composto}
                                    })
                            };
                        },
                    },
                    minimumInputLength: 3
            });

            $("select[name='regioes[]']").select2();
        });
    </script>
@endsection
