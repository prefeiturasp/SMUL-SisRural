@extends('backend.layouts.app')

@section('title', app_name() . ' | Sobre')

@section('content')
<div class="card card-ater">
    <div class="card-header">
        <div class="row">
            <div class="col-sm-5">
                <h1 class="card-title mb-0 mt-1 h4">
                    Sobre
                </h1>
            </div>

            <div class="col-sm-7 pull-right">
                <div class="float-right">
                    @can('update', $sobre)
                        <a href="{{ route('admin.core.sobre.edit', 1) }}" class="btn btn-primary px-5">Editar Sobre</a>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        {!! $sobre->texto !!}
    </div>
</div>
@endsection
