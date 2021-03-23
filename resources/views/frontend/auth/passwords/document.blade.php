@extends('frontend.layouts.app')

@section('title', app_name() . ' | ' . __('labels.frontend.passwords.reset_password_box_title'))

@section('content')
<div class="page row justify-content-center">
    <div class="col-md-12 col-lg-10 col-xl-8">
        <div class="card-group">
            <div class="card p-4">
                <div class="card-body">
                    @if(session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ html()->form('POST', route('frontend.auth.password.document.post'))->open() }}


                    <h1 class="title">@lang('labels.frontend.passwords.reset_password_box_title')</h1>

                    <fieldset>
                        <legend class="subtitle mt-5">Informe o seu CPF</legend>

                        <div class="mt-3"></div>

                        <label for="document">CPF</label>

                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-user"></i>
                                </span>
                            </div>

                            {{ html()->input()
                                ->name('document')
                                ->id('document')
                                ->class('form-control')
                                ->placeholder(__('validation.attributes.frontend.document'))
                                ->attribute('maxlength', 20)
                                ->required()
                                ->autofocus() }}
                        </div>

                        <div class="row mt-3">
                            <div class="col">
                                {{ form_submit(__('labels.frontend.passwords.send_password_reset_link_button'), 'btn btn-primary btn-block') }}
                            </div>
                        </div>

                        <div class="row mt-5">
                            <div class="col-12 text-right">
                                <a class="btn btn-link btn-sm px-0" href="{{ route('frontend.auth.login')}}">Voltar</a>
                            </div>
                        </div>
                    </fieldset>
                    {{ html()->form()->close() }}
                </div>
            </div>
            <div class="card text-white bg-primary d-none d-md-block">
                <div role="img" aria-label="Imagem de uma plantação" class="card-body image-login text-center">

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
