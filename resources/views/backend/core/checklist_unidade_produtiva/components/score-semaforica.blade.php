@if (@$data)
    @cardater(['title' => 'Pontuação', 'class'=>'card-pontuacao'])
        @slot('body')
            <table class="table table-ater table-pontuacao table-borderless">
                <tr>
                    <td class="font-weight-bold">
                        Pontuação final
                    </td>
                    <td class="font-weight-bold bg-cinza with-border">
                        {!!$data['pontuacaoFinal']!!}
                    </td>
                    <td>
                        @if (@$data['formula'])
                            <small class="text-muted">Fórmula aplicada: {!! @$data['formula']['plain'] !!}</small>
                        @else
                            &nbsp;
                        @endif
                    </td>
                    <td colspan="5">
                        &nbsp;
                    </td>
                </tr>

                <tr>
                    <td>&nbsp;</td>
                    <td>Verde</td>
                    <td>Amarelo</td>
                    <td>Vermelho</td>
                    <td>Não se aplica</td>
                    <td>Numérica/Escolha Simples</td>
                    <td>Pontuação realizada</td>
                    <td>Nota percentual</td>
                </tr>

                <tr>
                    <td>&nbsp;</td>
                    <td class="bg-verde with-border">{!!$data['coresRespostas']['verde']!!} resposta(s)</td>
                    <td class="bg-amarelo with-border">{!!$data['coresRespostas']['amarelo']!!} resposta(s)</td>
                    <td class="bg-vermelho with-border">{!!$data['coresRespostas']['vermelho']!!} resposta(s)</td>
                    <td class="bg-cinza with-border">{!!$data['coresRespostas']['cinza']!!} resposta(s)</td>
                    <td class="bg-cinza with-border">{!!$data['coresRespostas']['numerica']!!} resposta(s)</td>
                    <td class="bg-cinza with-border">{!!$data['pontuacao']!!}</td>
                    <td class="bg-cinza with-border">{!!$data['pontuacaoPercentual']!!}</td>
                </tr>

                <tr>
                    <td colspan="8">&nbsp;</td>
                </tr>

                <tr>
                    <td class="font-weight-bold">Resultados por categoria</td>
                    <td>Verde</td>
                    <td>Amarelo</td>
                    <td>Vermelho</td>
                    <td>Não se aplica</td>
                    <td>Numérica/Escolha Simples</td>
                    <td>Pontuação realizada</td>
                    <td>Nota percentual</td>
                </tr>

                @foreach ($data['categorias'] as $k=>$v)
                    @if (@$v['coresRespostas'])
                        <tr>
                            <td class="with-border">{!!$v['nome']!!}</td>
                            <td class="bg-verde with-border">{!!$v['coresRespostas']['verde']!!}</td>
                            <td class="bg-amarelo with-border">{!!$v['coresRespostas']['amarelo']!!}</td>
                            <td class="bg-vermelho with-border">{!!$v['coresRespostas']['vermelho']!!}</td>
                            <td class="bg-cinza with-border">{!!$v['coresRespostas']['cinza']!!}</td>
                            <td class="bg-cinza with-border">{!!$v['coresRespostas']['numerica']!!}</td>
                            <td class="bg-cinza with-border">{!!$v['pontuacao']!!}</td>
                            <td class="bg-cinza with-border">{!!$v['pontuacaoPercentual']!!}</td>
                        </tr>
                    @endif
                @endforeach

                {{-- @if (@$data['formula'])
                    <tr>
                        <td colspan="8">&nbsp;</td>
                    </tr>

                    <tr>
                        <td class="font-weight-bold" width="250">
                            Resultado
                        </td>

                        <td class="font-weight-bold bg-cinza with-border">
                            {!! $data['formula']['resultado'] !!}
                        </td>

                        <td colspan="3">
                            <small class="text-muted">Fórmula aplicada: {!! @$data['formula']['plain'] !!}</small>
                        </td>

                        <td colspan="3">&nbsp;</td>
                    </tr>
                @endif --}}
            </table>
            <br/>
        @endslot
    @endcardater
@endif

@if(@!$data['possuiColunaNaoSeAplica'])
    @push('before-styles')
        <style>
            .table-pontuacao tr td:nth-child(5){
                display:none;
            }
        </style>
    @endpush
@endif

{{-- @if(@!$data['possuiColunaNumerica'])
    @push('before-styles')
        <style>
            .table-pontuacao tr td:nth-child(6){
                display:none;
            }
        </style>
    @endpush
@endif --}}
