@extends('backend.layouts.app-template', ['iframe'=>true])

@section('body')
    <style>
        body {
            background: transparent;
        }
    </style>

    <div class="p-4">
        {!! form($form) !!}

        <div class="row">
            <div class="col text-right">
                <button type="submit" class="btn btn-primary px-5" form="form-builder">Salvar detalhe da ação</button>
            </div>
        </div>
    </div>
@endsection
