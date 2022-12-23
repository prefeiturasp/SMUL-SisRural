@extends('backend.layouts.app')

@section('content')
    @cardater(['title'=> 'Relatório','titleTag'=>'h2'])
        @slot('body')
            <div class="form-group row">
                <div class="col-md-5">
                    {{ html()->label('Tipo de Relatório')->for('report_type') }}
                    {{ html()->select('report_type',
                            [
                                'unidade_produtiva_produtor' => 'Unidade Produtiva/Produtor/a',
                                'unidade_produtiva_pessoa' => 'Unidade Produtiva - Pessoas',
                                'unidade_produtiva_infra' => 'Unidade Produtiva - Infraestrutura',
                                'unidade_produtiva_uso_solo' => 'Unidade Produtiva - Uso do Solo',
                                'caderno_campo' => 'Caderno de Campo',
                                'checklist' => 'Formulário',
                                'pda' => 'Plano de Ação'
                            ]
                        )->class('form-control')->value(request()->input('report_type')) }}
                </div>
            </div>
        @endslot
    @endcardater

    {!!@$viewFilter!!}
@endsection

@push('after-scripts')
    <style>
        #report_type { max-width:300px; }
    </style>

    <script>
        $("#report_type").change(function() {
            var actions = {
                unidade_produtiva_produtor: 'admin/report/unidade_produtiva_data',
                unidade_produtiva_pessoa: 'admin/report/unidade_produtiva_pessoa',
                unidade_produtiva_infra: 'admin/report/unidade_produtiva_infra',
                unidade_produtiva_uso_solo: 'admin/report/unidade_produtiva_uso_solo',
                caderno_campo: 'admin/report/unidade_produtiva_caderno',
                checklist: 'admin/report/unidade_produtiva_checklist',
                pda: 'admin/report/unidade_produtiva_pda'
            }

            var v = $(this).val();

            $('#form-filter').attr('action', "{{config('app.endpoint_bi')}}" + actions[v]);
        });

        $("#report_type").change(function() {
            $("#report_type option").each(function() {
                $(".filter-"+$(this).attr("value")).addClass("d-none");
                $(".filter-"+$(this).attr("value")+".is-required select").removeAttr("required");
            });

            $(".filter-"+$(this).val()).removeClass("d-none");
            $(".filter-"+$(this).val()+".is-required select").attr("required", true);

            //Custom p/ o checklist_unidade_produtiva
            if ($(this).val() == 'checklist') {
                $("select[name='checklist_id[]']").val(null).trigger("change");
                $("select[name='checklist_id[]']").attr("required", "required");
                $("select[name='checklist_id[]']").select2({width: 'style', maximumSelectionLength: 1});
            } else {
                $("select[name='checklist_id[]']").val(null).trigger("change");
                $("select[name='checklist_id[]']").removeAttr("required");
                $("select[name='checklist_id[]']").select2({width: 'style'})
            }
        }).change();

        window.submitFilter = function(form) {
            form.submit();
        }
    </script>
@endpush
