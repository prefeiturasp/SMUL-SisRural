@php
$cidades = [];
if (request()->has('cidade_id')) {
    $cidades = App\Models\Core\CidadeModel::whereIn('id', request()->input('cidade_id'))
        ->get(['id', 'nome'])
        ->pluck('nome', 'id');
}

$produtores = [];
if (request()->has('produtor_id')) {
    $produtores = App\Models\Core\ProdutorModel::whereIn('id', request()->input('produtor_id'))
        ->get(['id', 'nome'])
        ->pluck('nome', 'id');
}

$unidadesProdutivas = [];
if (request()->has('unidade_produtiva_id')) {
    $unidadesProdutivas = App\Models\Core\UnidadeProdutivaModel::whereIn('id', request()->input('unidade_produtiva_id'))
        ->get(['id', 'nome'])
        ->pluck('nome', 'id');
}

$templateCaderno = $templateCaderno->mapWithKeys(function ($v) {
    return [$v['id'] => $v['nome'] . ' (' . $v->dominio->nome . ')'];
});
@endphp

<form id="form-filter" method="POST" class="needs-validation" novalidate action="{{ $dataUrl }}">
    @csrf
    @cardater(['id'=>'card-filter', 'class' => $expand ? 'is-expand' : null, 'title'=> 'Filtro','titleTag'=>'h2'])
    @slot('headerRight')
        <div class="float-right">
            @if ($expand)
                <div class="icon-filter c-class-toggler" data-target="#card-filter" data-class="hide">
                    <i class="icon-top c-icon cil-chevron-top"></i>
                    <i class="icon-bottom c-icon cil-chevron-bottom"></i>
                </div>
            @endif
        </div>
    @endslot

    @slot('body')
        <div id="card-filter">
            <fieldset>
                <legend class="h5">
                    Intervalo de tempo
                </legend>

                <small class="pb-2 d-block">Aplicado no filtro por 'Atuação de Equipe' ou nos 'Formulários' (Data que foi
                    finalizado)</small>

                <div class="form-group row">
                    <div class="col-md-4 col-lg-3">
                        {{ html()->label('Data Inicial')->for('dt_ini') }}
                        {{ html()->date('dt_ini')->class('form-control')->value(request()->input('dt_ini') ? request()->input('dt_ini') : ($requiredDate ? date('Y-m-d', strtotime('-1 year')) : null))->required($requiredDate) }}
                        <div class="invalid-feedback">O campo Data Inicial é obrigatório.</div>
                    </div>

                    <div class="col-md-4 col-lg-3">
                        {{ html()->label('Data Final')->for('dt_end') }}
                        {{ html()->date('dt_end')->class('form-control')->value(request()->input('dt_end') ? request()->input('dt_end') : ($requiredDate ? date('Y-m-d') : null))->required($requiredDate) }}
                        <div class="invalid-feedback">O campo Data Final é obrigatório.</div>
                    </div>
                </div>
            </fieldset>

            <hr />

            <fieldset class="mt-4">
                <legend class="h5">
                    Abrangência Territorial
                </legend>

                @if (@$abrangenciasTxt)
                    <small class="pb-2 d-block">Sua abrangência é: {{ @$abrangenciasTxt }}.<br>Você somente terá acesso à
                        informações dentro de sua abrangência.</small>
                @endif

                <small class="pb-2 d-block">Este filtro trás todas as Unidades Produtivas do território selecionado e
                    todas as atividades realizadas sobre elas, independente das equipes de trabalho que as
                    realizaram.</small>

                <div class="form-group row">
                    <div class="col-md-3">
                        {{ html()->label('Domínios')->for('dominio_id') }}
                        {{ html()->select('dominio_id[]', $dominios->pluck('nome', 'id'))->class('form-control')->multiple()->value(request()->input('dominio_id')) }}
                    </div>

                    <div class="col-md-3">
                        {{ html()->label('Unidades Operacionais')->for('unidade_operacional_id') }}
                        {{ html()->select('unidade_operacional_id[]', $unidadeOperacionais->pluck('nome', 'id'))->class('form-control')->multiple()->value(request()->input('unidade_operacional_id')) }}
                    </div>

                    <div class="col-md-3">
                        {{ html()->label('Estados')->for('estado_id') }}
                        {{ html()->select('estado_id[]', $estados->pluck('nome', 'id'))->class('form-control')->multiple()->value(request()->input('estado_id')) }}
                    </div>

                    <div class="col-md-3">
                        {{ html()->label('Cidades')->for('cidade_id') }}
                        {{ html()->select('cidade_id[]', $cidades)->class('form-control')->multiple()->value(request()->input('cidade_id')) }}
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-3 position-relative">
                        {{ html()->label('Regiões')->for('regiao_id') }}
                        {{ html()->select('regiao_id[]', $regioes->pluck('nome', 'id'))->class('form-control')->multiple()->value(request()->input('regiao_id')) }}
                    </div>

                    @cannot('report restricted')
                    <div class="col-md-3">
                        {{ html()->label('Produtores')->for('produtor_id') }}
                        {{ html()->select('produtor_id[]', $produtores)->class('form-control')->multiple()->value(request()->input('produtor_id')) }}
                    </div>
                    <div class="col-md-3">
                        {{ html()->label('Unidades Produtivas')->for('unidade_produtiva_id') }}
                        {{ html()->select('unidade_produtiva_id[]', $unidadesProdutivas)->class('form-control')->multiple()->value(request()->input('unidade_produtiva_id')) }}
                    </div>
                    @endcannot
                </div>
            </fieldset>

            <hr />

            <fieldset class="mt-4">
                <legend class="h5">
                    Atuação de Equipe de Trabalho
                </legend>

                @if (@$abrangenciasTxt)
                    <small class="pb-2 d-block">Sua abrangência é: {{ @$abrangenciasTxt }}.<br>Você somente terá acesso à
                        informações dentro de sua abrangência.</small>
                @endif

                <small class="pb-2 d-block">Este filtro trás todas as atividades realizadas pela(s) equipe(s) de
                    trabalho selecionada(s) e as Unidades Produtivas com as quais interagiram.
                </small>

                <div class="form-group row">
                    <div class="col-md-3">
                        {{ html()->label('Domínios')->for('atuacao_dominio_id') }}
                        {{ html()->select('atuacao_dominio_id[]', $atuacaoDominios->pluck('nome', 'id'))->class('form-control')->multiple()->value(request()->input('atuacao_dominio_id')) }}
                    </div>

                    <div class="col-md-3">
                        {{ html()->label('Unidades Operacionais')->for('atuacao_unidade_operacional_id') }}
                        {{ html()->select('atuacao_unidade_operacional_id[]', $atuacaoUnidadeOperacionais->pluck('nome', 'id'))->class('form-control')->multiple()->value(request()->input('atuacao_unidade_operacional_id')) }}
                    </div>

                    <div class="col-md-3">
                        {{ html()->label('Técnicos')->for('atuacao_tecnico_id') }}
                        {{ html()->select('atuacao_tecnico_id[]', $atuacaoTecnicos)->class('form-control')->multiple()->value(request()->input('atuacao_tecnico_id')) }}
                    </div>
                </div>
            </fieldset>

            <hr />

            <fieldset class="mt-4">
                <legend class="h5">
                    Filtros adicionais
                </legend>

                <small class="mb-2 d-block">Serão retornadas Unidades Produtivas que possuírem as características selecionadas.</small>

                <div class="form-group row">
                    <div class="col-md-3">
                        {{ html()->label('Formulários')->for('checklist_id') }}

                        {{ html()->select('checklist_id[]', $checklists)->class('form-control')->multiple()->value(request()->input('checklist_id')) }}

                        <div class="invalid-feedback">O campo Formulários é obrigatório.</div>
                    </div>

                    <div class="col-md-3">
                        {{ html()->label('Certificações')->for('certificacao_id') }}
                        {{ html()->select('certificacao_id[]', $certificacoes->pluck('nome', 'id')->toArray() + ['0' => 'Não possui'])->class('form-control')->multiple()->value(request()->input('certificacao_id')) }}
                    </div>

                    <div class="col-md-3">
                        {{ html()->label('Uso do Solo')->for('solo_categoria_id') }}
                        {{ html()->select('solo_categoria_id[]', $soloCategorias->pluck('nome', 'id'))->class('form-control')->multiple()->value(request()->input('solo_categoria_id')) }}
                    </div>

                    <div class="col-md-3">
                        {{ html()->label('Faixa de área')->for('area') }}
                        {{ html()->select('area[]', $area)->class('form-control')->multiple()->value(request()->input('area')) }}
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-3">
                        {{ html()->label('Gênero')->for('genero_id') }}
                        {{ html()->select('genero_id[]', $generos->pluck('nome', 'id'))->class('form-control')->multiple()->value(request()->input('genero_id')) }}
                    </div>

                    <div class="col-md-3">
                        {{ html()->label('Status - Unidade Produtiva')->for('status_unidade_produtiva') }}
                        {{ html()->select('status_unidade_produtiva[]', $statusUnidadeProdutiva)->class('form-control')->multiple()->value(request()->input('status_unidade_produtiva')) }}
                    </div>

                    <div class="filter-caderno_campo col-md-3 {{ $bi ? '' : 'd-none is-required' }}">
                        {{ html()->label('Template - Caderno de Campo')->for('template_caderno_id') }}
                        {{ html()->select('template_caderno_id[]', $templateCaderno)->class('form-control')->multiple()->value(request()->input('template_caderno_id')) }}
                        {!! @$bi ? '<small>Este filtro influencia somente os "Indicadores de Cadernos de Campo"</small>' : null !!}
                    </div>

                    <div class="filter-pda is-required col-md-3 d-none">
                        {{ html()->label('Tipo do Plano de Ação*')->for('type_pda') }}
                        {{ html()->select('type_pda[]', ['individual' => 'Individual', 'coletivo' => 'Coletivo'] + $checklists)->multiple()->class('form-control')->value(request()->input('type_pda')) }}
                        <div class="invalid-feedback">O campo Tipo do Plano de Ação é obrigatório.</div>
                    </div>

                    @if (@$bi)
                        <div class="filter-caderno_campo col-md-3">
                            {{ html()->label('Perguntas')->for('pergunta_id') }}
                            {{ html()->select('pergunta_id[]', App\Models\Core\PerguntaModel::get()->pluck('pergunta', 'id'))->class('form-control')->multiple()->value(request()->input('pergunta_id')) }}
                            {!! @$bi ? '<small>Este filtro influencia somente os "Indicadores de Formulários"</small>' : null !!}
                        </div>
                    @endif
                </div>
            </fieldset>
        </div>
    @endslot

    @slot('footer')
        @if ($checkTerms)
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="check_terms" value="1">
                <label class="form-check-label" for="check_terms">Ao baixar os arquivos solicitados, declaro que estou
                    ciente das minhas permissões e responsabilidades sobre o uso destes dados, restritas às minhas
                    atividades profissionais para esta entidade e que não me é permitido divulgar ou compartilhar estes
                    dados e informações. Declaro também estar ciente e de acordo com os <a href="termos-de-uso"
                        target="_blank">termos de uso do SisRural</a>.</label>
            </div>

            <hr />
        @endif

        <div class="row {{ $checkTerms ? 'my-2' : null }}">
            <div class="col">
                <button id="form-reset" type="reset" class="btn btn-outline-danger px-4">Limpar Filtro</button>
            </div>

            <div class="col text-right">
                <button id="form-submit" class="btn btn-primary px-5 position-relative">
                    <span class="loading spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Buscar
                </button>
            </div>
        </div>
    @endslot
    @endcardater
