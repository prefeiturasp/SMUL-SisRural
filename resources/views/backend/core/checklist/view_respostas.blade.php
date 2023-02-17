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
