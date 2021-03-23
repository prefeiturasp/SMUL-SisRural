@extends('frontend.layouts.app')

@section('title', app_name() . ' | ' . __('labels.frontend.passwords.expired_password_box_title'))

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

                    {{ html()->form('PATCH', route('frontend.auth.password.expired.update'))->class('form-horizontal')->open() }}

                    <h2 class="title">ATER</h2>

                    <p class="subtitle mt-2">Informe os dados para resetar sua senha.</p>

                    <div class="row mt-5">
                        <div class="col">
                            <div class="form-group">
                                {{ html()->label(__('validation.attributes.frontend.old_password'))->for('old_password') }}

                                {{ html()->password('old_password')
                                    ->class('form-control')
                                    ->placeholder(__('validation.attributes.frontend.old_password'))
                                    ->required() }}
                            </div><!--form-group-->
                        </div><!--col-->
                    </div><!--row-->

                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                {{ html()->label(__('validation.attributes.frontend.password'))->for('password') }}

                                {{ html()->password('password')
                                    ->class('form-control')
                                    ->placeholder(__('validation.attributes.frontend.password'))
                                    ->required() }}
                            </div><!--form-group-->
                        </div><!--col-->
                    </div><!--row-->

                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                {{ html()->label(__('validation.attributes.frontend.password_confirmation'))->for('password_confirmation') }}

                                {{ html()->password('password_confirmation')
                                    ->class('form-control')
                                    ->placeholder(__('validation.attributes.frontend.password_confirmation'))
                                    ->required() }}
                            </div><!--form-group-->
                        </div><!--col-->
                    </div><!--row-->

                    <div class="row mt-5">
                        <div class="col-12">
                            {{
                                form_submit(
                                    __("labels.frontend.passwords.update_password_button"),
                                    'btn btn-primary px-5'
                                )
                            }}
                        </div>
                    </div>

                    {{ html()->form()->close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
