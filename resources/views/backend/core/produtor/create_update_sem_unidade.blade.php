@extends('backend.layouts.app')

@section('title', app_name() . ' | Produtor')

@section('content')
    <div class="card">
        <div class="card-body">
            @include('backend.components.title-form.index', ['title' => $title])

            <h4 class="mb-4">Produtor: {{$produtorSemUnidade->nome}}</h4>

            {!! form($form) !!}
        </div>

        <div class="card-footer">
            <div class="row">
                <div class="col">
                    {{ form_cancel(App\Helpers\General\AppHelper::prevUrl(route('admin.core.produtor.index_sem_unidade')), __('buttons.general.cancel'), 'btn btn-danger px-4') }}
                </div>

                <div class="col text-right">
                    <button type="submit" class="btn btn-primary px-5" form="form-builder">Salvar</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('scripts')
    <script>
        $(function () {
            $("select[name='unidade_produtiva_id']").select2();
        });
    </script>
@endsection
