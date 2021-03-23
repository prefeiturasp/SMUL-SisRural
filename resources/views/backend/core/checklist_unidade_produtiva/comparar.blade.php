@extends('backend.layouts.app')

@section('content')
    <div class="card-ater">
        <div class="card-body-ater">
            {!! form($form) !!}
        </div>

        <div class="card-footer-ater">
            <div class="row">
                <div class="col">
                    {{ form_cancel(route('admin.core.checklist_unidade_produtiva.index'), __('buttons.general.cancel'), 'btn btn-danger px-4') }}
                </div>

                <div class="col text-right">
                    <button type="submit" class="btn btn-primary px-5" form="form-builder">Comparar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-scripts')
    <script>
        $("select[multiple='multiple']").select2();
    </script>
@endpush
