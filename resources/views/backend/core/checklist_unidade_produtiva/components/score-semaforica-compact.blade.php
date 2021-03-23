@if (@$data)
    <table class="table table-ater table-pontuacao table-borderless">
        <tr>
            <td class="font-weight-bold">
                Pontuação final
            </td>

            <td class="font-weight-bold bg-cinza with-border">
                {!!$data['pontuacaoFinal']!!}
            </td>
        </tr>

        @if ($data['formula'])
            <tr>
                <td class="font-weight-bold">Fórmula aplicada</td>
                <td class="bg-cinza with-border">{!!$data['formula']['plain']!!} </td>
            </tr>
        @endif

        <tr>
            <td colspan="2">&nbsp;</td>
        </tr>

        <tr>
            <td>Verde</td>
            <td class="bg-verde with-border">{!!$data['coresRespostas']['verde']!!} resposta(s)</td>
        </tr>

        <tr>
            <td>Amarelo</td>
            <td class="bg-amarelo with-border">{!!$data['coresRespostas']['amarelo']!!} resposta(s)</td>
        </tr>

        <tr>
            <td>Vermelho</td>
            <td class="bg-vermelho with-border">{!!$data['coresRespostas']['vermelho']!!} resposta(s)</td>
        </tr>

        <tr>
            <td>Não se aplica</td>
            <td class="bg-cinza with-border">{!!$data['coresRespostas']['cinza']!!} resposta(s)</td>
        </tr>

        <tr>
            <td>Numérica/Escolha Simples</td>
            <td class="bg-cinza with-border">{!!$data['coresRespostas']['numerica']!!} resposta(s)</td>
        </tr>

        <tr>
            <td class="font-weight-bold">
                Pontuação realizada
            </td>

            <td class="bg-cinza with-border">
                {!!$data['pontuacao']!!}
            </td>
        </tr>

        <tr>
            <td class="font-weight-bold">
                Nota percentual
            </td>

            <td class="bg-cinza with-border">
                {!!$data['pontuacaoPercentual']!!}
            </td>
        </tr>

        <tr>
            <td colspan="2">&nbsp;</td>
        </tr>

        <tr>
            <td colspan="2" class="font-weight-bold">Resultados por categoria</td>
        </tr>

        @foreach ($data['categorias'] as $k=>$v)
            @if (@$v['coresRespostas'])
                <tr>
                    <td>Categoria</td>
                    <td class="with-border">{!!$v['nome']!!}</td>
                </tr>
                <tr>
                    <td>Verde</td>
                    <td class="bg-verde with-border">{!!$v['coresRespostas']['verde']!!}</td>
                </tr>
                <tr>
                    <td>Amarelo</td>
                    <td class="bg-amarelo with-border">{!!$v['coresRespostas']['amarelo']!!}</td>
                </tr>
                <tr>
                    <td>Vermelho</td>
                    <td class="bg-vermelho with-border">{!!$v['coresRespostas']['vermelho']!!}</td>
                </tr>
                <tr>
                    <td>Cinza</td>
                    <td class="bg-cinza with-border">{!!$v['coresRespostas']['cinza']!!}</td>
                </tr>
                <tr>
                    <td>Numérica/Escolha Simples</td>
                    <td class="bg-cinza with-border">{!!$v['coresRespostas']['numerica']!!}</td>
                </tr>
                <tr>
                    <td class="font-weight-bold">Pontuação realizada</td>
                    <td class="bg-cinza with-border">{!!$v['pontuacao']!!}</td>
                </tr>
                <tr>
                    <td class="font-weight-bold">Nota percentual</td>
                    <td class="bg-cinza with-border">{!!$v['pontuacaoPercentual']!!}</td>
                </tr>
                <tr>
                    <td colspan="2">&nbsp;</td>
                </tr>
            @endif
        @endforeach

        {{-- @if (@$data['formula'])
            <tr>
                <td>Resultado</td>
                <td class="bg-cinza with-border">{!!$data['formula']['resultado']!!} </td>
            </tr>
            <tr>
                <td>Fórmula aplicada</td>
                <td class="bg-cinza with-border">{!!$data['formula']['plain']!!} </td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
        @endif --}}

    </table>
    <br/>
@endif
