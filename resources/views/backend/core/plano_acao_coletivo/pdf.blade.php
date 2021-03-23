@extends('backend.layouts.pdf')

@section('content')
    @cardater(['title' => 'Informações gerais'])
        @slot('body')
            <table class="table-pdf">
                <tr>
                    <th>Nome</th>
                    <td>{{$planoAcao->nome}}</td>
                </tr>

                <tr>
                    <th>Status</th>
                    <td>{{\App\Enums\PlanoAcaoStatusEnum::toSelectArray()[$planoAcao->status]}}</td>
                </tr>

                <tr>
                    <th>Prazo</th>
                    <td>{{$planoAcao->prazo_formatted}}</td>
                </tr>

                <tr>
                    <th>Criado por</th>
                    <td>{{@$planoAcao->usuario->fullname}}</td>
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

    @cardater(['title' => 'Unidades Produtivas'])
        @slot('body')
            <table id="table-unidades-produtivas" class="table-pdf">
                <tr>
                    <th>Produtor/a</th>
                    <th>Unid. Produtiva</th>
                    <th><img class="icon-status" src='img/backend/select/nao_iniciado.png'/> Não iniciado</th>
                    <th><img class="icon-status" src='img/backend/select/em_andamento.png'/> Em Andamento</th>
                    <th><img class="icon-status" src='img/backend/select/cancelado.png'/> Cancelado</th>
                    <th><img class="icon-status" src='img/backend/select/concluido.png'/> Concluído</th>
                </tr>

                @foreach($planoAcao->plano_acao_filhos_with_count_status()->with(['unidade_produtiva', 'produtor'])->get() as $rowItem)
                    <tr>
                        <td>{{$rowItem->produtor->nome }}</td>
                        <td>{{$rowItem->unidade_produtiva->nome }}</td>
                        <td>{{$rowItem->nao_iniciado . ' ' . App\Helpers\General\AppHelper::toPerc($rowItem->nao_iniciado, $rowItem->total)}}</td>
                        <td>{{$rowItem->em_andamento . ' ' . App\Helpers\General\AppHelper::toPerc($rowItem->em_andamento, $rowItem->total)}}</td>
                        <td>{{$rowItem->cancelado . ' ' . App\Helpers\General\AppHelper::toPerc($rowItem->cancelado, $rowItem->total)}}</td>
                        <td>{{$rowItem->concluido . ' ' . App\Helpers\General\AppHelper::toPerc($rowItem->concluido, $rowItem->total)}}</td>
                    </tr>
                @endforeach
            </table>
        @endslot
    @endcardater

    @cardater(['title' => 'Ações coletivas cadastradas'])
        @slot('body')
            <table id="table-coletivas" class="table-pdf">
                <tr>
                    <th width="80">Prioridade</th>
                    <th>Ação</th>
                    <th>Prazo</th>
                    <th>Não Iniciado</th>
                    <th>Em Andamento</th>
                    <th>Cancelado</th>
                    <th>Concluído</th>
                    <th>Status Geral</th>
                </tr>
                @foreach($planoAcao->itens_with_count_status()->get() as $rowItem)
                    <tr>
                        <td><img src="{{url('/')}}/img/backend/select/{{$rowItem->prioridade}}.png" style="display:block; margin:auto; padding-right:30px;"/></td>
                        <td>{{$rowItem->descricao}}</td>
                        <td>{{$rowItem->prazo_formatted}}</td>
                        <td>{{$rowItem->nao_iniciado . ' ' . App\Helpers\General\AppHelper::toPerc($rowItem->nao_iniciado, $rowItem->total)}}</td>
                        <td>{{$rowItem->em_andamento . ' ' . App\Helpers\General\AppHelper::toPerc($rowItem->em_andamento, $rowItem->total)}}</td>
                        <td>{{$rowItem->cancelado . ' ' . App\Helpers\General\AppHelper::toPerc($rowItem->cancelado, $rowItem->total)}}</td>
                        <td>{{$rowItem->concluido . ' ' . App\Helpers\General\AppHelper::toPerc($rowItem->concluido, $rowItem->total)}}</td>
                        <td><img src="{{url('/')}}/img/backend/select/{{$rowItem->prioridade}}.png" style="display:block; margin:auto; padding-right:30px;"/></td>
                    </tr>
                @endforeach
            </table>
        @endslot
    @endcardater

    @cardater(['title' => 'Acompanhamento do plano de ação coletivo'])
        @slot('body')
            <table id="table-acompanhamento" class="table-pdf">
                <tr>
                    <th>Acompanhamento</th>
                    <th width="250">Usuário</th>
                    <th width="200">Adicionado em</th>
                </tr>
                @foreach ($planoAcao->historicos as $rowHistorico)
                    <tr>
                        <td>{{$rowHistorico->texto}}</td>
                        <td>{{$rowHistorico->usuario->fullname}}</td>
                        <td>{{$rowHistorico->created_at_formatted}}</td>
                    </tr>
                @endforeach
            </table>
        @endslot
    @endcardater

    @foreach ($planoAcao->plano_acao_filhos_with_count_status()->with(['unidade_produtiva', 'produtor', 'historicos'])->get() as $row)
        @cardater(['title' => 'Unidade Produtiva - '.$row->unidade_produtiva->nome])
            @slot('body')
                <table id="table-unidades-produtivas" class="table-pdf">
                        <tr>
                            <th width="200">Produtor/a</th>
                            <td>{!!$row->produtor->nome. '<br>' . $row->unidade_produtiva->socios!!}</td>
                        </tr>
                        <tr>
                            <th>Unid. Produtiva</th>
                            <td>{{$row->unidade_produtiva->nome}}</td>
                        </tr>
                        <tr>
                            <th><img class="icon-status" src='{{url('/')}}/img/backend/select/nao_iniciado.png'/> Não iniciado</th>
                            <td>{{$row->nao_iniciado . ' ' . App\Helpers\General\AppHelper::toPerc($row->nao_iniciado, $row->total)}}</td>
                        </tr>
                        <tr>
                            <th><img class="icon-status" src='{{url('/')}}/img/backend/select/em_andamento.png'/> Em Andamento</th>
                            <td>{{$row->em_andamento . ' ' . App\Helpers\General\AppHelper::toPerc($row->em_andamento, $row->total)}}</td>
                        </tr>
                        <tr>
                            <th><img class="icon-status" src='{{url('/')}}/img/backend/select/cancelado.png'/> Cancelado</th>
                            <td>{{$row->cancelado . ' ' . App\Helpers\General\AppHelper::toPerc($row->cancelado, $row->total)}}</td>
                        </tr>
                        <tr>
                            <th><img class="icon-status" src='{{url('/')}}/img/backend/select/concluido.png'/> Concluído</th>
                            <td>{{$row->concluido . ' ' . App\Helpers\General\AppHelper::toPerc($row->concluido, $row->total)}}</td>
                        </tr>
                </table>

                <br/>

                @cardater(['title' => 'Açoes individuais', 'class'=> 'card-ater-black'])
                    @slot('body')
                        @php
                            $acoesIndividuais = App\Models\Core\PlanoAcaoItemModel::where("plano_acao_id", $row->id)->with(['historicos', 'plano_acao:id,nome,unidade_produtiva_id'])->get();
                        @endphp
                        <table class="table-pdf">
                            <tr>
                                <th width="80">Prioridade</th>
                                <th>Ação</th>
                                <th>Prazo</th>
                                <th>Status</th>
                            </tr>
                            @foreach ($acoesIndividuais as $rowItem)
                                <tr>
                                    <td><img src="{{url('/')}}/img/backend/select/{{$rowItem->prioridade}}.png" style="display:block; margin:auto; padding-right:30px;"/></td>
                                    <td>{{$rowItem->descricao}}</td>
                                    <td>{{$rowItem->prazo_formatted}}</td>
                                    <td><img style="width:14px; height:auto;" src="{{url('/')}}/img/backend/select/{{$rowItem->status}}.png"/> <span class="ml-2">{{App\Enums\PlanoAcaoItemStatusEnum::toSelectArray()[$rowItem->status]}}</span></td>
                                </tr>

                                @if (count($rowItem->historicos) > 0)
                                    <tr>
                                        <td colspan="4" style="padding:0px;">
                                            <table class="table-pdf bg-white">
                                                <tr>
                                                    <th>Acompanhamento</th>
                                                    <th>Usuário</th>
                                                    <th>Adicionado em</th>
                                                </tr>
                                                @foreach ($rowItem->historicos as $rowItemHistorico)
                                                    <tr>
                                                        <td>{{$rowItemHistorico->texto}}</td>
                                                        <td>{{$rowItemHistorico->usuario->first_name}}</td>
                                                        <td>{{$rowItemHistorico->created_at_formatted}}</td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </table>
                    @endslot
                @endcardater

                @if (count($row->historicos) > 0)
                    <br/>

                    @cardater(['title' => 'Acompanhamento do plano de ação individual', 'class'=> 'card-ater-black'])
                        @slot('body')
                            <table class="table-pdf">
                                <tr>
                                    <th>Acompanhamento</th>
                                    <th width="250">Usuário</th>
                                    <th width="200">Adicionado em</th>
                                </tr>
                                @foreach ($row->historicos as $rowHistorico)
                                    <tr>
                                        <td>{{$rowHistorico->texto}}</td>
                                        <td>{{$rowHistorico->usuario->fullname}}</td>
                                        <td>{{$rowHistorico->created_at_formatted}}</td>
                                    </tr>
                                @endforeach
                            </table>
                        @endslot
                    @endcardater
                @endif

            @endslot
        @endcardater
    @endforeach
@endsection

@push('after-scripts')
    <style>
        .content-table-expand {
            background-color:#F8F8F8;
        }
    </style>
@endpush