</form>

@push('before-styles')
    <style>
        #card-filter .card-body {
            transition: max-height .3s, padding .3s;
            overflow: hidden;
            max-height: 3000px;
            height: auto;
        }

        #card-filter.hide .card-body {
            max-height: 0px;
            padding-top: 0px;
            padding-bottom: 0px;
            min-height: 0px;
        }

        #card-filter .icon-top {
            display: none;
        }

        #card-filter .icon-bottom {
            display: block;
        }

        #card-filter.hide .icon-bottom {
            display: none;
        }

        #card-filter.hide .icon-top {
            display: block;
        }

        .icon-filter i {
            margin-top: 2px;
            font-size: 22px;
        }

        #form-submit .loading {
            display: none;

            position: absolute;
            top: 9px;
            right: 25px;
        }

        #form-submit.loading .loading {
            display: block;
        }

        .btn-collapse-ater {
            cursor: pointer;
        }

        .btn-collapse-ater .cil-chevron-top {
            display: none;
        }

        .btn-collapse-ater .cil-chevron-bottom {
            display: block;
        }

        .btn-collapse-ater.show .cil-chevron-top {
            display: block;
        }

        .btn-collapse-ater.show .cil-chevron-bottom {
            display: none;
        }

        #card-filter .form-check label {
            font-size: 12px;
        }

        #card-filter #dt_ini,
        #card-filter #dt_end {
            min-width: 165px;
        }

    </style>
