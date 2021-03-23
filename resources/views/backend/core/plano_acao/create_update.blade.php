@extends('backend.layouts.app')

@section('content')
    @if(!$planoAcao)
        @cardater(['class'=>'card-info'])
            @slot('body')
                <div class="row align-items-center">
                    <div class="col col-lg-10">
                        <h4 class="mb-0 text-white2">Preencha os dados abaixos para prosseguir.</h4>
                    </div>

                    <div class="col col-lg-2 text-right">
                        <button type="submit" class="btn btn-primary px-5" form="form-builder">Prosseguir</button>
                    </div>
                </div>
            @endslot
        @endcardater
    @endif

    <div class="card-ater">
        <div class="card-body-ater">
            {!!form_start($form)!!}

            @can('view menu plano_acao')
                @if (@$planoAcao)
                    {!!form_until($form, 'card-inf-pda-end')!!}

                    <div class="mt-5">
                        <div id="cadastrar-item" class="card-item">
                            @include('backend.components.iframe.html', ["id"=>$itemId, "src"=>$itemSrc])
                        </div>
                    </div>
                @endif
            @endcan

            {!!form_rest($form)!!}

            @if (@$planoAcao)
                <div class="mt-5">
                    <div id="cadastrar-historico" class="card-historico">
                        @include('backend.components.iframe.html', ["id"=>$historicoId, "src"=>$historicoSrc])
                    </div>
                </div>
            @endif
        </div>

        <br/><br/><br/>

        <div class="card-footer-ater">
            <div class="row">
                <div class="col">
                    {{ form_cancel(route('admin.core.plano_acao.index'), __('buttons.general.cancel'), 'btn btn-danger px-4') }}
                </div>

                <div class="col text-right">
                    <button type="submit" class="btn btn-primary px-5" form="form-builder">
                            @if (@$planoAcao)
                                Salvar
                            @else
                                Prosseguir
                            @endif
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-scripts')
    @can('view menu plano_acao')
        @if (@$planoAcao)
            @include('backend.components.iframe.scripts', ["id"=>$itemId, "src"=>$itemSrc])
            @include('backend.components.iframe.scripts', ["id"=>$historicoId, "src"=>$historicoSrc])
        @endif
    @endcan
@endpush
