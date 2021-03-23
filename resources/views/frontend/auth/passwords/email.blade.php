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

                    {{ html()->form('POST', route('frontend.auth.password.email.post'))->class('form-horizontal')->open() }}

                    <h2 class="title">ATER</h2>

                    <p class="subtitle mt-2">@lang('labels.frontend.passwords.reset_password_box_title')</p>

                    <div class="row mt-5">
                        <div class="col">
                            <div class="form-group">
                                    {{ html()->label(__('validation.attributes.frontend.email'))->for('email') }}

                                    {{ html()->email('email')
                                        ->class('form-control')
                                        ->placeholder(__('validation.attributes.frontend.email'))
                                        ->attribute('maxlength', 191)
                                        ->required()
                                        ->autofocus() }}
                            </div><!--form-group-->
                        </div><!--col-->
                    </div><!--row-->

                    <div class="row mt-5">
                        <div class="col-12">
                            {{
                                form_submit(
                                     __("labels.frontend.passwords.send_password_reset_link_button"),
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
