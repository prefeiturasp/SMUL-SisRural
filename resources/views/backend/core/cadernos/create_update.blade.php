@extends('backend.layouts.app')

@section('content')
    <div class="card-ater">
        <div class="card-body-ater">
            {!! form($form) !!}

            @if (@$caderno)
                <div class="mt-5">
                    <div id="a-arquivos">
                        @include('backend.components.iframe.html', ["id"=>$arquivosId, "src"=>$arquivosSrc])
                    </div>
                </div>
            @endif

            @if (@!$caderno)
                @include('backend.components.card-iframe-add.html', ["title"=>"Arquivos", "data"=>"a-arquivos", "label"=>"Cadastrar Arquivo"])
            @endif
        </div>

        <div class="card-footer-ater">
            <div class="row">
                <div class="col">
                    {{ form_cancel($back, __('buttons.general.cancel'), 'btn btn-danger px-4') }}
                </div>

                <div class="col text-right">
                    <button type="submit" class="btn btn-primary px-5" form="form-builder">Salvar</button>
                </div>
            </div>
        </div>
    </div>

    <div id="modal-caderno-campo" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Aviso</h5>
            </div>
            <div class="modal-body">
              <p>Existe um caderno de campo em aberto (rascunho), vinculado a esse Produtor.</p>
              <p>Você será redirecionado.</p>
            </div>
            <div class="modal-footer">
              <a type="button" class="btn btn-primary">OK</a>
            </div>
          </div>
        </div>
    </div>
@endsection

@push('after-scripts')
    @if (@$caderno)
        @include('backend.components.iframe.scripts', ["id"=>$arquivosId, "src"=>$arquivosSrc])
    @endif

    <script>
        $("select[multiple='multiple']").select2();
    </script>
@endpush
