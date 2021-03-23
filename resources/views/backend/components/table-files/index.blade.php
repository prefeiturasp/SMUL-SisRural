<table class="table table-hover">
    <tr>
        <th>Nome</th>
        <th>Arquivo</th>
        <th>Descrição</th>
        {{-- <th>Latitude</th> --}}
        {{-- <th>Longitude</th> --}}
    </tr>

    @foreach ($arquivos as $k=>$arquivo)
        @php
            if (!@$arquivo->arquivo) {
                continue;
            }

            $nome = @$arquivo->nome ? $arquivo->nome : $arquivo->arquivo;
            $ext = explode(".", $nome);
            $ext = end($ext);
        @endphp

        <tr>
            <td width="20%">
                <a aria-label="Ampliar Imagem"  href="{{\Storage::url($arquivo->arquivo)}}" target="_blank">{{$nome}}</a>
            </td>

            <td>
                @if ($arquivo->tipo == 'imagem')
                    <a aria-label="Ampliar Imagem" href="{{\Storage::url($arquivo->arquivo)}}" target="_blank">
                        <img alt="{{$arquivo->descricao ?? 'Imagem sem descrição'}}" src="{{\Storage::url($arquivo->arquivo)}}" width="100"/>
                    </a>
                @else
                    @include('backend.components.icon-file.index', ['ext'=>$ext])
                @endif
            </td>

            <td>{{$arquivo->descricao}}</td>
            {{-- <td>{{$arquivo->lat}}</td> --}}
            {{-- <td>{{$arquivo->lng}}</td> --}}
        </tr>
    @endforeach
</table>
