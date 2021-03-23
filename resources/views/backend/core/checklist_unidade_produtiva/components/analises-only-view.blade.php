@cardater(['title' => 'Análise', 'class'=>'card-analise'])
    @slot('body')
        <table class="table table-ater table-analise">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Observação</th>
                    <th>Data</th>
                    <th>Status</th>
            </thead>

            <tbody>
                @if (@$analises->count())
                    @foreach ($analises as $analise)
                        <tr>
                            <th>{{$analise->usuario->full_name}}</th>
                            <th>{{$analise->message}}</th>
                            <th>{{$analise->created_at_formatted}}</th>
                            <th>{{$analise->status ? @\App\Enums\ChecklistStatusFlowEnum::toSelectArray()[$analise->status] : 'Aguardando Aprovação'}}</th>
                        </tr>
                    @endforeach
                @else
                        <tr><th colspan="4" class="text-center">Não há histórico até o momento</th></tr>
                @endif
            </tbody>
        </table>
    @endslot
@endcardater
