@extends('backend.layouts.app')

@section('content')
    @cardater(['title' => 'Unidade Produtiva: '.$unidadeProdutiva->nome,'titleTag'=>'h1'])
        @can('edit same operational units productive units')
            @slot('headerRight')
                <div class="float-right">
                    <a href="{{route('admin.core.unidade_produtiva.edit', ['unidadeProdutiva'=>$unidadeProdutiva])}}" class="btn btn-primary px-5" form="form-builder">Editar</a>
                </div>
            @endslot
        @endcan
    @endcardater

    @cardater(['title'=>'Dados Básicos'])
         @slot('body')
            <table class="table table-hover">
                <tr>
                    <th width="20%">Nome da Unidade Produtiva</th>
                    <td>{{$unidadeProdutiva->nome}}</td>
                </tr>
                <tr>
                    <th><abbr title="Código de Endereçamento Postal">CEP</abbr></th>
                    <td>{{$unidadeProdutiva->cep}}</td>
                </tr>
                <tr>
                    <th>Endereço</th>
                    <td>{{$unidadeProdutiva->endereco}}</td>
                </tr>
                <tr>
                    <th>Bairro</th>
                    <td>{{$unidadeProdutiva->bairro}}</td>
                </tr>
                <tr>
                    <th>Distrito</th>
                    <td>{{$unidadeProdutiva->subprefeitura}}</td>
                </tr>
                <tr>
                    <th>Estado</th>
                    <td>{{$unidadeProdutiva->estado ? $unidadeProdutiva->estado->nome : ''}}</td>
                </tr>
                <tr>
                    <th>Cidade</th>
                    <td>{{$unidadeProdutiva->cidade ? $unidadeProdutiva->cidade->nome : ''}}</td>
                </tr>
                <tr>
                    <th>Bacia Hidrográfica</th>
                    <td>{{$unidadeProdutiva->bacia_hidrografica}}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>{{$unidadeProdutiva->status}}</td>
                </tr>
                <tr>
                    <th>Status - Observação</th>
                    <td>{{$unidadeProdutiva->status_observacao}}</td>
                </tr>

                <tr>
                    <th>Criado por</th>
                    <td>{{$unidadeProdutiva->usuario->full_name}}</td>
                </tr>
                <tr>
                    <th>Criado em</th>
                    <td>{{$unidadeProdutiva->created_at_formatted }}</td>
                </tr>
                <tr>
                    <th>Atualizado em</th>
                    <td>{{$unidadeProdutiva->updated_at_formatted }}</td>
                </tr>
            </table>
        @endslot
    @endcardater

    @cardater(['title'=>'Coordenadas'])
        @slot('body')
            <table class="table table-hover">
                <tr>
                    <th width="20%">Latitude</th>
                    <td>{{$unidadeProdutiva->lat}}</td>
                </tr>
                <tr>
                    <th>Longitude</th>
                    <td>{{$unidadeProdutiva->lng}}</td>
                </tr>
            </table>

            <div class="map-lat-lng">
                <div id="map-content"></div>
            </div>
        @endslot
    @endcardater

    @cardater(['title'=>'Dados Complementares'])
        @slot('body')
            <table class="table table-hover">
                <tr>
                    <th width="20%">Possui Certificação?</th>
                    <td>{{boolean_sim_nao_sem_resposta($unidadeProdutiva->fl_certificacoes)}}</td>
                </tr>
                <tr>
                    <th>Cerificações</th>
                    <td>{{join(", ",$unidadeProdutiva->certificacoes->pluck('nome')->toArray())}}</td>
                </tr>
                <tr>
                    <th>Cerificações - Descrição</th>
                    <td>{{$unidadeProdutiva->certificacoes_descricao}}</td>
                </tr>
                <tr>
                    <th>Possui <abbr title="Cadastro Ambiental Rural">CAR</abbr>?</th>
                    <td>{{@App\Enums\UnidadeProdutivaCarEnum::toSelectArray()[$unidadeProdutiva->fl_car]}}</td>
                </tr>
                <tr>
                    <th><abbr title="Cadastro Ambiental Rural">CAR</abbr></th>
                    <td>{{$unidadeProdutiva->car}}</td>
                </tr>
                <tr>
                    <th>Possui <abbr title="Certificado de Cadastro de Imóvel Rural">CCIR</abbr>?</th>
                    <td>{{boolean_sim_nao_sem_resposta($unidadeProdutiva->fl_ccir)}}</td>
                </tr>
                <tr>
                    <th>Possui <abbr title="Imposto sobre a Propriedade Territorial Rural">ITR</abbr>?</th>
                    <td>{{boolean_sim_nao_sem_resposta($unidadeProdutiva->fl_itr)}}</td>
                </tr>
                <tr>
                    <th>Possui Matricula?</th>
                    <td>{{boolean_sim_nao_sem_resposta($unidadeProdutiva->fl_matricula)}}</td>
                </tr>
                <tr>
                    <th>Número da <abbr title="Unidade de Produção Agrícula">UPA</abbr></th>
                    <td>{{$unidadeProdutiva->upa}}</td>
                </tr>
                <tr>
                    <th>Palavras chave</th>
                    <td>{!!@App\Helpers\General\AppHelper::tableTags($unidadeProdutiva->tags)!!}</td>
                </tr>
            </table>
        @endslot
    @endcardater

    @cardater(['title'=>'Uso do Solo'])
        @slot('body')
            <table class="table table-hover">
                <tr>
                    <th width="20%">Área total da propriedade</th>
                    <td>{{$unidadeProdutiva->area_total_solo}}</td>
                </tr>
                <tr>
                    <th>Processa a produção?</th>
                    <td>{{$unidadeProdutiva->fl_producao_processa ? App\Enums\ProcessaProducaoEnum::toSelectArray()[$unidadeProdutiva->fl_producao_processa] : 'Sem resposta'}}</td>
                </tr>
                <tr>
                    <th>Descreva o processamento da produção</th>
                    <td>{{$unidadeProdutiva->producao_processa_descricao}}</td>
                </tr>
                <tr>
                    <th>Outros Usos</th>
                    <td>{{join(", ", $unidadeProdutiva->solosCategoria->pluck('nome')->toArray())}}</td>
                </tr>
                <tr>
                    <th>Outros Usos - Descrição</th>
                    <td>{{$unidadeProdutiva->outros_usos_descricao}}</td>
                </tr>
            </table>

            @foreach ($unidadeProdutiva->caracterizacoes as $k=>$v)
                @cardater(['title'=> $v->categoria->nome, 'class'=>'card-custom-border'])
                    @slot('body')
                        <table class="table table-hover">
                            @if ($v->area)
                                <tr>
                                    <th width="20%">Área (Hectares)</th>
                                    <td>{{$v->area}}</td>
                                </tr>
                            @endif

                            @if ($v->quantidade)
                                <tr>
                                    <th>Quantidade de Espécies</th>
                                    <td>{{$v->quantidade}}</td>
                                </tr>
                            @endif

                            @if ($v->descricao)
                                <tr>
                                    <th>Descrição</th>
                                    <td>{{$v->descricao}}</td>
                                </tr>
                            @endif

                            <tr>
                                <th>Agrobiodiversidade</th>
                                <td>{{$v->categoria->agrobiodiversidade($v->quantidade)}}</td>
                            </tr>
                        </table>
                    @endslot
                @endcardater
            @endforeach
        @endslot
    @endcardater


    @cardater(['title'=>'Comercialização'])
        @slot('body')
            <table class="table table-hover">
                <tr>
                    <th width="20%">Comercializa a Produção?</th>
                    <td>{{boolean_sim_nao_sem_resposta($unidadeProdutiva->fl_comercializacao)}}</td>
                </tr>
                <tr>
                    <th>Canais de Comercialização</th>
                    <td>{{join(", ", $unidadeProdutiva->canaisComercializacao->pluck('nome')->toArray())}}</td>
                </tr>
                <tr>
                    <th>Gargalos da produção, processamento e comercialização</th>
                    <td>{{$unidadeProdutiva->gargalos}}</td>
                </tr>
            </table>
        @endslot
    @endcardater

    @cardater(['title'=>'Saneamento Rural'])
        @slot('body')
            <table class="table table-hover">
                <tr>
                    <th width="20%">Possui Outorga?</th>
                    <td>{{$unidadeProdutiva->outorga ? $unidadeProdutiva->outorga->nome : ''}}</td>
                </tr>
                <tr>
                    <th>Fontes de uso de Água</th>
                    <td>{{join(", ", $unidadeProdutiva->tiposFonteAgua->pluck('nome')->toArray())}}</td>
                </tr>
                <tr>
                    <th>Há Risco de Contaminação?</th>
                    <td>{{boolean_sim_nao_sem_resposta($unidadeProdutiva->fl_risco_contaminacao)}}</td>
                </tr>
                <tr>
                    <th>Selecione os Tipos de Contaminação</th>
                    <td>{{join(", ", $unidadeProdutiva->riscosContaminacaoAgua->pluck('nome')->toArray())}}</td>
                </tr>
                <tr>
                    <th>Observações quanto à contaminação</th>
                    <td>{{$unidadeProdutiva->risco_contaminacao_observacoes}}</td>
                </tr>
                <tr>
                    <th>Destinação de resíduos sólidos</th>
                    <td>{{join(", ",$unidadeProdutiva->residuoSolidos->pluck('nome')->toArray())}}</td>
                </tr>
                <tr>
                    <th>Esgotamento Sanitário</th>
                    <td>{{join(", ",$unidadeProdutiva->esgotamentoSanitarios->pluck('nome')->toArray())}}</td>
                </tr>
            </table>
        @endslot
    @endcardater

    @if(count($unidadeProdutiva->colaboradores) > 0)
        @cardater(['title'=>'Pessoas'])
            @slot('body')
                @foreach ($unidadeProdutiva->colaboradores as $k=>$v)
                    @cardater(['title'=> $v->nome, 'class'=>'card-custom-border'])
                        @slot('body')
                            <table class="table table-hover">
                                @if ($v->nome)
                                    <tr>
                                        <th width="20%">Nome</th>
                                        <td>{{$v->nome}}</td>
                                    </tr>
                                @endif
                                @if ($v->relacao)
                                    <tr>
                                        <th>Relação</th>
                                        <td>{{$v->relacao ? $v->relacao->nome : ''}}</td>
                                    </tr>
                                @endif
                                @if ($v->cpf)
                                    <tr>
                                        <th><abbr title="Cadastro de Pessoa Física">CPF</abbr></th>
                                        <td>{{$v->cpf}}</td>
                                    </tr>
                                @endif
                                @if ($v->funcao)
                                    <tr>
                                        <th>Função</th>
                                        <td>{{$v->funcao}}</td>
                                    </tr>
                                @endif
                                @if ($v->dedicacao)
                                    <tr>
                                        <th>Dedicação</th>
                                        <td>{{$v->dedicacao->nome}}</td>
                                    </tr>
                                @endif
                            </table>
                        @endslot
                    @endcardater
                @endforeach
            @endslot
        @endcardater
    @endif

    @if(count($unidadeProdutiva->instalacoes) > 0)
        @cardater(['title'=>'Infra-Estrutura'])
            @slot('body')
                @foreach ($unidadeProdutiva->instalacoes as $k=>$v)
                    @cardater(['title'=> $v->instalacaoTipo->nome, 'class'=>'card-custom-border'])
                        @slot('body')
                            <table class="table table-hover">
                                @if ($v->instalacaoTipo)
                                    <tr>
                                        <th width="20%">Tipo</th>
                                        <td>{{$v->instalacaoTipo ? $v->instalacaoTipo->nome : ''}}</td>
                                    </tr>
                                @endif
                                @if ($v->descricao)
                                    <tr>
                                        <th>Descrição</th>
                                        <td>{{$v->descricao}}</td>
                                    </tr>
                                @endif
                                @if ($v->quantidade)
                                    <tr>
                                        <th>Quantidade</th>
                                        <td>{{$v->quantidade}}</td>
                                    </tr>
                                @endif
                                @if ($v->area)
                                    <tr>
                                        <th>Área (Hectares)</th>
                                        <td>{{$v->area}}</td>
                                    </tr>
                                @endif
                                @if ($v->observacao)
                                    <tr>
                                        <th>Observação</th>
                                        <td>{{$v->observacao}}</td>
                                    </tr>
                                @endif
                                @if ($v->localizacao)
                                    <tr>
                                        <th>Localização</th>
                                        <td>{{$v->localizacao}}</td>
                                    </tr>
                                @endif
                            </table>
                        @endslot
                    @endcardater
                @endforeach
            @endslot
        @endcardater
    @endif

    @cardater(['title'=>'Pressões Sociais'])
        @slot('body')
            <table class="table table-hover">
                <tr>
                    <th width="20%">Sente pressões sociais e urbanas?</th>
                    <td>{{boolean_sim_nao_sem_resposta($unidadeProdutiva->fl_pressao_social)}}</td>
                </tr>
                <tr>
                    <th>Pressões Sociais</th>
                    <td>{{join(", ", $unidadeProdutiva->pressaoSociais->pluck('nome')->toArray())}}</td>
                </tr>
                <tr>
                    <th>Pressão Social - Descrição</th>
                    <td>{{$unidadeProdutiva->pressao_social_descricao}}</td>
                </tr>
            </table>
        @endslot
    @endcardater

    @cardater(['title'=>'Arquivos'])
        @php
            $arquivos = $unidadeProdutiva->arquivos;
        @endphp

        @slot('body')
            <table class="table table-hover">
                <tr>
                    <th width="20%">Croqui</th>
                    <td>{{$unidadeProdutiva->croqui_propriedade}}</td>
                </tr>
            </table>

            @if ($arquivos && count($arquivos) > 0)
                @component('backend.components.table-files.index', ['arquivos'=> $arquivos]) @endcomponent
            @else
                <p>Nenhum arquivo foi adicionado.</p>
            @endif
        @endslot
    @endcardater

    <div class="row mb-4">
        <div class="col">
            {{ form_cancel(App\Helpers\General\AppHelper::prevUrl(route('admin.core.unidade_produtiva.index')), 'Voltar', 'btn btn-danger px-4') }}
        </div>

        @can('edit same operational units productive units')
            <div class="col text-right">
                <a href="{{route('admin.core.unidade_produtiva.edit', ['unidadeProdutiva'=>$unidadeProdutiva])}}" class="btn btn-primary px-5" form="form-builder">Editar</a>
            </div>
        @endcan
    </div>

@endsection

@push('after-styles')
    <link href="{{ asset('css/leaflet.css') }}" rel="stylesheet" />
@endpush

@push('after-scripts')
    <script type="text/javascript" src="{{ asset('js/leaflet.js') }}"></script>
@endpush

@push('after-scripts')
    <style>
        .map-lat-lng #map-content {
            height:300px;
        }
    </style>

    <script>
        try {
            var unidadeProdutiva = JSON.parse('{!!json_encode($unidadeProdutiva)!!}');

            if (unidadeProdutiva){
                var map = L.map('map-content').setView([unidadeProdutiva.lat, unidadeProdutiva.lng], 13);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                L.marker([unidadeProdutiva.lat, unidadeProdutiva.lng], { title: unidadeProdutiva.nome, draggable:false }).addTo(map);
            }
        } catch(e) {

        }
    </script>
@endpush
