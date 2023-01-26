@extends('backend.layouts.app')

@section('title', app_name() . ' | Produtor')

@section('content')
    <h1 class="mb-4">{{ !@$produtor?"Criar Novo Produtor":"Editar Produtor/a: ".$produtor->nome}}</h1>

    @if(@$unidadeProdutiva->id)
        @cardater(['class'=>''])
            @slot('body')
                <div class="row align-items-center">
                    <div class="col col-lg-10">
                        <h4 class="mb-0 text-white2">Complete agora os dados do/a Produtor/a ou pule para próximo passo.</h4>
                    </div>

                    <div class="col col-lg-2 text-right">
                        <a href="{{route('admin.core.novo_produtor_unidade_produtiva.unidade_produtiva_edit', ['produtor'=>$produtor, 'unidadeProdutiva'=>$unidadeProdutiva])}}" class="btn btn-primary px-5" form="form-builder">PULAR</a>
                    </div>
                </div>
            @endslot
        @endcardater
    @endif

    <div class="card-ater">
        <div class="card-body-ater">
            {{-- @include('backend.components.title-form.index', ['title' => $title]) --}}

            <div class="form-produtor">
                {!! form($form) !!}
            </div>

            @if ($produtor)
                <div id="a-unidade-produtiva">
                    @include('backend.components.iframe.html', ["id"=>$containerId, "label"=>"Lista de Unidades Produtivas", "src"=>$containerSrc])
                </div>
            @endif

            @if (@!$produtor)
                @include('backend.components.card-iframe-add.html', ["title"=>"Unidades Produtivas", "data"=>"a-unidade-produtiva", "label"=>"Vincular Unidade Produtiva"])
            @endif
        </div>

        <div class="card-footer-ater">
            <div class="row">
                <div class="col">
                    {{ form_cancel(App\Helpers\General\AppHelper::prevUrl(route('admin.core.produtor.index')), __('buttons.general.cancel'), 'btn btn-danger px-4') }}
                </div>

                <div class="col text-right">
                    <button type="submit" class="btn btn-primary px-5" form="form-builder">Salvar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-scripts')
    @if ($produtor)
        @include('backend.components.iframe.scripts', ["id"=>$containerId, "src"=>$containerSrc])
    @endif

    @include('backend.scripts.estado-cidade-select2');

    <script>
        $(function () {
            selectAutoYesNo("#fl_agricultor_familiar", '#card-agricultor-familiar');
            selectAutoYesNo("#fl_assistencia_tecnica", '#card-assistencia-tecnica');
            selectAutoYesNo("#fl_contrata_mao_de_obra_externa", '#card-mao-de-obra-externa');
            selectAutoYesNo("#fl_portador_deficiencia", '#card-portador-deficiencia');

            selectAutoYesNo("#fl_agricultor_familiar_dap", '.card-agricultor-familiar-dap');
            selectAutoYesNo("#fl_comunidade_tradicional", '#card-comunidade-tradicional');

            selectAutoYesNo("#fl_tipo_parceria", '#card-tipo-parceria');
            selectAutoYesNo("#fl_cnpj", '#card-cnpj');
            selectAutoYesNo("#fl_nota_fiscal_produtor", '#card-nota-fiscal-produtor');

            selectAutoNoYes("#fl_reside_unidade_produtiva", '#card-endereco');
        });
    </script>
@endpush
