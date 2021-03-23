@extends('backend.layouts.app')

@section('content')
    @cardater(['title'=>'Dados de Acesso', 'titleTag'=>'h2'])
         @slot('body')
            <table class="table table-hover">
                <tr>
                    <th>Token</th>
                    <td>Token informado no momento da edição/cadastro do acesso.<br>Após salvar ele será encriptado, não será mais possível recuperar qual foi o token digitado.</td>
                </tr>
                <tr>
                    <th>Endpoint</th>
                    <td>{{url('/api/dados/unidades_produtivas')}}</td>
                </tr>
                <tr>
                    <th>Método</th>
                    <td>POST</td>
                </tr>
                <tr>
                    <th>Headers</th>
                    <td>
                        <table>
                            <tr>
                                <td width="200">Content-Type</td>
                                <td>application/json</td>
                            </tr>
                            <tr>
                                <td>X-Requested-With</td>
                                <td>XMLHttpRequest</td>
                            </tr>
                            <tr>
                                <td>Authorization</td>
                                <td>Bearer TOKEN_DE_ACESSO</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <th>Parâmetros</th>
                    <td>
                        <table>
                            <tr>
                                <td width="200">page</td>
                                <td>NUMERO_DA_PAGINA</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <th>Exemplo de chamada</th>
                    <td>curl --location --request POST '{{url('/api/dados/unidades_produtivas')}}' <br>
                        --header 'Content-Type: application/json' <br>
                        --header 'X-Requested-With: XMLHttpRequest' <br>
                        --header 'Authorization: Bearer TOKEN_DE_ACESSO' <br>
                        --form 'page=1'</td>
                </tr>
            </table>
        @endslot
    @endcardater

    <div class="row mb-4">
        <div class="col">
            {{ form_cancel(App\Helpers\General\AppHelper::prevUrl(route('admin.core.dado.index')), 'Voltar', 'btn btn-danger px-4') }}
        </div>
    </div>
@endsection
