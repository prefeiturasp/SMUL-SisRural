@extends('backend.layouts.app')

@section('content')
    @cardater(['titleTag'=>'h1', 'title' => 'Visualizar Produtor/a: '.$produtor->nome])
    @can('edit same operational units farmers')
        @slot('headerRight')
            <div class="float-right">
                <a href="{{ route('admin.core.produtor.edit', ['produtor' => $produtor]) }}"
                    aria-label="Editar produtor/a: {{ $produtor->nome }}" class="btn btn-primary px-5"
                    form="form-builder">Editar</a>
            </div>
        @endslot
    @endcan
    @endcardater

    @cardater(['title'=>'Dados Básicos', 'titleTag'=>'h2'])
    @slot('body')
        <table class="table table-hover">
            <tr>
                <th width="20%">Nome</th>
                <td>{{ $produtor->nome }}</td>
            </tr>
            <tr>
                <th><abbr title="Cadastro de Pessoa Física">CPF</abbr></th>
                <td>{{ $produtor->cpf }}</td>
            </tr>
            <tr>
                <th>Telefone 1</th>
                <td>{{ $produtor->telefone_1 }}</td>
            </tr>
            <tr>
                <th>Telefone 2</th>
                <td>{{ $produtor->telefone_2 }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>{{ $produtor->status }}</td>
            </tr>
            <tr>
                <th>Status - Observação</th>
                <td>{{ $produtor->status_observacao }}</td>
            </tr>

            <tr>
                <th>Palavras chave</th>
                <td>{!! @App\Helpers\General\AppHelper::tableTags($produtor->tags) !!}</td>
            </tr>

            <tr>
                <th>Criado por</th>
                <td>{{ $produtor->usuario->full_name }}</td>
            </tr>
            <tr>
                <th>Criado em</th>
                <td>{{ $produtor->created_at_formatted }}</td>
            </tr>
            <tr>
                <th>Atualizado em</th>
                <td>{{ $produtor->updated_at_formatted }}</td>
            </tr>
        </table>
    @endslot
    @endcardater

    @cardater(['title'=>'Dados Complementares', 'titleTag'=>'h2'])
    @slot('body')
        <table class="table table-hover">
            <tr>
                <th width="20%">Nome Social</th>
                <td>{{ $produtor->nome_social }}</td>
            </tr>
            <tr>
                <th>E-mail
                </th>
                <td>{{ $produtor->email }}</td>
            </tr>
            <tr>
                <th>Gênero</th>
                <td>{{ $produtor->genero ? $produtor->genero->nome : '' }}</td>
            </tr>
            <tr>
                <th>Cor, Raça e Etnia</th>
                <td>{{ $produtor->etinia ? $produtor->etinia->nome : '' }}</td>
            </tr>
            <tr>
                <th>Portador de Necessidades Especiais?</th>
                <td>{{ boolean_sim_nao_sem_resposta($produtor->fl_portador_deficiencia) }}</td>
            </tr>
            <tr>
                <th>Tipo de Necessidade Especial</th>
                <td>{{ $produtor->portador_deficiencia_obs }}</td>
            </tr>
            <tr>
                <th>Data de Nascimento</th>
                <td>{{ $produtor->data_nascimento ? Carbon\Carbon::parse($produtor->data_nascimento)->format('d/m/Y') : null }}
                </td>
            </tr>
            <tr>
                <th><abbr title="Registro Geral">RG</abbr></th>
                <td>{{ $produtor->rg }}</td>
            </tr>

            @if($produtor->fl_cnpj)
            <tr>
                <th>Possui <abbr title="Cadastro Nacional de Pessoa Jurídica">CNPJ</abbr>?</th>
                <td>{{ boolean_sim_nao_sem_resposta($produtor->fl_cnpj) }}</td>
            </tr>
            <tr>
                <th><abbr title="Cadastro Nacional de Pessoa Jurídica">CNPJ</abbr></th>
                <td>{{ $produtor->cnpj }}</td>
            </tr>
            @endif

            @if($produtor->fl_nota_fiscal_produtor)
            <tr>
                <th>Possui Nota Fiscal de Produtor?</th>
                <td>{{ boolean_sim_nao_sem_resposta($produtor->fl_nota_fiscal_produtor) }}</td>
            </tr>
            <tr>
                <th>Número Nota Fiscal de Produtor</th>
                <td>{{ $produtor->nota_fiscal_produtor }}</td>
            </tr>
            @endif

            @if ($produtor->fl_agricultor_familiar)
            <tr>
                <th>É Agricultor/a Familiar?</th>
                <td>{{ boolean_sim_nao_sem_resposta($produtor->fl_agricultor_familiar) }}</td>
            </tr>
            <tr>
                <th>Possui <abbr title="Declaração de Aptidão ao Pronaf">DAP</abbr>?</th>
                <td>{{ boolean_sim_nao_sem_resposta($produtor->fl_agricultor_familiar_dap) }}</td>
            </tr>
            <tr>
                <th>Número <abbr title="Declaração de Aptidão ao Pronaf">DAP</abbr></th>
                <td>{{ $produtor->agricultor_familiar_numero }}</td>
            </tr>
            <tr>
                <th>Validade <abbr title="Declaração de Aptidão ao Pronaf">DAP</abbr></th>
                <td>{{ \Carbon\Carbon::parse($produtor->agricultor_familiar_data)->format('d/m/Y') }}</td>
            </tr>
            @endif

        </table>
    @endslot
    @endcardater

    @cardater(['title'=>'Assistência Técnica', 'titleTag'=>'h2', 'id'=>'bloco-assistencia-tecnica'])
    @slot('body')
        <table class="table table-hover">
            <tr>
                <th width="20%">Recebe Assistência Técnica?</th>
                <td>{{ boolean_sim_nao_sem_resposta($produtor->fl_assistencia_tecnica) }}</td>
            </tr>
            <tr>
                <th>Qual o Tipo da Assistência Técnica</th>
                <td>{{ $produtor->assistenciaTecnicaTipo ? $produtor->assistenciaTecnicaTipo->nome : '' }}</td>
            </tr>
            <tr>
                <th>Periodicidade da Assistência Técnica</th>
                <td>{{ $produtor->assistencia_tecnica_periodo }}</td>
            </tr>
        </table>
    @endslot
    @endcardater

    @cardater(['title'=>'Outras informações', 'titleTag'=>'h2'])
    @slot('body')
        <table class="table table-hover">            
            <tr>
                <th>Contrata mão-de-obra externa?</th>
                <td>{{ boolean_sim_nao_sem_resposta($produtor->fl_contrata_mao_de_obra_externa) }}</td>
            </tr>
            <tr>
                <th>Para qual o tipo de trabalho contrata mão-de-obra externa?</th>
                <td>{{ $produtor->mao_de_obra_externa_tipo }}</td>
            </tr>
            <tr>
                <th>Periodicidade da contratação de mão-de-obra externa</th>
                <td>{{ $produtor->mao_de_obra_externa_periodicidade }}</td>
            </tr>
            <tr>
                <th>É da Comunidade Tradicional?</th>
                <td>{{ boolean_sim_nao_sem_resposta($produtor->fl_comunidade_tradicional) }}</td>
            </tr>
            <tr>
                <th>Qual Comunidade Tradicional?</th>
                <td>{{ $produtor->comunidade_tradicional_obs }}</td>
            </tr>
            <tr>
                <th>Acessa a Internet?</th>
                <td>{{ boolean_sim_nao_sem_resposta($produtor->fl_internet) }}</td>
            </tr>
            <tr>
                <th>Participa de Cooperativa, Associação, Rede, Movimento ou Coletivo?</th>
                <td>{{ boolean_sim_nao_sem_resposta($produtor->fl_tipo_parceria) }}</td>
            </tr>
            <tr>
                <th>Qual?</th>
                <td>{{ $produtor->tipo_parcerias_obs }}</td>
            </tr>
            <tr>
                <th>% da renda advinda da agricultura</th>
                <td>{{ $produtor->rendaAgricultura ? $produtor->rendaAgricultura->nome : '' }}</td>
            </tr>
            <tr>
                <th>Rendimento da comercialização</th>
                <td>{{ $produtor->rendimentoComercializacao ? $produtor->rendimentoComercializacao->nome : '' }}</td>
            </tr>
            <tr>
                <th>Outras fontes de renda</th>
                <td>{{ $produtor->outras_fontes_renda }}</td>
            </tr>
            <tr>
                <th>Grau de instrução</th>
                <td>{{ $produtor->grauInstrucao ? $produtor->grauInstrucao->nome : '' }}</td>
            </tr>
            <tr>
                <th>Situação social</th>
                <td>{{ $produtor->situacaoSocial->nome }}</td>
            </tr>
            <tr>
                <th>Reside na Unidade Produtiva?</th>
                <td>{{ boolean_sim_nao_sem_resposta($produtor->fl_reside_unidade_produtiva) }}</td>
            </tr>

            @if($produtor->fl_reside_unidade_produtiva === 0)
            <tr id='cep'>
                <th><abbr title="Código de Endereçamento Postal">CEP</abbr></th>
                <td>{{ $produtor->cep }}</td>
            </tr>
            <tr>
                <th>Endereço</th>
                <td>{{ $produtor->endereco }}</td>
            </tr>
            <tr>
                <th>Bairro</th>
                <td>{{ $produtor->bairro }}</td>
            </tr>
            <tr>
                <th>Distrito</th>
                <td>{{ $produtor->subprefeitura }}</td>
            </tr>
            @endif

            <tr>
                <th>Município</th>
                <td>{{ $produtor->cidade ? $produtor->cidade->nome : '' }}</td>
            </tr>            
            <tr>
                <th>Estado</th>
                <td>{{ $produtor->estado ? $produtor->estado->nome : '' }}</td>
            </tr>

        </table>
    @endslot
    @endcardater


    @cardater(['title'=>'Unidades Produtivas', 'titleTag'=>'h2'])
    @slot('body')
        <table class="table table-hover">
            <tr>
                <th width="20%">Nome</th>
                <th>Tipo da Posse</th>
            </tr>

            @foreach ($produtor->unidadesProdutivas as $k => $v)
                <tr>
                    <td>{{ $v->nome }}</td>
                    <td>{{ $v->pivot->tipoPosse->nome }}</th>
                </tr>
            @endforeach

        </table>

        @include('backend.components.unidades-produtivas-latlng.html')
    @endslot
    @endcardater

    <div class="row mb-4">
        <div class="col">
            {{ form_cancel(App\Helpers\General\AppHelper::prevUrl(route('admin.core.produtor.index')), 'Voltar', 'btn btn-danger px-4') }}
        </div>

        @can('edit same operational units farmers')
            <div class="col text-right">
                <a href="{{ route('admin.core.produtor.edit', ['produtor' => $produtor]) }}" class="btn btn-primary px-5"
                    form="form-builder">Editar</a>
            </div>
        @endcan
    </div>
@endsection

@push('after-scripts')
    @include('backend.components.unidades-produtivas-latlng.scripts', ["produtor"=>$produtor])
@endpush
