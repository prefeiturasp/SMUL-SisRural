@extends('backend.layouts.pdf')

@section('content')
    <div class="mb-4">
        <div class="pdf-title">Plano de ação</div>
        <div class="pdf-text">Abaixo informações sobre o plano de ação aplicado.</div>
    </div>

    @cardater(['title' => 'Informações gerais'])
        @slot('body')
            <table class="table-pdf">
                @if ($planoAcao->checklist_unidade_produtiva_id)
                    <tr>
                        <th width="30%">Formulário</th>
                        <td>{{$planoAcao->checklist_unidade_produtiva->checklist->nome}}</td>
                    </tr>
                @endif

                @if ($planoAcao->nome)
                    <tr>
                        <th width="30%">Título</th>
                        <td>{{$planoAcao->nome}}</td>
                    </tr>
                @endif

                @if ($planoAcao->unidade_produtiva)
                    <tr>
                        <th>Produtor/a</th>
                        <td>{{$planoAcao->produtor->nome}}</td>
                    </tr>

                    @if ($planoAcao->unidade_produtiva->socios)
                        <tr>
                            <th>Coproprietários</th>
                            <td>{{$planoAcao->unidade_produtiva->socios}}</td>
                        </tr>
                    @endif

                    <tr>
                        <th>Unidade Produtiva</th>
                        <td>{{$planoAcao->unidade_produtiva->nome}}</td>
                    </tr>
                @endif
            </table>
        @endslot
    @endcardater

    @if (count($planoAcao->itens) > 0)
        @cardater(['title' => 'Ações'])
            @slot('body')
                <table class="table-pdf bg-f8f8f8">
                    <thead>
                        <tr>
                            <th width="80">Prioridade</th>
                            <th>Ação</th>
                            <th>Prazo</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($planoAcao->itens as $item)
                            <tr>
                                <td width="220">
                                    <img class="float-left" src="{{url('/')}}/img/backend/select/{{$item->prioridade}}.png"/>
                                    <span class="float-left ml-2" style="margin-top:1px;">{{App\Enums\PlanoAcaoPrioridadeEnum::toSelectArray()[$item->prioridade]}}</span>
                                </td>
                                <td width="300">{{$item->descricao}}</td>
                                <td width="220">{{$item->prazo_formatted}}</td>
                                <td>
                                    <img class="float-left" src="{{url('/')}}/img/backend/select/{{$item->status}}.png"/>
                                    <span class="float-left ml-2" style="margin-top:1px;">{{App\Enums\PlanoAcaoStatusEnum::toSelectArray()[$item->status]}}</span>
                                </td>
                            </tr>
                            @if (count($item->historicos) > 0)
                                <tr>
                                    <td colspan="5" style="padding:0px;">
                                        <table class="table-pdf table-sm bg-white">
                                            <tr>
                                                <th width="60">&nbsp;</th>
                                                <th width="525">Acompanhamento</th>
                                                <th width="220">Usuário</th>
                                                <th>Adicionado em</th>
                                            </tr>
                                            @foreach ($item->historicos as $kk=>$historico)
                                                <tr>
                                                    <td></td>
                                                    <td>{{$historico->texto}}</td>
                                                    <td>{{$historico->usuario->first_name}}</td>
                                                    <td>{{$historico->created_at_formatted}}</td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            @endslot
        @endcardater
    @endif

    @cardater(['title' => 'Detalhes'])
        @slot('body')
            <table class="table-pdf">
                <tr>
                    <th>Status</th>
                    <td>{{\App\Enums\PlanoAcaoStatusEnum::toSelectArray()[$planoAcao->status]}}</td>
                </tr>

                <tr>
                    <th>Prazo</th>
                    <td>{{$planoAcao->prazo_formatted}}</td>
                </tr>

                <tr>
                    <th>Criado em</th>
                    <td>{{$planoAcao->created_at_formatted }}</td>
                </tr>

                <tr>
                    <th>Atualizado em</th>
                    <td>{{ $planoAcao->updated_at_formatted }}</td>
                </tr>
            </table>
        @endslot
    @endcardater

    @cardater(['title' => 'Acompanhamento do plano de ação'])
        @slot('body')
            <table class="table-pdf">
                <thead>
                    <tr>
                        <th>Acompanhamento</th>
                        <th width="250">Usuário</th>
                        <th width="200">Adicionado em</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($planoAcao->historicos) > 0)
                        @foreach ($planoAcao->historicos as $kk=>$historico)
                            <tr>
                                <td>{{$historico->texto}}</td>
                                <td>{{$historico->usuario->full_name}}</td>
                                <td>{{$historico->created_at_formatted}}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4">
                                Não foi cadastrado nenhum acompanhamento para essa ação.
                            </td>
                        </tr>
                    @endif
            </table>
        @endslot
    @endcardater
@endsection
