@extends('backend.layouts.app')

@section('title', __('labels.backend.access.users.management') . ' | ' . __('labels.backend.access.users.create'))

@section('content')
    {{ html()->form('POST', route('admin.auth.user.store'))->class('form-horizontal')->open() }}
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-5">
                    <h1 class="card-title mb-0 h4">
                        @lang('labels.backend.access.users.management')
                        <small class="text-muted">@lang('labels.backend.access.users.create')</small>
                    </h1>
                </div><!--col-->
            </div><!--row-->

            <hr>

            <div class="row mt-4 mb-4">
                <div class="col">
                    <div class="form-group row">
                        {{ html()->label(__('validation.attributes.backend.access.users.first_name'))->class('col-md-2 form-control-label')->for('first_name') }}

                        <div class="col-md-10">
                            {{ html()->text('first_name')
                                ->class('form-control')
                                ->placeholder(__('validation.attributes.backend.access.users.first_name'))
                                ->attribute('maxlength', 191)
                                ->required()
                                ->autofocus() }}
                        </div><!--col-->
                    </div><!--form-group-->

                    <div class="form-group row">
                        {{ html()->label(__('validation.attributes.backend.access.users.last_name'))->class('col-md-2 form-control-label')->for('last_name') }}

                        <div class="col-md-10">
                            {{ html()->text('last_name')
                                ->class('form-control')
                                ->placeholder(__('validation.attributes.backend.access.users.last_name'))
                                ->attribute('maxlength', 191)
                                ->required() }}
                        </div><!--col-->
                    </div><!--form-group-->

                    <div class="form-group row">
                        {{ html()->label(__('validation.attributes.backend.access.users.email'))->class('col-md-2 form-control-label')->for('email') }}

                        <div class="col-md-10">
                            {{ html()->email('email')
                                ->class('form-control')
                                ->placeholder(__('validation.attributes.backend.access.users.email'))
                                ->attribute('maxlength', 191)
                                ->required() }}
                        </div><!--col-->
                    </div><!--form-group-->

                    <div class="form-group row">
                        {{ html()->label('CPF')->class('col-md-2 form-control-label')->for('document') }}

                        <div class="col-md-10">
                            {{ html()->text('document')
                                ->class('form-control')
                                ->placeholder('CPF')
                                ->attribute('_mask', '999.999.999-99')
                                ->required() }}
                        </div><!--col-->
                    </div><!--form-group-->

                    <div class="form-group row">
                        {{ html()->label('Telefone')->class('col-md-2 form-control-label')->for('phone') }}

                        <div class="col-md-10">
                            {{ html()->text('phone')
                                ->class('form-control')
                                ->placeholder('Telefone')
                                ->attribute('_mask', '99 99999999?9')
                            }}
                        </div><!--col-->
                    </div><!--form-group-->

                    <div class="form-group row">
                        {{ html()->label('Endereço')->class('col-md-2 form-control-label')->for('address') }}

                        <div class="col-md-10">
                            {{ html()->text('address')
                                ->class('form-control')
                                ->placeholder('Endereço')
                            }}
                        </div><!--col-->
                    </div><!--form-group-->

                    <div class="form-group row">
                        {{ html()->label('Onde Trabalha?')->class('col-md-2 form-control-label')->for('work') }}

                        <div class="col-md-10">
                            {{ html()->text('work')
                                ->class('form-control')
                                ->placeholder('Onde Trabalha?')
                            }}
                        </div><!--col-->
                    </div><!--form-group-->

                    <div class="form-group row">
                        {{ html()->label(__('validation.attributes.backend.access.users.password'))->class('col-md-2 form-control-label')->for('password') }}

                        <div class="col-md-10">
                            {{ html()->password('password')
                                ->class('form-control')
                                ->placeholder(__('validation.attributes.backend.access.users.password'))
                                ->required() }}
                        </div><!--col-->
                    </div><!--form-group-->

                    <div class="form-group row">
                        {{ html()->label(__('validation.attributes.backend.access.users.password_confirmation'))->class('col-md-2 form-control-label')->for('password_confirmation') }}

                        <div class="col-md-10">
                            {{ html()->password('password_confirmation')
                                ->class('form-control')
                                ->placeholder(__('validation.attributes.backend.access.users.password_confirmation'))
                                ->required() }}
                        </div><!--col-->
                    </div><!--form-group-->

                    <div class="form-group row">
                        {{ html()->label(__('validation.attributes.backend.access.users.active'))->class('col-md-2 form-control-label')->for('active') }}

                        <div class="col-md-10">
                            <label class="switch switch-label switch-pill switch-primary">
                                {{ html()->checkbox('active', true)->class('switch-input') }}
                                <span class="switch-slider" data-checked="yes" data-unchecked="no"></span>
                            </label>
                        </div><!--col-->
                    </div><!--form-group-->

                    @if($roles->count())
                        <div class="form-group row">
                            {{ html()->label(__('labels.backend.access.users.table.abilities'))->class('col-md-2 form-control-label') }}

                            <div class="col-md-10">
                                <div class="table-roles table-responsive">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>@lang('labels.backend.access.users.table.roles')</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>
                                                @if($roles->count())
                                                    @foreach($roles as $role)
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <div class="checkbox d-flex align-items-center">
                                                                    {{ html()->label(
                                                                            html()->radio('roles[]', old('roles') && in_array($role->name, old('roles')) ? true : false, $role->name)
                                                                                  ->class('switch-input')
                                                                                  ->id('role-'.$role->id)
                                                                            . '<span class="switch-slider" data-checked="on" data-unchecked="off"></span>')
                                                                        ->class('switch switch-label switch-pill switch-primary mr-2')
                                                                        ->for('role-'.$role->id) }}
                                                                    {{ html()->label(ucwords($role->name))->for('role-'.$role->id) }}
                                                                </div>
                                                            </div>
                                                        </div><!--card-->
                                                    @endforeach
                                                @endif
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div><!--col-->
                        </div><!--form-group-->
                    @endif

                    @if(Auth::user()->hasRole(['Administrator', 'Dominio']))
                        <div class="form-group row">
                            {{ html()->label('Domínio')->class('col-md-2 form-control-label')->for('dominios') }}

                            <div class="col-md-10">
                                {{ html()->select('dominios', $dominios)
                                    ->class('form-control')
                                    ->placeholder('Selecione')
                                    ->required()}}
                            </div><!--col-->
                        </div><!--form-group-->
                    @endif

                    @if(Auth::user()->hasRole(['Dominio', 'Unidade Operacional']))
                        <div class="form-group row">
                            {{ html()->label('Unidade Operacional')->class('col-md-2 form-control-label')->for('unidades_operacionais[]') }}

                            <div class="col-md-10">
                                {{ html()->select('unidades_operacionais[]', $unidadeOperacionais)
                                    ->class('form-control')
                                    ->multiple()
                                    ->required()}}
                            </div><!--col-->
                        </div><!--form-group-->
                    @endif

                    {{-- Permissions via Domínio --}}
                    @if(Auth::user()->hasRole(['Dominio', 'Administrator']))
                        @if($permissions->count())
                            <div class="form-group row">
                                {{ html()->label('Permissões')->class('col-md-2 form-control-label')->for('permissions[]') }}
                                <div class="col-md-10">
                                        @foreach($permissions as $permission)
                                            <div class="checkbox d-flex align-items-center">
                                                {{ html()->label(
                                                        html()->checkbox('permissions[]', in_array($permission->name, @$userPermissions ? $userPermissions : []), $permission->name)
                                                                ->class('switch-input')
                                                                ->id('permission-'.$permission->id)
                                                            . '<span class="switch-slider" data-checked="on" data-unchecked="off"></span>')
                                                        ->class('switch switch-label switch-pill switch-primary mr-2')
                                                    ->for('permission-'.$permission->id) }}
                                                {{ html()->label(__($permission->name))->for('permission-'.$permission->id) }}
                                            </div>
                                        @endforeach
                                </div><!--col-->
                            </div><!--form-group-->
                        @endif
                    @endif
                </div><!--col-->
            </div><!--row-->
        </div><!--card-body-->

        <div class="card-footer clearfix">
            <div class="row">
                <div class="col">
                    {{ form_cancel(route('admin.auth.user.index'), __('buttons.general.cancel')) }}
                </div><!--col-->

                <div class="col text-right">
                    {{ form_submit(__('buttons.general.crud.create')) }}
                </div><!--col-->
            </div><!--row-->
        </div><!--card-footer-->
    </div><!--card-->
    {{ html()->form()->close() }}
@endsection

@push('after-scripts')
    <style>
        .table, .card:last-child {
            margin-bottom:0px;
        }
        .table-roles label {
            margin-bottom:0px;
        }
    </style>
    @include("backend.auth.user.includes.create-or-update")
@endpush
