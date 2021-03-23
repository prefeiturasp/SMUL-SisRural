@extends('backend.layouts.app')

@section('content')
    @cardater(['title' => 'Formulários'])
        @slot('body')
            <div class="wrap-table-comparar">
                <table class='table table-sm- table-comparar'>
                    <tr class="table-comparar-header">
                        <th class="col-fixed">Pergunta&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                        <th colspan="{{count($checklists)}}">Respostas</th>
                    </tr>

                    <tr class="table-comparar-header">
                        <th class="col-fixed"></td>

                        @foreach ($checklists as $kCheck=>$vCheck)
                            <th>
                                <div>{!! $vCheck['checklist']['nome'] !!}</div>
                                <div class="font-weight-normal">{!! $vCheck['unidade_produtiva']['nome'] !!}</div>
                                <div class="font-weight-normal">{!! $vCheck['created_at_formatted'] !!}</div>
                                <a class="btn-sm btn btn-outline-primary mt-2" href="{{route('admin.core.checklist_unidade_produtiva.view', $vCheck['id'])}}" target="_blank">Visualizar Formulário Aplicado</a>
                            </td>
                        @endforeach
                    </tr>

                    @foreach ($perguntas as $kPergunta=>$pergunta)
                        <tr>
                            <td class="col-fixed">{{$pergunta->pergunta}}</td>

                            @foreach ($checklists as $kCheck=>$vCheck)
                                <td>
                                    @php
                                        $resposta = @$vCheck['respostas'][@$pergunta->id]
                                    @endphp

                                    @if (@$resposta && (@$resposta['resposta'] || @$resposta['resposta_cor']))
                                        <small class="text-muted">{!!$categorias[$resposta['pivot']['checklist_categoria_id']]!!}</small>

                                        <div>
                                            @if($resposta['resposta_cor'])
                                                <div class="badge-ater {{'badge-'.$resposta['resposta_cor']}}">
                                                        {!!@$resposta['resposta']!!}
                                                </div>
                                            @elseif ($resposta['tipo_pergunta'] == App\Enums\TipoPerguntaEnum::Anexo)
                                                <a href="{{\Storage::url($resposta['resposta'])}}" target="_blank">Visualizar arquivo</a>
                                            @else
                                                {!!@$resposta['resposta']!!}
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach


                    {{-- @foreach ($categorias as $kCategoria=>$categoria)
                        @foreach ($categoria->perguntas as $pergunta)
                            <tr>
                                <td class="col-fixed">{{$pergunta->pergunta}}</td>

                                @foreach ($checklists as $kCheck=>$vCheck)
                                    <td>
                                        @php
                                            $resposta = @$vCheck['respostas'][@$pergunta->id]
                                        @endphp

                                        @if (@$resposta && (@$resposta['resposta'] || @$resposta['resposta_cor']))
                                            <small class="text-muted">{!!$categoria->nome!!}</small>

                                            <div>
                                                @if($resposta['resposta_cor'])
                                                    <div class="badge-ater {{'badge-'.$resposta['resposta_cor']}}">
                                                            {!!@$resposta['resposta']!!}
                                                    </div>
                                                @elseif ($resposta['tipo_pergunta'] == App\Enums\TipoPerguntaEnum::Anexo)
                                                    <a href="{{\Storage::url($resposta['resposta'])}}" target="_blank">Visualizar arquivo</a>
                                                @else
                                                    {!!@$resposta['resposta']!!}
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    @endforeach --}}

                    <tr>
                        <td class="col-fixed">&nbsp;</td>

                        @foreach ($checklists as $kCheck=>$vCheck)
                            <td>
                                @component('backend.core.checklist_unidade_produtiva.components.score', ['data'=>$vCheck['score'], 'compact'=>true])
                                @endcomponent
                            </td>
                        @endforeach
                    </tr>
                </table>
            </div>
        @endslot
    @endcardater
@endsection

@push('after-scripts')
    <script type="text/javascript" src="{{ asset('js/table-fixer.jquery.js') }}"></script>

    <script>
        $(function() {
            if ($('body').width() > 500) {
                $(".table-comparar").tableHeadFixer({
                    left: 1,
                    "head": false
                });
            }
        })
    </script>

    <style>
        .wrap-table-comparar {
            position: relative;
            overflow-x: auto;
        }

        .table-comparar td, .table-comparar th {
            min-width: 295px;
        }

        .table-pontuacao td, .table-pontuacao th {
            width:50%;
            min-width: initial;
        }

        .table-comparar {
            display: block;
            border: 0px;
            min-width: 100%;

            height: 64vh;
        }

        .table-comparar a {
            text-decoration: underline;
        }

        .table-comparar .col-fixed {
            z-index: 10;
            width: 300px;
            white-space: normal;
            background-color: #F5F5F5;
        }

        .table-comparar-header,
        .table-comparar-header .col-fixed {
            background-color: #E8E8E8;
        }

        .table-comparar-header-categoria,
        .table-comparar-header-categoria .col-fixed {
            background-color: #F5F5F5;
        }

        .table-comparar-header-unidade-produtiva,
        .table-comparar-header-unidade-produtiva .col-fixed {
            background-color: #F5F5F5;
            color: #FFF;
        }
    </style>
@endpush
