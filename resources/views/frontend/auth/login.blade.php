@extends('frontend.layouts.app')

@section('title', app_name() . ' | ' . __('labels.frontend.auth.login_box_title')) @section('content')
<div class="page page-login row justify-content-center">
    <ul id="accessibility">
        <li>
            <a accesskey="1" class="fix-anchor" href="#main-content">
                Ir para o formulário <span>1</span>
            </a>
        </li>
    </ul>

    <div id="main-content" class="col-md-12 col-lg-12 col-xl-10">
        <div class="card-group">
            <div class="card p-4">
                <div class="card-body">
                    {{ html()->form('POST', route('frontend.auth.login.post'))->open() }}
                    <fieldset>
                        <h1 aria-label="SisRural" class="title">
                            <img alt="Logotipo SisRural" src="/img/backend/logo-black.svg" />
                        </h1>

                        <legend class="subtitle mt-5">Faça seu Login</legend>

                        <label for="document" class="mt-1">
                            <abbr title="Cadastro de Pessoa Física">CPF</abbr>
                        </label>

                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="c-icon cil-user"></i>
                                </span>
                            </div>

                            {{ html()->input()->name('document')->id('document')->class('form-control')->placeholder(__('validation.attributes.frontend.document'))->attribute('maxlength', 20)->attribute('aria-label', 'CPF')->required() }}
                        </div>

                        <label for="password" class="mt-1">Senha</label>

                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="c-icon cil-lock-locked"></i>
                                </span>
                            </div>

                            {{ html()->password('password')->id('password')->class('form-control')->attribute('aria-label', 'Senha')->placeholder(__('validation.attributes.frontend.password'))->required() }}
                        </div>

                        <label for="role" class="mt-1">
                            <abbr title="Domínio">Domínio</abbr>
                        </label>

                        <div class="input-group mb-4">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="c-icon cil-user"></i>
                                </span>
                            </div>

                            {{ html()->select()->name('id')->id('role')->options(['' => 'Selecione'])->class('form-control')->attribute('maxlength', 20)->attribute('aria-label', 'Domínio') }}
                        </div>

                        <div class="row">
                            <div class="col-6 form-group">
                                <div class="checkbox">
                                    {{ html()->checkbox('remember_foo', true, 1) }}
                                    {{ html()->label(__('labels.frontend.auth.remember_me'))->for('remember_foo')->class('label-checkbox') }}
                                </div>
                            </div>
                        </div>

                        @if (config('access.captcha.login'))
                            <div class="row">
                                <div class="col">
                                    @captcha
                                    {{ html()->hidden('captcha_status', 'true') }}
                                </div>
                                <!--col-->
                            </div>
                            <!--row-->
                        @endif

                        <div class="row mt-2">
                            <div class="col-12 col-md-4">
                                {{ form_submit(__('labels.frontend.auth.login_button'), 'btn btn-primary px-5') }}
                            </div>

                            <div class="col-12 text-right col-md-8">
                                <a class="btn btn-link btn-sm px-0 mt-1"
                                    href="{{ route('frontend.auth.password.reset') }}">@lang('labels.frontend.passwords.forgot_password')</a>
                            </div>
                        </div>
                    </fieldset>
                    {{ html()->form()->close() }}
                </div>
            </div>

            <div class="card text-white bg-primary">
                <div class="block-text p-5">
                    <div class="text">
                        <p><b>O Sistema de Assistência Técnica e Extensão Rural e Ambiental – SisRural</b> visa apoiar
                            políticas públicas de desenvolvimento rural sustentável e de preservação ambiental,
                            oferecendo ferramenta de consulta e coleta de dados em campo, aplicação de formulários e
                            acompanhamento de planos de ação. É um instrumento de trabalho para utilização por técnicos
                            autorizados, por conterem dados pessoais e de uso exclusivo das políticas públicas a que se
                            destinam.</p>

                        <p>Trabalha on-line e off-line, por meio de aplicação web e aplicativo para uso em campo. Foi
                            desenvolvido pela Prefeitura de São Paulo, por meio do projeto Ligue os Pontos, premiado
                            pelo Mayors Challenge da Bloomberg Philanthropies em 2016. A aplicação usa código aberto e
                            está disponível para replicação por outros entes interessados.</p>
                    </div>

                    <a href="https://ligueospontos.prefeitura.sp.gov.br/agricultura-familiar/sisrural/" target="_blank"
                        alt="Saiba mais" class="btn btn-outline-light px-4 mt-2 mb-4">&nbsp;&nbsp;Saiba
                        Mais&nbsp;&nbsp;</a>

                    <div class="font-weight-bold">Realização</div>
                    <div class="logos-prefeitura mt-1">
                        <a aria-label="Link para o site da Cidade de São Paulo" href="http://www.capital.sp.gov.br/"
                            target="_blank">
                            <img alt="Cidade de São Paulo" src="/img/frontend/login/logo-sp.png" width="158" />
                        </a>
                        <a aria-label="Link para o site do Ligue os Pontos"
                            href="https://ligueospontos.prefeitura.sp.gov.br/" target="_blank">
                            <img alt="Ligue os Pontos" src="/img/frontend/login/logo-ligue-pontos.png" width="56" />
                        </a>
                        <a aria-label="Link para o site da Bloomberg Philanthropies"
                            href="https://bloombergcities.jhu.edu/mayors-challenge/" target="_blank">
                            <img alt="Bloomberg Philanthropies" src="/img/frontend/login/logo-bloomberg.png"
                                width="110" />
                        </a>
                    </div>

                    <div class="font-weight-bold mt-3">Parceria</div>
                    <div class="logos-prefeitura mt-1">
                        <a aria-label="Link para o site do Governo de São Paulo"
                            href="https://www.agricultura.sp.gov.br/" target="_blank">
                            <img alt="Link para o site do Governo de São Paulo"
                                src="/img/frontend/login/logo-governo-sp.png" width="158" />
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('after-scripts')
<script>
    $(document).ready(function() {
        $("#document").blur(function() {
            $.post('/api/auth/document_roles', {
                    document: $("#document").val()
                },
                function(data) {
                    $('#role').empty();

                    if (data.roles) {
                        data.roles.forEach(function(e, i) {
                            $('#role').append($('<option></option>').val(e.id).text(e
                            .nome));
                        });
                    }
                });
        }).blur();
    })

</script>
<noscript>
    <div role="alert">Para navegar no sistema é necessário habilitar o Javascript</div>
</noscript>

@if (config('access.captcha.login'))
    @captchaScripts
@endif
@endpush
