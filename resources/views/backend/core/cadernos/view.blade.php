@extends('backend.layouts.app')

@section('content')
    @cardater(['title'=> $title,'titleTag'=>'h1'])
        @if (@$caderno->status == \App\Enums\CadernoStatusEnum::Rascunho)
            @can('update', $caderno)
                 @slot('headerRight')
                    <div class="float-right">
                        <a href="{{route('admin.core.cadernos.edit', ['caderno'=>$caderno])}}" class="btn btn-primary px-5" form="form-builder">Editar</a>
                    </div>
                @endslot
            @endcan
        @endif

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
                    <th>Técnico/a</th>
                    <td>
                    @php
                        foreach($caderno->tecnicas as $tecnica){
                            $tecnicas_str[] = $tecnica->first_name;
                        }
                        echo implode(", ", $tecnicas_str);
                    @endphp
                    </td>
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
                            {{-- <td>{{$pergunta->resposta}}</td> --}}
                            <td>{!!@nl2br($pergunta->resposta)!!}</td>
                        </tr>
                    @endif
                @endforeach

                <tr>
                    <th>Criado em</th>
                    <td>{{$caderno->created_at_formatted }}</td>
                </tr>
                {{-- <tr>
                    <th>Atualizado em</th>
                    <td>{{ $caderno->updated_at_formatted }}</td>
                </tr> --}}
                <tr>
                    <th>Finalizado em</th>
                    <td>{{ @$caderno->finished_at_formatted }}</td>
                </tr>
                <tr>
                    <th>Finalizado por</th>
                    <td>{{ @$caderno->usuarioFinish->full_name}}</td>
                </tr>
            </table>

            <div class="map-lat-lng">
                <div id="map-content"></div>
            </div>
        @endslot
    @endcardater

    @cardater(['title'=>'Arquivos'])
        @slot('body')
            @if ($arquivos && count($arquivos) > 0)
                @component('backend.components.table-files.index', ['arquivos'=> $arquivos]) @endcomponent
            @else
                <p>Nenhum arquivo foi adicionado.</p>
            @endif
        @endslot
    @endcardater

    <div class="row mb-4">
        <div class="col">
            {{ form_cancel($back, 'Voltar', 'btn btn-danger px-4') }}
        </div>

        @can('update', $caderno)
           @if (@$caderno->status == \App\Enums\CadernoStatusEnum::Rascunho)
                <div class="col text-right">
                    <a href="{{route('admin.core.cadernos.edit', ['caderno'=>$caderno])}}" class="btn btn-primary px-5" form="form-builder">Editar</a>
                </div>
            @endif
        @endcan
    </div>
@endsection
