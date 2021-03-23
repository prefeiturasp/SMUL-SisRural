@extends('backend.layouts.app')

@section('title', __('labels.backend.access.users.management') . ' | ' . __('labels.backend.access.users.edit'))

@section('content')
    {{ html()->modelForm($user, 'PATCH', route('admin.auth.user.update', $user->id))->class('form-horizontal')->open() }}
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-sm-5">
                    <h1 class="card-title mb-0 h4">
                        @lang('labels.backend.access.users.management')
                        <small class="text-muted">@lang('labels.backend.access.users.edit')</small>
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
                                ->required() }}
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
                            {{  auth()->user()->isTecnico()
                                ?
                                html()->text('document')
                                    ->class('form-control')
                                    ->placeholder('CPF')
                                    ->attribute('_mask', '999.999.999-99')
                                    ->attribute('readonly', 'readonly')
                                    ->required()
                                    :
                                html()->text('document')
                                    ->class('form-control')
                                    ->placeholder('CPF')
                                    ->attribute('_mask', '999.999.999-99')
                                    ->required() }}
                        </div><!--col-->
                    </div><!--form-group-->

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

            @include('backend.components.iframe.html', ["id"=>$rolesId, "src"=>$rolesSrc])

        </div><!--card-body-->

        <div class="card-footer">
            <div class="row">
                <div class="col">
                    {{ form_cancel(route('admin.auth.user.index'), __('buttons.general.cancel')) }}
                </div><!--col-->

                <div class="col text-right">
                    {{ form_submit(__('buttons.general.crud.update')) }}
                </div><!--row-->
            </div><!--row-->
        </div><!--card-footer-->
    </div><!--card-->
    {{ html()->closeModelForm() }}
@endsection

@push('after-scripts')
    @include('backend.components.iframe.scripts', ["id"=>$rolesId, "src"=>$rolesSrc])
@endpush
