@extends('backend.layouts.app')

@section('title', __('labels.backend.access.users.management') . ' | ' . __('labels.backend.access.users.deleted'))

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-sm-5">
                <h1 class="card-title mb-0 h4">
                    @lang('labels.backend.access.users.management')
                    <small class="text-muted">@lang('labels.backend.access.users.deleted')</small>
                </h1>
            </div><!--col-->

            <div class="col-sm-7">
                @include('backend.auth.user.includes.header-buttons', ['showAll'=>false, 'showAdd'=>false, 'showActivated'=>true, 'showDesactivated'=>true, 'showDeleted'=>false])
            </div><!--col-->
        </div><!--row-->

        <div class="row mt-4">
            <div class="col">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>CPF</th>
                            <th>Telefone</th>
                            <th>Papéis</th>
                            <th>Domínio</th>
                            <th>Unidades Operacionais</th>
                            <th>Ações</th>
                        </tr>
                        </thead>
                        <tbody>

                        @if($users->count())
                            @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->first_name. ' '.$user->last_name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone }}</td>
                                    <td>{{ App\Helpers\General\AppHelper::formatCpfCnpj($user->document) }}</td>
                                    <td>{{ $user->roles_label }}</td>
                                    <td>{{ $user->singleDominio()->nome }}</td>
                                    <td>{{ @join(", ", @$user->unidadesOperacionaisNS->pluck('nome')->toArray()) }}</td>
                                    <td>@include('backend.auth.user.includes.actions', ['user' => $user])</td>
                                </tr>
                            @endforeach
                        @else
                            <tr><td colspan="8"><p class="text-center">@lang('strings.backend.access.users.no_deleted')</p></td></tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div><!--col-->
        </div><!--row-->
        <div class="row">
            <div class="col-7">
                <div class="float-left">
                    {!! $users->total() !!} {{ trans_choice('labels.backend.access.users.table.total', $users->total()) }}
                </div>
            </div><!--col-->

            <div class="col-5">
                <div class="float-right">
                    {!! $users->render() !!}
                </div>
            </div><!--col-->
        </div><!--row-->
    </div><!--card-body-->
</div><!--card-->
@endsection
