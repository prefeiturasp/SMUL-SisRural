@extends('backend.layouts.pdf')

@section('content')
    <div class="mb-4">
        <div class="pdf-title">Aplicação de Formulário</div>
        <div class="pdf-text">Abaixo informações sobre o formulário aplicado.</div>
    </div>

    @cardater(['title' => 'Informações principais'])
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

                @if (@$checklistUnidadeProdutiva->unidade_produtiva->socios)
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
                        @foreach ($categoria->perguntas as $pergunta)
                            <tr>
                                <th width="30%">{{$pergunta->pergunta}}</th>

                                @if ($pergunta->resposta)
                                    @if($pergunta->resposta_cor)
                                        <td>
                                            <div class="badge-ater {{'badge-'.$pergunta->resposta_cor}}">
                                                    {!!@$pergunta->resposta!!}
                                            </div>
                                        </td>
                                    @elseif ($pergunta->tipo_pergunta == App\Enums\TipoPerguntaEnum::Anexo)
                                        <td>
                                            <a href="{{\Storage::url($pergunta->resposta)}}" target="_blank">Visualizar arquivo</a>
                                        </td>
                                    @else
                                        <td>{!!@$pergunta->resposta!!}</td>
                                    @endif
                                @else
                                    <td>
                                        &nbsp;
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </table>
                @endslot
            @endcardater
        @endif
    @endforeach

    @if (count($checklistUnidadeProdutiva->arquivos) > 0)
        @cardater(['title' => 'Fotos e Anexos', 'class'=>'card-custom-border'])
            @slot('body')
                <table class='table'>
                    <tr>
                        <th>Arquivo</th>
                        <th>Descrição</th>
                    </tr>
                    @foreach ($checklistUnidadeProdutiva->arquivos as $arquivo)
                        <tr>
                            <td width="30%"><a href="{{@$arquivo->arquivo ? \Storage::url($arquivo->arquivo) : null}}" target="_blank">{{$arquivo->nome}}</a></td>
                            <td>{{$arquivo->descricao}}</td>
                        </tr>
                    @endforeach
                </table>
            @endslot
        @endcardater
    @endif

    {{-- <div style="page-break-after:always;"></div> --}}

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
@endsection