@endpush

@push('after-scripts')
    <script>
        //Abrangencia Territorial
        $("select[name='dominio_id[]']").select2({
            width: 'style'
        });
        $("select[name='unidade_operacional_id[]']").select2({
            width: 'style'
        });
        $("select[name='estado_id[]']").select2({
            width: 'style'
        });
        $("select[name='regiao_id[]']").select2({
            width: 'style',
            maximumSelectionLength: 1
        });

        //Atuação
        $("select[name='atuacao_dominio_id[]']").select2({
            width: 'style'
        });
        $("select[name='atuacao_unidade_operacional_id[]']").select2({
            width: 'style'
        });
        $("select[name='atuacao_tecnico_id[]']").select2({
            width: 'style'
        });

        //Filtros adicionais
        $("select[name='checklist_id[]']").select2({
            width: 'style'
        });
        $("select[name='certificacao_id[]']").select2({
            width: 'style'
        });
        $("select[name='solo_categoria_id[]']").select2({
            width: 'style'
        });
        $("select[name='area[]']").select2({
            width: 'style'
        });
        $("select[name='genero_id[]']").select2({
            width: 'style'
        });
        $("select[name='status_unidade_produtiva[]']").select2({
            width: 'style'
        });

        $("select[name='type_pda[]']").select2({
            width: 'style'
        });

        //Caderno
        $("select[name='template_caderno_id[]']").select2({
            width: 'style',
            maximumSelectionLength: 1
        });

        if ($("select[name='pergunta_id[]']").length > 0) {
            $("select[name='pergunta_id[]']").select2({
                width: 'style'
            });
        }

        //Cidade - Select - Busca
        $("select[name='cidade_id[]']").select2({
            width: 'style',
            ajax: {
                url: base_url + 'api/estados/cidades/busca',
                data: function(params) {
                    var query = {
                        termo: params.term
                    }
                    return query;
                },
                processResults: function(data) {
                    return {
                        results: data.cidades.map(
                            function(v) {
                                return {
                                    id: v.id,
                                    text: v.nome_composto
                                }
                            })
                    };
                },
            },
            minimumInputLength: 3
        });

        //Produtor - Select - Busca
        $("select[name='produtor_id[]']").select2({
            width: 'style',
            ajax: {
                url: base_url + 'admin/api/produtores/busca',
                data: function(params) {
                    var query = {
                        termo: params.term
                    }
                    return query;
                },
                processResults: function(data) {
                    return {
                        results: data.data.map(
                            function(v) {
                                return {
                                    id: v.id,
                                    text: v.nome
                                }
                            })
                    };
                },
            },
            minimumInputLength: 3
        });

        //Unidade Produtiva - Select - Busca
        $("select[name='unidade_produtiva_id[]']").select2({
            width: 'style',
            ajax: {
                url: base_url + 'admin/api/unidades/busca',
                data: function(params) {
                    var query = {
                        termo: params.term
                    }
                    return query;
                },
                processResults: function(data) {
                    return {
                        results: data.data.map(
                            function(v) {
                                return {
                                    id: v.id,
                                    text: v.nome
                                }
                            })
                    };
                },
            },
            minimumInputLength: 3
        });

        //Limpar formulário
        $("#form-reset").click(function() {
            resetFilter();
        })

        function resetFilter() {
            $("#card-filter").removeClass("hide");

            $("select[name='dominio_id[]']").val(null).trigger("change");
            $("select[name='unidade_operacional_id[]']").val(null).trigger("change");
            $("select[name='estado_id[]']").val(null).trigger("change");
            $("select[name='cidade_id[]']").val(null).trigger("change");
            $("select[name='regiao_id[]']").val(null).trigger("change");
            $("select[name='produtor_id[]']").val(null).trigger("change");
            $("select[name='unidade_produtiva_id[]']").val(null).trigger("change");

            $("select[name='atuacao_dominio_id[]']").val(null).trigger("change");
            $("select[name='atuacao_unidade_operacional_id[]']").val(null).trigger("change");
            $("select[name='atuacao_tecnico_id[]']").val(null).trigger("change");

            $("select[name='checklist_id[]']").val(null).trigger("change");
            $("select[name='certificacao_id[]']").val(null).trigger("change");
            $("select[name='solo_categoria_id[]']").val(null).trigger("change");
            $("select[name='area[]']").val(null).trigger("change");
            $("select[name='genero_id[]']").val(null).trigger("change");
            $("select[name='status_unidade_produtiva[]']").val(null).trigger("change");

            $("select[name='template_caderno_id[]']").val(null).trigger("change");
            $("select[name='type_pda[]']").val(null).trigger("change");
        }

    </script>

    @if ($checkTerms)
        <script>
            $("#check_terms").on('change', function() {
                if ($(this).is(':checked')) {
                    $("#form-submit").removeAttr("disabled");
                } else {
                    $("#form-submit").attr("disabled", "disabled");
                }
            }).change();

        </script>
    @endif

@endpush
