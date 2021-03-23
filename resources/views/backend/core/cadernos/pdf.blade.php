@extends('backend.layouts.pdf')

@section('content')
    <div class="mb-4">
        <div class="pdf-title">Caderno de Campo</div>
        <div class="pdf-text">Abaixo informações sobre o caderno de campo aplicado.</div>
    </div>

    @cardater(['title' => 'Informações principais'])
        @slot('body')
            <table class="table table-hover">
                <tr>
                    <th>Protocolo</th>
                    <td>{{$caderno->protocolo}}</td>
                </tr>

                <tr>
                    <th width="400px">Modelo</th>
                    <td>{{$caderno->template->nome}}</td>
                </tr>

                <tr>
                    <th>Produtor/a</th>
                    <td>{{$caderno->produtor->nome}}</td>
                </tr>

                <tr>
                    <th>Unidade Produtiva</th>
                    <td>{{$caderno->unidadeProdutiva->nome}}</td>
                </tr>

                <tr>
                    <th>Técnico</th>
                    <td>{{$caderno->usuario->full_name}}</td>
                </tr>

                <tr>
                    <th>Status</th>

                    <td>
                        <h5>
                            <span class="badge {{$caderno->status == \App\Enums\CadernoStatusEnum::Rascunho ? 'badge-danger' : 'badge-primary'}}">{{\App\Enums\CadernoStatusEnum::toSelectArray()[$caderno->status]}}</span>
                        </h5>
                    </td>
                </tr>

                @foreach ($perguntas as $pergunta)
                    @if(@$pergunta->resposta)
                        <tr>
                            <th>{{$pergunta->pergunta}}</th>
                            {{-- <td>{{@$pergunta->resposta}}</td> --}}
                            <td>{!!@nl2br($pergunta->resposta)!!}</td>
                        </tr>
                    @endif
                @endforeach

                <tr>
                    <th>Criado em</th>
                    <td>{{$caderno->created_at_formatted }}</td>
                </tr>
                <tr>
                    <th>Atualizado em</th>
                    <td>{{ $caderno->updated_at_formatted }}</td>
                </tr>
                <tr>
                    <th>Finalizado em</th>
                    <td>{{ $caderno->finished_at_formatted }}</td>
                </tr>
                <tr>
                    <th>Finalizado por</th>
                    <td>{{ @$caderno->usuarioFinish->full_name}}</td>
                </tr>
            </table>
        @endslot
    @endcardater

    @if (count($caderno->arquivos) > 0)
        @cardater(['title' => 'Fotos e Anexos', 'class'=>'card-custom-border'])
            @slot('body')
                <table class='table'>
                    <tr>
                        <th>Arquivo</th>
                        <th>Descrição</th>
                    </tr>
                    @foreach ($caderno->arquivos as $arquivo)
                        <tr>
                            <td width="30%"><a href="{{@$arquivo->arquivo ? \Storage::url($arquivo->arquivo) : null}}" target="_blank">{{$arquivo->nome}}</a></td>
                            <td>{{$arquivo->descricao}}</td>
                        </tr>
                    @endforeach
                </table>
            @endslot
        @endcardater
    @endif
@endsection
