@extends('backend.layouts.app')

@section('content')
    <div class="card-ater">
        <div class="card-body-ater">
            {!! form($form) !!}
        </div>

        <div class="card-footer-ater">
            <div class="row">
                <div class="col">
                    {{ form_cancel(route('admin.core.checklist.index'), 'Voltar', 'btn btn-danger px-4') }}
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
