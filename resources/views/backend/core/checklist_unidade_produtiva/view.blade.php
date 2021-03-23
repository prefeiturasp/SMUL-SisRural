@extends('backend.layouts.app')

@section('content')
    @cardater(['title' => 'Informações principais'])
        @slot('headerRight')
            <div class="float-right">
                @if (@$checklistUnidadeProdutiva->status == \App\Enums\CadernoStatusEnum::Rascunho)
                    @can('update', @$checklistUnidadeProdutiva)
                        <a href="{{route('admin.core.checklist_unidade_produtiva.edit', ['checklistUnidadeProdutiva'=>$checklistUnidadeProdutiva])}}" class="btn btn-primary px-5" form="form-builder">Editar formulário aplicado</a>
                    @endcan
                @endif

                @if (@$checklistUnidadeProdutiva->plano_acao_principal)
                    <a href="{{route('admin.core.plano_acao.view', $checklistUnidadeProdutiva->plano_acao_principal->id)}}" class="btn btn-primary px-5 ml-3">Ver plano de ação</a>
                @endif
            </div>
        @endslot

        @slot('body')
            <table class="table table-hover">
                <tr>
                    <th width="30%">Título</th>
                    <td>{{$checklistUnidadeProdutiva->checklist->nome}}</td>
                </tr>

                <tr>
                    <th>Produtor/a</th>
                    <td>{{$checklistUnidadeProdutiva->produtor->nome}}</td>
                </tr>

                @if(@$checklistUnidadeProdutiva->unidade_produtiva->socios)
                    <tr>
                        <th>Coproprietários</th>
                        <td>{{$checklistUnidadeProdutiva->unidade_produtiva->socios}}</td>
                    </tr>
                @endif

                <tr>
                    <th>Unidade Produtiva</th>
                    <td>{{$checklistUnidadeProdutiva->unidade_produtiva->nome}}</td>
                </tr>

                <tr>
                    <th>Técnico</th>
                    <td>{{@$checklistUnidadeProdutiva->usuario->full_name}}</td>
                </tr>
            </table>
        @endslot
    @endcardater

    @foreach ($categorias as $k => $categoria)
         @if (count($categoria->perguntas) > 0)
            @cardater(['title' => $categoria->nome, 'class'=>'card-custom-border'])
                @slot('body')
                    <table class='table'>
                        {{-- @if(@count($pdaItens) > 0)
                            <tr>
                                <th>Pergunta</th>
                                <th>Resposta</th>
                                <th>Ação recomendada pelo técnico</th>
                                <th>Prioridade</th>
                            </tr>
                        @endif --}}

                        @foreach ($categoria->perguntas as $pergunta)
                            <tr>
                                <th width="30%">{!!$pergunta->pergunta.($pergunta->pivot->fl_obrigatorio ? '<span class="text-danger">*</span>' : '')!!}</th>

                                @if ($pergunta->resposta)
                                    @if($pergunta->resposta_cor)
                                        <td>
                                            <div class="badge-ater {{'badge-'.$pergunta->resposta_cor}}">
                                                    {!!@$pergunta->resposta!!}
                                            </div>
                                        </td>
                                    @elseif ($pergunta->tipo_pergunta == App\Enums\TipoPerguntaEnum::Anexo)
                                        <td>
                                            {!! App\Helpers\General\DatatablesHelper::renderColumnFile(basename($pergunta->resposta), \Storage::url($pergunta->resposta)) !!}
                                            {{-- <a href="{{\Storage::url($pergunta->resposta)}}" target="_blank">Visualizar arquivo</a> --}}
                                        </td>
                                    @else
                                        <td>{!!@$pergunta->resposta!!}</td>
                                    @endif
                                @else
                                    <td>
                                        &nbsp;
                                    </td>
                                @endif

                                {{-- @if(@count($pdaItens) > 0)
                                    <td>
                                        {{$pdaItens[$pergunta->pivot->id]['descricao']}}
                                    </td>
                                    <td width="80">
                                        <img src="img/backend/select/{{$pdaItens[$pergunta->pivot->id]['prioridade']}}.png" style="display:block; margin:auto;"/>
                                    </td>
                                @endif --}}
                            </tr>
                        @endforeach
                    </table>
                @endslot
            @endcardater
        @endif
    @endforeach

    @if (count($arquivos)> 0 || $checklistUnidadeProdutiva->checklist->fl_gallery)
        @cardater(['title'=>'Fotos e Anexos'])
            @slot('body')
                @if ($arquivos && count($arquivos) > 0)
                    @component('backend.components.table-files.index', ['arquivos'=> $arquivos]) @endcomponent
                @else
                    <p>Nenhum arquivo foi adicionado.</p>
                @endif
            @endslot
        @endcardater
    @endif

    @component('backend.core.checklist_unidade_produtiva.components.score', ['data'=>$score])
    @endcomponent

    @cardater(['title' => 'Detalhes'])
        @slot('body')
            <table class="table table-hover">
                <tr>
                    <th width="30%">Status</th>
                    <td>{{\App\Enums\ChecklistStatusEnum::toSelectArray()[$checklistUnidadeProdutiva->status]}}</td>
                </tr>

                <tr>
                    <th>Criado em</th>
                    <td>{{$checklistUnidadeProdutiva->created_at_formatted }}</td>
                </tr>

                <tr>
                    <th>Atualizado em</th>
                    <td>{{ $checklistUnidadeProdutiva->updated_at_formatted }}</td>
                </tr>

                <tr>
                    <th>Finalizado em</th>
                    <td>{{ @$checklistUnidadeProdutiva->finished_at_formatted }}</td>
                </tr>
                <tr>
                    <th>Finalizado por</th>
                    <td>{{ @$checklistUnidadeProdutiva->usuarioFinish->full_name}}</td>
                </tr>
            </table>
        @endslot
    @endcardater

    @if (@count($pdaItens) > 0)
        @cardater(['title' => 'Plano de ação'])
            @slot('body')
                <table class="table table-hover">
                    <tr>
                        <th>Prioridade</th>
                        <th>Pergunta</th>
                        <th>Resposta</th>
                        <th>Ação Recomendada</th>
                        <th>Detalhamento da Ação</th>
                    </tr>

                    @foreach ($pdaItens as $row)
                        <tr>
                            <td>
                                <img src="img/backend/select/{{$row->prioridade}}.png" style="display:block; margin:auto;"/>
                            </td>
                            <td>
                                {{$row->checklist_pergunta->pergunta->pergunta}}
                            </td>
                            <td>
                                @php
                                    $resposta = $row->checklist_snapshot_resposta;
                                    // $resposta = @$respostas[$row->checklist_snapshot_resposta->pergunta_id]['resposta'];
                                    if (@$resposta) {
                                        //vem do formulário
                                        $resposta = @$resposta->resposta_id ? $resposta->respostas()->pluck('descricao')->implode(",") : $resposta->resposta;
                                    } else {
                                        //vem da unidade produtiva
                                        $resposta = @$respostas[$row->checklist_pergunta->pergunta_id]['resposta'];
                                    }
                                @endphp

                                {{$resposta}}
                            </td>
                            <td>
                                {{$row->checklist_pergunta->pergunta->plano_acao_default}}
                            </td>
                            <td>
                                {{$row->checklist_pergunta->pergunta->plano_acao_default == $row->descricao ? '' : $row->descricao}}
                            </td>
                        </tr>
                    @endforeach
                </table>
            @endslot
        @endcardater
    @endif

    @component('backend.core.checklist_unidade_produtiva.components.analises', ['analises'=>$analises, 'analiseForm'=>$analiseForm, 'checklistUnidadeProdutiva' => @$checklistUnidadeProdutiva])
    @endcomponent

    <div class="row mb-4">
        <div class="col">
            {{ form_cancel($back, 'Voltar', 'btn btn-danger px-4') }}
        </div>

        @if (@$checklistUnidadeProdutiva->status == \App\Enums\ChecklistStatusEnum::Rascunho)
            @can('update', $checklistUnidadeProdutiva)
                <div class="col text-right">
                    <a href="{{route('admin.core.checklist_unidade_produtiva.edit', ['checklistUnidadeProdutiva'=>$checklistUnidadeProdutiva])}}" class="btn btn-primary px-5" form="form-builder">Editar</a>
                </div>
            @endcan
        @endif

        @if (@$checklistUnidadeProdutiva->status == \App\Enums\ChecklistStatusEnum::AguardandoAprovacao)
            @can('analize', $checklistUnidadeProdutiva)
                <div class="col text-right">
                    <button type="submit" class="btn btn-primary px-5" form="form-builder">Salvar</button>
                </div>
            @endcan
        @endif

    </div>
@endsection
