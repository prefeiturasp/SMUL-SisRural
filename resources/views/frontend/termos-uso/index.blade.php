@extends('frontend.layouts.app')

@section('title', app_name() . ' | Termos de Uso')

@push('after-styles')
    <style>
        html, body, #app, #app > .container {
            height: 100%;
        }
        .card-body {
            overflow-y: auto;
        }
    </style>
@endpush

@section('content')
    <div class="container justify-content-center h-100">
        <div class="row card-group h-100 pb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="p-2">
                        <strong><h2 align="center">Termos de Uso</h2></strong>
                        <br/>
                        {!!$terms->texto!!}
                    </div>
                </div>

                <div class="card-footer text-muted">
                    <div class="p-2">
                        @if($acceptTerms)
                            {{ Form::open(array('url' => route("frontend.termos-de-uso"))) }}
                                <div class="row mt-2">
                                    <div class="col">
                                        {!! Form::checkbox('fl_accept_terms', 1, '', ['id'=> 'fl_accept_terms']) !!}
                                        {!! Form::label('fl_accept_terms', 'Eu li e aceito os termos e condições', array('class' => 'ml-1 text-dark noselect')) !!}

                                        {!! @Form::hidden('user_id', auth()->user()->id) !!}
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col">
                                        <button type="submit" class="btn btn-primary px-4">Continuar</button>
                                    </div>

                                    <div class="col text-right">
                                        <a href="/logout" class="btn btn btn-outline-danger px-4">Voltar</a>
                                    </div>
                                </div>
                            {{ Form::close() }}
                        @else
                            <div class="row">
                                <div class="col">
                                    <a href="/" class="btn btn-outline-danger px-5">Voltar</a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
